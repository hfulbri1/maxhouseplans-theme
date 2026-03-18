<?php

/**
* Template Name: Gallery
* Description: Used as a page template to show gallery, followed by a loop 
* through the "gallery" category
*/

// Add our custom loop
function be_portfolio_post_class( $classes ) {
	
 the_content();
  
 get_post_gallery();
		
}

genesis();