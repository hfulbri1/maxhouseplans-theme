<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Genesis Sample Theme' );
define( 'CHILD_THEME_URL', 'http://www.studiopress.com/' );
define( 'CHILD_THEME_VERSION', '2.0.1' );

//* Enqueue styles and scripts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {
	wp_enqueue_style( 'google-font-lato', '//fonts.googleapis.com/css?family=Lato:300,400,700', array(), '2.0.1' );
    //wp_enqueue_script( 'ClickTabs', get_bloginfo('url').'/wp-content/themes/genesis-sample/js/ClickTabs.js');
    wp_enqueue_style( 'dashicons' );
    wp_enqueue_script( 'genesis-responsive-menu', esc_url( get_stylesheet_directory_uri() ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
    
}

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

/** Add support for 5-column footer widgets */
add_theme_support( 'genesis-footer-widgets', 5 );
 
//* Add structural wraps
add_theme_support( 'genesis-structural-wraps', array( 
    'header', 
    'site-inner',
    'footer-widgets', 
    'footer' 
) );

/** Add new image sizes */

add_image_size( 'home-featured-posts', 350, 216, TRUE );
add_image_size( 'home-plan-grid', 542, 334, TRUE );
 
 
//* Reposition Primary Nav & Secondary Nav
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_subnav' );
 
//* Add markup for footerwidgetheader widget area
add_action( 'genesis_before_footer' , 'genesischild_footerwidgetheader_position' );
function genesischild_footerwidgetheader_position() {
    
    genesis_widget_area( 'footerwidgetheader', array(
		'before' => '<div class="footersocial"><div class="wrap">',
		'after'  => '</div></div>',
	) );
 
}
 

/** Customize the credits */
add_filter('genesis_footer_creds_text', 'custom_footer_creds_text');
function custom_footer_creds_text() {
    echo '<div class="creds"><p>';
    echo 'Copyright &copy; ';
    echo date('Y');
    echo ' <img alt="Credit Cards" src="/wp-content/themes/genesis-sample/images/creditcards.png" style="float: left; padding-right: 3px;" />';
    echo ' <img alt="Secure Checkout" src="/wp-content/themes/genesis-sample/images/secureCheckout-sm.jpg" style="float: left; padding-right: 3px;" />';
    echo ' Max Fulbright Designs';
    echo '</p></div>';
}

//* Customize back to top link
add_filter( 'genesis_footer_backtotop_text', 'sp_footer_backtotop_text' );
function sp_footer_backtotop_text($backtotop) {
    $backtotop = '[footer_backtotop text="Return to Top"]';
    return $backtotop;
}

//* Add column classes to posts to create the two-column grid layout
add_filter( 'post_class', 'be_archive_post_class' );
function be_archive_post_class( $classes ) {
	// Don't run on single posts or front page.
	if( is_singular() || is_home() )
		return $classes;
	
    $classes[] = 'one-half'; //* Change to one-thid for three column
	global $wp_query;
	if( 0 == $wp_query->current_post || 0 == $wp_query->current_post % 2 ) //* Change last number to 3 for three column
		$classes[] = 'first';
	return $classes;
}

//* Force layouts depending on post/page
add_filter( 'genesis_pre_get_option_site_layout', 'child_do_layout' );
function child_do_layout( $opt ) {
    if ( is_single() || is_page_template( 'page_blog.php' ) ) {
        $opt = 'content-sidebar';
        return $opt;
    }
}


//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-featured',
	'name'        => __( 'home-featured', 'genesis' ),
	'description' => __( 'This is the home featured section', 'genesis' ),
) );
genesis_register_sidebar( array(
	'id'          => 'footerwidgetheader',
	'name'        => __( 'footerwidgetheader', 'genesis' ),
	'description' => __( 'This is for the Footer Widget Headline', 'genesis' ),
) );





/****************************************
 * Add custom taxonomy for Toys *
 ****************************************/

add_action('init', 'toys_categories_register');

function toys_categories_register() {
$labels = array(
    'name'                          => 'Home Plans Categories',
    'singular_name'                 => 'Home Plans Category',
    'search_items'                  => 'Search Home Plans Categories',
    'popular_items'                 => 'Popular Home Plans Categories',
    'all_items'                     => 'All Home Plans Categories',
    'parent_item'                   => 'Parent Home Plans Category',
    'edit_item'                     => 'Edit Home Plans Category',
    'update_item'                   => 'Update Home Plans Category',
    'add_new_item'                  => 'Add New Home Plans Category',
    'new_item_name'                 => 'New Home Plans Category',
    'separate_items_with_commas'    => 'Separate Home Plans categories with commas',
    'add_or_remove_items'           => 'Add or remove Home Plans categories',
    'choose_from_most_used'         => 'Choose from most used Home Plans categories'
    );

$args = array(
    'label'                         => 'Home Plans Categories',
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => false,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'home-plans', 'with_front' => false, 'hierarchical' => false ),
    'query_var'                     => true
);

register_taxonomy( 'home-plans_categories', 'plans', $args );
}

/*****************************************
 * Add custom post type for Toys *
 *****************************************/

  

/*
* Creating a function to create our CPT
*/
  
function custom_post_type() {
  
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'House Plans', 'Post Type General Name', 'twentytwentyone' ),
        'singular_name'       => _x( 'House Plans', 'Post Type Singular Name', 'twentytwentyone' ),
        'menu_name'           => __( 'House Plans', 'twentytwentyone' ),
        'parent_item_colon'   => __( 'Parent Plans', 'twentytwentyone' ),
        'all_items'           => __( 'All Plans', 'twentytwentyone' ),
        'view_item'           => __( 'View Plans', 'twentytwentyone' ),
        'add_new_item'        => __( 'Add New Plans', 'twentytwentyone' ),
        'add_new'             => __( 'Add New', 'twentytwentyone' ),
        'edit_item'           => __( 'Edit Plans', 'twentytwentyone' ),
        'update_item'         => __( 'Update Plans', 'twentytwentyone' ),
        'search_items'        => __( 'Search Plans', 'twentytwentyone' ),
        'not_found'           => __( 'Not Found', 'twentytwentyone' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwentyone' ),
    );
      
// Set other options for Custom Post Type
      
    $args = array(
        'label'               => __( 'House Plans', 'twentytwentyone' ),
        'description'         => __( 'Plans news and reviews', 'twentytwentyone' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'home-plans_categories' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
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
        'show_in_rest' => true,
  
    );
      
    // Registering your Custom Post Type
    register_post_type( 'plans', $args );
  
}
  
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
  
add_action( 'init', 'custom_post_type', 0 );




add_action('pre_get_posts', function($query) {
    if (!is_admin() && is_tax('ford_escort') && $query->is_main_query()) {
        $paged = get_query_var('paged') ?: 1;
        $query->set('paged', $paged);
        $query->set('post_type', 'plans');
        $query->set('posts_per_page', 50);
    }
});