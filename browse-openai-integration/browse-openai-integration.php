<?php
/*
Plugin Name: Browse OpenAI Integration
Description: Adds buttons to the 'browse' custom post type editor to generate content via OpenAI API.
Version: 1.4
Author: Mike
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if ACF is active
if( ! function_exists('update_field') ) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>The Browse OpenAI Integration plugin requires Advanced Custom Fields to be installed and active.</p></div>';
    });
    return;
}

/** Add settings menu **/
add_action('admin_menu', 'boai_add_settings_page');
function boai_add_settings_page() {
    add_options_page(
        'OpenAI Settings',
        'OpenAI Settings',
        'manage_options',
        'boai-settings',
        'boai_render_settings_page'
    );
}

/** Register settings **/
add_action('admin_init', 'boai_register_settings');
function boai_register_settings() {
    register_setting('boai_settings_group', 'boai_openai_api_key');
    register_setting('boai_settings_group', 'boai_content_prompt');
    register_setting('boai_settings_group', 'boai_faq_prompt');
    register_setting('boai_settings_group', 'boai_model');
    register_setting('boai_settings_group', 'boai_max_tokens');
	register_setting('boai_settings_group', 'boai_faq_role');
}

/** Render settings page **/
function boai_render_settings_page() {
    $available_models = array('o1-preview', 'gpt-4', 'gpt-4o', 'gpt-3.5-turbo', 'gpt-4-32k', 'gpt-3.5-turbo-16k'); // Add more models if needed
    $selected_model = get_option('boai_model', 'gpt-4');
	#$selected_model = 'o1-preview';
    $max_tokens = get_option('boai_max_tokens', '500');
    ?>
    <div class="wrap">
        <h1>OpenAI Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('boai_settings_group');
            do_settings_sections('boai_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">OpenAI API Key</th>
                <td><input type="text" name="boai_openai_api_key" value="<?php echo esc_attr( get_option('boai_openai_api_key') ); ?>" size="50" /></td>
                </tr>
                <tr valign="top">
                <th scope="row">Model</th>
                <td>
                    <select name="boai_model">
                        <?php foreach ($available_models as $model): ?>
                            <option value="<?php echo esc_attr($model); ?>" <?php selected($selected_model, $model); ?>><?php echo esc_html($model); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                </tr>
                <tr valign="top">
                <th scope="row">Max Tokens</th>
                <td><input type="number" name="boai_max_tokens" value="<?php echo esc_attr( $max_tokens ); ?>" min="100" max="16384" /></td>
                </tr>
				<tr valign="top">
                <th scope="row">Role</th>
                <td><textarea name="boai_faq_role" rows="3" cols="50"><?php echo esc_textarea( get_option('boai_faq_role', 'You are an AI assistant created by OpenAI.') ); ?></textarea></td>
                </tr>
                <tr valign="top">
                <th scope="row">Content Prompt</th>
                <td><textarea name="boai_content_prompt" rows="3" cols="50"><?php echo esc_textarea( get_option('boai_content_prompt', 'Write a short SEO paragraph for the following title: $payload_terms') ); ?></textarea></td>
                </tr>
                <tr valign="top">
                <th scope="row">FAQ Prompt</th>
                <td><textarea name="boai_faq_prompt" rows="3" cols="50"><?php echo esc_textarea( get_option('boai_faq_prompt', 'Write an SEO FAQ section for $payload_terms, and format the FAQ for the frontend in HTML <details> tags.') ); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/** Add meta box to 'browse' post type **/
add_action('add_meta_boxes', 'boai_add_custom_meta_box');
function boai_add_custom_meta_box() {
    add_meta_box(
        'boai_meta_box',
        'OpenAI Content Generation',
        'boai_render_meta_box',
        'browse',
        'side',
        'high'
    );
}

/** Render the meta box content **/
function boai_render_meta_box($post) {
    if ($post->post_status != 'publish') {
        echo '<p>Please publish and refresh this page.</p>';
    } else {
        ?>
        <button id="boai-generate-content" class="button button-primary" style="width:100%; margin-bottom:10px;">Generate SEO Paragraph</button>
        <button id="boai-generate-faq" class="button button-primary" style="width:100%;">Generate SEO FAQ</button>
        <div id="boai-status" style="margin-top:10px;"></div>
        <?php
    }
}

/** Enqueue scripts **/
add_action('admin_enqueue_scripts', 'boai_enqueue_admin_scripts');
function boai_enqueue_admin_scripts($hook) {
    global $post;
    if ( 'post.php' != $hook || 'browse' != get_post_type( $post ) ) {
        return;
    }

    // Only enqueue scripts if the post is published
    if ($post->post_status == 'publish') {
        wp_enqueue_script('boai-admin-script', plugin_dir_url(__FILE__) . 'boai-admin.js', array('jquery'), '1.4', true);
        wp_localize_script('boai-admin-script', 'boai_ajax', array(
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('boai_nonce'),
            'spinner_url' => admin_url('images/spinner.gif')
        ));
    }
}

/** Handle AJAX for generating content **/
add_action('wp_ajax_boai_generate_content', 'boai_generate_content');
function boai_generate_content() {
    check_ajax_referer('boai_nonce', 'nonce');
    $post_id = intval($_POST['post_id']);

    // Check if post is published
    $post = get_post($post_id);
    if ($post->post_status != 'publish') {
        wp_send_json_error('Please publish the post and refresh the page.');
    }

    // Get taxonomy term names
    $taxonomies = get_object_taxonomies('browse', 'names');
    $term_names = array();
    foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_post_terms($post_id, $taxonomy);
        foreach ($post_terms as $term) {
            $term_names[] = $term->name;
        }
    }
    $payload_terms = implode(', ', $term_names);

    // Get the prompt from settings
    $prompt_template = get_option('boai_content_prompt', 'Write a short SEO paragraph for the following WordPress post title: $payload_terms');
    $prompt = str_replace('$payload_terms', $payload_terms, $prompt_template);

    // Send request to OpenAI API
    $api_key = get_option('boai_openai_api_key');
    if (!$api_key) {
        wp_send_json_error('OpenAI API key is not set.');
    }

    // Send request to OpenAI
    $response = boai_send_openai_request($prompt, $api_key);
    if (is_wp_error($response)) {
        wp_send_json_error($response->get_error_message());
    }

    // Update the ACF field
    update_field('browse_page_content', $response, $post_id);

    wp_send_json_success();
}

/** Handle AJAX for generating FAQ **/
add_action('wp_ajax_boai_generate_faq', 'boai_generate_faq');
function boai_generate_faq() {
    check_ajax_referer('boai_nonce', 'nonce');
    $post_id = intval($_POST['post_id']);

    // Check if post is published
    $post = get_post($post_id);
    if ($post->post_status != 'publish') {
        wp_send_json_error('Please publish the post and refresh the page.');
    }

    // Get taxonomy term names
    $taxonomies = get_object_taxonomies('browse', 'names');
    $term_names = array();
    foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_post_terms($post_id, $taxonomy);
        foreach ($post_terms as $term) {
            $term_names[] = $term->name;
        }
    }
    $payload_terms = implode(' ', $term_names);

    // Get the prompt from settings
    $prompt_template = get_option('boai_faq_prompt', 'Write an SEO FAQ section for $payload_terms. Include the proper structured data meta tags, and format the FAQ for the frontend in HTML <details> tags.');
    $prompt = str_replace('$payload_terms', $payload_terms, $prompt_template);

    // Send request to OpenAI API
    $api_key = get_option('boai_openai_api_key');
    if (!$api_key) {
        wp_send_json_error('OpenAI API key is not set.');
    }

    // Send request to OpenAI
    $response = boai_send_openai_request($prompt, $api_key);
    if (is_wp_error($response)) {
        wp_send_json_error($response->get_error_message());
    }

    // Update the ACF field
    update_field('browse_page_faq', $response, $post_id);

    wp_send_json_success();
}

/** Function to send request to OpenAI API **/
function boai_send_openai_request($prompt, $api_key) {
    $model = get_option('boai_model', 'gpt-4'); // Default to 'gpt-4' if not set
    $max_tokens = intval(get_option('boai_max_tokens', '500')); // Default to 500 if not set
	$boai_faq_role = get_option('boai_faq_role', 'You are an AI assistant created by OpenAI.'); 

    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $data = array(
        'model' => $model,
        'messages' => array(
            array('role' => 'system', 'content' => $boai_faq_role),
            array('role' => 'user', 'content' => $prompt)
        ),
        'max_tokens' => $max_tokens,
        'temperature' => 0.4,
    );

    $args = array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'body'    => json_encode($data),
        'timeout' => 60,
    );

    $response = wp_remote_post($endpoint, $args);

    if (is_wp_error($response)) {
        return $response;
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        if ($status_code === 200 && isset($result['choices'][0]['message']['content'])) {
            return trim($result['choices'][0]['message']['content']);
        } else {
            // Extract OpenAI's error message if available
            $error_message = 'Failed to retrieve response from OpenAI API.';
            if (isset($result['error']['message'])) {
                $error_message = 'OpenAI API Error: ' . $result['error']['message'];
            }
            return new WP_Error('openai_error', $error_message);
        }
    }
}
