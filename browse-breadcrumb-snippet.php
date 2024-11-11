<?
function dynamic_breadcrumbs() {
    global $post;

    // Only run this function for post type 'browse'
    if (get_post_type($post->ID) !== 'browse') {
        return '';
    }

    // Define the taxonomy slugs and parameters
    $taxonomy_to_param = [
        'manufacturer' => '_brand',
        'subseries' => '_subseries',
        'product_cat' => '_category',
        'series' => '_series'
    ];

    // Get the terms for each relevant taxonomy
    $terms = [];
    foreach ($taxonomy_to_param as $taxonomy => $param) {
        $term = get_the_terms($post->ID, $taxonomy);
        if ($term && !is_wp_error($term)) {
            $terms[$taxonomy] = $term[0]; // Assuming one term per taxonomy
        }
    }

    // Build the h1 title
    $breadcrumb_title = '<h1 class="browse-title"><a class="browse-title" href="/products">Shop</a> ';

    // Build breadcrumb_titles based on available terms in the preferred order
    if (isset($terms['manufacturer'])) {
        $breadcrumb_title .= ' <a class="browse-title-crumb" href="/products/?' . $taxonomy_to_param['manufacturer'] . '=' . $terms['manufacturer']->slug . '">' . $terms['manufacturer']->name . '</a>';
    }

    if (isset($terms['subseries'])) {
        $breadcrumb_title .= ' <a class="browse-title-crumb" href="/products/?' . $taxonomy_to_param['subseries'] . '=' . $terms['subseries']->slug . '">' . $terms['subseries']->name . '</a>';
    }

    if (isset($terms['series']) && !isset($terms['subseries'])) {
        $breadcrumb_title .= ' <a class="browse-title-crumb" href="/products/?' . $taxonomy_to_param['series'] . '=' . $terms['series']->slug . '">' . $terms['series']->name . '</a>';
    }

    if (isset($terms['product_cat'])) {
        $breadcrumb_title .= ' <a class="browse-title-crumb" href="/products/?' . $taxonomy_to_param['product_cat'] . '=' . $terms['product_cat']->slug . '">' . $terms['product_cat']->name . '</a>';
    }

    $breadcrumb_title .= '</h1>';

    // Build the base breadcrumb_trail
    $breadcrumb_trail = '<p class="browse-crumb-trail">';

    // Build breadcrumb_trails based on available terms in the preferred order and join with " / "
    $breadcrumbs = [];
	
	if (isset($terms['product_cat'])) {
        $breadcrumbs[] = '<a class="browse-crumb" href="/products/?' . $taxonomy_to_param['product_cat'] . '=' . $terms['product_cat']->slug . '">' . $terms['product_cat']->name . '</a>';
    }
	
    if (isset($terms['manufacturer'])) {
        $breadcrumbs[] = '<a class="browse-crumb" href="/products/?' . $taxonomy_to_param['manufacturer'] . '=' . $terms['manufacturer']->slug . '">' . $terms['manufacturer']->name . '</a>';
    }

    if (isset($terms['series'])) {
        $breadcrumbs[] = '<a class="browse-crumb" href="/products/?' . $taxonomy_to_param['series'] . '=' . $terms['series']->slug . '">' . $terms['series']->name . '</a>';
    }
	
	    if (isset($terms['subseries'])) {
        $breadcrumbs[] = '<a class="browse-crumb" href="/products/?' . $taxonomy_to_param['subseries'] . '=' . $terms['subseries']->slug . '">' . $terms['subseries']->name . '</a>';
    }

    $breadcrumb_trail .= implode(' / ', $breadcrumbs);
    $breadcrumb_trail .= '</p>';

    return $breadcrumb_title . $breadcrumb_trail;
}

// Register the shortcode
add_shortcode('dynamic_breadcrumbs', 'dynamic_breadcrumbs');
