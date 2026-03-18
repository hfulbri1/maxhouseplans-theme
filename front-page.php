<?php
/**
 * Homepage template
 * Fix: featured section now pulls from 'plans' CPT (not blog posts)
 * Keeps existing hero/category tiles and about section intact.
 *
 * @package MaxHousePlans
 */

// Template Name: Home

add_action( 'genesis_meta', 'mhp_home_genesis_meta' );
function mhp_home_genesis_meta() {
    remove_action( 'genesis_loop', 'genesis_do_loop' );
    add_action( 'genesis_after_header', 'mhp_homepage_hero_section' );
    add_action( 'genesis_loop', 'mhp_homepage_main_content' );
    remove_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
}

/**
 * Hero section: category tiles (kept exactly as original)
 */
function mhp_homepage_hero_section() {
    ?>
    <div class="slider-wrap">
        <div class="slider-inner wrap">
            <div class="home-plan">
                <ul style="padding-top: 70px">
                    <li class="one-third first">
                        <a title="Lake House Plans" href="/home-plans/lake-house-plans/">
                            <img class="home-featured-plans"
                                 src="/wp-content/themes/genesis-sample/images/lake-house-plans-350x225.jpg"
                                 alt="lake house plans" width="350" height="225" loading="eager" />
                        </a>
                        <h2><a title="Lake House Plans" href="/home-plans/lake-house-plans/">Lake House Plans</a></h2>
                    </li>
                    <li class="one-third">
                        <a title="Craftsman House Plans" href="/home-plans/craftsman-house-plans/">
                            <img class="home-featured-plans"
                                 src="/wp-content/themes/genesis-sample/images/craftsman-house-plans-350x225.jpg"
                                 alt="craftsman house plans" width="350" height="225" loading="eager" />
                        </a>
                        <h2><a title="Craftsman House Plans" href="/home-plans/craftsman-house-plans/">Craftsman House Plans</a></h2>
                    </li>
                    <li class="one-third">
                        <a title="Cottage House Plans" href="/home-plans/cottage-house-plans/">
                            <img class="home-featured-plans"
                                 src="/wp-content/themes/genesis-sample/images/cottage-house-plans-350x250.jpg"
                                 alt="cottage house plans" width="350" height="185" loading="eager" />
                        </a>
                        <h2><a title="Cottage House Plans" href="/home-plans/cottage-house-plans/">Cottage House Plans</a></h2>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php
}

/**
 * Main homepage content loop
 */
function mhp_homepage_main_content() {
    ?>
    <div class="welcome-wrap">
        <div class="welcome-inner">
            All of our House Plans have been carefully designed with your family in mind by taking advantage of wasted space and maximizing your living areas. We save space, you save money.
        </div>
    </div>

    <?php mhp_homepage_featured_plans(); ?>

    <div class="home-feature-wrap">
        <div class="home-feature-sidebar">
            <h4 class="widgettitle">What makes us Unique?</h4>
            Choosing the right design for your family can be a difficult task. We are here to assist you from start to finish with all major decisions to ensure that your family ends up with the home that it deserves. Each design is carefully thought out so that you can focus on the small details that really matter.
            <br><br>
            <em>"We shape our homes and then our homes shape us." – Winston Churchill</em>
        </div>
        <div class="home-feature-section">
            <div class="home-feature-1">
                <br><br><br>
                <h4 class="widgettitle">Our Style</h4>
                <img class="alignleft" src="/wp-content/themes/genesis-sample/images/applications.png" alt="architectural styles" />
                We are skilled in all architectural styles but we have a passion for simplistic craftsman style designs with rustic elements that truly capture the surroundings.
                <h4 class="widgettitle">Support</h4>
                <img class="alignleft" src="/wp-content/themes/genesis-sample/images/screen_aurora.png" alt="house plan support" />
                We are available to you and your builder before, during and after construction to answer questions and make sure the process goes smoothly.
            </div>
            <div class="home-feature-2">
                <br><br><br>
                <h4 class="widgettitle">Experience</h4>
                <img class="alignleft" src="/wp-content/themes/genesis-sample/images/check.png" alt="" />
                Max has been designing and BUILDING one of a kind custom homes for over 25 years. His building experience allows him to envision structural problems before they ever occur.
                <h4 class="widgettitle">Modifications</h4>
                <img class="alignleft" src="/wp-content/themes/genesis-sample/images/folder_blue_stuffed.png" alt="" />
                We realize that your home is extremely important and personal. Work directly with Max on modifications so that your home fits your family perfectly.
            </div>
        </div>
    </div>

    <div class="home-bottom-message">
        "I have been looking at your designs on the internet for the past couple of years. No matter where else I look, I keep coming back to your designs due mostly to their simplistic, practical layouts and the use of natural materials that blend well in a rural lake setting."
        <br> - Fred Madox
    </div>
    <?php
}

/**
 * Featured Plans Section — pulls from 'plans' CPT (NOT blog posts)
 * Shows 6 most recent featured plans, fallback to 6 most recent published plans.
 */
function mhp_homepage_featured_plans() {
    // First try plans marked as featured via ACF checkbox
    $featured_args = array(
        'post_type'      => 'plans',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => 'featured_plan',
                'value' => '1',
            ),
        ),
        'orderby' => 'date',
        'order'   => 'DESC',
    );

    $plans = new WP_Query( $featured_args );

    // Fallback: just get the 6 most recent plans
    if ( ! $plans->have_posts() ) {
        $plans = new WP_Query( array(
            'post_type'      => 'plans',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
    }

    if ( ! $plans->have_posts() ) {
        wp_reset_postdata();
        return;
    }
    ?>

    <div class="popularsection">
        <h1>Popular House Plans:</h1>
        <div class="popularplans">
            <?php
            $count = 0;
            while ( $plans->have_posts() ) :
                $plans->the_post();

                $plan_name         = get_field( 'plan_name' ) ?: get_the_title();
                $total_living_area = get_field( 'total_living_area' );
                $bedrooms          = get_field( 'bedrooms' );
                $bathrooms         = get_field( 'bathrooms' );
                $permalink         = get_permalink();

                // Open a new <ul> row every 3 plans
                if ( $count % 3 === 0 ) {
                    echo '<ul>';
                }

                $is_first = ( $count % 3 === 0 ) ? ' first' : '';
                ?>
                <li class="one-third<?php echo $is_first; ?>">
                    <a title="<?php echo esc_attr( $plan_name ); ?>" href="<?php echo esc_url( $permalink ); ?>">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'home-featured-posts', array(
                                'class'   => 'home-featured-plans',
                                'alt'     => esc_attr( $plan_name ) . ' house plan',
                                'loading' => 'lazy',
                                'width'   => '350',
                                'height'  => '225',
                            ) );
                        }
                        ?>
                        <h2><?php echo esc_html( $plan_name ); ?></h2>
                    </a>
                    <?php if ( $total_living_area || $bedrooms || $bathrooms ) : ?>
                    <p style="font-size:13px;color:#666;margin:4px 0 0;text-align:center;">
                        <?php
                        $meta = array();
                        if ( $total_living_area ) $meta[] = $total_living_area . ' sq ft';
                        if ( $bedrooms )          $meta[] = $bedrooms . ' bed';
                        if ( $bathrooms )         $meta[] = $bathrooms . ' bath';
                        echo implode( ' &bull; ', $meta );
                        ?>
                    </p>
                    <?php endif; ?>
                </li>
                <?php

                $count++;

                // Close row every 3
                if ( $count % 3 === 0 ) {
                    echo '</ul>';
                }

            endwhile;

            // Close uncompleted row
            if ( $count % 3 !== 0 ) {
                echo '</ul>';
            }
            ?>
        </div>
        <p style="text-align:center;margin-top:24px;">
            <a href="<?php echo home_url( '/home-plans/' ); ?>" class="button" style="background:#7A5C3E;color:#fff;padding:12px 28px;border-radius:4px;text-decoration:none;font-weight:600;">Browse All House Plans</a>
        </p>
    </div>

    <?php
    wp_reset_postdata();
}

genesis();
