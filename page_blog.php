<?php

//* Template Name: Blog


//* Remove default Featured Image from Blog Page and replace with custom image.
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_content', 'genesis_add_resized_post_image', 8 );
function genesis_add_resized_post_image() {

	$image_args = array(
		'size' => 'home-featured-posts',
		'attr' => array(
			'class' => 'alignleft post-image',
		),
	);

	genesis_image( $image_args );
}

//* Remove the entry footer
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

genesis(); 