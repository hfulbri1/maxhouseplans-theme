<?php
/**
 * Plan Card Template Part
 * template-parts/plan-card.php
 *
 * Reusable plan card component for archive, category, and homepage grids.
 *
 * Usage:
 *   while ( $query->have_posts() ) {
 *       $query->the_post();
 *       get_template_part( 'template-parts/plan-card' );
 *   }
 *
 * Expects: to be called inside a WP loop (have_posts() / the_post() already called).
 *
 * ACF fields read (all optional — degrades gracefully if empty):
 *   plan_name         (text)   — display name
 *   total_living_area (text)   — e.g. "1850 Sq Ft"
 *   bedrooms          (text)   — e.g. "3"
 *   bathrooms         (text)   — e.g. "2.5"
 *   stories           (text)   — e.g. "1"
 *   price             (number) — explicit price field (new)
 *   paypal            (wysiwyg)— legacy; price parsed as fallback
 *   style             (text)   — comma-separated styles
 */

// -----------------------------------------------------------------------
// Collect data
// -----------------------------------------------------------------------
$card_name      = function_exists( 'get_field' ) ? get_field( 'plan_name' ) : '';
$card_name      = $card_name ?: get_the_title();

$card_sqft      = function_exists( 'get_field' ) ? get_field( 'total_living_area' ) : '';
$card_bedrooms  = function_exists( 'get_field' ) ? get_field( 'bedrooms' )          : '';
$card_bathrooms = function_exists( 'get_field' ) ? get_field( 'bathrooms' )         : '';
$card_stories   = function_exists( 'get_field' ) ? get_field( 'stories' )           : '';
$card_style     = function_exists( 'get_field' ) ? get_field( 'style' )             : '';
$card_price     = function_exists( 'get_field' ) ? get_field( 'price' )             : '';
$card_paypal    = function_exists( 'get_field' ) ? get_field( 'paypal', false, false ) : '';

// Price fallback — parse from PayPal HTML if dedicated field is empty
if ( empty( $card_price ) && ! empty( $card_paypal ) ) {
    preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?) USD/', $card_paypal, $price_match );
    if ( isset( $price_match[0] ) ) {
        $card_price = $price_match[0];
    }
}

$card_permalink = get_permalink();
$card_alt_text  = esc_attr( $card_name ) . ' house plan';

// Style tags array (comma-separated string)
$style_tags = array();
if ( $card_style ) {
    $style_tags = array_filter( array_map( 'trim', explode( ',', $card_style ) ) );
}

// Optional badge — check for collections ACF field (new field, may not exist yet)
$card_badge = '';
if ( function_exists( 'get_field' ) ) {
    $collections = get_field( 'collections' );
    if ( is_array( $collections ) && in_array( 'Best Seller', $collections, true ) ) {
        $card_badge = 'Best Seller';
    } elseif ( is_array( $collections ) && in_array( 'New Arrival', $collections, true ) ) {
        $card_badge = 'New';
    }
}
?>
<li class="plan-card" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="name" content="<?php echo esc_attr( $card_name ); ?>">

    <!-- Thumbnail image -->
    <div class="plan-card__image-wrap">
        <a href="<?php echo esc_url( $card_permalink ); ?>"
           tabindex="-1"
           aria-hidden="true"
           class="plan-card__image-link">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'plan-card', array(
                    'loading'   => 'lazy',
                    'alt'       => $card_alt_text,
                    'itemprop'  => 'image',
                    'sizes'     => '(max-width: 600px) calc(100vw - 2rem), (max-width: 1024px) calc(50vw - 2rem), 380px',
                    'class'     => 'plan-card__img',
                ) ); ?>
            <?php else : ?>
                <div class="plan-card__img-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </a>

        <?php if ( $card_badge ) : ?>
            <span class="plan-card__badge"><?php echo esc_html( $card_badge ); ?></span>
        <?php endif; ?>
    </div><!-- .plan-card__image-wrap -->

    <!-- Card body -->
    <div class="plan-card__body">

        <!-- Plan name -->
        <h3 class="plan-card__title">
            <a href="<?php echo esc_url( $card_permalink ); ?>" itemprop="url">
                <?php echo esc_html( $card_name ); ?>
            </a>
        </h3>

        <!-- Key specs inline -->
        <div class="plan-card__specs" aria-label="Plan specs">
            <?php if ( $card_sqft ) : ?>
                <span class="plan-card__spec plan-card__spec--sqft"><?php echo esc_html( $card_sqft ); ?></span>
            <?php endif; ?>
            <?php if ( $card_bedrooms ) : ?>
                <span class="plan-card__spec plan-card__spec--bed"><?php echo esc_html( $card_bedrooms ); ?> bed</span>
            <?php endif; ?>
            <?php if ( $card_bathrooms ) : ?>
                <span class="plan-card__spec plan-card__spec--bath"><?php echo esc_html( $card_bathrooms ); ?> bath</span>
            <?php endif; ?>
            <?php if ( $card_stories ) : ?>
                <span class="plan-card__spec plan-card__spec--stories"><?php echo esc_html( $card_stories ); ?> story</span>
            <?php endif; ?>
        </div><!-- .plan-card__specs -->

        <!-- Style tags -->
        <?php if ( ! empty( $style_tags ) ) : ?>
        <div class="plan-card__tags">
            <?php foreach ( $style_tags as $tag ) : ?>
                <span class="tag tag--sm"><?php echo esc_html( $tag ); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Price -->
        <?php if ( $card_price ) : ?>
            <div class="plan-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <meta itemprop="priceCurrency" content="USD">
                <meta itemprop="price" content="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', $card_price ) ); ?>">
                <span class="plan-card__price-label">From</span>
                <span class="plan-card__price-amount"><?php echo esc_html( $card_price ); ?></span>
            </div>
        <?php endif; ?>

        <!-- CTA Button -->
        <div class="plan-card__cta">
            <a href="<?php echo esc_url( $card_permalink ); ?>"
               class="btn btn--accent btn--full"
               aria-label="View <?php echo esc_attr( $card_name ); ?> house plan">
                View Plan
            </a>
        </div>

    </div><!-- .plan-card__body -->
</li><!-- .plan-card -->
