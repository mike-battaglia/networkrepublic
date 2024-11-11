<?

// Hook into WordPress to add query params on page load
add_action('template_redirect', 'add_query_params_to_browse_page');
// Add a canonical tag to the head section of the page
add_action('wp_head', 'add_canonical_tag_to_browse_page');

function add_query_params_to_browse_page() {
    // Ensure this runs only on single 'browse' post type pages, not in admin or during AJAX requests
    if (is_singular('browse') && !is_admin() && !isset($_GET['elementor-preview'])) {
        // Check if query parameters are already present to avoid an infinite redirect loop
        if (isset($_GET['_brand']) || isset($_GET['_category']) || isset($_GET['_series']) || isset($_GET['_subseries'])) {
            return;
        }

        global $post;
        
        // Retrieve terms for each taxonomy
        $manufacturer_terms = get_the_terms($post->ID, 'manufacturer');
        $product_cat_terms = get_the_terms($post->ID, 'product_cat');
        $series_terms = get_the_terms($post->ID, 'series');
        $subseries_terms = get_the_terms($post->ID, 'subseries');

        // Construct the URL with appropriate query params
        $query_params = array();

        // Add manufacturer term slug as a query parameter if available
        if (!empty($manufacturer_terms) && !is_wp_error($manufacturer_terms)) {
            $query_params['_brand'] = $manufacturer_terms[0]->slug;
        }

        // Add product category term slug as a query parameter if available
        if (!empty($product_cat_terms) && !is_wp_error($product_cat_terms)) {
            $query_params['_category'] = $product_cat_terms[0]->slug;
        }

        // Add series term slug as a query parameter if available
        if (!empty($series_terms) && !is_wp_error($series_terms)) {
            $query_params['_series'] = $series_terms[0]->slug;
        }

        // Add subseries term slug as a query parameter if available
        if (!empty($subseries_terms) && !is_wp_error($subseries_terms)) {
            $query_params['_subseries'] = $subseries_terms[0]->slug;
        }
        
        // Add a fixed query parameter for sorting in stock
        $query_params['_sorting'] = 'in_stock';

        // Build the new URL with the query parameters
        if (!empty($query_params)) {
            $new_url = add_query_arg($query_params, get_permalink($post->ID));
            
            // Redirect to the new URL with a 301 (permanent) redirect
            wp_redirect($new_url, 301);
            exit;
        }
    }
}

function add_canonical_tag_to_browse_page() {
    // Ensure this runs only on single 'browse' post type pages, not in admin or during AJAX requests
    if (is_singular('browse') && !is_admin() && !wp_doing_ajax() && !isset($_GET['elementor-preview'])) {
        global $post;
        
        // Retrieve the current URL, including all query parameters
        $canonical_url = add_query_arg($_GET, get_permalink($post->ID));
        
        // Output the canonical tag to indicate the canonical version of the URL
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />';
    }
}
