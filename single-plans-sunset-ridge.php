<?php
/**
 * Template Name: Plan - Sunset Ridge
 *
 * Dedicated plan template for the Sunset Ridge viral farmhouse plan.
 * Applies to plan slug: tiktok-house-plan-sunset-ridge
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-19
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_sunset_loop' );
add_action( 'wp_head', 'mhp_sunset_styles', 20 );
add_action( 'wp_head', 'mhp_sunset_schema', 5 );
add_action( 'wp_head', 'mhp_sunset_fonts', 1 );

function mhp_sunset_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">' . "\n";
}

function mhp_sunset_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );
    $name    = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $name    = $name ?: get_the_title();
    $sqft    = $acf ? get_field( 'total_living_area', $pid ) : '3196';
    $beds    = $acf ? get_field( 'bedrooms', $pid ) : '4';
    $baths   = $acf ? get_field( 'bathrooms', $pid ) : '3.5';
    $stories = $acf ? get_field( 'stories', $pid ) : '1';
    $price   = $acf ? get_field( 'price', $pid ) : '1500';
    $price_num = is_numeric( $price ) ? number_format( (float) $price, 2, '.', '' ) : '1500.00';
    $cad_price_num = number_format( (float) $price_num * 1.30, 2, '.', '' );
    $image_url = get_the_post_thumbnail_url( $pid, 'full' );
    $permalink = get_permalink( $pid );

    $product = array(
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name' => $name . ' House Plan',
        'description' => 'A build-ready one-story modern farmhouse plan featuring 3,196 sq ft of heated living space, 4 bedrooms, 3.5 bathrooms, open concept family room and kitchen, 10-foot ceilings, wraparound front porch, vaulted rear porch, and a 36-foot carport. The viral TikTok house plan.',
        'image' => $image_url ? array( $image_url ) : array(),
        'brand' => array( '@type' => 'Brand', 'name' => 'Max Fulbright Designs' ),
        'offers' => array(
            array( '@type' => 'Offer', 'name' => 'PDF Plan Set', 'price' => $price_num, 'priceCurrency' => 'USD', 'availability' => 'https://schema.org/InStock', 'url' => $permalink, 'seller' => array( '@type' => 'Organization', 'name' => 'Max House Plans' ) ),
            array( '@type' => 'Offer', 'name' => 'CAD File', 'price' => $cad_price_num, 'priceCurrency' => 'USD', 'availability' => 'https://schema.org/InStock', 'url' => $permalink ),
        ),
        'additionalProperty' => array(
            array( '@type' => 'PropertyValue', 'name' => 'Bedrooms', 'value' => $beds ),
            array( '@type' => 'PropertyValue', 'name' => 'Bathrooms', 'value' => $baths ),
            array( '@type' => 'PropertyValue', 'name' => 'Stories', 'value' => $stories ),
            array( '@type' => 'PropertyValue', 'name' => 'Heated Square Feet', 'value' => $sqft ),
            array( '@type' => 'PropertyValue', 'name' => 'Style', 'value' => 'Modern Farmhouse' ),
            array( '@type' => 'PropertyValue', 'name' => 'Ceiling Height', 'value' => '10 ft' ),
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $product, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";

    $faqs = $acf ? get_field( 'faqs', $pid ) : array();
    if ( ! empty( $faqs ) && is_array( $faqs ) ) {
        $faq_entities = array();
        foreach ( $faqs as $faq ) {
            $q = isset( $faq['question'] ) ? $faq['question'] : '';
            $a = isset( $faq['answer'] ) ? wp_strip_all_tags( $faq['answer'] ) : '';
            if ( $q && $a ) $faq_entities[] = array( '@type' => 'Question', 'name' => $q, 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ) );
        }
        if ( ! empty( $faq_entities ) ) {
            echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_entities ), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        }
    }

    $bc = array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array(
        array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
        array( '@type' => 'ListItem', 'position' => 2, 'name' => 'House Plans', 'item' => home_url( '/house-plans/' ) ),
        array( '@type' => 'ListItem', 'position' => 3, 'name' => 'Farmhouse House Plans', 'item' => home_url( '/house-plans/' ) ),
        array( '@type' => 'ListItem', 'position' => 4, 'name' => $name . ' House Plan' ),
    ) );
    echo '<script type="application/ld+json">' . wp_json_encode( $bc, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

function mhp_sunset_styles() {
?>
<style id="mhp-sunset-styles">
:root {
  --sr-primary: #3B3A37; --sr-primary-light: #5C5A55; --sr-primary-dark: #2A2926;
  --sr-accent: #8B6F47; --sr-accent-light: #A68A5F; --sr-accent-warm: #9C7B50;
  --sr-cream: #FDFBF7; --sr-cream-dark: #F5F0E8; --sr-warm-white: #FAF8F4;
  --sr-linen: #EDE8DE; --sr-sage: #7A8B6F; --sr-sage-light: #E8EDE4;
  --sr-text: #2D2C29; --sr-text-secondary: #6B6A65; --sr-text-light: #9A9890;
  --sr-white: #FFFFFF; --sr-border: #E6E1D8; --sr-border-light: #F0ECE4;
  --sr-success: #6B8F5E;
  --sr-font-display: 'Playfair Display', Georgia, serif;
  --sr-font-body: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
  --sr-space-xs: 0.25rem; --sr-space-sm: 0.5rem; --sr-space-md: 1rem;
  --sr-space-lg: 1.5rem; --sr-space-xl: 2rem; --sr-space-2xl: 3rem;
  --sr-space-3xl: 4rem; --sr-space-4xl: 6rem; --sr-max-width: 1280px;
  --sr-radius-sm: 8px; --sr-radius-md: 12px; --sr-radius-lg: 18px; --sr-radius-xl: 28px;
  --sr-shadow-sm: 0 1px 3px rgba(45,44,41,0.04); --sr-shadow-md: 0 4px 16px rgba(45,44,41,0.06);
  --sr-shadow-lg: 0 8px 32px rgba(45,44,41,0.08); --sr-shadow-xl: 0 20px 60px rgba(45,44,41,0.10);
  --sr-ease-out: cubic-bezier(0.16, 1, 0.3, 1);
  --sr-transition-fast: 0.2s var(--sr-ease-out); --sr-transition-base: 0.35s var(--sr-ease-out);
  --sr-transition-slow: 0.6s var(--sr-ease-out);
}
.sr-wrap *, .sr-wrap *::before, .sr-wrap *::after { margin: 0; padding: 0; box-sizing: border-box; }
.sr-wrap { font-family: var(--sr-font-body); color: var(--sr-text); background: var(--sr-cream); line-height: 1.7; font-size: 16px; overflow-x: hidden; font-weight: 400; }
.sr-wrap img { max-width: 100%; height: auto; display: block; }
.sr-wrap a { color: inherit; text-decoration: none; }
.sr-wrap button { cursor: pointer; border: none; background: none; font-family: inherit; }
.sr-container { max-width: var(--sr-max-width); margin: 0 auto; padding: 0 var(--sr-space-xl); }
@keyframes sr-fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes sr-fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes sr-scaleIn { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }
.sr-animate-in { opacity: 0; animation: sr-fadeInUp 0.7s var(--sr-ease-out) forwards; }
/* VIRAL BANNER */
.sr-viral-banner { background: var(--sr-primary-dark); color: var(--sr-white); text-align: center; padding: var(--sr-space-sm) var(--sr-space-md); font-size: 0.8rem; font-weight: 500; letter-spacing: 0.03em; }
.sr-viral-banner strong { color: var(--sr-accent-light); }
/* BREADCRUMBS */
.sr-breadcrumbs { padding: var(--sr-space-lg) 0 var(--sr-space-md); font-size: 0.8rem; color: var(--sr-text-light); font-weight: 400; }
.sr-breadcrumbs a { color: var(--sr-text-secondary); transition: color var(--sr-transition-fast); }
.sr-breadcrumbs a:hover { color: var(--sr-accent); }
.sr-breadcrumbs span { margin: 0 var(--sr-space-sm); opacity: 0.4; }
/* HERO */
.sr-plan-hero { padding-bottom: var(--sr-space-3xl); }
.sr-plan-hero__grid { display: grid; grid-template-columns: 1fr 400px; gap: var(--sr-space-3xl); align-items: start; }
.sr-gallery { animation: sr-fadeIn 0.5s var(--sr-ease-out) forwards; }
.sr-gallery__main { position: relative; border-radius: var(--sr-radius-lg); overflow: hidden; background: var(--sr-linen); aspect-ratio: 4/3; box-shadow: var(--sr-shadow-lg); }
.sr-gallery__main img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--sr-transition-slow); }
.sr-gallery__main:hover img { transform: scale(1.02); }
.sr-gallery__badge { position: absolute; top: var(--sr-space-lg); left: var(--sr-space-lg); display: flex; align-items: center; gap: var(--sr-space-xs); background: linear-gradient(135deg, #E1306C, #F77737); color: var(--sr-white); font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; padding: 6px var(--sr-space-md); border-radius: 100px; z-index: 2; }
.sr-gallery__badge svg { width: 14px; height: 14px; }
.sr-gallery__count { position: absolute; bottom: var(--sr-space-lg); right: var(--sr-space-lg); background: rgba(0,0,0,0.55); backdrop-filter: blur(8px); color: var(--sr-white); font-size: 0.78rem; font-weight: 500; padding: 5px var(--sr-space-md); border-radius: 100px; z-index: 2; display: flex; align-items: center; gap: 6px; }
.sr-gallery__count svg { width: 15px; height: 15px; }
.sr-gallery__thumbs { display: grid; grid-template-columns: repeat(5, 1fr); gap: var(--sr-space-sm); margin-top: var(--sr-space-sm); }
.sr-gallery__thumb { aspect-ratio: 1/1; border-radius: var(--sr-radius-sm); overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all var(--sr-transition-fast); opacity: 0.65; }
.sr-gallery__thumb:hover, .sr-gallery__thumb.sr-active { opacity: 1; border-color: var(--sr-accent); box-shadow: var(--sr-shadow-md); }
.sr-gallery__thumb img { width: 100%; height: 100%; object-fit: cover; }
/* PURCHASE CARD */
.sr-purchase-card { position: sticky; top: var(--sr-space-xl); background: var(--sr-white); border-radius: var(--sr-radius-lg); box-shadow: var(--sr-shadow-lg); overflow: hidden; animation: sr-scaleIn 0.5s var(--sr-ease-out) 0.15s forwards; opacity: 0; border: 1px solid var(--sr-border-light); }
.sr-purchase-card__header { padding: var(--sr-space-xl) var(--sr-space-xl) var(--sr-space-lg); background: linear-gradient(180deg, var(--sr-cream) 0%, var(--sr-white) 100%); border-bottom: 1px solid var(--sr-border-light); }
.sr-purchase-card__viral-tag { display: inline-flex; align-items: center; gap: 5px; background: linear-gradient(135deg, #FDE8EC, #FFF0E8); color: #C53060; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; padding: 4px 10px; border-radius: 100px; margin-bottom: var(--sr-space-sm); }
.sr-purchase-card__viral-tag svg { width: 12px; height: 12px; }
.sr-purchase-card__plan-name { font-family: var(--sr-font-display); font-size: 1.65rem; color: var(--sr-primary-dark); line-height: 1.2; margin-bottom: 4px; }
.sr-purchase-card__style { font-size: 0.8rem; color: var(--sr-text-light); font-weight: 400; }
.sr-purchase-card__specs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; background: var(--sr-border-light); }
.sr-spec-cell { background: var(--sr-white); padding: var(--sr-space-md) var(--sr-space-lg); text-align: center; }
.sr-spec-cell__value { font-family: var(--sr-font-display); font-size: 1.3rem; color: var(--sr-primary); line-height: 1.2; }
.sr-spec-cell__label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--sr-text-light); margin-top: 2px; font-weight: 500; }
.sr-purchase-card__body { padding: var(--sr-space-xl); }
.sr-price-option { display: flex; align-items: center; padding: var(--sr-space-md) var(--sr-space-lg); border: 2px solid var(--sr-border); border-radius: var(--sr-radius-md); margin-bottom: var(--sr-space-sm); cursor: pointer; transition: all var(--sr-transition-fast); }
.sr-price-option:hover { border-color: var(--sr-accent-light); background: var(--sr-cream); }
.sr-price-option.sr-selected { border-color: var(--sr-accent); background: linear-gradient(135deg, rgba(139,111,71,0.04), rgba(139,111,71,0.01)); }
.sr-price-option input[type="radio"] { appearance: none; -webkit-appearance: none; width: 20px; height: 20px; border: 2px solid var(--sr-border); border-radius: 50%; margin-right: var(--sr-space-md); flex-shrink: 0; transition: all var(--sr-transition-fast); }
.sr-price-option input[type="radio"]:checked { border-color: var(--sr-accent); background: var(--sr-accent); box-shadow: inset 0 0 0 3px var(--sr-white); }
.sr-price-option__info { flex: 1; }
.sr-price-option__format { font-weight: 600; font-size: 0.88rem; }
.sr-price-option__desc { font-size: 0.73rem; color: var(--sr-text-light); margin-top: 1px; }
.sr-price-option__price { font-family: var(--sr-font-display); font-size: 1.2rem; color: var(--sr-primary); }
.sr-btn-buy { display: flex; align-items: center; justify-content: center; width: 100%; padding: var(--sr-space-lg) var(--sr-space-xl); background: var(--sr-accent); color: var(--sr-white); font-size: 0.95rem; font-weight: 600; border-radius: var(--sr-radius-md); margin-top: var(--sr-space-lg); transition: all var(--sr-transition-fast); gap: var(--sr-space-sm); box-shadow: 0 4px 20px rgba(139,111,71,0.25); letter-spacing: 0.01em; text-decoration: none; }
.sr-btn-buy:hover { background: var(--sr-accent-warm); transform: translateY(-1px); box-shadow: 0 6px 28px rgba(139,111,71,0.3); color: var(--sr-white); }
.sr-btn-buy svg { width: 18px; height: 18px; }
.sr-trust-row { display: flex; justify-content: center; gap: var(--sr-space-lg); margin-top: var(--sr-space-lg); padding-top: var(--sr-space-lg); border-top: 1px solid var(--sr-border-light); }
.sr-trust-item { display: flex; align-items: center; gap: 5px; font-size: 0.72rem; color: var(--sr-text-light); font-weight: 500; }
.sr-trust-item svg { width: 15px; height: 15px; color: var(--sr-success); flex-shrink: 0; }
.sr-purchase-card__footer { padding: var(--sr-space-md) var(--sr-space-xl) var(--sr-space-xl); text-align: center; }
.sr-purchase-card__footer a { font-size: 0.8rem; color: var(--sr-accent); font-weight: 600; }
/* QUICK SPECS */
.sr-quick-specs { background: var(--sr-primary); padding: var(--sr-space-lg) 0; margin-bottom: var(--sr-space-4xl); position: relative; overflow: hidden; }
.sr-quick-specs::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(90deg, transparent, transparent 120px, rgba(255,255,255,0.02) 120px, rgba(255,255,255,0.02) 121px); }
.sr-quick-specs__grid { display: flex; justify-content: center; gap: var(--sr-space-3xl); position: relative; z-index: 1; flex-wrap: wrap; }
.sr-quick-spec { text-align: center; color: var(--sr-white); }
.sr-quick-spec__icon { width: 28px; height: 28px; margin: 0 auto var(--sr-space-xs); opacity: 0.6; }
.sr-quick-spec__value { font-family: var(--sr-font-display); font-size: 1.35rem; line-height: 1.2; }
.sr-quick-spec__label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.55; font-weight: 500; margin-top: 2px; }
/* SECTIONS */
.sr-section { padding: var(--sr-space-4xl) 0; }
.sr-section--alt { background: var(--sr-white); }
.sr-section--warm { background: var(--sr-cream-dark); }
.sr-section__header { text-align: center; margin-bottom: var(--sr-space-3xl); }
.sr-section__overline { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.14em; color: var(--sr-accent); margin-bottom: var(--sr-space-sm); }
.sr-section__title { font-family: var(--sr-font-display); font-size: 2.1rem; color: var(--sr-primary-dark); line-height: 1.2; margin-bottom: var(--sr-space-md); }
.sr-section__subtitle { font-size: 1rem; color: var(--sr-text-secondary); max-width: 620px; margin: 0 auto; line-height: 1.7; font-weight: 300; }
/* DESCRIPTION */
.sr-description-grid { display: grid; grid-template-columns: 1fr 360px; gap: var(--sr-space-3xl); align-items: start; }
.sr-description__content h2 { font-family: var(--sr-font-display); font-size: 1.9rem; color: var(--sr-primary-dark); margin-bottom: var(--sr-space-lg); line-height: 1.25; }
.sr-description__content p { color: var(--sr-text-secondary); margin-bottom: var(--sr-space-lg); font-size: 1rem; font-weight: 300; line-height: 1.8; }
/* ROOMS CARD */
.sr-rooms-card { background: var(--sr-white); border-radius: var(--sr-radius-lg); padding: var(--sr-space-xl); box-shadow: var(--sr-shadow-md); border: 1px solid var(--sr-border); }
.sr-rooms-card__title { font-family: var(--sr-font-display); font-size: 1.1rem; color: var(--sr-primary-dark); margin-bottom: var(--sr-space-lg); padding-bottom: var(--sr-space-sm); border-bottom: 2px solid var(--sr-accent); display: inline-block; }
.sr-room-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--sr-border-light); }
.sr-room-row:last-child { border-bottom: none; }
.sr-room-row__name { font-size: 0.85rem; color: var(--sr-text-secondary); font-weight: 400; }
.sr-room-row__dim { font-size: 0.85rem; font-weight: 600; color: var(--sr-text); font-variant-numeric: tabular-nums; }
/* HIGHLIGHTS */
.sr-highlights-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--sr-space-lg); margin-top: var(--sr-space-3xl); }
.sr-highlight-chip { display: flex; align-items: center; gap: var(--sr-space-md); background: var(--sr-white); padding: var(--sr-space-lg); border-radius: var(--sr-radius-md); border: 1px solid var(--sr-border); transition: all var(--sr-transition-fast); }
.sr-highlight-chip:hover { box-shadow: var(--sr-shadow-md); border-color: var(--sr-accent-light); transform: translateY(-2px); }
.sr-highlight-chip__icon { width: 40px; height: 40px; background: var(--sr-sage-light); border-radius: var(--sr-radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sr-highlight-chip__icon svg { width: 20px; height: 20px; color: var(--sr-sage); }
.sr-highlight-chip__label strong { display: block; font-size: 0.85rem; font-weight: 600; color: var(--sr-text); }
.sr-highlight-chip__label span { font-size: 0.73rem; color: var(--sr-text-light); font-weight: 300; }
/* SPECS */
.sr-specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sr-space-xl); }
.sr-specs-group { background: var(--sr-cream); border-radius: var(--sr-radius-lg); padding: var(--sr-space-xl); border: 1px solid var(--sr-border); }
.sr-specs-group__title { font-family: var(--sr-font-display); font-size: 1.05rem; color: var(--sr-primary-dark); margin-bottom: var(--sr-space-lg); padding-bottom: var(--sr-space-sm); border-bottom: 2px solid var(--sr-linen); }
.sr-spec-row { display: flex; justify-content: space-between; align-items: baseline; padding: 8px 0; border-bottom: 1px solid var(--sr-border-light); }
.sr-spec-row:last-child { border-bottom: none; }
.sr-spec-row__label { font-size: 0.83rem; color: var(--sr-text-secondary); font-weight: 400; }
.sr-spec-row__value { font-size: 0.85rem; font-weight: 600; color: var(--sr-text); text-align: right; }
/* FLOOR PLAN SHOWCASE */
.sr-floor-plan-showcase { background: var(--sr-white); border-radius: var(--sr-radius-xl); overflow: hidden; box-shadow: var(--sr-shadow-lg); border: 1px solid var(--sr-border); }
.sr-floor-plan-showcase__image { padding: var(--sr-space-3xl); background: var(--sr-cream); text-align: center; }
.sr-floor-plan-showcase__image img { max-width: 100%; margin: 0 auto; border-radius: var(--sr-radius-sm); }
.sr-floor-plan-showcase__bar { padding: var(--sr-space-lg) var(--sr-space-xl); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--sr-border-light); }
.sr-floor-plan-showcase__label { font-family: var(--sr-font-display); font-size: 1.15rem; color: var(--sr-primary-dark); }
.sr-floor-plan-showcase__meta { display: flex; gap: var(--sr-space-xl); }
.sr-floor-plan-showcase__stat { text-align: right; }
.sr-floor-plan-showcase__stat-value { font-weight: 600; font-size: 0.9rem; color: var(--sr-text); }
.sr-floor-plan-showcase__stat-label { font-size: 0.68rem; color: var(--sr-text-light); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500; }
/* FLOOR PLANS WYSIWYG */
.sr-floor-plans__wysiwyg img { max-width: 100%; border-radius: var(--sr-radius-lg); box-shadow: var(--sr-shadow-md); }
/* ESTIMATOR */
.sr-estimator { background: linear-gradient(135deg, var(--sr-primary-dark) 0%, var(--sr-primary) 50%, #4A483F 100%); border-radius: var(--sr-radius-xl); overflow: hidden; position: relative; color: var(--sr-white); }
.sr-estimator::before { content: ''; position: absolute; top: -40%; right: -15%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(139,111,71,0.15) 0%, transparent 55%); pointer-events: none; }
.sr-estimator__inner { display: grid; grid-template-columns: 1fr 1fr; position: relative; z-index: 1; }
.sr-estimator__info { padding: var(--sr-space-3xl); }
.sr-estimator__overline { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; color: var(--sr-accent-light); margin-bottom: var(--sr-space-md); }
.sr-estimator__title { font-family: var(--sr-font-display); font-size: 1.85rem; line-height: 1.25; margin-bottom: var(--sr-space-lg); }
.sr-estimator__desc { opacity: 0.7; line-height: 1.75; margin-bottom: var(--sr-space-lg); font-size: 0.92rem; font-weight: 300; }
.sr-estimator__note { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--sr-radius-md); padding: var(--sr-space-md) var(--sr-space-lg); font-size: 0.78rem; opacity: 0.6; line-height: 1.6; display: flex; align-items: flex-start; gap: var(--sr-space-sm); }
.sr-estimator__note svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
.sr-estimator__calc { padding: var(--sr-space-3xl); background: rgba(255,255,255,0.03); border-left: 1px solid rgba(255,255,255,0.06); }
.sr-calc-field { margin-bottom: var(--sr-space-xl); }
.sr-calc-field label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: var(--sr-space-sm); opacity: 0.75; }
.sr-calc-select { width: 100%; padding: var(--sr-space-md) var(--sr-space-lg); background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); border-radius: var(--sr-radius-md); color: var(--sr-white); font-family: var(--sr-font-body); font-size: 0.88rem; appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ffffff' stroke-width='1.5' fill='none' opacity='0.4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; }
.sr-calc-select option { background: var(--sr-primary-dark); color: var(--sr-white); }
.sr-range-wrapper { padding-top: var(--sr-space-xs); }
.sr-range-labels { display: flex; justify-content: space-between; margin-top: var(--sr-space-xs); font-size: 0.68rem; opacity: 0.45; }
.sr-estimator input[type="range"] { -webkit-appearance: none; appearance: none; width: 100%; height: 5px; border-radius: 3px; background: rgba(255,255,255,0.12); outline: none; }
.sr-estimator input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: var(--sr-accent); cursor: pointer; border: 3px solid var(--sr-white); box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
.sr-range-current { text-align: center; font-family: var(--sr-font-display); font-size: 1rem; color: var(--sr-accent-light); margin-top: var(--sr-space-xs); }
.sr-estimate-result { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--sr-radius-lg); padding: var(--sr-space-xl); text-align: center; margin-top: var(--sr-space-lg); }
.sr-estimate-result__label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.55; font-weight: 600; margin-bottom: var(--sr-space-sm); }
.sr-estimate-result__value { font-family: var(--sr-font-display); font-size: 2rem; color: var(--sr-accent-light); line-height: 1.1; margin-bottom: 4px; }
.sr-estimate-result__range { font-size: 0.8rem; opacity: 0.5; }
.sr-estimate-result__breakdown { display: grid; grid-template-columns: 1fr 1fr; gap: var(--sr-space-md); margin-top: var(--sr-space-lg); padding-top: var(--sr-space-lg); border-top: 1px solid rgba(255,255,255,0.06); }
.sr-breakdown-item__label { font-size: 0.68rem; text-transform: uppercase; opacity: 0.4; }
.sr-breakdown-item__value { font-size: 0.95rem; font-weight: 600; margin-top: 2px; }
/* INCLUDED */
.sr-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--sr-space-xl); }
.sr-included-card { background: var(--sr-white); border-radius: var(--sr-radius-lg); padding: var(--sr-space-xl) var(--sr-space-lg); text-align: center; border: 1px solid var(--sr-border); transition: all var(--sr-transition-base); }
.sr-included-card:hover { transform: translateY(-3px); box-shadow: var(--sr-shadow-lg); border-color: var(--sr-accent-light); }
.sr-included-card__icon { width: 52px; height: 52px; background: var(--sr-sage-light); border-radius: var(--sr-radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--sr-space-lg); }
.sr-included-card__icon svg { width: 26px; height: 26px; color: var(--sr-sage); }
.sr-included-card__title { font-weight: 600; font-size: 0.92rem; margin-bottom: var(--sr-space-xs); color: var(--sr-primary-dark); }
.sr-included-card__desc { font-size: 0.8rem; color: var(--sr-text-light); line-height: 1.6; font-weight: 300; }
/* FAQ */
.sr-faq-list { max-width: 800px; margin: 0 auto; }
.sr-faq-item { background: var(--sr-white); border-radius: var(--sr-radius-md); margin-bottom: var(--sr-space-sm); border: 1px solid var(--sr-border); overflow: hidden; transition: all var(--sr-transition-fast); }
.sr-faq-item.sr-open { box-shadow: var(--sr-shadow-md); border-color: var(--sr-accent-light); }
.sr-faq-question { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: var(--sr-space-lg) var(--sr-space-xl); font-size: 0.92rem; font-weight: 500; text-align: left; color: var(--sr-text); transition: color var(--sr-transition-fast); gap: var(--sr-space-md); }
.sr-faq-question:hover { color: var(--sr-accent); }
.sr-faq-question__icon { width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: var(--sr-cream-dark); display: flex; align-items: center; justify-content: center; transition: all var(--sr-transition-fast); }
.sr-faq-item.sr-open .sr-faq-question__icon { background: var(--sr-accent); transform: rotate(45deg); }
.sr-faq-question__icon svg { width: 13px; height: 13px; color: var(--sr-text-secondary); }
.sr-faq-item.sr-open .sr-faq-question__icon svg { color: var(--sr-white); }
.sr-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s var(--sr-ease-out); }
.sr-faq-answer__inner { padding: 0 var(--sr-space-xl) var(--sr-space-xl); font-size: 0.88rem; color: var(--sr-text-secondary); line-height: 1.8; font-weight: 300; }
/* CTA */
.sr-cta-section { background: var(--sr-primary-dark); padding: var(--sr-space-4xl) 0; text-align: center; color: var(--sr-white); position: relative; overflow: hidden; }
.sr-cta-section::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(255,255,255,0.015) 40px, rgba(255,255,255,0.015) 41px); }
.sr-cta-section .sr-container { position: relative; z-index: 1; }
.sr-cta__title { font-family: var(--sr-font-display); font-size: 2.1rem; margin-bottom: var(--sr-space-md); }
.sr-cta__text { font-size: 1rem; opacity: 0.7; max-width: 540px; margin: 0 auto var(--sr-space-xl); line-height: 1.75; font-weight: 300; }
.sr-cta__buttons { display: flex; justify-content: center; gap: var(--sr-space-lg); flex-wrap: wrap; }
.sr-btn-cta { display: inline-flex; align-items: center; gap: var(--sr-space-sm); padding: var(--sr-space-md) var(--sr-space-2xl); border-radius: var(--sr-radius-md); font-weight: 600; font-size: 0.92rem; transition: all var(--sr-transition-fast); }
.sr-btn-cta--primary { background: var(--sr-accent); color: var(--sr-white); box-shadow: 0 4px 20px rgba(139,111,71,0.3); }
.sr-btn-cta--primary:hover { background: var(--sr-accent-warm); transform: translateY(-2px); }
.sr-btn-cta--outline { border: 2px solid rgba(255,255,255,0.25); color: var(--sr-white); }
.sr-btn-cta--outline:hover { border-color: var(--sr-white); background: rgba(255,255,255,0.06); }
/* MOBILE BAR */
.sr-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--sr-white); border-top: 1px solid var(--sr-border); padding: var(--sr-space-md) var(--sr-space-lg); z-index: 100; box-shadow: 0 -4px 24px rgba(0,0,0,0.08); }
.sr-mobile-buy-bar__inner { display: flex; align-items: center; justify-content: space-between; max-width: var(--sr-max-width); margin: 0 auto; }
.sr-mobile-buy-bar__name { font-weight: 600; font-size: 0.85rem; color: var(--sr-primary-dark); }
.sr-mobile-buy-bar__price { font-family: var(--sr-font-display); color: var(--sr-accent); font-size: 1.05rem; }
.sr-mobile-buy-bar .sr-btn-buy { width: auto; padding: var(--sr-space-sm) var(--sr-space-xl); margin-top: 0; font-size: 0.85rem; }
/* RESPONSIVE */
@media (max-width: 1024px) {
  .sr-plan-hero__grid { grid-template-columns: 1fr; }
  .sr-purchase-card { position: relative; top: 0; }
  .sr-description-grid { grid-template-columns: 1fr; }
  .sr-specs-grid { grid-template-columns: 1fr 1fr; }
  .sr-estimator__inner { grid-template-columns: 1fr; }
  .sr-included-grid { grid-template-columns: repeat(2, 1fr); }
  .sr-highlights-strip { grid-template-columns: repeat(2, 1fr); }
  .sr-mobile-buy-bar { display: block; }
  .sr-wrap { padding-bottom: 80px; }
}
@media (max-width: 768px) {
  .sr-gallery__thumbs { grid-template-columns: repeat(4, 1fr); }
  .sr-section__title { font-size: 1.65rem; }
  .sr-specs-grid { grid-template-columns: 1fr; }
  .sr-included-grid { grid-template-columns: 1fr; }
  .sr-highlights-strip { grid-template-columns: 1fr; }
  .sr-estimator__info, .sr-estimator__calc { padding: var(--sr-space-xl); }
}
@media (max-width: 480px) {
  .sr-container { padding: 0 var(--sr-space-lg); }
  .sr-gallery__thumbs { grid-template-columns: repeat(3, 1fr); }
  .sr-quick-specs__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sr-space-lg); }
  .sr-cta__buttons { flex-direction: column; align-items: center; }
}
</style>
<?php
}

function mhp_sunset_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    $plan_name        = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft             = $acf ? get_field( 'total_living_area',   $pid ) : '3,196';
    $main_floor       = $acf ? get_field( 'main_floor',          $pid ) : '3,196';
    $upper_floor      = $acf ? get_field( 'upper_floor',         $pid ) : '';
    $lower_floor      = $acf ? get_field( 'lower_floor',         $pid ) : '';
    $bedrooms         = $acf ? get_field( 'bedrooms',            $pid ) : '4';
    $bathrooms        = $acf ? get_field( 'bathrooms',           $pid ) : '3½';
    $stories          = $acf ? get_field( 'stories',             $pid ) : '1';
    $width            = $acf ? get_field( 'width',               $pid ) : "86'8\"";
    $depth            = $acf ? get_field( 'depth',               $pid ) : "63'10\"";
    $garage           = $acf ? get_field( 'garage',              $pid ) : '36\' Carport';
    $style            = $acf ? get_field( 'style',               $pid ) : 'Modern Farmhouse · One Story · Open Concept';
    $outdoor          = $acf ? get_field( 'outdoor',             $pid ) : 'Front Porch, Vaulted Rear Porch';
    $roof             = $acf ? get_field( 'roof',                $pid ) : '9/12 & 12/12';
    $ceiling          = $acf ? get_field( 'ceiling',             $pid ) : "10'";
    $exterior         = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms = $acf ? get_field( 'additional_rooms',    $pid ) : 'Laundry, Pantry, Office, Mud Room';
    $other_features   = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style        = $acf ? get_field( 'lot_style',           $pid ) : 'Flat';
    $plan_description = $acf ? get_field( 'plan_description',    $pid ) : '';
    $floor_plans_wysiwyg = $acf ? get_field( 'floor_plans',      $pid ) : '';
    $paypal           = $acf ? get_field( 'paypal',              $pid ) : '';
    $price            = $acf ? get_field( 'price',               $pid ) : '1500';
    $related_plans    = $acf ? get_field( 'related_plans',       $pid ) : array();
    $faqs             = $acf ? get_field( 'faqs',                $pid ) : array();

    $plan_name = $plan_name ?: get_the_title();
    $price_num = is_numeric( $price ) ? (float) $price : 1500;
    $price_fmt = '$' . number_format( $price_num, 0, '.', ',' );
    $cad_price = '$' . number_format( $price_num * 1.30, 0, '.', ',' );

    $paypal_url = '';
    if ( $paypal && preg_match( "/name=[\"']hosted_button_id[\"']\s+value=[\"']([^\"']+)[\"']/", $paypal, $m ) ) {
        $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
    }
    $buy_href = $paypal_url ?: ( get_permalink() . '#contact' );

    $hero_url = get_the_post_thumbnail_url( $pid, 'large' );
    if ( ! $hero_url ) $hero_url = 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/viral-home-plan-sunset-ridge-scaled.jpg';

    $gallery_images = array();
    $post_content = get_post_field( 'post_content', $pid );
    if ( has_shortcode( $post_content, 'gallery' ) ) {
        preg_match( '/\[gallery[^\]]+ids=["\']?([\d,]+)["\']?/i', $post_content, $gm );
        if ( ! empty( $gm[1] ) ) {
            foreach ( explode( ',', $gm[1] ) as $img_id ) {
                $img_id = (int) trim( $img_id );
                $full  = wp_get_attachment_image_src( $img_id, 'full' );
                $thumb = wp_get_attachment_image_src( $img_id, 'thumbnail' );
                $alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                if ( $full ) $gallery_images[] = array( 'full' => $full[0], 'thumb' => $thumb ? $thumb[0] : $full[0], 'alt' => $alt ?: $plan_name );
            }
        }
    }
    if ( empty( $gallery_images ) ) {
        $gallery_images = array(
            array( 'full' => $hero_url, 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/viral-home-plan-sunset-ridge-300x276.jpg', 'alt' => $plan_name . ' front exterior' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/tiktok-home-plan-sunset-ridge-scaled.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/tiktok-home-plan-sunset-ridge-300x289.jpg', 'alt' => $plan_name . ' TikTok angle' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/craftsman-farmhouse-style-viral-house-plan-scaled.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/craftsman-farmhouse-style-viral-house-plan-300x295.jpg', 'alt' => 'Craftsman farmhouse style detail' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/viral-farmhouse-house-plan-sunset-ridge-front.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/viral-farmhouse-house-plan-sunset-ridge-front-300x194.jpg', 'alt' => $plan_name . ' front elevation' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/viral-farmhouse-kitchen-with-brick-backsplash-and-wood-floor-1.png', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/12/viral-farmhouse-kitchen-with-brick-backsplash-and-wood-floor-1-300x435.png', 'alt' => 'Farmhouse kitchen interior' ),
        );
    }

    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft' ), '', $v ); };
    $sqft_display    = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display = $clean($main_floor) > 0 ? number_format( $clean($main_floor) ) : $main_floor;
    $sqft_int = $clean($sqft) > 100 ? $clean($sqft) : 3196;
?>
<div class="sr-wrap">

<!-- VIRAL BANNER -->
<div class="sr-viral-banner">
  As seen on TikTok &#8212; <strong>Millions of views</strong> and counting. This is the original plan, designed by our family.
</div>

<!-- BREADCRUMBS -->
<nav class="sr-breadcrumbs" aria-label="Breadcrumb">
  <div class="sr-container">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/home-plans/one-story-house-plans/')); ?>">One Story House Plans</a><span>&#8250;</span>
    <strong><?php echo esc_html($plan_name); ?></strong>
  </div>
</nav>

<!-- HERO -->
<section class="sr-plan-hero">
  <div class="sr-container">
    <div class="sr-plan-hero__grid">
      <div class="sr-gallery sr-animate-in">
        <div class="sr-gallery__main" id="srGalleryMain">
          <span class="sr-gallery__badge">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            Viral Plan
          </span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> modern farmhouse plan &#8212; one story home with wide front porch, board and batten siding, and metal roof accents" id="srMainImage" fetchpriority="high">
          <span class="sr-gallery__count">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            14 Photos
          </span>
        </div>
        <div class="sr-gallery__thumbs">
          <?php foreach ( $gallery_images as $idx => $img ) : ?>
          <div class="sr-gallery__thumb<?php echo $idx === 0 ? ' sr-active' : ''; ?>" onclick="srSwapImage(this)" data-full="<?php echo esc_url($img['full']); ?>">
            <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <aside class="sr-purchase-card" aria-label="Plan purchase options">
        <div class="sr-purchase-card__header">
          <div class="sr-purchase-card__viral-tag">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            Viral on TikTok
          </div>
          <h1 class="sr-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
          <p class="sr-purchase-card__style"><?php echo esc_html($style); ?></p>
        </div>
        <div class="sr-purchase-card__specs">
          <div class="sr-spec-cell"><div class="sr-spec-cell__value"><?php echo esc_html($sqft_display); ?></div><div class="sr-spec-cell__label">Sq Ft</div></div>
          <div class="sr-spec-cell"><div class="sr-spec-cell__value"><?php echo esc_html($bedrooms); ?></div><div class="sr-spec-cell__label">Bedrooms</div></div>
          <div class="sr-spec-cell"><div class="sr-spec-cell__value"><?php echo esc_html($bathrooms); ?></div><div class="sr-spec-cell__label">Bathrooms</div></div>
          <div class="sr-spec-cell"><div class="sr-spec-cell__value"><?php echo esc_html($stories); ?></div><div class="sr-spec-cell__label">Story</div></div>
        </div>
        <div class="sr-purchase-card__body">
          <label class="sr-price-option sr-selected" id="srOptionPdf">
            <input type="radio" name="sr_plan_format" value="pdf" checked onchange="srSelectOption('pdf')">
            <div class="sr-price-option__info"><div class="sr-price-option__format">PDF Plan Set</div><div class="sr-price-option__desc">Print-ready digital plans</div></div>
            <div class="sr-price-option__price"><?php echo esc_html($price_fmt); ?></div>
          </label>
          <label class="sr-price-option" id="srOptionCad">
            <input type="radio" name="sr_plan_format" value="cad" onchange="srSelectOption('cad')">
            <div class="sr-price-option__info"><div class="sr-price-option__format">CAD File</div><div class="sr-price-option__desc">Editable for your builder</div></div>
            <div class="sr-price-option__price"><?php echo esc_html($cad_price); ?></div>
          </label>
          <a href="<?php echo esc_url($buy_href); ?>" class="sr-btn-buy" id="srBuyButton">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="m16 10-4 4-4-4"/></svg>
            Purchase This Plan
          </a>
          <div class="sr-trust-row">
            <span class="sr-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Secure Checkout</span>
            <span class="sr-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Instant Download</span>
          </div>
        </div>
        <div class="sr-purchase-card__footer">
          <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>">Need changes? Request a modification &#8594;</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- QUICK SPECS -->
<div class="sr-quick-specs">
  <div class="sr-container">
    <div class="sr-quick-specs__grid">
      <div class="sr-quick-spec"><svg class="sr-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg><div class="sr-quick-spec__value"><?php echo esc_html($sqft_display); ?> sq ft</div><div class="sr-quick-spec__label">Heated Area</div></div>
      <div class="sr-quick-spec"><svg class="sr-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg><div class="sr-quick-spec__value"><?php echo esc_html($width); ?> &times; <?php echo esc_html($depth); ?></div><div class="sr-quick-spec__label">Footprint</div></div>
      <div class="sr-quick-spec"><svg class="sr-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg><div class="sr-quick-spec__value"><?php echo esc_html($ceiling); ?> Ceilings</div><div class="sr-quick-spec__label">Ceiling Height</div></div>
      <div class="sr-quick-spec"><svg class="sr-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg><div class="sr-quick-spec__value">36&#8217; Carport</div><div class="sr-quick-spec__label">Covered Parking</div></div>
      <div class="sr-quick-spec"><svg class="sr-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><div class="sr-quick-spec__value">From <?php echo esc_html($price_fmt); ?></div><div class="sr-quick-spec__label">Plan Price</div></div>
    </div>
  </div>
</div>

<!-- DESCRIPTION -->
<section class="sr-section" id="description">
  <div class="sr-container">
    <div class="sr-description-grid">
      <div class="sr-description__content">
        <?php if ( $plan_description ) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <h2>Modern Farmhouse Living, All on One Level</h2>
          <p><?php echo esc_html($plan_name); ?> is one of our newest designs &#8212; and thanks to a TikTok build series that followed a family constructing this home in Alabama, it&#8217;s become one of the most-requested plans we&#8217;ve ever drawn. The appeal is straightforward: a wide front porch, an open layout that lives bigger than its footprint, and the kind of modern farmhouse character that looks just as good in person as it does on screen.</p>
          <p>The front porch stretches across the full width of the home, setting the farmhouse tone before you walk through the door. Inside, a bright foyer leads into the heart of the plan &#8212; an expansive family room (18&#8242; &times; 22&#8242;6&#8221;) that opens directly to the kitchen (14&#8242;8&#8221; &times; 22&#8242;6&#8221;). This is a space designed for how families actually live: cooking while keeping an eye on kids, hosting without walls between you and your guests. Large windows fill both rooms with natural light, and French doors open to the vaulted rear porch for seamless indoor-outdoor living.</p>
          <p>The primary bedroom (13&#8242; &times; 16&#8242;) is set apart on the left side of the home for privacy, with its own bathroom and walk-in closet. Three additional bedrooms are positioned on the opposite side of the plan &#8212; a true split-bedroom layout that gives everyone their own space.</p>
          <p>Practical details that builders and homeowners both appreciate: a dedicated mudroom off the 36-foot carport, an oversized pantry adjacent to the kitchen, and a laundry room placed where it&#8217;s actually convenient. Every room connects logically, with 10-foot ceilings throughout that give the single-story layout a sense of volume without adding square footage.</p>
          <p>This plan is designed for a flat lot with a slab foundation. The roof combines 9/12 and 12/12 pitches for that layered farmhouse profile. Exterior framing supports either 2x4 or 2x6 construction to meet your local energy requirements.</p>
        <?php endif; ?>
      </div>

      <!-- Room Dimensions -->
      <div class="sr-rooms-card">
        <h3 class="sr-rooms-card__title">Key Room Sizes</h3>
        <div class="sr-room-row"><span class="sr-room-row__name">Family Room</span><span class="sr-room-row__dim">18&#8242; &times; 22&#8242;6&#8221;</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Kitchen</span><span class="sr-room-row__dim">14&#8242;8&#8221; &times; 22&#8242;6&#8221;</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Primary Bedroom</span><span class="sr-room-row__dim">13&#8242; &times; 16&#8242;</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Rear Porch (Vaulted)</span><span class="sr-room-row__dim">19&#8242;8&#8221; &times; 16&#8242;</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Front Porch</span><span class="sr-room-row__dim">Full Width</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Carport</span><span class="sr-room-row__dim">36&#8242; Wide</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Foyer</span><span class="sr-room-row__dim">Included</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Office</span><span class="sr-room-row__dim">Included</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Dining Room</span><span class="sr-room-row__dim">Included</span></div>
        <div class="sr-room-row"><span class="sr-room-row__name">Mud Room</span><span class="sr-room-row__dim">Included</span></div>
      </div>
    </div>

    <!-- Highlights Strip -->
    <div class="sr-highlights-strip">
      <div class="sr-highlight-chip">
        <div class="sr-highlight-chip__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
        <div class="sr-highlight-chip__label"><strong>Single Story</strong><span>All living on one level</span></div>
      </div>
      <div class="sr-highlight-chip">
        <div class="sr-highlight-chip__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>
        <div class="sr-highlight-chip__label"><strong>Open Concept</strong><span>Kitchen + family room connected</span></div>
      </div>
      <div class="sr-highlight-chip">
        <div class="sr-highlight-chip__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg></div>
        <div class="sr-highlight-chip__label"><strong>Split Bedrooms</strong><span>Primary isolated for privacy</span></div>
      </div>
      <div class="sr-highlight-chip">
        <div class="sr-highlight-chip__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/></svg></div>
        <div class="sr-highlight-chip__label"><strong>Vaulted Rear Porch</strong><span>Indoor-outdoor living</span></div>
      </div>
    </div>
  </div>
</section>

<!-- SPECS -->
<section class="sr-section sr-section--alt" id="specs">
  <div class="sr-container">
    <div class="sr-section__header">
      <p class="sr-section__overline">Specifications</p>
      <h2 class="sr-section__title">Full Plan Details</h2>
      <p class="sr-section__subtitle">Complete specifications for the <?php echo esc_html($plan_name); ?> plan.</p>
    </div>
    <div class="sr-specs-grid">
      <div class="sr-specs-group">
        <h3 class="sr-specs-group__title">Living Area</h3>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Main Floor</span><span class="sr-spec-row__value"><?php echo esc_html($main_fl_display); ?> sq ft</span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Upper Floor</span><span class="sr-spec-row__value">None</span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Lower Floor</span><span class="sr-spec-row__value">None</span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Total Heated</span><span class="sr-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Width</span><span class="sr-spec-row__value"><?php echo esc_html($width); ?></span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Depth</span><span class="sr-spec-row__value"><?php echo esc_html($depth); ?></span></div>
      </div>
      <div class="sr-specs-group">
        <h3 class="sr-specs-group__title">House Features</h3>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Bedrooms</span><span class="sr-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Bathrooms</span><span class="sr-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Stories</span><span class="sr-spec-row__value"><?php echo esc_html($stories); ?></span></div>
        <?php if ( $additional_rooms ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Additional Rooms</span><span class="sr-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div><?php endif; ?>
        <?php if ( $garage ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Garage / Carport</span><span class="sr-spec-row__value"><?php echo esc_html($garage); ?></span></div><?php endif; ?>
        <?php if ( $outdoor ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Outdoor Spaces</span><span class="sr-spec-row__value"><?php echo esc_html($outdoor); ?></span></div><?php endif; ?>
        <?php if ( $other_features ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Other Features</span><span class="sr-spec-row__value"><?php echo esc_html($other_features); ?></span></div><?php endif; ?>
      </div>
      <div class="sr-specs-group">
        <h3 class="sr-specs-group__title">Construction</h3>
        <?php if ( $roof ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Roof Pitch</span><span class="sr-spec-row__value"><?php echo esc_html($roof); ?></span></div><?php endif; ?>
        <?php if ( $exterior ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Exterior Framing</span><span class="sr-spec-row__value"><?php echo esc_html($exterior); ?></span></div><?php endif; ?>
        <?php if ( $ceiling ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Ceiling Height</span><span class="sr-spec-row__value"><?php echo esc_html($ceiling); ?></span></div><?php endif; ?>
        <div class="sr-spec-row"><span class="sr-spec-row__label">Foundation</span><span class="sr-spec-row__value">Slab</span></div>
        <?php if ( $lot_style ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Lot Style</span><span class="sr-spec-row__value"><?php echo esc_html($lot_style); ?></span></div><?php endif; ?>
        <?php if ( $style ) : ?><div class="sr-spec-row"><span class="sr-spec-row__label">Home Style</span><span class="sr-spec-row__value"><?php echo esc_html($style); ?></span></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- FLOOR PLAN -->
<section class="sr-section" id="floorplans">
  <div class="sr-container">
    <div class="sr-section__header">
      <p class="sr-section__overline">Floor Plan</p>
      <h2 class="sr-section__title">Explore the Layout</h2>
      <p class="sr-section__subtitle">A single-story plan with clear circulation, split bedrooms, and an open-concept core.</p>
    </div>
    <?php if ( $floor_plans_wysiwyg ) : ?>
      <div class="sr-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans_wysiwyg); ?></div>
    <?php else : ?>
    <div class="sr-floor-plan-showcase">
      <div class="sr-floor-plan-showcase__image">
        <img src="https://www.maxhouseplans.com/wp-content/uploads/2025/12/3-bedroom-on-main-level-floor-plan-scaled.jpg" alt="<?php echo esc_attr($plan_name); ?> full floor plan &#8212; <?php echo esc_attr($sqft_display); ?> sq ft single story layout with split bedrooms, open concept family room and kitchen, front porch, vaulted rear porch, and 36-foot carport" loading="lazy">
      </div>
      <div class="sr-floor-plan-showcase__bar">
        <span class="sr-floor-plan-showcase__label">Main Floor &#8212; Single Story</span>
        <div class="sr-floor-plan-showcase__meta">
          <div class="sr-floor-plan-showcase__stat">
            <div class="sr-floor-plan-showcase__stat-value"><?php echo esc_html($sqft_display); ?> sq ft</div>
            <div class="sr-floor-plan-showcase__stat-label">Heated Area</div>
          </div>
          <div class="sr-floor-plan-showcase__stat">
            <div class="sr-floor-plan-showcase__stat-value"><?php echo esc_html($width); ?> &times; <?php echo esc_html($depth); ?></div>
            <div class="sr-floor-plan-showcase__stat-label">Footprint</div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- COST ESTIMATOR -->
<section class="sr-section sr-section--alt" id="cost-estimator">
  <div class="sr-container">
    <div class="sr-section__header">
      <p class="sr-section__overline">Budget Planning</p>
      <h2 class="sr-section__title">Cost to Build Estimator</h2>
      <p class="sr-section__subtitle">Get a ballpark construction estimate for <?php echo esc_html($plan_name); ?> based on current 2026 national data.</p>
    </div>
    <div class="sr-estimator">
      <div class="sr-estimator__inner">
        <div class="sr-estimator__info">
          <p class="sr-estimator__overline">Plan Your Budget</p>
          <h3 class="sr-estimator__title">What Will This Home Cost to Build?</h3>
          <p class="sr-estimator__desc">This estimator uses 2026 national averages for residential construction, adjusted by region and finish quality. It covers the structure &#8212; framing, mechanicals, roofing, and finishes &#8212; but not land, permits, site work, or landscaping.</p>
          <p class="sr-estimator__desc">One thing to keep in mind: single-story homes typically cost slightly more per square foot than two-story homes. The larger roof and foundation footprint means more materials and labor for the same heated square footage. That&#8217;s reflected in the numbers here.</p>
          <div class="sr-estimator__note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Estimates based on <?php echo esc_html($sqft_display); ?> sq ft using 2026 industry cost-per-square-foot data. This is a planning tool, not a quote. Always get a professional bid for your specific project.</span>
          </div>
        </div>
        <div class="sr-estimator__calc">
          <div class="sr-calc-field">
            <label for="srRegionSelect">Your Region</label>
            <select id="srRegionSelect" class="sr-calc-select" onchange="srUpdateEstimate()">
              <option value="southeast">Southeast (GA, NC, SC, TN, AL)</option>
              <option value="south_central">South Central (TX, OK, AR, LA)</option>
              <option value="midwest">Midwest (OH, IN, MI, IL, MO)</option>
              <option value="mountain_west">Mountain West (CO, UT, MT, ID)</option>
              <option value="northeast">Northeast (NY, NJ, PA, CT, MA)</option>
              <option value="pacific_nw">Pacific Northwest (WA, OR)</option>
              <option value="west_coast">West Coast (CA)</option>
            </select>
          </div>
          <div class="sr-calc-field">
            <label>Finish Level</label>
            <div class="sr-range-wrapper">
              <input type="range" id="srFinishLevel" min="1" max="4" step="1" value="2" onchange="srUpdateEstimate()" oninput="srUpdateEstimate()">
              <div class="sr-range-labels"><span>Standard</span><span>Mid-Range</span><span>Custom</span><span>Premium</span></div>
              <div class="sr-range-current" id="srFinishLabel">Mid-Range Build</div>
            </div>
          </div>
          <div class="sr-estimate-result">
            <div class="sr-estimate-result__label">Estimated Construction Cost</div>
            <div class="sr-estimate-result__value" id="srEstimateValue">Calculating&#8230;</div>
            <div class="sr-estimate-result__range" id="srEstimatePerSqft"></div>
            <div class="sr-estimate-result__breakdown">
              <div><div class="sr-breakdown-item__label">Materials (est. 45%)</div><div class="sr-breakdown-item__value" id="srMaterialsCost">&#8211;</div></div>
              <div><div class="sr-breakdown-item__label">Labor (est. 35%)</div><div class="sr-breakdown-item__value" id="srLaborCost">&#8211;</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="sr-section" id="whats-included">
  <div class="sr-container">
    <div class="sr-section__header">
      <p class="sr-section__overline">Your Plan Set</p>
      <h2 class="sr-section__title">What&#8217;s Included</h2>
      <p class="sr-section__subtitle">Every plan set is complete and build-ready. Here&#8217;s what you&#8217;ll receive.</p>
    </div>
    <div class="sr-included-grid">
      <div class="sr-included-card"><div class="sr-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg></div><h3 class="sr-included-card__title">Elevations</h3><p class="sr-included-card__desc">Front, side, and rear elevations at &frac14;&Prime; scale with material notes and dimensions.</p></div>
      <div class="sr-included-card"><div class="sr-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><h3 class="sr-included-card__title">Floor Plan</h3><p class="sr-included-card__desc">Fully dimensioned and detailed plan for the single-story layout.</p></div>
      <div class="sr-included-card"><div class="sr-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20"/><path d="M5 20V8l7-5 7 5v12"/></svg></div><h3 class="sr-included-card__title">Foundation Plan</h3><p class="sr-included-card__desc">Slab foundation layout with footing dimensions and details.</p></div>
      <div class="sr-included-card"><div class="sr-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7"/><path d="M3 9v0l9 4 9-4"/></svg></div><h3 class="sr-included-card__title">Roof Plan</h3><p class="sr-included-card__desc">Complete roof plan with 9/12 and 12/12 pitches, ridges, and drainage.</p></div>
    </div>
  </div>
</section>

<!-- RELATED PLANS -->
<?php if ( ! empty($related_plans) && is_array($related_plans) ) : ?>
<section class="sr-section sr-section--alt" id="related-plans">
  <div class="sr-container">
    <div class="sr-section__header">
      <p class="sr-section__overline">More to Explore</p>
      <h2 class="sr-section__title">Related House Plans</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--sr-space-xl);">
      <?php foreach ( $related_plans as $rp ) :
            if ( ! is_object($rp) ) continue;
            $r_id    = $rp->ID;
            $r_name  = $acf ? get_field('plan_name',$r_id) : get_the_title($r_id);
            $r_sqft  = $acf ? get_field('total_living_area',$r_id) : '';
            $r_beds  = $acf ? get_field('bedrooms',$r_id) : '';
            $r_baths = $acf ? get_field('bathrooms',$r_id) : '';
            $r_img   = get_the_post_thumbnail_url($r_id,'medium');
            $r_link  = get_permalink($r_id);
            $r_specs = array_filter(array( $r_sqft ? number_format((int)str_replace(',','',$r_sqft)).' sq ft' : '', $r_beds ? $r_beds.' bed' : '', $r_baths ? $r_baths.' bath' : '' )); ?>
      <div style="background:var(--sr-white);border-radius:var(--sr-radius-lg);overflow:hidden;box-shadow:var(--sr-shadow-md);border:1px solid var(--sr-border);">
        <?php if ($r_img) : ?><div style="aspect-ratio:3/2;overflow:hidden;"><img src="<?php echo esc_url($r_img); ?>" alt="<?php echo esc_attr($r_name ?: get_the_title($r_id)); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div><?php endif; ?>
        <div style="padding:var(--sr-space-lg);">
          <div style="font-family:var(--sr-font-display);font-size:1.1rem;color:var(--sr-primary-dark);margin-bottom:4px;"><?php echo esc_html($r_name ?: get_the_title($r_id)); ?></div>
          <div style="font-size:0.82rem;color:var(--sr-text-light);"><?php echo esc_html(implode(' · ', $r_specs)); ?></div>
          <a href="<?php echo esc_url($r_link); ?>" style="display:inline-block;margin-top:var(--sr-space-md);font-size:0.875rem;font-weight:600;color:var(--sr-accent);">View Plan &#8594;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="sr-section<?php echo empty($related_plans) ? ' sr-section--alt' : ''; ?>" id="faq">
  <div class="sr-container">
    <div class="sr-section__header">
      <p class="sr-section__overline">Common Questions</p>
      <h2 class="sr-section__title">Frequently Asked Questions</h2>
      <p class="sr-section__subtitle">Answers to the most common questions about <?php echo esc_html($plan_name); ?>, modifications, and the build process.</p>
    </div>
    <div class="sr-faq-list">
      <?php if ( ! empty($faqs) && is_array($faqs) ) :
            foreach ( $faqs as $faq ) :
              $q = isset($faq['question']) ? $faq['question'] : '';
              $a = isset($faq['answer'])   ? $faq['answer']   : '';
              if ( ! $q ) continue; ?>
      <div class="sr-faq-item">
        <button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false">
          <span><?php echo esc_html($q); ?></span>
          <span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="sr-faq-answer"><div class="sr-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
      </div>
      <?php endforeach; else : ?>
      <div class="sr-faq-item"><button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false"><span>What is included in the <?php echo esc_html($plan_name); ?> plan set?</span><span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="sr-faq-answer"><div class="sr-faq-answer__inner">Each set includes a dimensioned floor plan, front/side/rear elevations with material notes, a foundation plan, and a roof plan. Plans are available as PDF (<?php echo esc_html($price_fmt); ?>) or editable CAD files (<?php echo esc_html($cad_price); ?>). Electrical plans aren&#8217;t included in the standard set &#8212; we offer those as a $350 add-on.</div></div></div>
      <div class="sr-faq-item"><button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false"><span>Is this the house plan from TikTok?</span><span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="sr-faq-answer"><div class="sr-faq-answer__inner">Yes. <?php echo esc_html($plan_name); ?> gained widespread attention on TikTok when a family documented their build from foundation to move-in. The open farmhouse layout, wide front porch, and modern interior finishes resonated with millions of viewers. This is the original plan, available directly from us &#8212; the designers.</div></div></div>
      <div class="sr-faq-item"><button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false"><span>Can this plan be modified?</span><span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="sr-faq-answer"><div class="sr-faq-answer__inner">Yes. We handle all modifications in-house. Common requests include swapping the carport for an attached garage, adjusting bedroom sizes, or modifying the kitchen layout. Contact us directly and we&#8217;ll give you a clear scope and price before starting any work.</div></div></div>
      <div class="sr-faq-item"><button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false"><span>How much does it cost to build <?php echo esc_html($plan_name); ?>?</span><span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="sr-faq-answer"><div class="sr-faq-answer__inner">Use the cost estimator above for a ballpark based on 2026 national averages. For a 3,196 sq ft single-story home, expect roughly $479,400&#8211;$799,000 for standard to mid-range construction, and higher for premium finishes. Single-story homes tend to cost slightly more per square foot than two-story homes due to the larger foundation and roof area. Always get a detailed bid from a local builder.</div></div></div>
      <div class="sr-faq-item"><button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false"><span>What lot size do I need for this plan?</span><span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="sr-faq-answer"><div class="sr-faq-answer__inner">The plan footprint is 86&#8217;8&#8221; wide by 63&#8217;10&#8221; deep. With typical setback requirements (which vary by jurisdiction), you&#8217;ll generally need at least a 120-foot-wide lot. The plan is designed for a flat lot with a slab foundation. Check with your local building department for specific setback requirements in your area.</div></div></div>
      <div class="sr-faq-item"><button class="sr-faq-question" onclick="srToggleFaq(this)" aria-expanded="false"><span>What makes this a modern farmhouse plan?</span><span class="sr-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="sr-faq-answer"><div class="sr-faq-answer__inner">Several elements define the modern farmhouse style here: the wide wraparound front porch, board and batten exterior details, metal roof accents, 10-foot ceilings, and the fully open-concept layout connecting the family room and kitchen. The plan blends that aesthetic with practical features like a split-bedroom layout, oversized pantry, mudroom off the carport, and a vaulted rear porch.</div></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="sr-cta-section">
  <div class="sr-container">
    <h2 class="sr-cta__title">Ready to Build Your Farmhouse?</h2>
    <p class="sr-cta__text">Whether you&#8217;re ready to purchase <?php echo esc_html($plan_name); ?> or have questions about making it yours, our family is here to help.</p>
    <div class="sr-cta__buttons">
      <a href="<?php echo esc_url($buy_href); ?>" class="sr-btn-cta sr-btn-cta--primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
        Purchase This Plan
      </a>
      <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="sr-btn-cta sr-btn-cta--outline">Request a Modification</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="sr-btn-cta sr-btn-cta--outline">Talk With Our Family</a>
    </div>
  </div>
</section>

<!-- MOBILE BAR -->
<div class="sr-mobile-buy-bar">
  <div class="sr-mobile-buy-bar__inner">
    <div>
      <div class="sr-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
      <div class="sr-mobile-buy-bar__price">From <?php echo esc_html($price_fmt); ?></div>
    </div>
    <a href="<?php echo esc_url($buy_href); ?>" class="sr-btn-buy">Purchase Plan</a>
  </div>
</div>

</div><!-- .sr-wrap -->

<script>
(function(){
  var SR_SQFT = <?php echo (int)$sqft_int; ?>;
  var srRegions = {southeast:{low:160,mid:215},south_central:{low:150,mid:205},midwest:{low:155,mid:210},mountain_west:{low:180,mid:245},northeast:{low:210,mid:285},pacific_nw:{low:195,mid:265},west_coast:{low:230,mid:320}};
  var srFinish = {1:{f:0.85,l:'Standard Build'},2:{f:1.0,l:'Mid-Range Build'},3:{f:1.30,l:'Custom Build'},4:{f:1.65,l:'Premium Custom Build'}};
  window.srSwapImage = function(thumb) {
    var img = document.getElementById('srMainImage'); if (img) img.src = thumb.dataset.full;
    document.querySelectorAll('.sr-gallery__thumb').forEach(function(t){t.classList.remove('sr-active');});
    thumb.classList.add('sr-active');
  };
  window.srSelectOption = function(fmt) {
    document.querySelectorAll('.sr-price-option').forEach(function(o){o.classList.remove('sr-selected');});
    var el = document.getElementById(fmt==='pdf'?'srOptionPdf':'srOptionCad'); if(el) el.classList.add('sr-selected');
  };
  window.srUpdateEstimate = function() {
    var rEl = document.getElementById('srRegionSelect'), fEl = document.getElementById('srFinishLevel');
    if (!rEl||!fEl) return;
    var rd = srRegions[rEl.value], fm = srFinish[parseInt(fEl.value,10)];
    var lbl = document.getElementById('srFinishLabel'); if (lbl) lbl.textContent = fm.l;
    var lo = Math.round(rd.low*fm.f), hi = Math.round(rd.mid*fm.f);
    var loT = lo*SR_SQFT, hiT = hi*SR_SQFT;
    var fmt = function(n){return '$'+n.toLocaleString();}, fK = function(n){return '$'+Math.round(n/1000)+'K';};
    var set = function(id,v){var e=document.getElementById(id);if(e)e.textContent=v;};
    set('srEstimateValue', fmt(loT)+' \u2013 '+fmt(hiT));
    set('srEstimatePerSqft', '$'+lo+' \u2013 $'+hi+' per sq ft');
    set('srMaterialsCost', fK(loT*0.45)+' \u2013 '+fK(hiT*0.45));
    set('srLaborCost', fK(loT*0.35)+' \u2013 '+fK(hiT*0.35));
  };
  window.srToggleFaq = function(btn) {
    var item = btn.parentElement, isOpen = item.classList.contains('sr-open');
    document.querySelectorAll('.sr-faq-item').forEach(function(f){
      f.classList.remove('sr-open');
      var q=f.querySelector('.sr-faq-question'); if(q) q.setAttribute('aria-expanded','false');
      var a=f.querySelector('.sr-faq-answer'); if(a) a.style.maxHeight='0';
    });
    if (!isOpen) {
      item.classList.add('sr-open'); btn.setAttribute('aria-expanded','true');
      var ans = item.querySelector('.sr-faq-answer'); if(ans) ans.style.maxHeight = ans.scrollHeight+'px';
    }
  };
  if (document.readyState==='loading') { document.addEventListener('DOMContentLoaded', srUpdateEstimate); } else { srUpdateEstimate(); }
})();
</script>
<?php
}

genesis();
