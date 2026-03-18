<?php
/**
 * This file controls all archives
 *
 * @author David Browne
 * @package Genesis Sample
 * @subpackage Customizations
 */

 if ( is_tax( 'home-plans_categories' )) { ?>
         
    
 	
<?php	
 include('archive_pages_plan.php');

 }else{ 
		 
		
	 

// echo'<pre>';
	// print_)r(*);
// echo'</pre>';


//* Remove the post content (requires HTML5 theme support)
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

//* Remove the entry meta in the entry header (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

//* Remove the entry title (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
add_action( 'genesis_entry_header', 'mhp_do_custom_post_title' );
function mhp_do_custom_post_title() {
    ?>

    <h2 class="entry-title">
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_field( "plan_name" );  ?></a>
    </h2> <?php
}

//* Remove the entry footer markup (requires HTML5 theme support)
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

//* Force full width content layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

//* Display custom fields to each home plan
add_action( 'genesis_entry_content', 'genesis_sample_get_fields' );
function genesis_sample_get_fields() { ?>
    
    <div class="plandata">
		<ul>
			<li><strong>Total Living Area:</strong>  <?php the_field("total_living_area" ); ?></li>
			<li><strong>Bedrooms:</strong>   <?php the_field("bedrooms" ); ?></li>
			<li><strong>Bathrooms:</strong>  <?php the_field( "bathrooms" );  ?></li>
			<li><strong>Stories:</strong>  <?php the_field( "stories" );  ?></li>
			<li><strong>Style:</strong>   <?php the_field("style" ); ?></li>
		</ul>
        <div class="planbutton"><a href="<?php the_permalink() ?>" rel="bookmark" alt="View House Plan" class="button">View House Plan</a>
    </div> <?php

}

//* Add Search and Filter Shortcode to appear above the archives
add_action( 'genesis_loop', 'child_do_search_filter', 5 );
function child_do_search_filter() {
  
        echo do_shortcode('[searchandfilter post_type="page" fields="search,category"]');
      
}

genesis();

}