<?php
/**
 * Single Plan Page Template
 * CPT: plans
 *
 * Improvements over original (plans1.php / single-plans.php):
 *  - Above-fold section: plan name H1, specs grid, price, BUY button — all visible without scrolling
 *  - Price extracted from ACF `price` field or parsed from PayPal HTML fallback
 *  - Sticky mobile CTA bar (fixed bottom on mobile)
 *  - Product schema JSON-LD in <head> (injected via functions.php hook)
 *  - Full description section with proper heading hierarchy
 *  - Floor plans section
 *  - "What's Included" checklist
 *  - Related plans section
 *  - Genesis sidebar removed on plan pages
 *  - PayPal ad script removed
 *  - No jQuery 1.9.1 or Bootstrap — uses WP bundled jQuery if needed
 */

// ---------------------------------------------------------------------------
// Body class
// ---------------------------------------------------------------------------
add_filter( 'body_class', 'mhp_plan_body_class' );
function mhp_plan_body_class( $classes ) {
    $classes[] = 'single-home-plan';
    return $classes;
}

// ---------------------------------------------------------------------------
// Remove Genesis structural wrap from site-inner (kept from original)
// ---------------------------------------------------------------------------
add_theme_support( 'genesis-structural-wraps', array(
    'header',
    'footer-widgets',
    'footer',
) );

// ---------------------------------------------------------------------------
// Force full-width layout (no sidebar on plan pages)
// ---------------------------------------------------------------------------
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

// ---------------------------------------------------------------------------
// Remove default Genesis entry output — we control all rendering
// ---------------------------------------------------------------------------
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open',  5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open',  5 );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Remove default Genesis loop
remove_action( 'genesis_loop', 'genesis_do_loop' );

// ---------------------------------------------------------------------------
// Custom Loop — wraps everything in a single <div class="wrap">
// ---------------------------------------------------------------------------
add_action( 'genesis_loop', 'mhp_plan_custom_loop' );
function mhp_plan_custom_loop() {
    while ( have_posts() ) :
        the_post();
        mhp_plan_above_fold();
        mhp_plan_gallery_section();
        mhp_plan_description_section();
        mhp_plan_floor_plans_section();
        mhp_plan_specs_full_section();
        mhp_plan_whats_included_section();
        mhp_plan_related_plans_section();
    endwhile;
}

// ---------------------------------------------------------------------------
// Helper: extract price from PayPal HTML
// ---------------------------------------------------------------------------
function mhp_extract_price_from_paypal( $paypal_html ) {
    if ( empty( $paypal_html ) ) {
        return '';
    }
    preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?) USD/', $paypal_html, $matches );
    return isset( $matches[0] ) ? $matches[0] : '';
}

// ---------------------------------------------------------------------------
// Section 1: Above-the-Fold — Plan Name, Specs, Price, Buy CTA
// ---------------------------------------------------------------------------
function mhp_plan_above_fold() {
    $post_id    = get_the_ID();
    $plan_name  = function_exists( 'get_field' ) ? get_field( 'plan_name' ) : '';
    $plan_name  = $plan_name ?: get_the_title();

    $sqft       = function_exists( 'get_field' ) ? get_field( 'total_living_area' ) : '';
    $bedrooms   = function_exists( 'get_field' ) ? get_field( 'bedrooms' ) : '';
    $bathrooms  = function_exists( 'get_field' ) ? get_field( 'bathrooms' ) : '';
    $stories    = function_exists( 'get_field' ) ? get_field( 'stories' ) : '';
    $width      = function_exists( 'get_field' ) ? get_field( 'width' ) : '';
    $depth      = function_exists( 'get_field' ) ? get_field( 'depth' ) : '';
    $garage     = function_exists( 'get_field' ) ? get_field( 'garage' ) : '';
    $style      = function_exists( 'get_field' ) ? get_field( 'style' ) : '';
    $price      = function_exists( 'get_field' ) ? get_field( 'price' ) : '';
    $paypal_html = function_exists( 'get_field' ) ? get_field( 'paypal', false, false ) : '';

    // Price fallback from PayPal HTML
    if ( empty( $price ) && $paypal_html ) {
        $price = mhp_extract_price_from_paypal( $paypal_html );
    }

    $excerpt = get_the_excerpt();
    ?>
    <div class="plan-header" itemscope itemtype="https://schema.org/Product">
        <meta itemprop="name" content="<?php echo esc_attr( $plan_name ); ?>">
        <?php if ( $excerpt ) : ?>
            <meta itemprop="description" content="<?php echo esc_attr( $excerpt ); ?>">
        <?php endif; ?>

        <!-- Left: Hero Image -->
        <div class="plan-header__image">
            <?php
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'plan-hero', array(
                    'itemprop'      => 'image',
                    'fetchpriority' => 'high',
                    'loading'       => 'eager',
                    'alt'           => esc_attr( $plan_name ) . ( $style ? ' ' . esc_attr( $style ) . ' house plan exterior' : ' house plan exterior' ),
                    'class'         => 'plan-hero-img',
                ) );
            }
            ?>
        </div>

        <!-- Right: Details, Specs, Price, CTA -->
        <div class="plan-header__details">

            <h1 class="plan-header__title" itemprop="name">
                <?php echo esc_html( $plan_name ); ?>
            </h1>

            <?php if ( $style ) : ?>
            <div class="plan-header__tags" aria-label="Plan styles">
                <?php foreach ( explode( ',', $style ) as $tag ) : ?>
                    <span class="tag"><?php echo esc_html( trim( $tag ) ); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Key Specs Grid -->
            <div class="plan-specs-grid" aria-label="Plan specifications">
                <?php if ( $sqft ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__icon" aria-hidden="true">📐</span>
                    <span class="plan-spec__value"><?php echo esc_html( $sqft ); ?></span>
                    <span class="plan-spec__label">sq ft</span>
                </div>
                <?php endif; ?>
                <?php if ( $bedrooms ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__icon" aria-hidden="true">🛏</span>
                    <span class="plan-spec__value"><?php echo esc_html( $bedrooms ); ?></span>
                    <span class="plan-spec__label">bed</span>
                </div>
                <?php endif; ?>
                <?php if ( $bathrooms ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__icon" aria-hidden="true">🚿</span>
                    <span class="plan-spec__value"><?php echo esc_html( $bathrooms ); ?></span>
                    <span class="plan-spec__label">bath</span>
                </div>
                <?php endif; ?>
                <?php if ( $stories ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__icon" aria-hidden="true">🏠</span>
                    <span class="plan-spec__value"><?php echo esc_html( $stories ); ?></span>
                    <span class="plan-spec__label">story</span>
                </div>
                <?php endif; ?>
                <?php if ( $garage ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__icon" aria-hidden="true">🚗</span>
                    <span class="plan-spec__value plan-spec__value--sm"><?php echo esc_html( $garage ); ?></span>
                    <span class="plan-spec__label">garage</span>
                </div>
                <?php endif; ?>
                <?php if ( $width ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__label">Width</span>
                    <span class="plan-spec__value plan-spec__value--sm"><?php echo esc_html( $width ); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $depth ) : ?>
                <div class="plan-spec">
                    <span class="plan-spec__label">Depth</span>
                    <span class="plan-spec__value plan-spec__value--sm"><?php echo esc_html( $depth ); ?></span>
                </div>
                <?php endif; ?>
            </div><!-- .plan-specs-grid -->

            <!-- Price -->
            <?php if ( $price ) : ?>
            <div class="plan-header__pricing" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <meta itemprop="priceCurrency" content="USD">
                <meta itemprop="price" content="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', $price ) ); ?>">
                <meta itemprop="availability" content="https://schema.org/InStock">
                <span class="plan-header__price-label">From</span>
                <span class="plan-header__price"><?php echo esc_html( $price ); ?></span>
            </div>
            <?php endif; ?>

            <!-- Buy CTA + PayPal form -->
            <?php if ( $paypal_html ) : ?>
            <div class="plan-header__buy-button">
                <?php echo $paypal_html; // PayPal form — styled via CSS, GIF button replaced by sticky-cta.js ?>
            </div>
            <?php endif; ?>

            <div class="plan-header__secondary-actions">
                <a href="<?php echo esc_url( home_url( '/home-plan-modifications/' ) ); ?>?plan=<?php echo urlencode( $plan_name ); ?>"
                   class="btn btn--secondary">
                    Request Modification
                </a>
            </div>

            <!-- Contact -->
            <div class="plan-header__contact">
                <a href="tel:+17703014214" class="plan-contact-link">
                    📞 Questions? Call Max: (770) 301-4214
                </a>
                <p>Or <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">email us</a> — response within 24 hours</p>
            </div>

        </div><!-- .plan-header__details -->
    </div><!-- .plan-header -->
    <?php
}

// ---------------------------------------------------------------------------
// Section 2: Photo Gallery
// ---------------------------------------------------------------------------
function mhp_plan_gallery_section() {
    $gallery = get_post_gallery();
    if ( ! $gallery ) {
        return;
    }
    echo '<div class="plan-gallery-section">';
    echo '<h2 class="section-title">Plan Photos</h2>';
    echo $gallery;
    echo '</div>';
}

// ---------------------------------------------------------------------------
// Section 3: Plan Description
// ---------------------------------------------------------------------------
function mhp_plan_description_section() {
    $description = function_exists( 'get_field' ) ? get_field( 'plan_description' ) : '';
    if ( empty( $description ) ) {
        return;
    }
    echo '<div class="plan-description-section">';
    echo '<h2 class="section-title">About This House Plan</h2>';
    echo '<div class="plan-description">' . $description . '</div>';
    echo '</div>';
}

// ---------------------------------------------------------------------------
// Section 4: Floor Plans
// ---------------------------------------------------------------------------
function mhp_plan_floor_plans_section() {
    if ( ! function_exists( 'get_field' ) ) {
        return;
    }

    $floor_plans_wysiwyg = get_field( 'floor_plans' );
    $floor_plan_1        = get_field( 'floor_plan_1' );
    $floor_plan_2        = get_field( 'floor_plan_2' );
    $floor_plan_3        = get_field( 'floor_plan_3' );

    $has_content = $floor_plans_wysiwyg || $floor_plan_1 || $floor_plan_2 || $floor_plan_3;
    if ( ! $has_content ) {
        return;
    }
    ?>
    <div class="plan-floor-plans-section">
        <h2 class="section-title">Floor Plans</h2>
        <div class="plan-floor-plans-grid">
            <?php if ( $floor_plans_wysiwyg ) : ?>
                <div class="plan-floor-plan plan-floor-plan--legacy">
                    <?php echo $floor_plans_wysiwyg; ?>
                </div>
            <?php endif; ?>

            <?php
            $floor_images = array_filter( array( $floor_plan_1, $floor_plan_2, $floor_plan_3 ) );
            foreach ( $floor_images as $image ) :
                if ( empty( $image ) ) continue;
                $url    = is_array( $image ) ? $image['url']   : $image;
                $alt    = is_array( $image ) ? $image['alt']   : '';
                $title  = is_array( $image ) ? $image['title'] : '';
                $thumb  = is_array( $image ) ? ( $image['sizes']['large'] ?? $url ) : $url;
                $w      = is_array( $image ) ? ( $image['sizes']['large-width'] ?? '' ) : '';
                $h      = is_array( $image ) ? ( $image['sizes']['large-height'] ?? '' ) : '';
            ?>
            <div class="plan-floor-plan">
                <a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $title ); ?>" class="plan-floor-plan__link">
                    <img src="<?php echo esc_url( $thumb ); ?>"
                         alt="<?php echo esc_attr( $alt ); ?>"
                         <?php if ( $w ) echo 'width="' . esc_attr( $w ) . '"'; ?>
                         <?php if ( $h ) echo 'height="' . esc_attr( $h ) . '"'; ?>
                         loading="lazy">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// Section 5: Full Specs Table
// ---------------------------------------------------------------------------
function mhp_plan_specs_full_section() {
    if ( ! function_exists( 'get_field' ) ) {
        return;
    }

    $specs = array(
        'Living Area'       => array( get_field( 'total_living_area' ) ),
        'Main Floor'        => array( get_field( 'main_floor' ) ),
        'Upper Floor'       => array( get_field( 'upper_floor' ) ),
        'Lower Floor'       => array( get_field( 'lower_floor' ) ),
        'Bedrooms'          => array( get_field( 'bedrooms' ) ),
        'Bathrooms'         => array( get_field( 'bathrooms' ) ),
        'Stories'           => array( get_field( 'stories' ) ),
        'Width'             => array( get_field( 'width' ) ),
        'Depth'             => array( get_field( 'depth' ) ),
        'Garage'            => array( get_field( 'garage' ) ),
        'Additional Rooms'  => array( get_field( 'additional_rooms' ) ),
        'Outdoor Spaces'    => array( get_field( 'outdoor' ) ),
        'Roof Pitch'        => array( get_field( 'roof' ) ),
        'Ceiling Height'    => array( get_field( 'ceiling' ) ),
        'Exterior Framing'  => array( get_field( 'exterior' ) ),
        'Other Features'    => array( get_field( 'other_features' ) ),
        'Home Style'        => array( get_field( 'style' ) ),
        'Lot Style'         => array( get_field( 'lot_style' ) ),
    );

    // Filter out empty values
    $specs = array_filter( $specs, function( $v ) { return ! empty( $v[0] ); } );
    if ( empty( $specs ) ) {
        return;
    }
    ?>
    <div class="plan-specs-full-section">
        <h2 class="section-title">Full Specifications</h2>
        <div class="plan-specs-table">
            <?php foreach ( $specs as $label => $value ) : ?>
            <div class="plan-specs-table__row">
                <span class="plan-specs-table__label"><?php echo esc_html( $label ); ?></span>
                <span class="plan-specs-table__value"><?php echo esc_html( $value[0] ); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// Section 6: What's Included
// ---------------------------------------------------------------------------
function mhp_plan_whats_included_section() {
    ?>
    <div class="plan-whats-included-section">
        <h2 class="section-title">What's Included</h2>
        <div class="plan-included-grid">
            <ul class="plan-included-list">
                <li>✅ Detailed floor plans (all levels)</li>
                <li>✅ Foundation plan</li>
                <li>✅ Roof plan</li>
                <li>✅ All elevations — front, rear, and sides</li>
                <li>✅ Notes, dimensions, and recommended materials</li>
                <li>✅ PDF format included</li>
                <li>✅ CAD file available (select at checkout)</li>
            </ul>
            <div class="plan-included-notes">
                <h3>A Note on Electrical Plans</h3>
                <p>Electrical plans are highly customer-specific and are not included in the standard set.
                   We can produce custom electrical plans for <strong>$350</strong>.</p>
                <h3>Important Information</h3>
                <p>All sales on house plans are final. Plans are designed to conform to local codes
                   where the original house was constructed. Some regions may require review by a
                   local licensed engineer.</p>
            </div>
        </div>

        <!-- Modifications CTA -->
        <div class="plan-modifications-cta">
            <h3>Want to Modify This Plan?</h3>
            <p>We can adjust layout, add a garage, change square footage, or customize anything to
               fit your lot and family.</p>
            <a href="<?php echo esc_url( home_url( '/home-plan-modifications/' ) ); ?>"
               class="btn btn--accent">
                Request a Modification Quote
            </a>
        </div>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// Section 7: Related Plans
// ---------------------------------------------------------------------------
function mhp_plan_related_plans_section() {
    $post_id = get_the_ID();
    $terms   = wp_get_post_terms( $post_id, 'home-plans_categories', array( 'fields' => 'ids' ) );

    $args = array(
        'post_type'      => 'plans',
        'posts_per_page' => 4,
        'post__not_in'   => array( $post_id ),
        'post_status'    => 'publish',
        'orderby'        => 'rand',
    );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'home-plans_categories',
                'field'    => 'term_id',
                'terms'    => $terms,
            ),
        );
    }

    $related = new WP_Query( $args );

    if ( ! $related->have_posts() ) {
        wp_reset_postdata();
        return;
    }
    ?>
    <div class="plan-related-section">
        <h2 class="section-title">More House Plans You Might Like</h2>
        <ul class="plans-grid" role="list">
            <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php
                // Inline plan card to avoid template-parts dependency issue
                $card_name   = function_exists( 'get_field' ) ? get_field( 'plan_name' ) : '';
                $card_name   = $card_name ?: get_the_title();
                $card_sqft   = function_exists( 'get_field' ) ? get_field( 'total_living_area' ) : '';
                $card_bed    = function_exists( 'get_field' ) ? get_field( 'bedrooms' ) : '';
                $card_bath   = function_exists( 'get_field' ) ? get_field( 'bathrooms' ) : '';
                $card_price  = function_exists( 'get_field' ) ? get_field( 'price' ) : '';
                $card_paypal = function_exists( 'get_field' ) ? get_field( 'paypal', false, false ) : '';
                if ( empty( $card_price ) && $card_paypal ) {
                    $card_price = mhp_extract_price_from_paypal( $card_paypal );
                }
                ?>
                <li class="plan-card">
                    <div class="plan-card__image-wrap">
                        <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'plan-card', array(
                                    'loading' => 'lazy',
                                    'alt'     => esc_attr( $card_name ) . ' house plan',
                                    'sizes'   => '(max-width: 600px) calc(100vw - 2rem), (max-width: 1024px) calc(50vw - 2rem), 380px',
                                ) ); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="plan-card__body">
                        <h3 class="plan-card__title">
                            <a href="<?php the_permalink(); ?>"><?php echo esc_html( $card_name ); ?></a>
                        </h3>
                        <div class="plan-card__specs">
                            <?php if ( $card_sqft ) : ?>
                                <span class="plan-card__spec"><?php echo esc_html( $card_sqft ); ?></span>
                            <?php endif; ?>
                            <?php if ( $card_bed ) : ?>
                                <span class="plan-card__spec"><?php echo esc_html( $card_bed ); ?> bed</span>
                            <?php endif; ?>
                            <?php if ( $card_bath ) : ?>
                                <span class="plan-card__spec"><?php echo esc_html( $card_bath ); ?> bath</span>
                            <?php endif; ?>
                        </div>
                        <?php if ( $card_price ) : ?>
                            <div class="plan-card__price">From <?php echo esc_html( $card_price ); ?></div>
                        <?php endif; ?>
                        <div class="plan-card__cta">
                            <a href="<?php the_permalink(); ?>" class="btn btn--accent btn--full">View Plan</a>
                        </div>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php
    wp_reset_postdata();
}

// ---------------------------------------------------------------------------
// Sticky Mobile CTA Bar — injected before footer
// ---------------------------------------------------------------------------
add_action( 'genesis_before_footer', 'mhp_sticky_mobile_cta_bar', 5 );
function mhp_sticky_mobile_cta_bar() {
    if ( ! is_singular( 'plans' ) ) {
        return;
    }

    $plan_name   = function_exists( 'get_field' ) ? get_field( 'plan_name' ) : '';
    $plan_name   = $plan_name ?: get_the_title();
    $price       = function_exists( 'get_field' ) ? get_field( 'price' ) : '';
    $paypal_html = function_exists( 'get_field' ) ? get_field( 'paypal', false, false ) : '';

    if ( empty( $price ) && $paypal_html ) {
        $price = mhp_extract_price_from_paypal( $paypal_html );
    }

    // Truncate name for mobile bar
    $short_name = mb_strlen( $plan_name ) > 20 ? mb_substr( $plan_name, 0, 18 ) . '…' : $plan_name;
    ?>
    <div id="mobile-cta-bar" class="mobile-cta-bar" aria-hidden="true" role="complementary" aria-label="Plan purchase bar">
        <div class="mobile-cta-bar__info">
            <span class="mobile-cta-bar__name"><?php echo esc_html( $short_name ); ?></span>
            <?php if ( $price ) : ?>
                <span class="mobile-cta-bar__price"><?php echo esc_html( $price ); ?></span>
            <?php endif; ?>
        </div>
        <div class="mobile-cta-bar__actions">
            <a href="tel:+17703014214" class="mobile-cta-bar__call btn btn--sm btn--outline" aria-label="Call Max">
                📞 Call
            </a>
            <button class="mobile-cta-bar__buy btn btn--accent btn--sm" type="button">
                BUY PLAN
            </button>
        </div>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// Render the page via Genesis
// ---------------------------------------------------------------------------
genesis();
