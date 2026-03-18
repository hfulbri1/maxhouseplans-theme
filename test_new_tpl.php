<?php
/* Template Name: Test */

/**
 * Genesis custom loop
 */
function be_custom_loop() {
	global $post;

	// arguments, adjust as needed
	$args = array(
		'post_type'      => 'plans',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'paged'          => get_query_var( 'paged' )
	);

	/* 
	Overwrite $wp_query with our new query.
	The only reason we're doing this is so the pagination functions work,
	since they use $wp_query. If pagination wasn't an issue, 
	use: https://gist.github.com/3218106
	*/
	global $wp_query;
	$wp_query = new WP_Query( $args );

	if ( have_posts() ) : 
		echo '<ul>';
		while ( have_posts() ) : the_post(); 

			echo '<li>' . get_the_title() . '</li>';

		endwhile;
		echo '</ul>';
		do_action( 'genesis_after_endwhile' );
	endif;

	wp_reset_query();
}
add_action( 'genesis_loop', 'be_custom_loop' );
remove_action( 'genesis_loop', 'genesis_do_loop' );


genesis();