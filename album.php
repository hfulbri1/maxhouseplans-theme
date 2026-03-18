<?php 
/** 
 * Template Name: album
 */ 

//* Force Full Width Layout
remove_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

//* Remove Genesis loop and replace with custom loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'custom_pics' );		
function custom_pics() { ?>


    <div class="popularsection">
		<h1><?php the_title(); ?></h1>
        <div class="popularplans">

            <ul>
                <li class="one-third first">
                    <a title="Exterior Pictures" href="http://www.maxhouseplans.com/house-pictures/exterior-house-pictures-2/"><img class="home-featured-plans" title="exterior-pictures" src="http://www.maxhouseplans.com/wp-content/uploads/2011/05/exterior-house-pictures.jpg" alt="Exterior Pictures" width="350" height="225" />
                    <h2>Exterior Pictures</a></h2>
                </li>
                <li class="one-third">
                    <a title="Bathroom Pictures" href="http://www.maxhouseplans.com/house-pictures/bathroom-pictures-2/"><img class="home-featured-plans" title="Bathroom Pictures" src="http://www.maxhouseplans.com/wp-content/uploads/2011/05/bathroom-pictures.jpg" alt="Bathroom Pictures" width="350" height="225" />
                    <h2>Bathroom Pictures</a></h2>
                </li>
                <li class="one-third">
                    <a title="Bedroom Pictures" href="http://www.maxhouseplans.com/house-pictures/bedroom-pictures-2/"><img class="home-featured-plans" title="bedroom pictures" src="http://www.maxhouseplans.com/wp-content/uploads/2011/05/bedroom-pictures.jpg" alt="Bedroom Pictures" width="350" height="225" />
                    <h2>Bedroom Pictures</a></h2>
                </li>
            </ul>
            <ul>
                <li class="one-third first">
                    <a title="Interior Pictures" href="http://www.maxhouseplans.com/house-pictures/interior-pictures-of-homes/"><img class="home-featured-plans" title="interior-pictures" src="http://www.maxhouseplans.com/wp-content/uploads/2011/05/interior-house-pictures.jpg" alt="Interior Pictures" width="350" height="225" />
                    <h2>Interior Pictures</a></h2>
                </li>
                <li class="one-third">
                    <a title="Kitchen and Dining" href="http://www.maxhouseplans.com/house-pictures/kitchen-and-dining-room-pictures/"><img class="home-featured-plans" title="kitchen-and-dining" src="http://www.maxhouseplans.com/wp-content/uploads/2011/05/kitchen-and-dining-pictures.jpg" alt="Kitchen and Dining" width="350" height="225" />
                    <h2>Kitchen and Dining</a></h2>
                </li>
                <li class="one-third">
                    <a title="Porch Pictures" href="http://www.maxhouseplans.com/house-pictures/porch-and-deck-pictures/"><img class="home-featured-plans" title="porch-pictures" src="http://www.maxhouseplans.com/wp-content/uploads/2011/05/exterior-porch-pictures.jpg" alt="Porch Pictures" width="350" height="225" />
                    <h2>Porch Pictures</a></h2>
                </li>
            </ul>
        </div>
    </div>


<?php }

genesis();