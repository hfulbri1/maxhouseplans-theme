<?php 

/* Template Name: All Home Plans */

//* Add body class to style grid view
add_filter( 'body_class', 'mhp_body_class' );
function mhp_body_class( $classes ) {

    $classes[] = 'grid-view';
    return $classes;

}

//* Force full width content layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

//* Replace Loop with Custom Loop with ability to change categories on a page per page basis
add_action( 'genesis_loop', 'child_do_custom_loop' );
function child_do_custom_loop() {
  
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

		$query_args = wp_parse_args(
			genesis_get_custom_field( 'query_args' ),
			array(
				'showposts'        => genesis_get_option( 'blog_cat_num' ),
				'paged'            => $paged,
                'meta_key'          => 'plan_name',
                'post_type'         => 'page',
                'orderby'           => 'meta_value', 
                'order'             => 'ASC'
			)
		);
    
        //echo do_shortcode('[searchandfilter post_type="page" fields="search,category"]');
		genesis_custom_loop( $query_args ); 
    
}

//* Add the post content (requires HTML5 theme support)
add_action( 'genesis_entry_content', 'mhp_yes' );
function mhp_yes() {
    remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
    remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
    remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
    add_action( 'genesis_entry_header', 'mhp_do_title_field' );
    add_action( 'genesis_entry_content', 'mhp_do_post_fields' );
}

//* Add custom title
function mhp_do_title_field() { ?>

    <h2 class="entry-title">
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_field( "plan_name" );  ?></a>
    </h2> <?php
    
}

//* Add fields to content
function mhp_do_post_fields() { ?>
    
		<div class="planthumb"><?php 
            $image_args = array(
            'size' => 'home-plan-grid',
        );
                            
        $image = genesis_get_image( $image_args );
	
        echo '<a rel="bookmark" href="'. get_permalink() .'">'. $image .'</a>'; ?>

        </div>
		
        <div class="planwrap">
		  <div class="plandata">
            <ul>
                <li><strong>Total Living Area:</strong>  <?php the_field("total_living_area" ); ?></li>
                <li><strong>Bedrooms:</strong>   <?php the_field("bedrooms" ); ?></li>
                <li><strong>Bathrooms:</strong>  <?php the_field( "bathrooms" );  ?></li>
                <li><strong>Stories:</strong>  <?php the_field( "stories" );  ?></li>
                <li><strong>Style:</strong>   <?php the_field("style" ); ?></li>
            </div>
            </ul>
            <div class="planbutton"><a href="<?php the_permalink() ?>" rel="bookmark" alt="View House Plan" class="button">View House Plan</a>
          </div>
          
        </div> <?php

}

genesis();