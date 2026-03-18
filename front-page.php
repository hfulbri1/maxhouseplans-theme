<?php

// Template Name: Home

//* Add widget support for homepage. If no widgets active, display the default loop.
add_action( 'genesis_meta', 'optimal_home_genesis_meta' );
function optimal_home_genesis_meta() {

		remove_action( 'genesis_loop', 'genesis_do_loop' );
		add_action( 'genesis_after_header', 'optimal_home_loop_helper_top' );
		add_action( 'genesis_loop', 'optimal_home_loop_helper_middle' );		
		remove_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
}


//* Display widget content for "slider" and "welcome" sections.
function optimal_home_loop_helper_top() { ?>
			
    <div class="slider-wrap">
        <div class="slider-inner wrap">
            <div class="home-plan">
                <ul style="padding-top: 70px">
			         <li class="one-third first">
<a title="Lake House Plans" href="/home-plans/lake-house-plans/" rel="attachment wp-att-2842"><img class="home-featured-plans" title="lake-house-plans" src="/wp-content/themes/genesis-sample/images/lake-house-plans-350x225.jpg" alt="lake house plans" width="350" height="225" /></a>
<h2><a title="Lake House Plans" href="/home-plans/lake-house-plans/">Lake House Plans</a></h2>
                    </li>

                    <li class="one-third"><a title="Craftsman House Plans" href="/home-plans/craftsman-house-plans/" rel="attachment wp-att-2843"><img class="home-featured-plans" title="craftsman-house-plans-350x225" src="/wp-content/themes/genesis-sample/images/craftsman-house-plans-350x225.jpg" alt="craftsman house plans" width="350" height="225" /></a>
<h2><a title="Craftsman House Plans" href="/home-plans/craftsman-house-plans/">Craftsman House Plans</a></h2>
                    </li>

                    <li class="one-third">
<a title="Cottage House Plans" href="/home-plans/cottage-house-plans/" rel="attachment wp-att-2844"><img class="home-featured-plans" title="cottage-house-plans-350x250" src="/wp-content/themes/genesis-sample/images/cottage-house-plans-350x250.jpg" alt="cottage house plans" width="350" height="185" /></a>
<h2><a title="Cottage House Plans" href="/home-plans/cottage-house-plans/">Cottage House Plans</a></h2>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<div class="clearfix">
</div>
		
<?php }

function optimal_home_loop_helper_middle() { ?>

    <div class="welcome-wrap">
        <div class="welcome-inner">All of our House Plans have been carefully designed with your family in mind by taking advantage of wasted space and maximizing your living areas. We save space, you save money.
        </div>
    </div>

    <div class="popularsection">
        <h1>Popular House Plans:</h1>
            <div class="popularplans">
                <ul>
		          <li class="one-third first"><a title="Asheville Mountain House Plan" href="/home-plans/3-story-open-mountain-house-floor-plan/"><img class="home-featured-plans" title="Asheville Mountain House Plan" src="/wp-content/themes/genesis-sample/images/asheville-mountain-house-plan-350x225.jpg" alt="Asheville Mountain House" width="350" height="225" />
			<h2>Asheville Mountain</a></h2>
		          </li>
		          <li class="one-third"><a title="Appalachia Mountain House" href="/home-plans/appalachia-mountain-house-plan/"><img class="home-featured-plans" title="appalachian mountain house plan" src="/wp-content/themes/genesis-sample/images/Appalchian-Mountain-House-Plan-350x225.jpg" alt="Appalachia Mountain House" width="350" height="225" />
			<h2>Appalachia Mountain</a></h2>
		          </li>
		          <li class="one-third"><a title="Foothills Cottage House Plan" href="/home-plans/cottage-style-house-plan/"><img class="home-featured-plans" title="Foothills Cottage House Plan" src="/wp-content/uploads/2025/01/foothills-cottage-house-plan-350x225-1.jpg" alt="foothills cottage house plan" width="350" height="225" />
			<h2>Foothills Cottage</a></h2>
		          </li>
	           </ul>
	           <ul>
		          <li class="one-third first"><a title="Banner Elk House Plan" href="/home-plans/open-floor-plan-with-wraparound-porch/"><img class="home-featured-plans" title="mountain-house-plans-banner-elk" src="/wp-content/uploads/2025/01/lake-house-plan-3-story-with-porches.jpg" alt="mountain house plan banner elk" width="350" height="225" />
			<h2>Banner Elk</a></h2>
		          </li>
		          <li class="one-third"><a title="Dogtrot House Plan" href="/home-plans/dog-trot-house-plan/"><img class="home-featured-plans" title="dog-trot-house-plan" src="/wp-content/themes/genesis-sample/images/dog-trot-house-plan-camp-creek-350x225.jpg" alt="Dogtrot House Plan" width="350" height="225" />
			<h2>Camp Creek</a></h2>
		          </li>
		          <li class="one-third"><a title="Lake Wedowee Creek Retreat" href="/home-plans/wedowee-creek-retreat-house-plan/"><img class="home-featured-plans" title="lake-wedowee-creek-house-plan" src="/wp-content/themes/genesis-sample/images/wedowee-creek-retreat-house-plan-350x225.jpg" alt="Lake Wedowee House Plan" width="350" height="225" />
			<h2>Wedowee Creek Retreat</a></h2>
		          </li>
	           </ul>
            </div>
    </div>

    <div class="home-feature-wrap">
        <div class="home-feature-sidebar">
            <h4 class="widgettitle">What makes us Unique?</h4>Choosing the right design for your family can be a difficult task. We are here to assist you from start to finish with all major decisions to ensure that your family ends up with the home that it deserves. Each design is carefully thought out so that you can focus on the small details that really matter. 
            <br>
            <br><em>“We shape our homes and then our homes shape us.” – Winston Churchill</em>
        </div>
        <div class="home-feature-section">
            <div class="home-feature-1">
                <br>
                <br><br><h4 class="widgettitle">Our Style</h4><img class="alignleft" src="/wp-content/themes/genesis-sample/images/applications.png" alt="architectural styles" /> We are skilled in all architectural styles but we have a passion for simplistic craftsman style designs with rustic elements that truly capture the surroundings.<br><h4 class="widgettitle">Support</h4><img class="alignleft" src="/wp-content/themes/genesis-sample/images/screen_aurora.png" alt="house plan support" /> We are available to you and your builder before, during and after construction to answer questions and make sure the process goes smoothly.
            </div>
            <div class="home-feature-2"><br><br><br><h4 class="widgettitle">Experience</h4><img class="alignleft" src="/wp-content/themes/genesis-sample/images/check.png" alt="" /> Max has been designing and BUILDING one of a kind custom homes for over 25 years. His building experience allows him to envision structural problems before they ever occur.<h4 class="widgettitle">Modifications</h4><img class="alignleft" src="/wp-content/themes/genesis-sample/images/folder_blue_stuffed.png" alt="" /> We realize that your home is extremely important and personal. Work directly with Max on modifications so that your home fits your family perfectly.
            </div>
        </div>
    </div>
        
    <?php 
        
        genesis_widget_area( 'home-featured', array(
		'before' => '<div class="home-featured widget-area">',
		'after'  => '</div>',
	) );                                    
                                            
    ?>    
        
    <div class="home-bottom-message">“I have been looking at your designs on the internet for the past couple of years.  No matter where else I look, I keep coming back to your designs due mostly to their simplistic, practical layouts and the use of natural materials that blend well in a rural lake setting.” 
</br> - Fred Madox
    </div>	

<?php }

genesis();