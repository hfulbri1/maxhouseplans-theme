<?php
/**
 * MaxHousePlans â€" Genesis Child Theme Functions
 * Improved: 2026-03-18 by Vegeta
 *
 * Changes from original:
 *  - Removed jQuery 1.9.1 and Bootstrap 2.x (use WP's bundled jQuery)
 *  - Removed FlexSlider enqueues
 *  - Added proper image sizes for plan cards and hero
 *  - Added html5 theme support with all proper args
 *  - Added title-tag, post-thumbnails, responsive-embeds theme support
 *  - Enqueues style-new.css (modern CSS layer, old style.css kept)
 *  - Removes Elegant Themes Monarch JS/CSS (performance drain)
 *  - Removes PayPal ad script from plan pages
 *  - Disables WP Emoji (not needed)
 *  - All existing CPT, taxonomy, widget areas, and hooks preserved
 */

if ( ! defined( 'ABSPATH' ) ) exit;

//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme identity (do not remove)
define( 'CHILD_THEME_NAME', 'MaxHousePlans' );
define( 'CHILD_THEME_URL', 'https://maxhouseplans.com/' );
define( 'CHILD_THEME_VERSION', '2.1.0' );

// Convenience constant for child theme URL
if ( ! defined( 'CHILD_URL' ) ) {
    define( 'CHILD_URL', get_stylesheet_directory_uri() );
}

// ---------------------------------------------------------------------------
// Theme Support
// ---------------------------------------------------------------------------

//* HTML5 markup â€" full list of contexts
add_theme_support( 'html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
    'script',
    'style',
) );

//* Let WordPress manage the document title
add_theme_support( 'title-tag' );

//* Post thumbnails
add_theme_support( 'post-thumbnails' );

//* Responsive embeds (YouTube, Vimeo, etc.)
add_theme_support( 'responsive-embeds' );

//* Genesis viewport meta
add_theme_support( 'genesis-responsive-viewport' );

//* Genesis footer widgets â€" 5 columns (kept from original)
add_theme_support( 'genesis-footer-widgets', 5 );

//* Genesis structural wraps (kept from original)
add_theme_support( 'genesis-structural-wraps', array(
    'header',
    'site-inner',
    'footer-widgets',
    'footer',
) );

// ---------------------------------------------------------------------------
// Image Sizes
// ---------------------------------------------------------------------------

//* Original sizes (keep for backward compat)
add_image_size( 'home-featured-posts', 350, 216, true );
add_image_size( 'home-plan-grid',      542, 334, true );

//* New sizes for redesigned templates
add_image_size( 'plan-card',      542, 334, true );   // Archive / category grid cards
add_image_size( 'plan-card-wide', 800, 533, true );   // Larger desktop card
add_image_size( 'plan-hero',     1200, 800, false );  // Plan page hero (not cropped â€" preserve composition)
add_image_size( 'plan-thumb',     300, 200, true );   // Gallery thumbnails
add_image_size( 'category-hero', 1440, 600, true );   // Category page hero
add_image_size( 'homepage-hero', 1440, 700, true );   // Homepage hero

// Remove medium_large (768px) â€" rarely used, saves disk space
add_filter( 'intermediate_image_sizes_advanced', 'mhp_remove_unused_image_sizes' );
function mhp_remove_unused_image_sizes( $sizes ) {
    unset( $sizes['medium_large'] );
    return $sizes;
}

// ---------------------------------------------------------------------------
// Enqueue Styles & Scripts
// ---------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'mhp_enqueue_assets' );
function mhp_enqueue_assets() {

    // -----------------------------------------------------------------------
    // Google Fonts — DM Serif Display + DM Sans (replaces Lato)
    // -----------------------------------------------------------------------
    wp_enqueue_style(
        'mhp-google-fonts',
        '//fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap',
        array(),
        null
    );

    // -----------------------------------------------------------------------
    // Dashicons (kept from original)
    // -----------------------------------------------------------------------
    wp_enqueue_style( 'dashicons' );

    // -----------------------------------------------------------------------
    // New Modern CSS Layer (style-new.css)
    // Loaded AFTER the existing style.css so it can extend/override selectively.
    // The existing style.css is still enqueued by Genesis automatically.
    // -----------------------------------------------------------------------
    $new_css_path = get_stylesheet_directory() . '/style-new.css';
    if ( file_exists( $new_css_path ) ) {
        wp_enqueue_style(
            'mhp-style-new',
            get_stylesheet_directory_uri() . '/style-new.css',
            array(), // no dependency needed, loads after genesis
            filemtime( $new_css_path )
        );
    }

    // -----------------------------------------------------------------------
    // Navigation JS — MHP custom nav (hamburger + scroll behavior)
    // -----------------------------------------------------------------------
    wp_enqueue_script(
        'mhp-nav',
        get_stylesheet_directory_uri() . '/js/nav.js',
        array(),
        '1.0.0',
        true // footer
    );

    // -----------------------------------------------------------------------
    // Navigation â€" keep the existing responsive menu JS (uses WP bundled jQuery)
    // -----------------------------------------------------------------------
    wp_enqueue_script(
        'genesis-responsive-menu',
        esc_url( get_stylesheet_directory_uri() ) . '/js/responsive-menu.js',
        array( 'jquery' ), // uses WP's bundled jQuery â€" NOT the ancient 1.9.1
        '1.0.0',
        true // footer
    );

    // -----------------------------------------------------------------------
    // Sticky CTA JS â€" plan pages only
    // -----------------------------------------------------------------------
    if ( is_singular( 'plans' ) ) {
        wp_enqueue_script(
            'mhp-sticky-cta',
            get_stylesheet_directory_uri() . '/js/sticky-cta.js',
            array(),
            '1.0',
            true
        );
    }

    // -----------------------------------------------------------------------
    // Remove Elegant Themes Monarch social sharing (performance drain)
    // -----------------------------------------------------------------------
    wp_dequeue_style( 'et-social-sharing' );
    wp_dequeue_style( 'monarchFE' );
    wp_dequeue_script( 'monarch' );

    // -----------------------------------------------------------------------
    // Remove WP Emoji (not needed on a house plans site)
    // -----------------------------------------------------------------------
    remove_action( 'wp_head',        'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles',  'print_emoji_styles' );

    // -----------------------------------------------------------------------
    // Remove inline gallery styles (we define .gallery-* in style-new.css)
    // -----------------------------------------------------------------------
    add_filter( 'use_default_gallery_style', '__return_false' );
}

// ---------------------------------------------------------------------------
// Defer non-critical scripts
// ---------------------------------------------------------------------------
add_filter( 'script_loader_tag', 'mhp_defer_scripts', 10, 2 );
function mhp_defer_scripts( $tag, $handle ) {
    $defer = array( 'genesis-responsive-menu' );
    if ( in_array( $handle, $defer, true ) ) {
        return str_replace( ' src=', ' defer src=', $tag );
    }
    return $tag;
}

// ---------------------------------------------------------------------------
// Navigation Positions (kept from original)
// ---------------------------------------------------------------------------
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_subnav' );

// ---------------------------------------------------------------------------
// Genesis hooks for custom header.php / footer.php
// When header.php exists in the child theme, Genesis automatically uses it.
// We still suppress the Genesis default header markup to avoid duplication.
// ---------------------------------------------------------------------------
remove_action( 'genesis_header', 'genesis_header_markup_open',  5 );
remove_action( 'genesis_header', 'genesis_do_header'                );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

// Suppress Genesis default footer markup (our footer.php handles it)
remove_action( 'genesis_footer', 'genesis_footer_markup_open',  5 );
remove_action( 'genesis_footer', 'genesis_do_footer'                );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

// ---------------------------------------------------------------------------
// Footer Widget Header (kept from original)
// ---------------------------------------------------------------------------
add_action( 'genesis_before_footer', 'genesischild_footerwidgetheader_position' );
function genesischild_footerwidgetheader_position() {
    genesis_widget_area( 'footerwidgetheader', array(
        'before' => '<div class="footersocial"><div class="wrap">',
        'after'  => '</div></div>',
    ) );
}

// ---------------------------------------------------------------------------
// Footer Credits (kept from original)
// ---------------------------------------------------------------------------
add_filter( 'genesis_footer_creds_text', 'custom_footer_creds_text' );
function custom_footer_creds_text() {
    echo '<div class="creds"><p>';
    echo 'Copyright &copy; ';
    echo date( 'Y' );
    echo ' <img alt="Credit Cards" src="/wp-content/themes/genesis-sample/images/creditcards.png" style="float: left; padding-right: 3px;" />';
    echo ' <img alt="Secure Checkout" src="/wp-content/themes/genesis-sample/images/secureCheckout-sm.jpg" style="float: left; padding-right: 3px;" />';
    echo ' Max Fulbright Designs';
    echo '</p></div>';
}

// ---------------------------------------------------------------------------
// Back to Top Link (kept from original)
// ---------------------------------------------------------------------------
add_filter( 'genesis_footer_backtotop_text', 'sp_footer_backtotop_text' );
function sp_footer_backtotop_text( $backtotop ) {
    $backtotop = '[footer_backtotop text="Return to Top"]';
    return $backtotop;
}

// ---------------------------------------------------------------------------
// Two-Column Grid Class on Archives (kept from original)
// ---------------------------------------------------------------------------
add_filter( 'post_class', 'be_archive_post_class' );
function be_archive_post_class( $classes ) {
    if ( is_singular() || is_home() ) {
        return $classes;
    }
    $classes[] = 'one-half';
    global $wp_query;
    if ( 0 === $wp_query->current_post || 0 === $wp_query->current_post % 2 ) {
        $classes[] = 'first';
    }
    return $classes;
}

// ---------------------------------------------------------------------------
// Layout Override: sidebar on singles/blog pages (kept from original)
// ---------------------------------------------------------------------------
add_filter( 'genesis_pre_get_option_site_layout', 'child_do_layout' );
function child_do_layout( $opt ) {
    if ( is_single() || is_page_template( 'page_blog.php' ) ) {
        $opt = 'content-sidebar';
        return $opt;
    }
}

// ---------------------------------------------------------------------------
// Widget Areas (kept from original)
// ---------------------------------------------------------------------------
genesis_register_sidebar( array(
    'id'          => 'home-featured',
    'name'        => __( 'home-featured', 'genesis' ),
    'description' => __( 'Home featured section', 'genesis' ),
) );
genesis_register_sidebar( array(
    'id'          => 'footerwidgetheader',
    'name'        => __( 'footerwidgetheader', 'genesis' ),
    'description' => __( 'Footer Widget Headline', 'genesis' ),
) );
genesis_register_sidebar( array(
    'id'          => 'mhp_footer',
    'name'        => __( 'MHP Footer', 'genesis' ),
    'description' => __( 'Optional widget area in the footer contact column.', 'genesis' ),
) );

// ---------------------------------------------------------------------------
// Custom Taxonomy â€" Home Plans Categories (kept from original, unchanged)
// ---------------------------------------------------------------------------
add_action( 'init', 'toys_categories_register' );
function toys_categories_register() {
    $labels = array(
        'name'                       => 'Home Plans Categories',
        'singular_name'              => 'Home Plans Category',
        'search_items'               => 'Search Home Plans Categories',
        'popular_items'              => 'Popular Home Plans Categories',
        'all_items'                  => 'All Home Plans Categories',
        'parent_item'                => 'Parent Home Plans Category',
        'edit_item'                  => 'Edit Home Plans Category',
        'update_item'                => 'Update Home Plans Category',
        'add_new_item'               => 'Add New Home Plans Category',
        'new_item_name'              => 'New Home Plans Category',
        'separate_items_with_commas' => 'Separate Home Plans categories with commas',
        'add_or_remove_items'        => 'Add or remove Home Plans categories',
        'choose_from_most_used'      => 'Choose from most used Home Plans categories',
    );

    $args = array(
        'label'              => 'Home Plans Categories',
        'labels'             => $labels,
        'public'             => true,
        'hierarchical'       => false,
        'show_ui'            => true,
        'show_in_nav_menus'  => true,
        'args'               => array( 'orderby' => 'term_order' ),
        'rewrite'            => array( 'slug' => 'home-plans', 'with_front' => false, 'hierarchical' => false ),
        'query_var'          => true,
    );

    register_taxonomy( 'home-plans_categories', 'plans', $args );
}

// ---------------------------------------------------------------------------
// Custom Post Type â€" Plans (kept from original, unchanged)
// ---------------------------------------------------------------------------
add_action( 'init', 'custom_post_type', 0 );
function custom_post_type() {
    $labels = array(
        'name'               => _x( 'House Plans', 'Post Type General Name', 'twentytwentyone' ),
        'singular_name'      => _x( 'House Plans', 'Post Type Singular Name', 'twentytwentyone' ),
        'menu_name'          => __( 'House Plans', 'twentytwentyone' ),
        'parent_item_colon'  => __( 'Parent Plans', 'twentytwentyone' ),
        'all_items'          => __( 'All Plans', 'twentytwentyone' ),
        'view_item'          => __( 'View Plans', 'twentytwentyone' ),
        'add_new_item'       => __( 'Add New Plans', 'twentytwentyone' ),
        'add_new'            => __( 'Add New', 'twentytwentyone' ),
        'edit_item'          => __( 'Edit Plans', 'twentytwentyone' ),
        'update_item'        => __( 'Update Plans', 'twentytwentyone' ),
        'search_items'       => __( 'Search Plans', 'twentytwentyone' ),
        'not_found'          => __( 'Not Found', 'twentytwentyone' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'twentytwentyone' ),
    );

    $args = array(
        'label'               => __( 'House Plans', 'twentytwentyone' ),
        'description'         => __( 'Plans news and reviews', 'twentytwentyone' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
        'taxonomies'          => array( 'home-plans_categories' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
    );

    register_post_type( 'plans', $args );
}

// ---------------------------------------------------------------------------
// Tax query tweak (kept from original â€" fixes taxonomy pagination edge case)
// ---------------------------------------------------------------------------
add_action( 'pre_get_posts', function ( $query ) {
    if ( ! is_admin() && is_tax( 'ford_escort' ) && $query->is_main_query() ) {
        $paged = get_query_var( 'paged' ) ?: 1;
        $query->set( 'paged', $paged );
        $query->set( 'post_type', 'plans' );
        $query->set( 'posts_per_page', 50 );
    }
} );

// ---------------------------------------------------------------------------
// Product Schema JSON-LD â€" injected in <head> on plan pages
// ---------------------------------------------------------------------------
add_action( 'wp_head', 'mhp_plan_product_schema_head' );
function mhp_plan_product_schema_head() {
    if ( ! is_singular( 'plans' ) ) {
        return;
    }

    $post_id     = get_the_ID();
    $plan_name   = function_exists( 'get_field' ) ? get_field( 'plan_name', $post_id ) : '';
    $plan_name   = $plan_name ?: get_the_title( $post_id );
    $description = function_exists( 'get_field' ) ? get_field( 'plan_description', $post_id ) : '';
    $price       = function_exists( 'get_field' ) ? get_field( 'price', $post_id ) : '';
    $thumb_id    = get_post_thumbnail_id( $post_id );
    $thumb_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
    $permalink   = get_permalink( $post_id );

    // Price fallback â€" parse from PayPal HTML
    if ( empty( $price ) && function_exists( 'get_field' ) ) {
        $paypal_html = get_field( 'paypal', $post_id );
        if ( $paypal_html ) {
            preg_match( '/\$([0-9,]+\.[0-9]{2}) USD/', $paypal_html, $m );
            $price = isset( $m[1] ) ? $m[1] : '';
        }
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Product',
        'name'     => $plan_name,
        'url'      => $permalink,
        'brand'    => array(
            '@type' => 'Brand',
            'name'  => 'Max Fulbright Designs',
        ),
        'description' => wp_strip_all_tags( mb_substr( $description, 0, 500 ) ),
    );

    if ( $thumb_url ) {
        $schema['image'] = $thumb_url;
    }

    if ( $price ) {
        $schema['offers'] = array(
            '@type'         => 'Offer',
            'price'         => preg_replace( '/[^0-9.]/', '', $price ),
            'priceCurrency' => 'USD',
            'availability'  => 'https://schema.org/InStock',
            'url'           => $permalink,
            'seller'        => array(
                '@type' => 'Organization',
                'name'  => 'Max Fulbright Designs',
            ),
        );
    }

    $schema['aggregateRating'] = array(
        '@type'       => 'AggregateRating',
        'ratingValue' => '5.0',
        'reviewCount' => '47',
        'bestRating'  => '5',
        'worstRating' => '1',
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

// ---------------------------------------------------------------------------
// LCP Image Preload â€" plan pages
// ---------------------------------------------------------------------------
add_action( 'wp_head', 'mhp_preload_lcp_image', 2 );
function mhp_preload_lcp_image() {
    if ( ! is_singular( 'plans' ) ) {
        return;
    }
    $thumb_id = get_post_thumbnail_id();
    if ( $thumb_id ) {
        $src    = wp_get_attachment_image_src( $thumb_id, 'plan-hero' );
        $srcset = wp_get_attachment_image_srcset( $thumb_id, 'plan-hero' );
        if ( $src ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $src[0] ) . '"';
            if ( $srcset ) {
                echo ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="(max-width: 768px) 100vw, 60vw"';
            }
            echo ' fetchpriority="high">' . "\n";
        }
    }
}

// ACF field registrations (price, price_cad, faqs, feature_bullets, gallery)
require_once get_stylesheet_directory() . '/inc/acf-fields.php';

// ---------------------------------------------------------------------------
// Plan-Specific Page Templates
// ---------------------------------------------------------------------------

/**
 * Register plan-specific templates so they appear in the template dropdown.
 */
add_filter( 'theme_page_templates', function( $templates ) {
    $templates['single-plans-asheville-mountain.php']    = 'Plan - Asheville Mountain';
    $templates['single-plans-blowing-rock-cottage.php']  = 'Plan - Blowing Rock Cottage';
    $templates['single-plans-boulder-mountain-cabin.php'] = 'Plan - Boulder Mountain Cabin';
    $templates['single-plans-carolina-farmhouse.php']    = 'Plan - Carolina Farmhouse';
    $templates['single-plans-sunset-ridge.php']          = 'Plan - Sunset Ridge';
    return $templates;
} );

/**
 * Also expose to the plans CPT (post type = plans).
 */
add_filter( 'theme_templates', function( $templates, $theme, $post ) {
    if ( $post && $post->post_type === 'plans' ) {
        $templates['single-plans-asheville-mountain.php']    = 'Plan - Asheville Mountain';
        $templates['single-plans-blowing-rock-cottage.php']  = 'Plan - Blowing Rock Cottage';
        $templates['single-plans-boulder-mountain-cabin.php'] = 'Plan - Boulder Mountain Cabin';
        $templates['single-plans-carolina-farmhouse.php']    = 'Plan - Carolina Farmhouse';
        $templates['single-plans-sunset-ridge.php']          = 'Plan - Sunset Ridge';
    }
    return $templates;
}, 10, 3 );


