<?php 
/** 
 * Template Name: Plans1 
 */ 


//* Add body class
add_filter( 'body_class', 'genesis_home_plan_body_class' );
function genesis_home_plan_body_class( $classes ) {

    $classes[] = 'single-home-plan';
    return $classes;

}


//* Enqueue Scripts
add_action('get_header', 'prettyPhoto_scripts');
function prettyPhoto_scripts() {	
    wp_enqueue_script('jquery-1', CHILD_URL.'/js/jquery-1.9.1.min.js');
    wp_enqueue_style('prettyPhoto', CHILD_URL.'/css/bootstrap1.min.css');
    wp_enqueue_script('tab', CHILD_URL.'/js/tab.js');
    wp_enqueue_script('tab', CHILD_URL.'/js/social.js');
}

//* Remove structural wrap from site-inner
add_theme_support( 'genesis-structural-wraps', array( 
    'header', 
    'footer-widgets', 
    'footer' 
) );

//* Force Content Sidebar Layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );
genesis_unregister_layout( 'full-width-content' );

//* Wrap content and sidebar
add_filter( 'genesis_attr_content-sidebar-wrap', 'genesis_add_css_attr' );
function genesis_add_css_attr( $attributes ) {
 
    $attributes['class'] .= ' wrap';
    return $attributes;
 
}

//* Add home plan title
add_action( 'genesis_before_content_sidebar_wrap', 'max_title' );
function max_title() { ?>
	<div id="plan-title">
        <h1><?php the_field( "plan_name" );  ?></h1>
    </div>
<?php 
}


remove_action( 'genesis_loop', 'genesis_do_loop' ); 
add_action( 'genesis_loop', 'child_do_custom_loop' );
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );
genesis_unregister_layout( 'full-width-content' );
function child_do_custom_loop() {  ?> 



<div class="wrap">
<?php /* The loop */
while ( have_posts() ) : the_post();
    if ( get_post_gallery() ) :
        echo get_post_gallery();
    endif; 
endwhile; 
?>
</div>
<?php }

//* Replace Sidebar with Custom Sidebar
remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); 
add_action( 'genesis_sidebar', 'child_sidebar_max' );
function child_sidebar_max() { ?> 

    <div class="plansinfo">
        <h2 class="planheader">Plan Details</h2>
			<div class="plan-description">
                <ul>
                    <h3>Total Living Area:</h3>
                        <li><p>Main Floor:</p><span><?php the_field( "main_floor" );  ?></span></li>
                        <li><p>Upper Floor:</p><span><?php the_field( "upper_floor" );  ?></span></li>
                        <li><p>Lower Floor:</p><span><?php the_field( "lower_floor" );  ?></span></li>
                        <li><p>Heated Area:</p><span style="border-top: 1px solid black;"><?php the_field("total_living_area" ); ?></span></li>
                </ul>
                <ul>
                    <h3>Plan Dimensions:</h3>
                        <li><p>Width:</p><span><?php the_field( "width" );  ?></span></li>
                        <li><p>Depth:</p><span><?php the_field( "depth" );  ?></span></li>
                </ul>   
				<ul>
                    <h3>House Features</h3>
						<li><p>Bedrooms:</p><span><?php the_field( "bedrooms" );  ?></span></li>
						<li><p>Bathrooms:</p><span><?php the_field( "bathrooms" );  ?></span></li>
						<li><p>Stories:</p><span><?php the_field( "stories" );  ?></span></li>
						<li><p>Additional Rooms:</p><span><?php the_field( "additional_rooms" ); ?></span</li>
						<li><p>Garage:</p><span><?php the_field( "garage" );  ?></span></li>


            </div>
    </div>

<?php 

}

//* Add Markup for Plan Content Area
add_action( 'genesis_after_content_sidebar_wrap', 'plan_content' );  
function plan_content() { ?> 

    <div class="floorplans">
	   <div class="wrap">
           <h2>Floor Plans:</h2>
	           <?php the_field("floor_plans" );


                    $image = get_field('floor_plan_1');

                    if( !empty($image) ): 

                        // vars
                        $url = $image['url'];
                        $title = $image['title'];
                        $alt = $image['alt'];
                        $caption = $image['caption'];

                        // thumbnail
                        $size = 'large';
                        $thumb = $image['sizes'][ $size ];
                        $width = $image['sizes'][ $size . '-width' ];
                        $height = $image['sizes'][ $size . '-height' ];

                        if( $caption ): ?>

                        <?php endif; ?>
                            <div class="one-half first">
                        <a href="<?php echo $url; ?>" title="<?php echo $title; ?>">

                            <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

                        </a>
                            </div>
                        <?php if( $caption ): ?>

                                <p class="wp-caption-text"><?php echo $caption; ?></p>



                        <?php endif; ?>

                    <?php endif; ?>
                    <?php 

                        $image = get_field('floor_plan_2');

                        if( !empty($image) ): 

                            // vars
                            $url = $image['url'];
                            $title = $image['title'];
                            $alt = $image['alt'];
                            $caption = $image['caption'];

                            // thumbnail
                            $size = 'large';
                            $thumb = $image['sizes'][ $size ];
                            $width = $image['sizes'][ $size . '-width' ];
                            $height = $image['sizes'][ $size . '-height' ];

                            if( $caption ): ?>



                            <?php endif; ?>
                                <div class="one-half">
                            <a href="<?php echo $url; ?>" title="<?php echo $title; ?>">

                                <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

                            </a>
                                </div>
                            <?php if( $caption ): ?>

                                    <p class="wp-caption-text"><?php echo $caption; ?></p>



                            <?php endif; ?>

                        <?php endif; ?>

                        <?php 

                        $image = get_field('floor_plan_3');

                        if( !empty($image) ): 

                            // vars
                            $url = $image['url'];
                            $title = $image['title'];
                            $alt = $image['alt'];
                            $caption = $image['caption'];

                            // thumbnail
                            $size = 'large';
                            $thumb = $image['sizes'][ $size ];
                            $width = $image['sizes'][ $size . '-width' ];
                            $height = $image['sizes'][ $size . '-height' ];

                            if( $caption ): ?>



                            <?php endif; ?>
                                <div class="one-half">
                            <a href="<?php echo $url; ?>" title="<?php echo $title; ?>">

                                <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

                            </a>
                                </div>
                            <?php if( $caption ): ?>

                                    <p class="wp-caption-text"><?php echo $caption; ?></p>



                            <?php endif; ?>

                        <?php endif; ?>

        </div>
    </div>
    <div class="plan-description">
        <div class="wrap">
            <h2 align="center">House Plan Specs</h2>
                <div class="one-third first">
	               <ul><h3>Total Living Area:</h3>
                                <li><p>Main Floor:</p><span><?php the_field( "main_floor" );  ?></span></li>
								<li><p>Upper Floor:</p><span><?php the_field( "upper_floor" );  ?></span></li>
                                <li><p>Lower Floor:</p><span><?php the_field( "lower_floor" );  ?></span></li>
                                <li><p>Heated Area:</p><span style="border-top: 1px solid black;"><?php the_field("total_living_area" ); ?></span></li>
                            </ul>
                            <ul><h3>Plan Dimensions:</h3>
                                <li><p>Width:</p><span><?php the_field( "width" );  ?></span></li>
                                <li><p>Depth:</p><span><?php the_field( "depth" );  ?></span></li>
                            </ul>
                </div>
                <div class="one-third">
                    <ul><h3>House Features</h3>
						<li>
							<p>Bedrooms:</p><span><?php the_field( "bedrooms" );  ?></span>
						</li>
						<li>
							<p>Bathrooms:</p><span><?php the_field( "bathrooms" );  ?></span>
						</li>
						<li>
							<p>Stories:</p><span><?php the_field( "stories" );  ?></span>
						</li>
						<li>
							<p>Additional Rooms:</p><span><?php the_field( "additional_rooms" );  ?></span>
						</li>
						<li>
							<p>Garage:</p><span><?php the_field( "garage" );  ?></span>
						</li>
						<li>
							<p>Outdoor Spaces:</p><span><?php the_field( "outdoor" );  ?></span>
						</li>
						<li>
							<p>Other:</p><span><?php the_field( "other_features" );  ?></span>
						</li>
					</ul>
                </div>
                <div class="one-third">
								<ul><h3>Plan Features</h3>
								<li>
									<p>Roof:</p><span><?php the_field( "roof" );  ?></span>
								</li>
								<li>
									<p>Exterior Framing:</p><span><?php the_field( "exterior" );  ?></span>
								</li>
								<li>
									<p>Ceiling Height:</p><span><?php the_field( "ceiling" );  ?></span>
								</li>
								</ul>
								
							<ul><h3>Plan Styles</h3>
								<li>
									<p>Home Style:</p><span><?php the_field( "style" );  ?></span>
								</li>
								<li>
									<p>Lot Style:</p><span><?php the_field( "lot_style" );  ?></span>
								</li>
							</ul>
                    </div>
            </div>
        </div>

        <div class="floorplan-tabs">
            <section class="plan-tabs">
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="#description" data-toggle="tab"><h4>Plan Description</h4></a></li>
              <li><a href="#profile" data-toggle="tab"><h4>What's Included</h4></a></li>
              <li><a href="#messages" data-toggle="tab"><h4>Important Information</h4></a></li>
            </ul>

            <div class="tab-content wrap">
              <div class="tab-pane active" id="description">
                <div class="one-half first">
                    <div class="description"><?php the_field("plan_description" ); ?></div>
                </div>
                <div class="one-half">
                    <div class="plan-image"><img src="<?php the_field('plan_image'); ?>" alt="" /></div>
                </div>
              </div>
              <div class="tab-pane" id="profile">
                <div class="elevations borderlow">
                <p>Each set of plans includes:
                    <h3>Elevations</h3>
                    <li>Front, side and rear elevations at 1/4 ” scale. All elevations include notes, dimensions, and recommended materials.</li>
                </p>
                    <div class="one-third first border">
                        <img class="alignnone size-full wp-image-1466" style="margin-bottom: 30px;" title="House Plan Front Elevation" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/House-Plan-Front-Elevation.png" alt="House Plan Front Elevation" width="295" height="191">
                    </div>
                    <div class="one-third border">
                        <img class="alignnone size-full wp-image-1467" style="margin-bottom: 30px;" title="House Plan Left Elevation" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/House-Plan-Left-Elevation.png" alt="House Plan Left Elevation" width="295" height="205">
                    </div>
                    <div class="one-third">
                        <img class="alignnone size-full wp-image-1468" style="margin-bottom: 30px;" title="House Plan Rear Elevation" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/House-Plan-Rear-Elevation.png" alt="" width="295" height="186">
                    </div>
                </div>
                <div class="elevations borderlow">
                    <h3>Floor Plans</h3>
                        <p><li>Complete dimensioned and detailed Floor Plans.</li></p>
                    <div class="one-half first">
                        <img class="alignnone size-full wp-image-1471" title="Main Level Floor Plan" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/Main-Level-Floor-Plan.png" alt="Main Level Floor Plan" width="400" height="356">
                    </div>
                    <div class="one-half">
                    <img class="size-full wp-image-1492 alignnone" title="Upper Level Floorplan" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/Upper-Level-Floorplan.png" alt="Upper Level Floorplan" width="400" height="374">
                    </div>
                </div>
                <div class="elevations">
                    <div class="one-half first border">
                    <h3>Foundation Plan</h3>
                        <img class="alignnone size-full wp-image-1470" style="margin-bottom: 31px;" title="Lower Level Floor Plan" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/Lower-Level-Floor-Plan.png" alt="Lower Level Floor Plan" width="400" height="330">
                    </div>
                    <div class="one-half">
                    <h3>Roof Plan</h3>
                        <img class="size-full wp-image-1469 alignnone" title="House Roof Plan" src="http://www.maxhouseplans.com/wp-content/uploads/2011/08/House-Roof-Plan.png" alt="House Roof Plan" width="450" height="494">
                    </div>
                </div>
                    <p>Notes:
                        <li>Floor Plans and Elevations depicted may vary slightly from website depictions. Over time, we make plan improvements that may not be updated on the site immediately.</li>
                        <li>Square Footage is typically calculated using heated Square Footage on the main and upper levels. Stairs are counted once. Two story open areas and vaulted spaces are not included in Square Foot totals.</li>
                        <li>Electrical Plans are very customer specific and are not included in the standard plan set. The types of lights, switches, and where they are located vary with each customer.  An on site meeting with the builder and electrician after the house is framed is the easiest way to make good decisions regarding electrical.  However, we can produce custom electrical plans for $350.00.</li>
                    </p>
                </div>
                  <div class="tab-pane" id="messages">
                  <p>All sales on house plans and customization/modifications are final. No refunds or exchanges can be given once your order has started the fulfillment process.

                All house plans from maxhouseplans are designed to conform to the local codes when and where the original house was constructed.

                In addition to the house plans you order, you may also need a site plan that shows where the house is going to be located on the property. You might also need beams sized to accommodate roof loads specific to your region. Your home builder can usually help you with this. Many areas now have area-specific energy codes that also have to be followed. This normally involves filling out a simple form providing documentation that your house plans are in compliance.

                In some regions, there is a second step you will need to take to insure your house plans are in compliance with local codes. Some areas of North America have very strict engineering requirements. New York, New Jersey, Nevada, and parts of Illinois require review by a local professional as well as some other areas. If you are building in these areas, it is most likely you will need to hire a state licensed structural engineer to analyze the design and provide additional drawings and calculations required by your building department. If you aren’t sure, building departments typically have a handout they will give you listing all of the items they require to submit for and obtain a building permit.

                Additionally, stock plans do not have a professional stamp attached. If your building department requires one, they will only accept a stamp from a professional licensed in the state where you plan to build. In this case, you will need to take your house plans to a local engineer or architect for review and stamping.</p>
                  </div>
                </div>
            </section>
        </div>
    <div class="paypal">
        <div class="one-half first paypal">
            <p><?php the_field( "paypal" );  ?></p>
        </div>
        <div class="one-half">
            <script type="text/javascript" data-pp-pubid="57d9476d44" data-pp-placementtype="468x60"> (function (d, t) {
            "use strict";
            var s = d.getElementsByTagName(t)[0], n = d.createElement(t);
            n.src = "//paypal.adtag.where.com/merchant.js";
            s.parentNode.insertBefore(n, s);
            }(document, "script"));
            </script>
        </div>
    </div>
 <?php 

}


//* Add related posts
add_action( 'genesis_before_footer', 'max_before_footer', 1 );
function max_before_footer() {  ?> 

<div class="moreplanscont">
    <div class="wrap">
        <div class="relatedplans">	
            <?php

            $backup = $post;
            $categories = get_the_category($post->ID);
            if ($categories) {
                $category_ids = array();
                foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

                $args=array(
                    'post_type'    => 'page',		
                    'category__in' => $category_ids,
                    'post__not_in' => array($post->ID),
                    'orderby'=> rand,
                    'showposts'=>3, // Number of related posts that will be shown.
                    'caller_get_posts'=>1
                );
              $my_query = new wp_query($args);
                if( $my_query->have_posts() ) {
                    echo '<h2>More House Plans</h2><ul>';
                    while ($my_query->have_posts()) {
                        $my_query->the_post();
                    ?>
                        <div class="moreplans">
                            <li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo genesis_get_image( array( 'format' => 'html', 'size' => 'home-featured-posts', 'attr' => array( 'class' => 'alignnone post-image' ) ) ); ?><div id="plan-title">
                    <h3><?php the_field( "plan_name" );  ?></h3>
                </div></a></li>
                        </div>
                    <?php
                    } 
                    echo '</ul>';
                }
            }

                    $post = $backup;
                    wp_reset_query();

            ?>
</div></div></div>

<div class="clear"></div>


<?php 

}

// Adds javascript below footer
add_action('genesis_after_footer', 'prettyPhoto_javascript');
function prettyPhoto_javascript() {
?>
<script type="text/javascript" charset="utf-8">
  $(function () {
    $('#myTab a:first').tab('show')
  })
</script>
<?php
}

genesis();