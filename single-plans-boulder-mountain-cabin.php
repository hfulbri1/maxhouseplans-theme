<?php
/**
 * Template Name: Plan - Boulder Mountain Cabin
 *
 * Dedicated plan template for the Boulder Mountain Cabin A-frame house plan.
 * Applies to plan slug: a-frame-cabin-house-plan
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-19
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_boulder_loop' );
add_action( 'wp_head', 'mhp_boulder_styles', 20 );
add_action( 'wp_head', 'mhp_boulder_schema', 5 );
add_action( 'wp_head', 'mhp_boulder_fonts', 1 );

function mhp_boulder_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">' . "\n";
}

function mhp_boulder_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );
    $name    = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $name    = $name ?: get_the_title();
    $sqft    = $acf ? get_field( 'total_living_area', $pid ) : '2141';
    $beds    = $acf ? get_field( 'bedrooms', $pid ) : '3';
    $baths   = $acf ? get_field( 'bathrooms', $pid ) : '3';
    $stories = $acf ? get_field( 'stories', $pid ) : '3';
    $price   = $acf ? get_field( 'price', $pid ) : '1195';
    $price_num = is_numeric( $price ) ? number_format( (float) $price, 2, '.', '' ) : '1195.00';
    $image_url = get_the_post_thumbnail_url( $pid, 'full' );
    $permalink = get_permalink( $pid );

    $product = array(
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name' => $name . ' House Plan',
        'description' => 'A build-ready A-frame cabin plan featuring 2,141 sq ft of heated living space across three levels, 3 bedrooms, 3 bathrooms, vaulted great room with A-framed wall of windows, wraparound deck, walkout basement, and rustic craftsman exterior.',
        'image' => $image_url ? array( $image_url ) : array(),
        'brand' => array( '@type' => 'Brand', 'name' => 'Max Fulbright Designs' ),
        'offers' => array(
            array( '@type' => 'Offer', 'name' => 'PDF Plan Set', 'price' => $price_num, 'priceCurrency' => 'USD', 'availability' => 'https://schema.org/InStock', 'url' => $permalink, 'seller' => array( '@type' => 'Organization', 'name' => 'Max House Plans' ) ),
            array( '@type' => 'Offer', 'name' => 'CAD File', 'price' => number_format( (float) $price_num * 1.26, 2, '.', '' ), 'priceCurrency' => 'USD', 'availability' => 'https://schema.org/InStock', 'url' => $permalink ),
        ),
        'additionalProperty' => array(
            array( '@type' => 'PropertyValue', 'name' => 'Bedrooms', 'value' => $beds ),
            array( '@type' => 'PropertyValue', 'name' => 'Bathrooms', 'value' => $baths ),
            array( '@type' => 'PropertyValue', 'name' => 'Stories', 'value' => $stories ),
            array( '@type' => 'PropertyValue', 'name' => 'Heated Square Feet', 'value' => $sqft ),
            array( '@type' => 'PropertyValue', 'name' => 'Style', 'value' => 'A-Frame, Cabin, Rustic, Mountain, Lake, Craftsman' ),
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
        array( '@type' => 'ListItem', 'position' => 3, 'name' => 'Mountain House Plans', 'item' => home_url( '/home-plans/mountain-house-plans/' ) ),
        array( '@type' => 'ListItem', 'position' => 4, 'name' => $name . ' House Plan' ),
    ) );
    echo '<script type="application/ld+json">' . wp_json_encode( $bc, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

function mhp_boulder_styles() {
?>
<style id="mhp-boulder-styles">
:root {
  --bld-primary: #2A2D2E; --bld-primary-light: #3E4244; --bld-primary-dark: #1A1C1D;
  --bld-accent: #D4943A; --bld-accent-light: #E4B06A; --bld-accent-warm: #C88832;
  --bld-pine: #3D4F3E; --bld-pine-light: #E2E8E2;
  --bld-slate: #F4F3F0; --bld-slate-dark: #E8E6E1; --bld-charcoal: #1E2021;
  --bld-text: #2A2D2E; --bld-text-secondary: #62676A; --bld-text-light: #95999D;
  --bld-white: #FFFFFF; --bld-border: #DDD9D2; --bld-border-light: #ECEAE5;
  --bld-success: #4A6B4D;
  --bld-font-display: 'Instrument Serif', Georgia, serif;
  --bld-font-body: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
  --bld-space-xs: 0.25rem; --bld-space-sm: 0.5rem; --bld-space-md: 1rem;
  --bld-space-lg: 1.5rem; --bld-space-xl: 2rem; --bld-space-2xl: 3rem;
  --bld-space-3xl: 4rem; --bld-space-4xl: 6rem; --bld-max-width: 1260px;
  --bld-radius-sm: 6px; --bld-radius-md: 10px; --bld-radius-lg: 16px; --bld-radius-xl: 24px;
  --bld-shadow-sm: 0 1px 3px rgba(0,0,0,.04); --bld-shadow-md: 0 4px 16px rgba(0,0,0,.06);
  --bld-shadow-lg: 0 8px 32px rgba(0,0,0,.08); --bld-shadow-xl: 0 20px 60px rgba(0,0,0,.10);
  --bld-ease-out: cubic-bezier(.16,1,.3,1);
  --bld-transition-fast: .2s var(--bld-ease-out); --bld-transition-base: .35s var(--bld-ease-out);
  --bld-transition-slow: .6s var(--bld-ease-out);
}
.bld-wrap *, .bld-wrap *::before, .bld-wrap *::after { box-sizing: border-box; margin: 0; padding: 0; }
.bld-wrap { font-family: var(--bld-font-body); color: var(--bld-text); background: var(--bld-slate); line-height: 1.7; font-size: 16px; overflow-x: hidden; font-weight: 400; }
.bld-wrap img { max-width: 100%; height: auto; display: block; }
.bld-wrap a { color: inherit; text-decoration: none; }
.bld-wrap button { cursor: pointer; border: none; background: none; font-family: inherit; }
.bld-container { max-width: var(--bld-max-width); margin: 0 auto; padding: 0 var(--bld-space-xl); }
@keyframes bld-fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes bld-fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes bld-scaleIn { from { opacity: 0; transform: scale(.96); } to { opacity: 1; transform: scale(1); } }
.bld-animate-in { opacity: 0; animation: bld-fadeInUp .7s var(--bld-ease-out) forwards; }
.bld-breadcrumbs { padding: var(--bld-space-lg) 0 var(--bld-space-md); font-size: .78rem; color: var(--bld-text-light); }
.bld-breadcrumbs a { color: var(--bld-text-secondary); transition: color var(--bld-transition-fast); }
.bld-breadcrumbs a:hover { color: var(--bld-accent); }
.bld-breadcrumbs span { margin: 0 var(--bld-space-sm); opacity: .4; }
.bld-plan-hero { padding-bottom: var(--bld-space-3xl); }
.bld-plan-hero__grid { display: grid; grid-template-columns: 1fr 400px; gap: var(--bld-space-3xl); align-items: start; }
.bld-gallery { animation: bld-fadeIn .5s var(--bld-ease-out) forwards; }
.bld-gallery__main { position: relative; border-radius: var(--bld-radius-lg); overflow: hidden; background: var(--bld-primary); aspect-ratio: 16/10; box-shadow: var(--bld-shadow-lg); }
.bld-gallery__main img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--bld-transition-slow); }
.bld-gallery__main:hover img { transform: scale(1.02); }
.bld-gallery__badge { position: absolute; top: var(--bld-space-lg); left: var(--bld-space-lg); background: var(--bld-charcoal); color: var(--bld-white); font-size: .68rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; padding: 5px 14px; border-radius: 100px; z-index: 2; display: flex; align-items: center; gap: 5px; border: 1px solid rgba(255,255,255,.1); }
.bld-gallery__badge svg { width: 14px; height: 14px; color: var(--bld-accent); }
.bld-gallery__count { position: absolute; bottom: var(--bld-space-lg); right: var(--bld-space-lg); background: rgba(0,0,0,.55); backdrop-filter: blur(8px); color: var(--bld-white); font-size: .76rem; font-weight: 500; padding: 5px 14px; border-radius: 100px; z-index: 2; display: flex; align-items: center; gap: 5px; }
.bld-gallery__count svg { width: 15px; height: 15px; }
.bld-gallery__thumbs { display: grid; grid-template-columns: repeat(5, 1fr); gap: var(--bld-space-sm); margin-top: var(--bld-space-sm); }
.bld-gallery__thumb { aspect-ratio: 3/2; border-radius: var(--bld-radius-sm); overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all var(--bld-transition-fast); opacity: .55; }
.bld-gallery__thumb:hover, .bld-gallery__thumb.bld-active { opacity: 1; border-color: var(--bld-accent); box-shadow: var(--bld-shadow-md); }
.bld-gallery__thumb img { width: 100%; height: 100%; object-fit: cover; }
.bld-purchase-card { position: sticky; top: var(--bld-space-xl); background: var(--bld-white); border-radius: var(--bld-radius-lg); box-shadow: var(--bld-shadow-lg); overflow: hidden; animation: bld-scaleIn .5s var(--bld-ease-out) .15s forwards; opacity: 0; border: 1px solid var(--bld-border-light); }
.bld-purchase-card__header { padding: var(--bld-space-xl) var(--bld-space-xl) var(--bld-space-lg); background: var(--bld-charcoal); color: var(--bld-white); }
.bld-purchase-card__tag { display: inline-flex; align-items: center; gap: 4px; background: rgba(212,148,58,.15); color: var(--bld-accent-light); font-size: .65rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; padding: 4px 10px; border-radius: 100px; margin-bottom: var(--bld-space-sm); border: 1px solid rgba(212,148,58,.2); }
.bld-purchase-card__plan-name { font-family: var(--bld-font-display); font-size: 1.65rem; line-height: 1.2; margin-bottom: 4px; }
.bld-purchase-card__style { font-size: .76rem; opacity: .55; }
.bld-purchase-card__specs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; background: var(--bld-border-light); }
.bld-spec-cell { background: var(--bld-white); padding: var(--bld-space-md) var(--bld-space-lg); text-align: center; }
.bld-spec-cell__value { font-family: var(--bld-font-display); font-size: 1.3rem; color: var(--bld-primary); line-height: 1.2; }
.bld-spec-cell__label { font-size: .65rem; text-transform: uppercase; letter-spacing: .08em; color: var(--bld-text-light); margin-top: 2px; font-weight: 600; }
.bld-purchase-card__body { padding: var(--bld-space-xl); }
.bld-price-option { display: flex; align-items: center; padding: var(--bld-space-md) var(--bld-space-lg); border: 2px solid var(--bld-border); border-radius: var(--bld-radius-md); margin-bottom: var(--bld-space-sm); cursor: pointer; transition: all var(--bld-transition-fast); }
.bld-price-option:hover { border-color: var(--bld-accent-light); background: var(--bld-slate); }
.bld-price-option.bld-selected { border-color: var(--bld-accent); background: linear-gradient(135deg, rgba(212,148,58,.04), rgba(212,148,58,.01)); }
.bld-price-option input[type="radio"] { appearance: none; -webkit-appearance: none; width: 18px; height: 18px; border: 2px solid var(--bld-border); border-radius: 50%; margin-right: var(--bld-space-md); flex-shrink: 0; transition: all var(--bld-transition-fast); }
.bld-price-option input[type="radio"]:checked { border-color: var(--bld-accent); background: var(--bld-accent); box-shadow: inset 0 0 0 3px var(--bld-white); }
.bld-price-option__info { flex: 1; }
.bld-price-option__format { font-weight: 600; font-size: .87rem; }
.bld-price-option__desc { font-size: .71rem; color: var(--bld-text-light); margin-top: 1px; }
.bld-price-option__price { font-family: var(--bld-font-display); font-size: 1.15rem; color: var(--bld-primary); }
.bld-btn-buy { display: flex; align-items: center; justify-content: center; width: 100%; padding: 14px var(--bld-space-xl); background: var(--bld-accent); color: var(--bld-white); font-size: .93rem; font-weight: 700; border-radius: var(--bld-radius-md); margin-top: var(--bld-space-lg); transition: all var(--bld-transition-fast); gap: var(--bld-space-sm); box-shadow: 0 4px 18px rgba(212,148,58,.25); letter-spacing: .01em; text-decoration: none; }
.bld-btn-buy:hover { background: var(--bld-accent-warm); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(212,148,58,.3); color: var(--bld-white); }
.bld-btn-buy svg { width: 18px; height: 18px; }
.bld-trust-row { display: flex; justify-content: center; gap: var(--bld-space-lg); margin-top: var(--bld-space-lg); padding-top: var(--bld-space-lg); border-top: 1px solid var(--bld-border-light); }
.bld-trust-item { display: flex; align-items: center; gap: 5px; font-size: .71rem; color: var(--bld-text-light); font-weight: 500; }
.bld-trust-item svg { width: 14px; height: 14px; color: var(--bld-success); flex-shrink: 0; }
.bld-purchase-card__footer { padding: var(--bld-space-md) var(--bld-space-xl) var(--bld-space-xl); text-align: center; }
.bld-purchase-card__footer a { font-size: .78rem; color: var(--bld-accent); font-weight: 600; }
.bld-quick-specs { background: var(--bld-charcoal); padding: var(--bld-space-lg) 0; margin-bottom: var(--bld-space-4xl); position: relative; overflow: hidden; }
.bld-quick-specs::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, transparent 49.5%, rgba(212,148,58,.03) 49.5%, rgba(212,148,58,.03) 50.5%, transparent 50.5%); background-size: 30px 30px; }
.bld-quick-specs__grid { display: flex; justify-content: center; gap: var(--bld-space-3xl); position: relative; z-index: 1; flex-wrap: wrap; }
.bld-quick-spec { text-align: center; color: var(--bld-white); }
.bld-quick-spec__icon { width: 26px; height: 26px; margin: 0 auto var(--bld-space-xs); opacity: .5; }
.bld-quick-spec__value { font-family: var(--bld-font-display); font-size: 1.3rem; line-height: 1.2; }
.bld-quick-spec__label { font-size: .63rem; text-transform: uppercase; letter-spacing: .1em; opacity: .45; font-weight: 600; margin-top: 2px; }
.bld-section { padding: var(--bld-space-4xl) 0; }
.bld-section--alt { background: var(--bld-white); }
.bld-section--dark { background: var(--bld-charcoal); color: var(--bld-white); }
.bld-section__header { text-align: center; margin-bottom: var(--bld-space-3xl); }
.bld-section__overline { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .14em; color: var(--bld-accent); margin-bottom: var(--bld-space-sm); }
.bld-section--dark .bld-section__overline { color: var(--bld-accent-light); }
.bld-section__title { font-family: var(--bld-font-display); font-size: 2.1rem; color: var(--bld-primary-dark); line-height: 1.25; margin-bottom: var(--bld-space-md); }
.bld-section--dark .bld-section__title { color: var(--bld-white); }
.bld-section__subtitle { font-size: .97rem; color: var(--bld-text-secondary); max-width: 620px; margin: 0 auto; line-height: 1.7; }
.bld-section--dark .bld-section__subtitle { color: rgba(255,255,255,.55); }
.bld-description-grid { display: grid; grid-template-columns: 1fr 350px; gap: var(--bld-space-3xl); align-items: start; }
.bld-description__content h2 { font-family: var(--bld-font-display); font-size: 1.85rem; color: var(--bld-primary-dark); margin-bottom: var(--bld-space-lg); line-height: 1.3; }
.bld-description__content p { color: var(--bld-text-secondary); margin-bottom: var(--bld-space-lg); font-size: .97rem; line-height: 1.8; }
.bld-levels-card { background: var(--bld-charcoal); border-radius: var(--bld-radius-lg); padding: var(--bld-space-xl); color: var(--bld-white); }
.bld-levels-card__title { font-family: var(--bld-font-display); font-size: 1.1rem; margin-bottom: var(--bld-space-xl); padding-bottom: var(--bld-space-sm); border-bottom: 2px solid var(--bld-accent); display: inline-block; }
.bld-level-item { display: flex; gap: var(--bld-space-md); margin-bottom: var(--bld-space-xl); align-items: flex-start; }
.bld-level-item:last-child { margin-bottom: 0; }
.bld-level-item__number { width: 36px; height: 36px; background: rgba(212,148,58,.12); border: 1px solid rgba(212,148,58,.2); border-radius: var(--bld-radius-sm); display: flex; align-items: center; justify-content: center; font-family: var(--bld-font-display); font-size: 1.1rem; color: var(--bld-accent-light); flex-shrink: 0; }
.bld-level-item__text strong { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 2px; }
.bld-level-item__text span { font-size: .76rem; opacity: .55; line-height: 1.5; }
.bld-level-item__sqft { font-family: var(--bld-font-display); font-size: .85rem; color: var(--bld-accent-light); margin-top: 4px; display: block; }
.bld-specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--bld-space-xl); }
.bld-specs-group { background: var(--bld-white); border-radius: var(--bld-radius-lg); padding: var(--bld-space-xl); border: 1px solid var(--bld-border); }
.bld-section--dark .bld-specs-group { background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.06); }
.bld-specs-group__title { font-family: var(--bld-font-display); font-size: 1rem; color: var(--bld-primary-dark); margin-bottom: var(--bld-space-lg); padding-bottom: var(--bld-space-sm); border-bottom: 2px solid var(--bld-slate-dark); }
.bld-section--dark .bld-specs-group__title { color: var(--bld-white); border-color: rgba(255,255,255,.08); }
.bld-spec-row { display: flex; justify-content: space-between; align-items: baseline; padding: 7px 0; border-bottom: 1px solid var(--bld-border-light); }
.bld-section--dark .bld-spec-row { border-color: rgba(255,255,255,.05); }
.bld-spec-row:last-child { border-bottom: none; }
.bld-spec-row__label { font-size: .82rem; color: var(--bld-text-secondary); }
.bld-section--dark .bld-spec-row__label { color: rgba(255,255,255,.45); }
.bld-spec-row__value { font-size: .83rem; font-weight: 600; color: var(--bld-text); text-align: right; }
.bld-section--dark .bld-spec-row__value { color: var(--bld-white); }
.bld-floor-plans-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--bld-space-xl); }
.bld-floor-plan-card { background: var(--bld-white); border-radius: var(--bld-radius-lg); overflow: hidden; box-shadow: var(--bld-shadow-md); border: 1px solid var(--bld-border); transition: all var(--bld-transition-base); }
.bld-floor-plan-card:hover { box-shadow: var(--bld-shadow-xl); transform: translateY(-3px); }
.bld-floor-plan-card__image { padding: var(--bld-space-lg); background: var(--bld-slate); }
.bld-floor-plan-card__image img { width: 100%; border-radius: var(--bld-radius-sm); }
.bld-floor-plan-card__info { padding: var(--bld-space-md) var(--bld-space-lg); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--bld-border-light); }
.bld-floor-plan-card__label { font-weight: 700; font-size: .88rem; color: var(--bld-primary-dark); }
.bld-floor-plan-card__sqft { font-size: .78rem; color: var(--bld-text-light); font-weight: 500; }
.bld-floor-plans__wysiwyg img { max-width: 100%; border-radius: var(--bld-radius-lg); box-shadow: var(--bld-shadow-md); }
.bld-estimator { background: linear-gradient(145deg, #1A1C1D 0%, #2A2D2E 50%, #35383A 100%); border-radius: var(--bld-radius-xl); overflow: hidden; position: relative; color: var(--bld-white); }
.bld-estimator::before { content: ''; position: absolute; top: 0; right: 0; width: 0; height: 0; border-style: solid; border-width: 0 350px 350px 0; border-color: transparent rgba(212,148,58,.04) transparent transparent; pointer-events: none; }
.bld-estimator__inner { display: grid; grid-template-columns: 1fr 1fr; position: relative; z-index: 1; }
.bld-estimator__info { padding: var(--bld-space-3xl); }
.bld-estimator__overline { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--bld-accent-light); margin-bottom: var(--bld-space-md); }
.bld-estimator__title { font-family: var(--bld-font-display); font-size: 1.8rem; line-height: 1.3; margin-bottom: var(--bld-space-lg); }
.bld-estimator__desc { opacity: .65; line-height: 1.75; margin-bottom: var(--bld-space-lg); font-size: .9rem; }
.bld-estimator__note { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.06); border-radius: var(--bld-radius-md); padding: var(--bld-space-md) var(--bld-space-lg); font-size: .75rem; opacity: .5; line-height: 1.6; display: flex; align-items: flex-start; gap: var(--bld-space-sm); }
.bld-estimator__note svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
.bld-estimator__calc { padding: var(--bld-space-3xl); background: rgba(255,255,255,.02); border-left: 1px solid rgba(255,255,255,.05); }
.bld-calc-field { margin-bottom: var(--bld-space-xl); }
.bld-calc-field label { display: block; font-size: .73rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; margin-bottom: var(--bld-space-sm); opacity: .7; }
.bld-calc-select { width: 100%; padding: var(--bld-space-md) var(--bld-space-lg); background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: var(--bld-radius-md); color: var(--bld-white); font-family: var(--bld-font-body); font-size: .87rem; appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ffffff' stroke-width='1.5' fill='none' opacity='0.4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; }
.bld-calc-select option { background: var(--bld-charcoal); color: var(--bld-white); }
.bld-range-wrapper { padding-top: var(--bld-space-xs); }
.bld-range-labels { display: flex; justify-content: space-between; margin-top: var(--bld-space-xs); font-size: .66rem; opacity: .4; }
.bld-estimator input[type="range"] { -webkit-appearance: none; appearance: none; width: 100%; height: 5px; border-radius: 3px; background: rgba(255,255,255,.1); outline: none; }
.bld-estimator input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: var(--bld-accent); cursor: pointer; border: 3px solid var(--bld-white); box-shadow: 0 2px 10px rgba(0,0,0,.3); }
.bld-range-current { text-align: center; font-family: var(--bld-font-display); font-size: .95rem; color: var(--bld-accent-light); margin-top: var(--bld-space-xs); }
.bld-estimate-result { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.06); border-radius: var(--bld-radius-lg); padding: var(--bld-space-xl); text-align: center; margin-top: var(--bld-space-lg); }
.bld-estimate-result__label { font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; opacity: .45; font-weight: 600; margin-bottom: var(--bld-space-sm); }
.bld-estimate-result__value { font-family: var(--bld-font-display); font-size: 1.9rem; color: var(--bld-accent-light); line-height: 1.1; margin-bottom: 4px; }
.bld-estimate-result__range { font-size: .78rem; opacity: .4; }
.bld-estimate-result__breakdown { display: grid; grid-template-columns: 1fr 1fr; gap: var(--bld-space-md); margin-top: var(--bld-space-lg); padding-top: var(--bld-space-lg); border-top: 1px solid rgba(255,255,255,.05); }
.bld-breakdown-item__label { font-size: .66rem; text-transform: uppercase; opacity: .35; }
.bld-breakdown-item__value { font-size: .93rem; font-weight: 600; margin-top: 2px; }
.bld-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--bld-space-xl); }
.bld-included-card { background: var(--bld-slate); border-radius: var(--bld-radius-lg); padding: var(--bld-space-xl) var(--bld-space-lg); text-align: center; border: 1px solid var(--bld-border); transition: all var(--bld-transition-base); }
.bld-included-card:hover { transform: translateY(-3px); box-shadow: var(--bld-shadow-lg); border-color: var(--bld-accent-light); }
.bld-included-card__icon { width: 50px; height: 50px; background: var(--bld-charcoal); border-radius: var(--bld-radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--bld-space-lg); }
.bld-included-card__icon svg { width: 24px; height: 24px; color: var(--bld-accent-light); }
.bld-included-card__title { font-weight: 600; font-size: .9rem; margin-bottom: var(--bld-space-xs); color: var(--bld-primary-dark); }
.bld-included-card__desc { font-size: .78rem; color: var(--bld-text-light); line-height: 1.6; }
.bld-faq-list { max-width: 780px; margin: 0 auto; }
.bld-faq-item { background: var(--bld-white); border-radius: var(--bld-radius-md); margin-bottom: var(--bld-space-sm); border: 1px solid var(--bld-border); overflow: hidden; transition: all var(--bld-transition-fast); }
.bld-faq-item.bld-open { box-shadow: var(--bld-shadow-md); border-color: var(--bld-accent-light); }
.bld-faq-question { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: var(--bld-space-lg) var(--bld-space-xl); font-size: .9rem; font-weight: 500; text-align: left; color: var(--bld-text); transition: color var(--bld-transition-fast); gap: var(--bld-space-md); }
.bld-faq-question:hover { color: var(--bld-accent); }
.bld-faq-question__icon { width: 22px; height: 22px; flex-shrink: 0; border-radius: 50%; background: var(--bld-slate); display: flex; align-items: center; justify-content: center; transition: all var(--bld-transition-fast); }
.bld-faq-item.bld-open .bld-faq-question__icon { background: var(--bld-accent); transform: rotate(45deg); }
.bld-faq-question__icon svg { width: 12px; height: 12px; color: var(--bld-text-secondary); }
.bld-faq-item.bld-open .bld-faq-question__icon svg { color: var(--bld-white); }
.bld-faq-answer { max-height: 0; overflow: hidden; transition: max-height .4s var(--bld-ease-out); }
.bld-faq-answer__inner { padding: 0 var(--bld-space-xl) var(--bld-space-xl); font-size: .87rem; color: var(--bld-text-secondary); line-height: 1.8; }
.bld-cta-section { background: var(--bld-charcoal); padding: var(--bld-space-4xl) 0; text-align: center; color: var(--bld-white); position: relative; overflow: hidden; }
.bld-cta-section::before { content: ''; position: absolute; left: 50%; top: 0; transform: translateX(-50%); width: 0; height: 0; border-style: solid; border-width: 120px 800px 0 800px; border-color: rgba(212,148,58,.03) transparent transparent transparent; pointer-events: none; }
.bld-cta-section .bld-container { position: relative; z-index: 1; }
.bld-cta__title { font-family: var(--bld-font-display); font-size: 2.1rem; margin-bottom: var(--bld-space-md); }
.bld-cta__text { font-size: .97rem; opacity: .6; max-width: 520px; margin: 0 auto var(--bld-space-xl); line-height: 1.75; }
.bld-cta__buttons { display: flex; justify-content: center; gap: var(--bld-space-lg); flex-wrap: wrap; }
.bld-btn-cta { display: inline-flex; align-items: center; gap: var(--bld-space-sm); padding: var(--bld-space-md) var(--bld-space-2xl); border-radius: var(--bld-radius-md); font-weight: 700; font-size: .9rem; transition: all var(--bld-transition-fast); }
.bld-btn-cta--primary { background: var(--bld-accent); color: var(--bld-white); box-shadow: 0 4px 18px rgba(212,148,58,.3); }
.bld-btn-cta--primary:hover { background: var(--bld-accent-warm); transform: translateY(-2px); }
.bld-btn-cta--outline { border: 2px solid rgba(255,255,255,.18); color: var(--bld-white); }
.bld-btn-cta--outline:hover { border-color: var(--bld-white); background: rgba(255,255,255,.05); }
.bld-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--bld-white); border-top: 1px solid var(--bld-border); padding: var(--bld-space-md) var(--bld-space-lg); z-index: 100; box-shadow: 0 -4px 20px rgba(0,0,0,.08); }
.bld-mobile-buy-bar__inner { display: flex; align-items: center; justify-content: space-between; max-width: var(--bld-max-width); margin: 0 auto; }
.bld-mobile-buy-bar__name { font-weight: 700; font-size: .83rem; color: var(--bld-primary-dark); }
.bld-mobile-buy-bar__price { font-family: var(--bld-font-display); color: var(--bld-accent); font-size: 1rem; }
.bld-mobile-buy-bar .bld-btn-buy { width: auto; padding: var(--bld-space-sm) var(--bld-space-xl); margin-top: 0; font-size: .83rem; }
@media(max-width:1024px){.bld-plan-hero__grid{grid-template-columns:1fr}.bld-purchase-card{position:relative;top:0}.bld-description-grid{grid-template-columns:1fr}.bld-specs-grid{grid-template-columns:1fr 1fr}.bld-estimator__inner{grid-template-columns:1fr}.bld-included-grid{grid-template-columns:repeat(2,1fr)}.bld-floor-plans-grid{grid-template-columns:1fr 1fr}.bld-mobile-buy-bar{display:block}.bld-wrap{padding-bottom:80px}}
@media(max-width:768px){.bld-gallery__thumbs{grid-template-columns:repeat(4,1fr)}.bld-section__title{font-size:1.6rem}.bld-specs-grid,.bld-floor-plans-grid{grid-template-columns:1fr}.bld-included-grid{grid-template-columns:1fr}.bld-estimator__info,.bld-estimator__calc{padding:var(--bld-space-xl)}}
@media(max-width:480px){.bld-container{padding:0 var(--bld-space-lg)}.bld-gallery__thumbs{grid-template-columns:repeat(3,1fr)}.bld-quick-specs__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--bld-space-lg)}.bld-cta__buttons{flex-direction:column;align-items:center}}
</style>
<?php
}

function mhp_boulder_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    $plan_name        = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft             = $acf ? get_field( 'total_living_area',   $pid ) : '2,141';
    $main_floor       = $acf ? get_field( 'main_floor',          $pid ) : '1,466';
    $upper_floor      = $acf ? get_field( 'upper_floor',         $pid ) : '675';
    $lower_floor      = $acf ? get_field( 'lower_floor',         $pid ) : '1,466';
    $bedrooms         = $acf ? get_field( 'bedrooms',            $pid ) : '3';
    $bathrooms        = $acf ? get_field( 'bathrooms',           $pid ) : '3';
    $stories          = $acf ? get_field( 'stories',             $pid ) : '3';
    $width            = $acf ? get_field( 'width',               $pid ) : "78'1½\"";
    $depth            = $acf ? get_field( 'depth',               $pid ) : "54'8\"";
    $garage           = $acf ? get_field( 'garage',              $pid ) : 'Carport';
    $style            = $acf ? get_field( 'style',               $pid ) : 'A-Frame · Cabin · Rustic · Mountain · Lake';
    $outdoor          = $acf ? get_field( 'outdoor',             $pid ) : 'Deck, Porches, Patio';
    $roof             = $acf ? get_field( 'roof',                $pid ) : '12/12 Gable';
    $ceiling          = $acf ? get_field( 'ceiling',             $pid ) : "9' / Vaulted";
    $exterior         = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms = $acf ? get_field( 'additional_rooms',    $pid ) : 'Rec Room, Storage';
    $other_features   = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style        = $acf ? get_field( 'lot_style',           $pid ) : 'Sloping, Mountain, Lake';
    $plan_description = $acf ? get_field( 'plan_description',    $pid ) : '';
    $floor_plans_wysiwyg = $acf ? get_field( 'floor_plans',      $pid ) : '';
    $paypal           = $acf ? get_field( 'paypal',              $pid ) : '';
    $price            = $acf ? get_field( 'price',               $pid ) : '1195';
    $related_plans    = $acf ? get_field( 'related_plans',       $pid ) : array();
    $faqs             = $acf ? get_field( 'faqs',                $pid ) : array();

    $plan_name = $plan_name ?: get_the_title();
    $price_num = is_numeric( $price ) ? (float) $price : 1195;
    $price_fmt = '$' . number_format( $price_num, 0, '.', ',' );
    $cad_price = '$' . number_format( $price_num * 1.26, 0, '.', ',' );

    $paypal_url = '';
    if ( $paypal && preg_match( "/name=[\"']hosted_button_id[\"']\s+value=[\"']([^\"']+)[\"']/", $paypal, $m ) ) {
        $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
    }
    $buy_href = $paypal_url ?: ( get_permalink() . '#contact' );

    $hero_url = get_the_post_thumbnail_url( $pid, 'large' );
    if ( ! $hero_url ) $hero_url = 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/a-frame-cabin-house-plan-with-walkout-basement-and-porch.jpg';

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
            array( 'full' => $hero_url, 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/a-frame-cabin-house-plan-with-walkout-basement-and-porch-300x225.jpg', 'alt' => 'A-frame front with porch' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/a-frame-cabin-house-plan-3-story-with-walkout-basement.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/a-frame-cabin-house-plan-3-story-with-walkout-basement-300x169.jpg', 'alt' => '3-story A-frame with walkout' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/a-frame-cabin-house-plan-three-story-walkout-basement.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/a-frame-cabin-house-plan-three-story-walkout-basement-300x169.jpg', 'alt' => 'Three story side view' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-cabin-house-plans-rustic-design-snow.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-cabin-house-plans-rustic-design-snow-100x67.jpg', 'alt' => 'A-frame in snow' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-house-plan-outdoor-porch-fireplace.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-house-plan-outdoor-porch-fireplace-100x67.jpg', 'alt' => 'Outdoor porch fireplace' ),
        );
    }

    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft' ), '', $v ); };
    $sqft_display    = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display = $clean($main_floor) > 0 ? number_format( $clean($main_floor) ) : $main_floor;
    $upper_fl_display = $clean($upper_floor) > 0 ? number_format( $clean($upper_floor) ) : $upper_floor;
    $lower_fl_display = $clean($lower_floor) > 0 ? number_format( $clean($lower_floor) ) : $lower_floor;
    $sqft_int = $clean($sqft) > 100 ? $clean($sqft) : 2141;
?>
<div class="bld-wrap">

<!-- BREADCRUMBS -->
<nav class="bld-breadcrumbs" aria-label="Breadcrumb">
  <div class="bld-container">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/home-plans/mountain-house-plans/')); ?>">Mountain House Plans</a><span>&#8250;</span>
    <strong><?php echo esc_html($plan_name); ?></strong>
  </div>
</nav>

<!-- HERO -->
<section class="bld-plan-hero">
  <div class="bld-container">
    <div class="bld-plan-hero__grid">
      <div class="bld-gallery bld-animate-in">
        <div class="bld-gallery__main" id="bldGalleryMain">
          <span class="bld-gallery__badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 22h20L12 2z"/></svg>
            A-Frame Cabin
          </span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> — A-frame cabin house plan with wraparound deck, walkout basement, and dramatic window wall" id="bldMainImage" fetchpriority="high">
          <span class="bld-gallery__count">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            15 Photos
          </span>
        </div>
        <div class="bld-gallery__thumbs">
          <?php foreach ( $gallery_images as $idx => $img ) : ?>
          <div class="bld-gallery__thumb<?php echo $idx === 0 ? ' bld-active' : ''; ?>" onclick="bldSwapImage(this)" data-full="<?php echo esc_url($img['full']); ?>">
            <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <aside class="bld-purchase-card" aria-label="Plan purchase options">
        <div class="bld-purchase-card__header">
          <div class="bld-purchase-card__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 22h20L12 2z"/></svg>
            Iconic A-Frame Design
          </div>
          <h1 class="bld-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
          <p class="bld-purchase-card__style"><?php echo esc_html($style); ?></p>
        </div>
        <div class="bld-purchase-card__specs">
          <div class="bld-spec-cell"><div class="bld-spec-cell__value"><?php echo esc_html($sqft_display); ?></div><div class="bld-spec-cell__label">Sq Ft</div></div>
          <div class="bld-spec-cell"><div class="bld-spec-cell__value"><?php echo esc_html($bedrooms); ?></div><div class="bld-spec-cell__label">Bedrooms</div></div>
          <div class="bld-spec-cell"><div class="bld-spec-cell__value"><?php echo esc_html($bathrooms); ?></div><div class="bld-spec-cell__label">Bathrooms</div></div>
          <div class="bld-spec-cell"><div class="bld-spec-cell__value"><?php echo esc_html($stories); ?></div><div class="bld-spec-cell__label">Stories</div></div>
        </div>
        <div class="bld-purchase-card__body">
          <label class="bld-price-option bld-selected" id="bldOptionPdf">
            <input type="radio" name="bld_plan_format" value="pdf" checked onchange="bldSelectOption('pdf')">
            <div class="bld-price-option__info"><div class="bld-price-option__format">PDF Plan Set</div><div class="bld-price-option__desc">Print-ready digital plans</div></div>
            <div class="bld-price-option__price"><?php echo esc_html($price_fmt); ?></div>
          </label>
          <label class="bld-price-option" id="bldOptionCad">
            <input type="radio" name="bld_plan_format" value="cad" onchange="bldSelectOption('cad')">
            <div class="bld-price-option__info"><div class="bld-price-option__format">CAD File</div><div class="bld-price-option__desc">Editable for your builder</div></div>
            <div class="bld-price-option__price"><?php echo esc_html($cad_price); ?></div>
          </label>
          <a href="<?php echo esc_url($buy_href); ?>" class="bld-btn-buy" id="bldBuyButton">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="m16 10-4 4-4-4"/></svg>
            Purchase This Plan
          </a>
          <div class="bld-trust-row">
            <span class="bld-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Secure Checkout</span>
            <span class="bld-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Instant Download</span>
          </div>
        </div>
        <div class="bld-purchase-card__footer">
          <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>">Need changes? Request a modification &#8594;</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- QUICK SPECS -->
<div class="bld-quick-specs">
  <div class="bld-container">
    <div class="bld-quick-specs__grid">
      <div class="bld-quick-spec"><svg class="bld-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg><div class="bld-quick-spec__value"><?php echo esc_html($sqft_display); ?> sq ft</div><div class="bld-quick-spec__label">Heated Area</div></div>
      <div class="bld-quick-spec"><svg class="bld-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 22h20L12 2z"/></svg><div class="bld-quick-spec__value"><?php echo esc_html($width); ?> &times; <?php echo esc_html($depth); ?></div><div class="bld-quick-spec__label">Footprint</div></div>
      <div class="bld-quick-spec"><svg class="bld-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg><div class="bld-quick-spec__value">3 Levels</div><div class="bld-quick-spec__label">Main + Upper + Walkout</div></div>
      <div class="bld-quick-spec"><svg class="bld-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg><div class="bld-quick-spec__value"><?php echo esc_html($roof ?: '12/12 Gable'); ?></div><div class="bld-quick-spec__label">Roof Pitch</div></div>
      <div class="bld-quick-spec"><svg class="bld-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><div class="bld-quick-spec__value">From <?php echo esc_html($price_fmt); ?></div><div class="bld-quick-spec__label">Plan Price</div></div>
    </div>
  </div>
</div>

<!-- DESCRIPTION -->
<section class="bld-section" id="description">
  <div class="bld-container">
    <div class="bld-description-grid">
      <div class="bld-description__content">
        <?php if ( $plan_description ) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <h2>An A-Frame Built for Real Living, Not Just Weekends</h2>
          <p>The <?php echo esc_html($plan_name); ?> takes the iconic A-frame shape and expands it into a genuine three-level home. The steep 12/12 gable roof creates that unmistakable silhouette &#8212; but instead of the cramped interiors most A-frames are known for, this plan delivers 2,141 square feet of heated space across three thoughtfully laid out levels.</p>
          <p>You enter through the front porch into a vaulted great room with a full A-framed wall of windows. In a mountain or lakefront setting, that glass wall becomes the focal point of the entire home &#8212; the view fills the room from floor to peak. The main level also includes the master bedroom and bathroom, an open kitchen, and access to the wraparound deck that lets you take in the landscape from multiple angles.</p>
          <p>Upstairs, two bedrooms sit tucked under the roofline. One features sliding barn doors that open to the great room below &#8212; creating a loft-like connection to the main living space that can be closed off for privacy. Both bedrooms have their own bathrooms.</p>
          <p>The walkout basement adds a recreation room, ample storage, and access to a covered porch at the lower level. A carport keeps vehicles out of the weather. This lower level is where the plan goes beyond a typical A-frame &#8212; it gives you the utility and flex space that makes the cabin work as a full-time residence, not just a getaway.</p>
          <p>The exterior combines rustic materials with craftsman details: stone, wood, and metal accents that let the home settle naturally into a mountain or wooded lot. An outdoor porch fireplace extends your living space into the cooler months.</p>
        <?php endif; ?>
      </div>

      <div class="bld-levels-card">
        <h3 class="bld-levels-card__title">Three Levels of Living</h3>
        <div class="bld-level-item">
          <div class="bld-level-item__number">1</div>
          <div class="bld-level-item__text">
            <strong>Main Floor</strong>
            <span>Vaulted great room, A-frame window wall, open kitchen, master suite, wraparound deck</span>
            <span class="bld-level-item__sqft"><?php echo esc_html($main_fl_display); ?> sq ft</span>
          </div>
        </div>
        <div class="bld-level-item">
          <div class="bld-level-item__number">2</div>
          <div class="bld-level-item__text">
            <strong>Upper Level</strong>
            <span>Two bedrooms with private baths, sliding barn doors open to great room below</span>
            <span class="bld-level-item__sqft"><?php echo esc_html($upper_fl_display); ?> sq ft</span>
          </div>
        </div>
        <div class="bld-level-item">
          <div class="bld-level-item__number">3</div>
          <div class="bld-level-item__text">
            <strong>Walkout Basement</strong>
            <span>Recreation room, storage, covered porch, carport</span>
            <span class="bld-level-item__sqft"><?php echo esc_html($lower_fl_display); ?> sq ft</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SPECS — dark section -->
<section class="bld-section bld-section--dark" id="specs">
  <div class="bld-container">
    <div class="bld-section__header">
      <p class="bld-section__overline">Specifications</p>
      <h2 class="bld-section__title">Full Plan Details</h2>
      <p class="bld-section__subtitle">Complete specifications for the <?php echo esc_html($plan_name); ?> &#8212; everything you and your builder need.</p>
    </div>
    <div class="bld-specs-grid">
      <div class="bld-specs-group">
        <h3 class="bld-specs-group__title">Living Area</h3>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Main Floor</span><span class="bld-spec-row__value"><?php echo esc_html($main_fl_display); ?> sq ft</span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Upper Floor</span><span class="bld-spec-row__value"><?php echo esc_html($upper_fl_display); ?> sq ft</span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Lower Floor</span><span class="bld-spec-row__value"><?php echo esc_html($lower_fl_display); ?> sq ft</span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Total Heated</span><span class="bld-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Width</span><span class="bld-spec-row__value"><?php echo esc_html($width); ?></span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Depth</span><span class="bld-spec-row__value"><?php echo esc_html($depth); ?></span></div>
      </div>
      <div class="bld-specs-group">
        <h3 class="bld-specs-group__title">House Features</h3>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Bedrooms</span><span class="bld-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Bathrooms</span><span class="bld-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Stories</span><span class="bld-spec-row__value"><?php echo esc_html($stories); ?></span></div>
        <?php if ( $additional_rooms ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Additional Rooms</span><span class="bld-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div><?php endif; ?>
        <?php if ( $garage ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Garage</span><span class="bld-spec-row__value"><?php echo esc_html($garage); ?></span></div><?php endif; ?>
        <?php if ( $outdoor ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Outdoor Spaces</span><span class="bld-spec-row__value"><?php echo esc_html($outdoor); ?></span></div><?php endif; ?>
        <?php if ( $other_features ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Other Features</span><span class="bld-spec-row__value"><?php echo esc_html($other_features); ?></span></div><?php endif; ?>
      </div>
      <div class="bld-specs-group">
        <h3 class="bld-specs-group__title">Construction</h3>
        <?php if ( $roof ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Roof Pitch</span><span class="bld-spec-row__value"><?php echo esc_html($roof); ?></span></div><?php endif; ?>
        <?php if ( $exterior ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Exterior Framing</span><span class="bld-spec-row__value"><?php echo esc_html($exterior); ?></span></div><?php endif; ?>
        <?php if ( $ceiling ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Ceiling Height</span><span class="bld-spec-row__value"><?php echo esc_html($ceiling); ?></span></div><?php endif; ?>
        <div class="bld-spec-row"><span class="bld-spec-row__label">Foundation</span><span class="bld-spec-row__value">Walkout Basement</span></div>
        <?php if ( $lot_style ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Lot Style</span><span class="bld-spec-row__value"><?php echo esc_html($lot_style); ?></span></div><?php endif; ?>
        <?php if ( $style ) : ?><div class="bld-spec-row"><span class="bld-spec-row__label">Home Style</span><span class="bld-spec-row__value"><?php echo esc_html($style); ?></span></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- FLOOR PLANS -->
<section class="bld-section" id="floorplans">
  <div class="bld-container">
    <div class="bld-section__header">
      <p class="bld-section__overline">Floor Plans</p>
      <h2 class="bld-section__title">Explore All Three Levels</h2>
      <p class="bld-section__subtitle">Main-level living with upper loft bedrooms and a full walkout basement.</p>
    </div>
    <?php if ( $floor_plans_wysiwyg ) : ?>
      <div class="bld-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans_wysiwyg); ?></div>
    <?php else : ?>
    <div class="bld-floor-plans-grid">
      <div class="bld-floor-plan-card">
        <div class="bld-floor-plan-card__image"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-cabin-open-living-floor-plans.jpg" alt="<?php echo esc_attr($plan_name); ?> main floor plan — <?php echo esc_attr($main_fl_display); ?> sq ft with vaulted great room, A-frame window wall, open kitchen, master bedroom, and wraparound deck" loading="lazy"></div>
        <div class="bld-floor-plan-card__info"><span class="bld-floor-plan-card__label">Main Floor</span><span class="bld-floor-plan-card__sqft"><?php echo esc_html($main_fl_display); ?> sq ft</span></div>
      </div>
      <div class="bld-floor-plan-card">
        <div class="bld-floor-plan-card__image"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-cabin-open-to-below-floor-plans.jpg" alt="<?php echo esc_attr($plan_name); ?> upper floor plan — <?php echo esc_attr($upper_fl_display); ?> sq ft with two bedrooms, private baths, and barn door loft" loading="lazy"></div>
        <div class="bld-floor-plan-card__info"><span class="bld-floor-plan-card__label">Upper Level</span><span class="bld-floor-plan-card__sqft"><?php echo esc_html($upper_fl_display); ?> sq ft</span></div>
      </div>
      <div class="bld-floor-plan-card">
        <div class="bld-floor-plan-card__image"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/a-frame-cabin-open-living-floor-plan-walkout-basement.jpg" alt="<?php echo esc_attr($plan_name); ?> walkout basement plan — <?php echo esc_attr($lower_fl_display); ?> sq ft with recreation room, storage, covered porch, and carport" loading="lazy"></div>
        <div class="bld-floor-plan-card__info"><span class="bld-floor-plan-card__label">Walkout Basement</span><span class="bld-floor-plan-card__sqft"><?php echo esc_html($lower_fl_display); ?> sq ft</span></div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- COST ESTIMATOR -->
<section class="bld-section bld-section--alt" id="cost-estimator">
  <div class="bld-container">
    <div class="bld-section__header">
      <p class="bld-section__overline">Budget Planning</p>
      <h2 class="bld-section__title">Cost to Build Estimator</h2>
      <p class="bld-section__subtitle">Get a ballpark construction estimate for the <?php echo esc_html($plan_name); ?> based on 2026 national data.</p>
    </div>
    <div class="bld-estimator">
      <div class="bld-estimator__inner">
        <div class="bld-estimator__info">
          <p class="bld-estimator__overline">Plan Your Budget</p>
          <h3 class="bld-estimator__title">What Will This A-Frame Cost to Build?</h3>
          <p class="bld-estimator__desc">This estimator uses 2026 national construction averages adjusted for region and finish quality. It covers the structure &#8212; framing, mechanicals, roofing, and finishes &#8212; but not land, permits, site work, or landscaping.</p>
          <p class="bld-estimator__desc">A-frame construction can involve specialty framing and steeper roof angles that may add 5&#8211;10% compared to a conventional gable home. Mountain lots with steep access, rock conditions, or remote locations will also affect your total cost.</p>
          <div class="bld-estimator__note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Estimates based on <?php echo esc_html($sqft_display); ?> sq ft using 2026 industry data. This is a planning tool, not a quote.</span>
          </div>
        </div>
        <div class="bld-estimator__calc">
          <div class="bld-calc-field">
            <label for="bldRegionSelect">Your Region</label>
            <select id="bldRegionSelect" class="bld-calc-select" onchange="bldUpdateEstimate()">
              <option value="southeast">Southeast (GA, NC, SC, TN, AL)</option>
              <option value="south_central">South Central (TX, OK, AR, LA)</option>
              <option value="midwest">Midwest (OH, IN, MI, IL, MO)</option>
              <option value="mountain_west" selected>Mountain West (CO, UT, MT, ID)</option>
              <option value="northeast">Northeast (NY, NJ, PA, CT, MA)</option>
              <option value="pacific_nw">Pacific Northwest (WA, OR)</option>
              <option value="west_coast">West Coast (CA)</option>
            </select>
          </div>
          <div class="bld-calc-field">
            <label>Finish Level</label>
            <div class="bld-range-wrapper">
              <input type="range" id="bldFinishLevel" min="1" max="4" step="1" value="2" onchange="bldUpdateEstimate()" oninput="bldUpdateEstimate()">
              <div class="bld-range-labels"><span>Standard</span><span>Mid-Range</span><span>Custom</span><span>Premium</span></div>
              <div class="bld-range-current" id="bldFinishLabel">Mid-Range Build</div>
            </div>
          </div>
          <div class="bld-estimate-result">
            <div class="bld-estimate-result__label">Estimated Construction Cost</div>
            <div class="bld-estimate-result__value" id="bldEstimateValue">Calculating&#8230;</div>
            <div class="bld-estimate-result__range" id="bldEstimatePerSqft"></div>
            <div class="bld-estimate-result__breakdown">
              <div><div class="bld-breakdown-item__label">Materials (est. 45%)</div><div class="bld-breakdown-item__value" id="bldMaterialsCost">&#8211;</div></div>
              <div><div class="bld-breakdown-item__label">Labor (est. 35%)</div><div class="bld-breakdown-item__value" id="bldLaborCost">&#8211;</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="bld-section" id="whats-included">
  <div class="bld-container">
    <div class="bld-section__header">
      <p class="bld-section__overline">Your Plan Set</p>
      <h2 class="bld-section__title">What&#8217;s Included</h2>
      <p class="bld-section__subtitle">Every plan set is complete and build-ready.</p>
    </div>
    <div class="bld-included-grid">
      <div class="bld-included-card"><div class="bld-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg></div><h3 class="bld-included-card__title">Elevations</h3><p class="bld-included-card__desc">Front, side, and rear at &frac14;&Prime; scale with material notes.</p></div>
      <div class="bld-included-card"><div class="bld-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><h3 class="bld-included-card__title">Floor Plans</h3><p class="bld-included-card__desc">Dimensioned plans for all three levels.</p></div>
      <div class="bld-included-card"><div class="bld-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20"/><path d="M5 20V8l7-5 7 5v12"/></svg></div><h3 class="bld-included-card__title">Foundation Plan</h3><p class="bld-included-card__desc">Walkout basement with footings and wall details.</p></div>
      <div class="bld-included-card"><div class="bld-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 22h20L12 2z"/></svg></div><h3 class="bld-included-card__title">Roof Plan</h3><p class="bld-included-card__desc">12/12 A-frame gable with ridges and drainage.</p></div>
    </div>
  </div>
</section>

<!-- RELATED PLANS -->
<?php if ( ! empty($related_plans) && is_array($related_plans) ) : ?>
<section class="bld-section bld-section--alt" id="related-plans">
  <div class="bld-container">
    <div class="bld-section__header">
      <p class="bld-section__overline">More to Explore</p>
      <h2 class="bld-section__title">Related House Plans</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--bld-space-xl);">
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
      <div style="background:var(--bld-white);border-radius:var(--bld-radius-lg);overflow:hidden;box-shadow:var(--bld-shadow-md);border:1px solid var(--bld-border);">
        <?php if ($r_img) : ?><div style="aspect-ratio:3/2;overflow:hidden;"><img src="<?php echo esc_url($r_img); ?>" alt="<?php echo esc_attr($r_name ?: get_the_title($r_id)); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div><?php endif; ?>
        <div style="padding:var(--bld-space-lg);">
          <div style="font-family:var(--bld-font-display);font-size:1.1rem;color:var(--bld-primary-dark);margin-bottom:4px;"><?php echo esc_html($r_name ?: get_the_title($r_id)); ?></div>
          <div style="font-size:0.82rem;color:var(--bld-text-light);"><?php echo esc_html(implode(' · ', $r_specs)); ?></div>
          <a href="<?php echo esc_url($r_link); ?>" style="display:inline-block;margin-top:var(--bld-space-md);font-size:0.875rem;font-weight:600;color:var(--bld-accent);">View Plan &#8594;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="bld-section<?php echo empty($related_plans) ? ' bld-section--alt' : ''; ?>" id="faq">
  <div class="bld-container">
    <div class="bld-section__header">
      <p class="bld-section__overline">Common Questions</p>
      <h2 class="bld-section__title">Frequently Asked Questions</h2>
      <p class="bld-section__subtitle">Answers about the <?php echo esc_html($plan_name); ?>, A-frame construction, and the build process.</p>
    </div>
    <div class="bld-faq-list">
      <?php if ( ! empty($faqs) && is_array($faqs) ) :
            foreach ( $faqs as $faq ) :
              $q = isset($faq['question']) ? $faq['question'] : '';
              $a = isset($faq['answer'])   ? $faq['answer']   : '';
              if ( ! $q ) continue; ?>
      <div class="bld-faq-item">
        <button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false">
          <span><?php echo esc_html($q); ?></span>
          <span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="bld-faq-answer"><div class="bld-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
      </div>
      <?php endforeach; else : ?>
      <div class="bld-faq-item"><button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false"><span>What is included in the plan set?</span><span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="bld-faq-answer"><div class="bld-faq-answer__inner">Each set includes dimensioned floor plans for all three levels, front/side/rear elevations with material notes, a foundation plan, and a roof plan. Available as PDF (<?php echo esc_html($price_fmt); ?>) or editable CAD files (<?php echo esc_html($cad_price); ?>). Electrical plans can be added for $350.</div></div></div>
      <div class="bld-faq-item"><button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false"><span>Can this A-frame plan be modified?</span><span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="bld-faq-answer"><div class="bld-faq-answer__inner">Yes. We handle all modifications in-house. Common requests include adjusting bedroom sizes, modifying the walkout basement layout, changing deck and porch configurations, or adding a full garage. Contact us for a clear scope and quote before work begins.</div></div></div>
      <div class="bld-faq-item"><button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false"><span>What type of lot does this A-frame need?</span><span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="bld-faq-answer"><div class="bld-faq-answer__inner">The plan is designed for sloping lots and works well on mountain and lakefront properties. The walkout basement takes advantage of the slope for grade-level rear access. At 78 feet wide, it requires a wider buildable area than some cabin plans &#8212; factor in your setback requirements when evaluating lot fit.</div></div></div>
      <div class="bld-faq-item"><button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false"><span>How much does it cost to build an A-frame cabin?</span><span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="bld-faq-answer"><div class="bld-faq-answer__inner">Use the cost estimator above for a ballpark. For 2,141 sq ft in 2026, expect roughly $321,150&#8211;$535,250 for standard to mid-range construction. A-frame framing can add 5&#8211;10% compared to conventional gable homes. Always get a detailed bid from a local builder.</div></div></div>
      <div class="bld-faq-item"><button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false"><span>Is the upper bedroom open to the great room?</span><span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="bld-faq-answer"><div class="bld-faq-answer__inner">Yes. One of the upper-level bedrooms features sliding barn doors that open to the vaulted great room below. When open, it creates a dramatic loft-like connection to the main living space. When closed, the room functions as a private bedroom with full privacy.</div></div></div>
      <div class="bld-faq-item"><button class="bld-faq-question" onclick="bldToggleFaq(this)" aria-expanded="false"><span>What makes this different from a traditional A-frame?</span><span class="bld-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="bld-faq-answer"><div class="bld-faq-answer__inner">This plan combines the iconic A-frame roofline with craftsman details and a practical three-level layout. Unlike most A-frames, it includes a full walkout basement with recreation room and storage, a wraparound deck, screened porch, outdoor fireplace, and over 2,100 square feet of heated space. It&#8217;s designed to work as a full-time home &#8212; not just a weekend cabin.</div></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="bld-cta-section">
  <div class="bld-container">
    <h2 class="bld-cta__title">Ready to Build Your A-Frame?</h2>
    <p class="bld-cta__text">Whether you&#8217;re ready to purchase the <?php echo esc_html($plan_name); ?> or have questions about making it work for your lot, our family is here to help.</p>
    <div class="bld-cta__buttons">
      <a href="<?php echo esc_url($buy_href); ?>" class="bld-btn-cta bld-btn-cta--primary"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>Purchase This Plan</a>
      <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="bld-btn-cta bld-btn-cta--outline">Request a Modification</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="bld-btn-cta bld-btn-cta--outline">Talk With Our Family</a>
    </div>
  </div>
</section>

<!-- MOBILE BAR -->
<div class="bld-mobile-buy-bar">
  <div class="bld-mobile-buy-bar__inner">
    <div>
      <div class="bld-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
      <div class="bld-mobile-buy-bar__price">From <?php echo esc_html($price_fmt); ?></div>
    </div>
    <a href="<?php echo esc_url($buy_href); ?>" class="bld-btn-buy">Purchase Plan</a>
  </div>
</div>

</div><!-- .bld-wrap -->

<script>
(function(){
  var BLD_SQFT = <?php echo (int)$sqft_int; ?>;
  var bldRegions = {southeast:{low:160,mid:215},south_central:{low:150,mid:205},midwest:{low:155,mid:210},mountain_west:{low:185,mid:252},northeast:{low:210,mid:285},pacific_nw:{low:195,mid:265},west_coast:{low:230,mid:320}};
  var bldFinish = {1:{f:0.85,l:'Standard Build'},2:{f:1.0,l:'Mid-Range Build'},3:{f:1.30,l:'Custom Build'},4:{f:1.65,l:'Premium Custom Build'}};
  window.bldSwapImage = function(thumb) {
    var img = document.getElementById('bldMainImage'); if (img) img.src = thumb.dataset.full;
    document.querySelectorAll('.bld-gallery__thumb').forEach(function(t){t.classList.remove('bld-active');});
    thumb.classList.add('bld-active');
  };
  window.bldSelectOption = function(fmt) {
    document.querySelectorAll('.bld-price-option').forEach(function(o){o.classList.remove('bld-selected');});
    var el = document.getElementById(fmt==='pdf'?'bldOptionPdf':'bldOptionCad'); if(el) el.classList.add('bld-selected');
  };
  window.bldUpdateEstimate = function() {
    var rEl = document.getElementById('bldRegionSelect'), fEl = document.getElementById('bldFinishLevel');
    if (!rEl||!fEl) return;
    var rd = bldRegions[rEl.value], fm = bldFinish[parseInt(fEl.value,10)];
    var lbl = document.getElementById('bldFinishLabel'); if (lbl) lbl.textContent = fm.l;
    var lo = Math.round(rd.low*fm.f), hi = Math.round(rd.mid*fm.f);
    var loT = lo*BLD_SQFT, hiT = hi*BLD_SQFT;
    var fmt = function(n){return '$'+n.toLocaleString();}, fK = function(n){return '$'+Math.round(n/1000)+'K';};
    var set = function(id,v){var e=document.getElementById(id);if(e)e.textContent=v;};
    set('bldEstimateValue', fmt(loT)+' \u2013 '+fmt(hiT));
    set('bldEstimatePerSqft', '$'+lo+' \u2013 $'+hi+' per sq ft');
    set('bldMaterialsCost', fK(loT*0.45)+' \u2013 '+fK(hiT*0.45));
    set('bldLaborCost', fK(loT*0.35)+' \u2013 '+fK(hiT*0.35));
  };
  window.bldToggleFaq = function(btn) {
    var item = btn.parentElement, isOpen = item.classList.contains('bld-open');
    document.querySelectorAll('.bld-faq-item').forEach(function(f){
      f.classList.remove('bld-open');
      var q=f.querySelector('.bld-faq-question'); if(q) q.setAttribute('aria-expanded','false');
      var a=f.querySelector('.bld-faq-answer'); if(a) a.style.maxHeight='0';
    });
    if (!isOpen) {
      item.classList.add('bld-open'); btn.setAttribute('aria-expanded','true');
      var ans = item.querySelector('.bld-faq-answer'); if(ans) ans.style.maxHeight = ans.scrollHeight+'px';
    }
  };
  if (document.readyState==='loading') { document.addEventListener('DOMContentLoaded', bldUpdateEstimate); } else { bldUpdateEstimate(); }
})();
</script>
<?php
}

genesis();
