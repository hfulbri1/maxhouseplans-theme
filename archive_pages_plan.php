<?php 
 

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
    wp_enqueue_script('tab', CHILD_URL.'/js/tab.js');
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
		
		 
		
        <h1><?php
    printf( __( '%s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
?></h1>
    </div>
<?php 
}


remove_action( 'genesis_loop', 'genesis_do_loop' ); 
add_action( 'genesis_loop', 'child_do_custom_loop' );
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );
genesis_unregister_layout( 'full-width-content' );
function child_do_custom_loop() {  ?> 

<style>
.wrap {
  max-width: 1300px;
}
.search_f {
    margin-bottom: 20px;
}

h2.entry-title.titless2 {
    margin-bottom: 10px;
    text-align: left;
    text-transform: capitalize;
}

.wrap1 li.li_class {
 width: 49%;
    float: left;
    padding: 3px;
    background: white;
    border: solid 1px #dddddd;
    margin-right: 1%;
    min-height: 475px;
    margin-bottom: 20px;
    position: relative;
	
}

.content {
     width: 70%;
}

.wrap1 li h2 {
    font-size: 16px;
    line-height: 25px;
}
.plandata ul {
    padding: 0;
    margin: 0;
    margin-bottom: 20px;
    float: left;
    margin-top: 6px !important;
}
li.styless {
    font-size: 13px !important;
}
.plandata li {
    width: 100%;
    padding: 0;
}

.planbutton {
    position: absolute;
    bottom: 0;
	    left: 0;

    width: 100%;
}




.plandata {
    float: left;
    width: 100%;
}


.plandata ul li span {
    width: 100% !important;
    float: left;
}
.plandata ul li {
   list-style-type: none !important;
    width: 25%;
    font-size: 15px;
    line-height: 22px;
    text-align: center;
    padding: 2px 4px;
    border: none !important;
    margin-right: 0;
    font-weight: bold;
    float: left;

}
h2.entry-title.titless2 {
     
	    padding: 0px 8px;
    min-height: 40px;
    display: inline-block;
}
.plandata ul {
    margin: 0 !important;
    padding: 0 !important;
    list-style-type: none !important;
}
li.styless {
    width: 100% !important;
    text-align: left !important;
    margin-top: 10px;
    margin-bottom: 10px;
}

.plandata ul li span {
    font-weight: normal;
    font-size: 13px;
}


.planbutton a {
    width: 100% !important;
    text-align: center;
    padding: 12px !important;
    border-radius: 0 !important;
}

.planbutton {
    margin: 0;
}
.plandata ul {
    width: 100%;
}




@media only screen and (max-width: 1211px) {
	 .sidebar-primary {
        display: block;
		        width: 30%;

    }
	
}
@media only screen and (max-width: 1024px) {
	 .sidebar-primary {
        display: block;
		        width: 30%;

    }



.wrap1 li.li_class {
    width: 49%;
 
}

}

	@media only screen and (max-width: 800px) {

.wrap1 li.li_class {
    width: 100%;
 
}
		
	}

@media only screen and (max-width: 650px) {

.content {
    width: 100%;
}


.wrap1 li.li_class {
    width: 100%;
   
    margin-right: 0% !important;
    min-height: auto !important; 
}

.planbutton {
    position: relative !important; 
}

    .sidebar-primary {
        display: block;
    }

}
</style>

<div class="wrap1">
 <ul>
 
 <?php
 $queried_object = get_queried_object();
$term_id = $queried_object->term_id;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$aaa = $_POST;

// Initialize meta query
$meta_query = array('relation' => 'AND');

// Check and process bedrooms
if (!empty($_POST['bedroom'])) {
    $bedroom = $_POST['bedroom'];
	
// Output results for debugging
    $bedroom_withcoma = implode(', ', $bedroom);
    $meta_query[] = array(
        'key'     => 'bedrooms',
        'value'   =>  $bedroom_withcoma,
        'compare' => 'IN',
    );
}

// Check and process bathrooms
if (!empty($_POST['bathrooms'])) {
    $bathrooms = $_POST['bathrooms'];
    $bathrooms_withcoma = implode(', ', $bathrooms);
    $meta_query[] = array(
        'key'     => 'bathrooms',
        'value'   => $bathrooms_withcoma,
        'compare' => 'IN',
    );
}

// Check and process stories
if (!empty($_POST['stories'])) {
    $stories = $_POST['stories'];
    $stories_withcoma = implode(', ', $stories);
    $meta_query[] = array(
        'key'     => 'stories',
        'value'   => $stories_withcoma,
        'compare' => 'IN',
    );
}
  
// Fetch posts
$posts = array(
    'numberposts' => -1,
    'post_type'   => 'plans',  
	'meta_or_tax' => TRUE,
'tax_query' => array(
            array(
                'taxonomy'  => 'home-plans_categories',
                'field'     => 'term_id',
                'terms' => $term_id,
                'operator'  => 'IN'
            )
    ),
    'meta_query'  => $meta_query,
);
 

// Output results for debugging

if($posts){
	 
	//echo '<pre>';
 //print_r($meta_query);
//echo '</pre>';
//die('aa');

 
$result = new WP_Query( $posts );
if ( $result-> have_posts() ) { ?>
<?php while ( $result->have_posts() ) : $result->the_post(); 

 $pname = get_field( "plan_name" ); 

?>


<li class="li_class" aria-label="<?php echo $pname; ?>">

 
<div class="entry-content">

<a class="entry-image-link" href="<?php echo get_the_permalink(); ?>" aria-hidden="true" tabindex="-1">

 <?php the_post_thumbnail('single-post-thumbnail'); ?>
<h2 class="entry-title titless2">
		<a class="entry-title-link" rel="bookmark" href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>
 <div class="plandata">
 
  <?php 
	$total_living_area = get_field( "total_living_area" );
	$bedrooms = get_field( "bedrooms" );
	$bathrooms = get_field( "bathrooms" );
	$stories = get_field( "stories" );
	$style = get_field( "style" );

 ?>
		<ul>
			<?php if($total_living_area){ ?><li><?php the_field("total_living_area" ); ?>  <!--<span>Sq Ft.</span> -->  </li><?php } ?>
				<?php if($bedrooms){ ?><li><?php the_field("bedrooms" ); ?> <span>Bed</span>  </li><?php } ?>
				<?php if($bathrooms){ ?><li><?php the_field("bathrooms" ); ?> <span>Bath</span>  </li><?php } ?>
				<?php if($stories){ ?><li><?php the_field("stories" ); ?> <span>Story</span>  </li><?php } ?>
				<?php if($style){ ?><li class="styless"><span>Style</span> <?php the_field("style" ); ?>  </li><?php } ?>
		 
		</ul>
        <div class="planbutton"><a href="<?php the_permalink() ?>" rel="bookmark" alt="View House Plan" class="button">View House Plan</a>
    </div> 
    </div>

</a>

</div>


</li>

<?php endwhile; ?>
<?php }else{
	
		echo'Results Not Found.';

}



 wp_reset_postdata(); ?>

<?php


}else{
	
	echo'Results Not Found.';
	
}



}else{ 
 
	 

?>
 
 <?php
	
	
	
	if(have_posts()): ?>

  <?php while(have_posts()): the_post() ?>


<?php

 $pname = get_field( "plan_name" ); 

 ?>
<li class="li_class" aria-label="<?php echo $pname; ?>">

 
<div class="entry-content">

<a class="entry-image-link" href="<?php echo get_the_permalink(); ?>" aria-hidden="true" tabindex="-1">

 <?php the_post_thumbnail('single-post-thumbnail'); ?>
<h2 class="entry-title titless2">
		<a class="entry-title-link" rel="bookmark" href="<?php echo get_the_permalink(); ?>"><?php echo $pname; ?></a>
	</h2>
 <div class="plandata">
 
  <?php 
	$total_living_area = get_field( "total_living_area" );
	$bedrooms = get_field( "bedrooms" );
	$bathrooms = get_field( "bathrooms" );
	$stories = get_field( "stories" );
	$style = get_field( "style" );

 ?>
		<ul>
			<?php if($total_living_area){ ?><li><?php the_field("total_living_area" ); ?> <!--<span>Sq Ft.</span> -->  </li><?php } ?>
				<?php if($bedrooms){ ?><li><?php the_field("bedrooms" ); ?> <span>Bed</span>  </li><?php } ?>
				<?php if($bathrooms){ ?><li><?php the_field("bathrooms" ); ?> <span>Bath</span>  </li><?php } ?>
				<?php if($stories){ ?><li><?php the_field("stories" ); ?> <span>Story</span>  </li><?php } ?>
				<?php if($style){ ?><li class="styless"><span>Style</span> <?php the_field("style" ); ?>  </li><?php } ?>
		 
		</ul>
        <div class="planbutton"><a href="<?php the_permalink() ?>" rel="bookmark" alt="View House Plan" class="button">View House Plan</a>
    </div> 
    </div>

</a>

</div>


</li>

 
 
  <?php endwhile; ?>

<?php endif; ?>
 <?php if(function_exists('wp_paginate')) {
    wp_paginate('range=4&anchor=2&nextpage=Next&previouspage=Previous');
} ?>
 
<?php } ?>
 
 
</div>
<?php }

//* Replace Sidebar with Custom Sidebar
remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); 
add_action( 'genesis_sidebar', 'child_sidebar_max' );
function child_sidebar_max() { ?> 

    
    <div class="plansinfo">
        <h2 class="planheader">Filter</h2>
			<div class="plan-description">
                  
				  <div class="search_f">
				  <?php echo do_shortcode('[ivory-search id="7504" title="AJAX Search Form"]'); ?>
				  </div>
				  
				  
<form action="" method="post">
	<div class="form_div">
		<h3>Bedrooms</h3>
		
		<ul class="bedrooms">
         
        <li>
          <input type="checkbox" id="2_Bedroom" name="bedroom[]" value="2" />
          <label for="2_Bedroom">2 Bedroom House Plans</label>
        </li>
        <li>
          <input type="checkbox" id="3_Bedroom" name="bedroom[]" value="3" />
          <label for="3_Bedroom">3 Bedroom House Plans</label>
        </li>
        <li>
          <input type="checkbox" id="4_Bedroom" name="bedroom[]" value="4" />
          <label for="4_Bedroom">4 Bedroom House Plans</label>
        </li>
        
      </ul>
		
	</div>
	<!--<div class="form_div">
		<h3>Bathrooms</h3>
		
		<ul class="bedrooms">
        <li>
          <input type="checkbox" id="b1" name="bathrooms[]" value="1" />
          <label for="b1">1 Bathrooms</label>
        </li>
        <li>
          <input type="checkbox" id="b2" name="bathrooms[]" value="2" />
          <label for="b2">2 Bathrooms</label>
        </li>
        <li>
          <input type="checkbox" id="b3" name="bathrooms[]" value="3" />
          <label for="b3">3  Bathrooms</label>
        </li>
        <li>
          <input type="checkbox" id="b4" name="bathrooms[]" value="4" />
          <label for="b4">4  Bathrooms</label>
        </li>
        <li>
          <input type="checkbox" id="b5" name="bathrooms[]" value="5" />
          <label for="b5">5+ Bathrooms</label>
        </li>
      </ul>
		
	</div>-->
	<div class="form_div">
		<h3>Floors:</h3>
		
		<ul class="bedrooms">
        <li>
          <input type="checkbox" id="f1" name="stories[]" value="1" />
          <label for="f1">1 Story House Plans</label>
        </li>
        
        <li>
          <input type="checkbox" id="f3" name="stories[]" value="2" />
          <label for="f3">2 Story House Plans</label>
        </li>
 
        <li>
          <input type="checkbox" id="f5" name="stories[]" value="3" />
          <label for="f5">3 Story House Plans</label>
        </li>
          
         
        
        
      </ul>
		
	</div>
	
	<div class="form_div">
				  <input type="submit" name="submit" value="Search">  

	</div>
	
</form>
		<div class="cat_section">
			
				
		<h3>Categories</h3>		
				<ul>
					
			<?php 
							  
				$orderby = 'name';
                $order = 'asc';
                $hide_empty = true ;
                $cat_args = array(
                    'orderby'    => $orderby,
                    'order'      => $order,
                    'hide_empty' => $hide_empty,
                );

                $product_categories = get_terms( 'home-plans_categories', $cat_args );

                
                    foreach ($product_categories as $key => $category) {
                        
					?>
				<li><a href="<?php echo get_term_link($category->term_id); ?>"><?php echo $category->name; ?></a></li>
				
				<?php 
				
                 }
				
				?>
				
				
				

				</ul>
			</div>
            </div>
    </div>

<?php 

}

//* Add Markup for Plan Content Area
add_action( 'genesis_after_content_sidebar_wrap', 'plan_content' );  
function plan_content() { ?> 
 
         
    
 <?php 

}


//* Add related posts
add_action( 'genesis_before_footer', 'max_before_footer', 1 );
function max_before_footer() {  ?> 

 
 
 

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