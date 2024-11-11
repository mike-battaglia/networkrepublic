<?
function enqueue_dashicons_front_end() {
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'enqueue_dashicons_front_end');

function browse_files_shortcode() {
    global $post;

    // Initialize output variable
    $output = '';

    // Check if the repeater field has rows
    if ( have_rows('browse_file_uploads', $post->ID) ) {
        $output .= '<div class="browse-files">';
        // Loop through the rows of data
        while ( have_rows('browse_file_uploads', $post->ID) ) {
            the_row();
            // Get subfield values
            $file_name = get_sub_field('browse_page_file_name');
            $file = get_sub_field('browse_page_files');

            if ( $file ) {
                $file_url = $file['url'];
                // Construct the link with a download icon
                $output .= '<a href="' . esc_url($file_url) . '"><span class="dashicons dashicons-download"></span> ' . esc_html($file_name) . '</a> ';
            }
        }
        $output .= '</div>';
    }

    // Return the content to display
    return $output;
}
add_shortcode('browse_files', 'browse_files_shortcode');
