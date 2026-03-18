<?php
/**
 * Archive template — handles post type archive for 'plans' CPT
 * Fixes: "Sorry, no content matched your criteria" bug
 *
 * @package MaxHousePlans
 */

// If this is a taxonomy archive for home-plans_categories, let taxonomy template handle it
// (taxonomy-home-plans_categories.php or taxonomy-plan-categories.php takes precedence)

// Remove default Genesis loop — we run our own WP_Query
remove_action( 'genesis_loop', 'genesis_do_loop' );

// Remove default entry meta that doesn't apply to plans
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Force full-width layout for archive
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );

// Add archive title above content
add_action( 'genesis_before_content_sidebar_wrap', 'mhp_archive_page_title' );
function mhp_archive_page_title() {
    $title = '';

    if ( is_post_type_archive( 'plans' ) ) {
        $title = 'All House Plans';
    } elseif ( is_search() ) {
        $title = 'Search Results: ' . get_search_query();
    } else {
        $title = get_the_archive_title();
    }

    echo '<div id="plan-title"><h1>' . esc_html( $title ) . '</h1></div>';
}

// Custom loop — query 'plans' CPT
add_action( 'genesis_loop', 'mhp_archive_plans_loop' );
function mhp_archive_plans_loop() {
    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

    $args = array(
        'post_type'      => 'plans',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Honour search queries
    if ( is_search() ) {
        $args['s'] = get_search_query();
    }

    $plans_query = new WP_Query( $args );

    if ( $plans_query->have_posts() ) {
        echo '<style>
        .wrap { max-width: 1300px; }
        .plans-archive-grid { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 16px; }
        .plans-archive-grid li.li_class { width: calc(50% - 8px); background: #fff; border: 1px solid #ddd; padding: 0; position: relative; min-height: 440px; margin-bottom: 0; box-sizing: border-box; }
        .plans-archive-grid li.li_class img { width: 100%; height: auto; display: block; }
        h2.entry-title.titless2 { font-size: 16px; line-height: 1.4; padding: 8px; margin: 0; }
        .plandata ul { list-style: none; padding: 4px 8px; margin: 0; display: flex; flex-wrap: wrap; }
        .plandata ul li { font-size: 14px; padding: 2px 8px; font-weight: bold; }
        .plandata ul li span { font-weight: normal; font-size: 12px; display: block; }
        .planbutton { padding: 0 8px 12px; }
        .planbutton a.button { display: block; text-align: center; padding: 10px; background: #7A5C3E; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 600; }
        @media (max-width: 650px) { .plans-archive-grid li.li_class { width: 100%; min-height: auto; } .planbutton { position: static; } }
        </style>';

        echo '<ul class="plans-archive-grid">';

        while ( $plans_query->have_posts() ) {
            $plans_query->the_post();

            $plan_name          = get_field( 'plan_name' ) ?: get_the_title();
            $total_living_area  = get_field( 'total_living_area' );
            $bedrooms           = get_field( 'bedrooms' );
            $bathrooms          = get_field( 'bathrooms' );
            $stories            = get_field( 'stories' );
            $style              = get_field( 'style' );

            echo '<li class="li_class">';
            echo '<div class="entry-content">';
            echo '<a href="' . get_permalink() . '">';
            the_post_thumbnail( 'home-plan-grid', array( 'loading' => 'lazy', 'alt' => esc_attr( $plan_name ) . ' house plan' ) );
            echo '</a>';
            echo '<h2 class="entry-title titless2"><a href="' . get_permalink() . '">' . esc_html( $plan_name ) . '</a></h2>';
            echo '<div class="plandata"><ul>';
            if ( $total_living_area ) echo '<li>' . esc_html( $total_living_area ) . ' <span>Sq Ft</span></li>';
            if ( $bedrooms )         echo '<li>' . esc_html( $bedrooms )         . ' <span>Bed</span></li>';
            if ( $bathrooms )        echo '<li>' . esc_html( $bathrooms )        . ' <span>Bath</span></li>';
            if ( $stories )          echo '<li>' . esc_html( $stories )          . ' <span>Story</span></li>';
            echo '</ul></div>';
            echo '<div class="planbutton"><a href="' . get_permalink() . '" class="button">View House Plan</a></div>';
            echo '</div></li>';
        }

        echo '</ul>';

        // Pagination
        $big = 999999999;
        echo '<div class="pagination" style="text-align:center;margin:24px 0;">';
        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $plans_query->max_num_pages,
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ) );
        echo '</div>';

    } else {
        echo '<p>No house plans found. <a href="' . home_url( '/home-plans/' ) . '">Browse all plans</a>.</p>';
    }

    wp_reset_postdata();
}

// Sidebar — filter options
remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
add_action( 'genesis_sidebar', 'mhp_archive_sidebar' );
function mhp_archive_sidebar() {
    $categories = get_terms( array(
        'taxonomy'   => 'home-plans_categories',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
    ?>
    <div class="plansinfo">
        <h2 class="planheader" style="font-size:18px;border-bottom:2px solid #7A5C3E;padding-bottom:8px;margin-bottom:16px;">Filter Plans</h2>

        <div class="plan-description">
            <form action="" method="post" style="margin-bottom:24px;">
                <div class="form_div" style="margin-bottom:16px;">
                    <h3 style="font-size:15px;margin-bottom:8px;">Bedrooms</h3>
                    <ul style="list-style:none;padding:0;margin:0;">
                        <?php foreach ( array( 2, 3, 4 ) as $beds ) : ?>
                        <li><label><input type="checkbox" name="bedroom[]" value="<?php echo $beds; ?>" /> <?php echo $beds; ?> Bedrooms</label></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="form_div" style="margin-bottom:16px;">
                    <h3 style="font-size:15px;margin-bottom:8px;">Floors</h3>
                    <ul style="list-style:none;padding:0;margin:0;">
                        <?php foreach ( array( '1' => '1 Story', '2' => '2 Story', '3' => '3 Story' ) as $val => $label ) : ?>
                        <li><label><input type="checkbox" name="stories[]" value="<?php echo $val; ?>" /> <?php echo $label; ?></label></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="form_div">
                    <input type="submit" name="submit" value="Apply Filter" style="background:#7A5C3E;color:#fff;border:none;padding:10px 20px;cursor:pointer;border-radius:4px;width:100%;font-size:15px;" />
                </div>
            </form>

            <?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
            <div class="cat_section">
                <h3 style="font-size:15px;margin-bottom:8px;">Browse by Style</h3>
                <ul style="list-style:none;padding:0;margin:0;">
                    <?php foreach ( $categories as $category ) : ?>
                    <li style="margin-bottom:4px;"><a href="<?php echo get_term_link( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

genesis();
