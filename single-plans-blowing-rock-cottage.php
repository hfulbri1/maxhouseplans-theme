<?php
/**
 * Template Name: Plan - Blowing Rock Cottage
 *
 * Dedicated plan template for the Blowing Rock Cottage house plan.
 * Applies to plan slug: small-cabin-home-plan-open-living
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-19
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_blowing_rock_loop' );
add_action( 'wp_head', 'mhp_blowing_rock_styles', 20 );
add_action( 'wp_head', 'mhp_blowing_rock_schema', 5 );
add_action( 'wp_head', 'mhp_blowing_rock_fonts', 1 );

function mhp_blowing_rock_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">' . "\n";
}

function mhp_blowing_rock_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid  = get_the_ID();
    $acf  = function_exists( 'get_field' );
    $name    = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $name    = $name ?: get_the_title();
    $sqft    = $acf ? get_field( 'total_living_area', $pid ) : '2533';
    $beds    = $acf ? get_field( 'bedrooms', $pid ) : '3';
    $baths   = $acf ? get_field( 'bathrooms', $pid ) : '3';
    $stories = $acf ? get_field( 'stories', $pid ) : '2';
    $price   = $acf ? get_field( 'price', $pid ) : '1195';
    $price_num = is_numeric( $price ) ? number_format( (float) $price, 2, '.', '' ) : '1195.00';
    $image_url = get_the_post_thumbnail_url( $pid, 'full' );
    $permalink = get_permalink( $pid );

    $product = array(
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name'  => $name . ' House Plan',
        'description' => 'A build-ready small cabin home plan featuring 2,533 sq ft of heated living space, 3 bedrooms, 3 bathrooms, vaulted open living room with stone fireplace, craftsman exterior, walkout basement, and porches on all sides.',
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
            array( '@type' => 'PropertyValue', 'name' => 'Style', 'value' => 'Rustic, Mountain, Lake, Craftsman, Cottage' ),
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
        array( '@type' => 'ListItem', 'position' => 3, 'name' => 'Small House Plans', 'item' => home_url( '/home-plans/small-house-plans/' ) ),
        array( '@type' => 'ListItem', 'position' => 4, 'name' => $name . ' House Plan' ),
    ) );
    echo '<script type="application/ld+json">' . wp_json_encode( $bc, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

function mhp_blowing_rock_styles() {
?>
<style id="mhp-blowing-rock-styles">
:root {
  --brc-primary: #4A3F35; --brc-primary-light: #6B5D4F; --brc-primary-dark: #352D24;
  --brc-accent: #A0714F; --brc-accent-light: #C49574; --brc-accent-warm: #B5835C;
  --brc-cabin-green: #5B6B52; --brc-cabin-green-light: #E4E9E1;
  --brc-cream: #FBF8F3; --brc-cream-dark: #F2ECE3; --brc-warm-tan: #EDE5D8;
  --brc-bark: #3D3329; --brc-text: #33302B; --brc-text-secondary: #6E6860;
  --brc-text-light: #9D978F; --brc-white: #FFFFFF; --brc-border: #E3DCCF;
  --brc-border-light: #EDE8DF; --brc-success: #5B6B52;
  --brc-font-display: 'Libre Baskerville', Georgia, serif;
  --brc-font-body: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --brc-space-xs: 0.25rem; --brc-space-sm: 0.5rem; --brc-space-md: 1rem;
  --brc-space-lg: 1.5rem; --brc-space-xl: 2rem; --brc-space-2xl: 3rem;
  --brc-space-3xl: 4rem; --brc-space-4xl: 6rem; --brc-max-width: 1240px;
  --brc-radius-sm: 6px; --brc-radius-md: 10px; --brc-radius-lg: 16px; --brc-radius-xl: 24px;
  --brc-shadow-sm: 0 1px 3px rgba(51,48,43,0.05); --brc-shadow-md: 0 4px 16px rgba(51,48,43,0.07);
  --brc-shadow-lg: 0 8px 32px rgba(51,48,43,0.09); --brc-shadow-xl: 0 20px 60px rgba(51,48,43,0.11);
  --brc-ease-out: cubic-bezier(0.16, 1, 0.3, 1);
  --brc-transition-fast: 0.2s var(--brc-ease-out); --brc-transition-base: 0.35s var(--brc-ease-out);
  --brc-transition-slow: 0.6s var(--brc-ease-out);
}
.brc-wrap *, .brc-wrap *::before, .brc-wrap *::after { box-sizing: border-box; margin: 0; padding: 0; }
.brc-wrap { font-family: var(--brc-font-body); color: var(--brc-text); background: var(--brc-cream); line-height: 1.7; font-size: 16px; overflow-x: hidden; }
.brc-wrap img { max-width: 100%; height: auto; display: block; }
.brc-wrap a { color: inherit; text-decoration: none; }
.brc-wrap button { cursor: pointer; border: none; background: none; font-family: inherit; }
.brc-container { max-width: var(--brc-max-width); margin: 0 auto; padding: 0 var(--brc-space-xl); }
@keyframes brc-fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes brc-fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes brc-scaleIn { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }
.brc-animate-in { opacity: 0; animation: brc-fadeInUp 0.7s var(--brc-ease-out) forwards; }
.brc-breadcrumbs { padding: var(--brc-space-lg) 0 var(--brc-space-md); font-size: 0.8rem; color: var(--brc-text-light); }
.brc-breadcrumbs a { color: var(--brc-text-secondary); transition: color var(--brc-transition-fast); }
.brc-breadcrumbs a:hover { color: var(--brc-accent); }
.brc-breadcrumbs span { margin: 0 var(--brc-space-sm); opacity: 0.4; }
.brc-plan-hero { padding-bottom: var(--brc-space-3xl); }
.brc-plan-hero__grid { display: grid; grid-template-columns: 1fr 390px; gap: var(--brc-space-3xl); align-items: start; }
.brc-gallery { animation: brc-fadeIn 0.5s var(--brc-ease-out) forwards; }
.brc-gallery__main { position: relative; border-radius: var(--brc-radius-lg); overflow: hidden; background: var(--brc-warm-tan); aspect-ratio: 3/2; box-shadow: var(--brc-shadow-lg); }
.brc-gallery__main img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--brc-transition-slow); }
.brc-gallery__main:hover img { transform: scale(1.02); }
.brc-gallery__badge { position: absolute; top: var(--brc-space-lg); left: var(--brc-space-lg); background: var(--brc-cabin-green); color: var(--brc-white); font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: 5px var(--brc-space-md); border-radius: 100px; z-index: 2; display: flex; align-items: center; gap: 5px; }
.brc-gallery__badge svg { width: 13px; height: 13px; }
.brc-gallery__count { position: absolute; bottom: var(--brc-space-lg); right: var(--brc-space-lg); background: rgba(0,0,0,0.55); backdrop-filter: blur(8px); color: var(--brc-white); font-size: 0.78rem; font-weight: 500; padding: 5px var(--brc-space-md); border-radius: 100px; z-index: 2; display: flex; align-items: center; gap: 5px; }
.brc-gallery__count svg { width: 15px; height: 15px; }
.brc-gallery__thumbs { display: grid; grid-template-columns: repeat(5, 1fr); gap: var(--brc-space-sm); margin-top: var(--brc-space-sm); }
.brc-gallery__thumb { aspect-ratio: 3/2; border-radius: var(--brc-radius-sm); overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all var(--brc-transition-fast); opacity: 0.6; }
.brc-gallery__thumb:hover, .brc-gallery__thumb.brc-active { opacity: 1; border-color: var(--brc-accent); box-shadow: var(--brc-shadow-md); }
.brc-gallery__thumb img { width: 100%; height: 100%; object-fit: cover; }
.brc-purchase-card { position: sticky; top: var(--brc-space-xl); background: var(--brc-white); border-radius: var(--brc-radius-lg); box-shadow: var(--brc-shadow-lg); overflow: hidden; animation: brc-scaleIn 0.5s var(--brc-ease-out) 0.15s forwards; opacity: 0; border: 1px solid var(--brc-border-light); }
.brc-purchase-card__header { padding: var(--brc-space-xl) var(--brc-space-xl) var(--brc-space-lg); border-bottom: 1px solid var(--brc-border-light); }
.brc-purchase-card__tag { display: inline-flex; align-items: center; gap: 4px; background: var(--brc-cabin-green-light); color: var(--brc-cabin-green); font-size: 0.67rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; padding: 4px 10px; border-radius: 100px; margin-bottom: var(--brc-space-sm); }
.brc-purchase-card__plan-name { font-family: var(--brc-font-display); font-size: 1.55rem; color: var(--brc-primary-dark); line-height: 1.25; margin-bottom: 4px; }
.brc-purchase-card__style { font-size: 0.78rem; color: var(--brc-text-light); }
.brc-purchase-card__specs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; background: var(--brc-border-light); }
.brc-spec-cell { background: var(--brc-white); padding: var(--brc-space-md) var(--brc-space-lg); text-align: center; }
.brc-spec-cell__value { font-family: var(--brc-font-display); font-size: 1.25rem; color: var(--brc-primary); line-height: 1.2; }
.brc-spec-cell__label { font-size: 0.66rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--brc-text-light); margin-top: 2px; font-weight: 500; }
.brc-purchase-card__body { padding: var(--brc-space-xl); }
.brc-price-option { display: flex; align-items: center; padding: var(--brc-space-md) var(--brc-space-lg); border: 2px solid var(--brc-border); border-radius: var(--brc-radius-md); margin-bottom: var(--brc-space-sm); cursor: pointer; transition: all var(--brc-transition-fast); }
.brc-price-option:hover { border-color: var(--brc-accent-light); background: var(--brc-cream); }
.brc-price-option.brc-selected { border-color: var(--brc-accent); background: linear-gradient(135deg, rgba(160,113,79,0.04), rgba(160,113,79,0.01)); }
.brc-price-option input[type="radio"] { appearance: none; -webkit-appearance: none; width: 18px; height: 18px; border: 2px solid var(--brc-border); border-radius: 50%; margin-right: var(--brc-space-md); flex-shrink: 0; transition: all var(--brc-transition-fast); }
.brc-price-option input[type="radio"]:checked { border-color: var(--brc-accent); background: var(--brc-accent); box-shadow: inset 0 0 0 3px var(--brc-white); }
.brc-price-option__info { flex: 1; }
.brc-price-option__format { font-weight: 600; font-size: 0.87rem; }
.brc-price-option__desc { font-size: 0.72rem; color: var(--brc-text-light); margin-top: 1px; }
.brc-price-option__price { font-family: var(--brc-font-display); font-size: 1.15rem; color: var(--brc-primary); }
.brc-btn-buy { display: flex; align-items: center; justify-content: center; width: 100%; padding: 14px var(--brc-space-xl); background: var(--brc-accent); color: var(--brc-white); font-size: 0.93rem; font-weight: 600; border-radius: var(--brc-radius-md); margin-top: var(--brc-space-lg); transition: all var(--brc-transition-fast); gap: var(--brc-space-sm); box-shadow: 0 4px 18px rgba(160,113,79,0.25); text-decoration: none; }
.brc-btn-buy:hover { background: var(--brc-accent-warm); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(160,113,79,0.3); color: var(--brc-white); }
.brc-btn-buy svg { width: 18px; height: 18px; }
.brc-trust-row { display: flex; justify-content: center; gap: var(--brc-space-lg); margin-top: var(--brc-space-lg); padding-top: var(--brc-space-lg); border-top: 1px solid var(--brc-border-light); }
.brc-trust-item { display: flex; align-items: center; gap: 5px; font-size: 0.71rem; color: var(--brc-text-light); font-weight: 500; }
.brc-trust-item svg { width: 14px; height: 14px; color: var(--brc-success); flex-shrink: 0; }
.brc-purchase-card__footer { padding: var(--brc-space-md) var(--brc-space-xl) var(--brc-space-xl); text-align: center; }
.brc-purchase-card__footer a { font-size: 0.78rem; color: var(--brc-accent); font-weight: 600; }
.brc-quick-specs { background: var(--brc-primary); padding: var(--brc-space-lg) 0; margin-bottom: var(--brc-space-4xl); position: relative; overflow: hidden; }
.brc-quick-specs::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(255,255,255,0.02) 80px, rgba(255,255,255,0.02) 81px); }
.brc-quick-specs__grid { display: flex; justify-content: center; gap: var(--brc-space-3xl); position: relative; z-index: 1; flex-wrap: wrap; }
.brc-quick-spec { text-align: center; color: var(--brc-white); }
.brc-quick-spec__icon { width: 26px; height: 26px; margin: 0 auto var(--brc-space-xs); opacity: 0.55; }
.brc-quick-spec__value { font-family: var(--brc-font-display); font-size: 1.25rem; line-height: 1.2; }
.brc-quick-spec__label { font-size: 0.63rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5; font-weight: 500; margin-top: 2px; }
.brc-section { padding: var(--brc-space-4xl) 0; }
.brc-section--alt { background: var(--brc-white); }
.brc-section__header { text-align: center; margin-bottom: var(--brc-space-3xl); }
.brc-section__overline { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.14em; color: var(--brc-accent); margin-bottom: var(--brc-space-sm); }
.brc-section__title { font-family: var(--brc-font-display); font-size: 2rem; color: var(--brc-primary-dark); line-height: 1.25; margin-bottom: var(--brc-space-md); }
.brc-section__subtitle { font-size: 0.97rem; color: var(--brc-text-secondary); max-width: 600px; margin: 0 auto; line-height: 1.7; }
.brc-description-grid { display: grid; grid-template-columns: 1fr 340px; gap: var(--brc-space-3xl); align-items: start; }
.brc-description__content h2 { font-family: var(--brc-font-display); font-size: 1.8rem; color: var(--brc-primary-dark); margin-bottom: var(--brc-space-lg); line-height: 1.3; }
.brc-description__content p { color: var(--brc-text-secondary); margin-bottom: var(--brc-space-lg); font-size: 0.97rem; line-height: 1.8; }
.brc-highlights-card { background: var(--brc-white); border-radius: var(--brc-radius-lg); padding: var(--brc-space-xl); box-shadow: var(--brc-shadow-md); border: 1px solid var(--brc-border); }
.brc-highlights-card__title { font-family: var(--brc-font-display); font-size: 1.05rem; color: var(--brc-primary-dark); margin-bottom: var(--brc-space-lg); padding-bottom: var(--brc-space-sm); border-bottom: 2px solid var(--brc-accent); display: inline-block; }
.brc-highlight-item { display: flex; align-items: flex-start; gap: var(--brc-space-md); margin-bottom: var(--brc-space-lg); }
.brc-highlight-item:last-child { margin-bottom: 0; }
.brc-highlight-item__icon { width: 34px; height: 34px; background: var(--brc-cabin-green-light); border-radius: var(--brc-radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.brc-highlight-item__icon svg { width: 17px; height: 17px; color: var(--brc-cabin-green); }
.brc-highlight-item__text strong { display: block; font-size: 0.83rem; font-weight: 600; color: var(--brc-text); margin-bottom: 1px; }
.brc-highlight-item__text span { font-size: 0.76rem; color: var(--brc-text-light); }
.brc-story-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--brc-space-md); }
.brc-story-card { position: relative; border-radius: var(--brc-radius-md); overflow: hidden; aspect-ratio: 4/3; }
.brc-story-card img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--brc-transition-slow); }
.brc-story-card:hover img { transform: scale(1.05); }
.brc-story-card__overlay { position: absolute; bottom: 0; left: 0; right: 0; padding: var(--brc-space-lg); background: linear-gradient(transparent, rgba(0,0,0,0.65)); color: var(--brc-white); }
.brc-story-card__label { font-size: 0.82rem; font-weight: 600; }
.brc-story-card__detail { font-size: 0.7rem; opacity: 0.75; margin-top: 2px; }
.brc-specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--brc-space-xl); }
.brc-specs-group { background: var(--brc-cream); border-radius: var(--brc-radius-lg); padding: var(--brc-space-xl); border: 1px solid var(--brc-border); }
.brc-specs-group__title { font-family: var(--brc-font-display); font-size: 1rem; color: var(--brc-primary-dark); margin-bottom: var(--brc-space-lg); padding-bottom: var(--brc-space-sm); border-bottom: 2px solid var(--brc-warm-tan); }
.brc-spec-row { display: flex; justify-content: space-between; align-items: baseline; padding: 7px 0; border-bottom: 1px solid var(--brc-border-light); }
.brc-spec-row:last-child { border-bottom: none; }
.brc-spec-row__label { font-size: 0.82rem; color: var(--brc-text-secondary); }
.brc-spec-row__value { font-size: 0.83rem; font-weight: 600; color: var(--brc-text); text-align: right; }
.brc-floor-plans-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--brc-space-xl); }
.brc-floor-plan-card { background: var(--brc-white); border-radius: var(--brc-radius-lg); overflow: hidden; box-shadow: var(--brc-shadow-md); border: 1px solid var(--brc-border); transition: all var(--brc-transition-base); }
.brc-floor-plan-card:hover { box-shadow: var(--brc-shadow-xl); transform: translateY(-3px); }
.brc-floor-plan-card__image { padding: var(--brc-space-xl); background: var(--brc-cream); }
.brc-floor-plan-card__image img { width: 100%; border-radius: var(--brc-radius-sm); }
.brc-floor-plan-card__info { padding: var(--brc-space-lg) var(--brc-space-xl); display: flex; justify-content: space-between; align-items: center; }
.brc-floor-plan-card__label { font-weight: 700; font-size: 0.92rem; color: var(--brc-primary-dark); }
.brc-floor-plan-card__sqft { font-size: 0.8rem; color: var(--brc-text-light); font-weight: 500; }
.brc-floor-plans__wysiwyg img { max-width: 100%; border-radius: var(--brc-radius-lg); box-shadow: var(--brc-shadow-md); }
.brc-estimator { background: linear-gradient(145deg, var(--brc-bark) 0%, var(--brc-primary) 60%, #594D3E 100%); border-radius: var(--brc-radius-xl); overflow: hidden; position: relative; color: var(--brc-white); }
.brc-estimator::before { content: ''; position: absolute; top: -30%; right: -15%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(160,113,79,0.14) 0%, transparent 55%); pointer-events: none; }
.brc-estimator__inner { display: grid; grid-template-columns: 1fr 1fr; position: relative; z-index: 1; }
.brc-estimator__info { padding: var(--brc-space-3xl); }
.brc-estimator__overline { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; color: var(--brc-accent-light); margin-bottom: var(--brc-space-md); }
.brc-estimator__title { font-family: var(--brc-font-display); font-size: 1.75rem; line-height: 1.3; margin-bottom: var(--brc-space-lg); }
.brc-estimator__desc { opacity: 0.7; line-height: 1.75; margin-bottom: var(--brc-space-lg); font-size: 0.9rem; }
.brc-estimator__note { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--brc-radius-md); padding: var(--brc-space-md) var(--brc-space-lg); font-size: 0.75rem; opacity: 0.55; line-height: 1.6; display: flex; align-items: flex-start; gap: var(--brc-space-sm); }
.brc-estimator__note svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
.brc-estimator__calc { padding: var(--brc-space-3xl); background: rgba(255,255,255,0.03); border-left: 1px solid rgba(255,255,255,0.06); }
.brc-calc-field { margin-bottom: var(--brc-space-xl); }
.brc-calc-field label { display: block; font-size: 0.73rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: var(--brc-space-sm); opacity: 0.75; }
.brc-calc-select { width: 100%; padding: var(--brc-space-md) var(--brc-space-lg); background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); border-radius: var(--brc-radius-md); color: var(--brc-white); font-family: var(--brc-font-body); font-size: 0.87rem; appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ffffff' stroke-width='1.5' fill='none' opacity='0.4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; }
.brc-calc-select option { background: var(--brc-bark); color: var(--brc-white); }
.brc-range-wrapper { padding-top: var(--brc-space-xs); }
.brc-range-labels { display: flex; justify-content: space-between; margin-top: var(--brc-space-xs); font-size: 0.67rem; opacity: 0.4; }
.brc-estimator input[type="range"] { -webkit-appearance: none; appearance: none; width: 100%; height: 5px; border-radius: 3px; background: rgba(255,255,255,0.12); outline: none; }
.brc-estimator input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: var(--brc-accent); cursor: pointer; border: 3px solid var(--brc-white); box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
.brc-range-current { text-align: center; font-family: var(--brc-font-display); font-size: 0.95rem; color: var(--brc-accent-light); margin-top: var(--brc-space-xs); }
.brc-estimate-result { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--brc-radius-lg); padding: var(--brc-space-xl); text-align: center; margin-top: var(--brc-space-lg); }
.brc-estimate-result__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5; font-weight: 600; margin-bottom: var(--brc-space-sm); }
.brc-estimate-result__value { font-family: var(--brc-font-display); font-size: 1.9rem; color: var(--brc-accent-light); line-height: 1.1; margin-bottom: 4px; }
.brc-estimate-result__range { font-size: 0.78rem; opacity: 0.45; }
.brc-estimate-result__breakdown { display: grid; grid-template-columns: 1fr 1fr; gap: var(--brc-space-md); margin-top: var(--brc-space-lg); padding-top: var(--brc-space-lg); border-top: 1px solid rgba(255,255,255,0.06); }
.brc-breakdown-item__label { font-size: 0.67rem; text-transform: uppercase; opacity: 0.4; }
.brc-breakdown-item__value { font-size: 0.93rem; font-weight: 600; margin-top: 2px; }
.brc-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--brc-space-xl); }
.brc-included-card { background: var(--brc-white); border-radius: var(--brc-radius-lg); padding: var(--brc-space-xl) var(--brc-space-lg); text-align: center; border: 1px solid var(--brc-border); transition: all var(--brc-transition-base); }
.brc-included-card:hover { transform: translateY(-3px); box-shadow: var(--brc-shadow-lg); border-color: var(--brc-accent-light); }
.brc-included-card__icon { width: 50px; height: 50px; background: var(--brc-cabin-green-light); border-radius: var(--brc-radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--brc-space-lg); }
.brc-included-card__icon svg { width: 24px; height: 24px; color: var(--brc-cabin-green); }
.brc-included-card__title { font-weight: 600; font-size: 0.9rem; margin-bottom: var(--brc-space-xs); color: var(--brc-primary-dark); }
.brc-included-card__desc { font-size: 0.78rem; color: var(--brc-text-light); line-height: 1.6; }
.brc-faq-list { max-width: 780px; margin: 0 auto; }
.brc-faq-item { background: var(--brc-white); border-radius: var(--brc-radius-md); margin-bottom: var(--brc-space-sm); border: 1px solid var(--brc-border); overflow: hidden; transition: all var(--brc-transition-fast); }
.brc-faq-item.brc-open { box-shadow: var(--brc-shadow-md); border-color: var(--brc-accent-light); }
.brc-faq-question { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: var(--brc-space-lg) var(--brc-space-xl); font-size: 0.9rem; font-weight: 500; text-align: left; color: var(--brc-text); gap: var(--brc-space-md); }
.brc-faq-question:hover { color: var(--brc-accent); }
.brc-faq-question__icon { width: 22px; height: 22px; flex-shrink: 0; border-radius: 50%; background: var(--brc-cream-dark); display: flex; align-items: center; justify-content: center; transition: all var(--brc-transition-fast); }
.brc-faq-item.brc-open .brc-faq-question__icon { background: var(--brc-accent); transform: rotate(45deg); }
.brc-faq-question__icon svg { width: 12px; height: 12px; color: var(--brc-text-secondary); }
.brc-faq-item.brc-open .brc-faq-question__icon svg { color: var(--brc-white); }
.brc-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s var(--brc-ease-out); }
.brc-faq-answer__inner { padding: 0 var(--brc-space-xl) var(--brc-space-xl); font-size: 0.87rem; color: var(--brc-text-secondary); line-height: 1.8; }
.brc-cta-section { background: var(--brc-bark); padding: var(--brc-space-4xl) 0; text-align: center; color: var(--brc-white); position: relative; overflow: hidden; }
.brc-cta-section::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.02' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E") repeat; }
.brc-cta-section .brc-container { position: relative; z-index: 1; }
.brc-cta__title { font-family: var(--brc-font-display); font-size: 2rem; margin-bottom: var(--brc-space-md); }
.brc-cta__text { font-size: 0.97rem; opacity: 0.7; max-width: 520px; margin: 0 auto var(--brc-space-xl); line-height: 1.75; }
.brc-cta__buttons { display: flex; justify-content: center; gap: var(--brc-space-lg); flex-wrap: wrap; }
.brc-btn-cta { display: inline-flex; align-items: center; gap: var(--brc-space-sm); padding: var(--brc-space-md) var(--brc-space-2xl); border-radius: var(--brc-radius-md); font-weight: 600; font-size: 0.9rem; transition: all var(--brc-transition-fast); }
.brc-btn-cta--primary { background: var(--brc-accent); color: var(--brc-white); box-shadow: 0 4px 18px rgba(160,113,79,0.3); }
.brc-btn-cta--primary:hover { background: var(--brc-accent-warm); transform: translateY(-2px); }
.brc-btn-cta--outline { border: 2px solid rgba(255,255,255,0.2); color: var(--brc-white); }
.brc-btn-cta--outline:hover { border-color: var(--brc-white); background: rgba(255,255,255,0.06); }
.brc-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--brc-white); border-top: 1px solid var(--brc-border); padding: var(--brc-space-md) var(--brc-space-lg); z-index: 100; box-shadow: 0 -4px 20px rgba(0,0,0,0.08); }
.brc-mobile-buy-bar__inner { display: flex; align-items: center; justify-content: space-between; max-width: var(--brc-max-width); margin: 0 auto; }
.brc-mobile-buy-bar__name { font-weight: 600; font-size: 0.83rem; color: var(--brc-primary-dark); }
.brc-mobile-buy-bar__price { font-family: var(--brc-font-display); color: var(--brc-accent); font-size: 1rem; }
.brc-mobile-buy-bar .brc-btn-buy { width: auto; padding: var(--brc-space-sm) var(--brc-space-xl); margin-top: 0; font-size: 0.83rem; }
@media (max-width: 1024px) {
  .brc-plan-hero__grid { grid-template-columns: 1fr; }
  .brc-purchase-card { position: relative; top: 0; }
  .brc-description-grid { grid-template-columns: 1fr; }
  .brc-specs-grid { grid-template-columns: 1fr 1fr; }
  .brc-estimator__inner { grid-template-columns: 1fr; }
  .brc-included-grid { grid-template-columns: repeat(2, 1fr); }
  .brc-story-grid { grid-template-columns: repeat(2, 1fr); }
  .brc-floor-plans-grid { grid-template-columns: 1fr; }
  .brc-mobile-buy-bar { display: block; }
  .brc-wrap { padding-bottom: 80px; }
}
@media (max-width: 768px) {
  .brc-gallery__thumbs { grid-template-columns: repeat(4, 1fr); }
  .brc-section__title { font-size: 1.6rem; }
  .brc-specs-grid { grid-template-columns: 1fr; }
  .brc-included-grid { grid-template-columns: 1fr; }
  .brc-story-grid { grid-template-columns: 1fr; }
  .brc-estimator__info, .brc-estimator__calc { padding: var(--brc-space-xl); }
}
@media (max-width: 480px) {
  .brc-container { padding: 0 var(--brc-space-lg); }
  .brc-gallery__thumbs { grid-template-columns: repeat(3, 1fr); }
  .brc-quick-specs__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--brc-space-lg); }
  .brc-cta__buttons { flex-direction: column; align-items: center; }
}
</style>
<?php
}

function mhp_blowing_rock_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    $plan_name        = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft             = $acf ? get_field( 'total_living_area',   $pid ) : '2,533';
    $main_floor       = $acf ? get_field( 'main_floor',          $pid ) : '1,401';
    $upper_floor      = $acf ? get_field( 'upper_floor',         $pid ) : '';
    $lower_floor      = $acf ? get_field( 'lower_floor',         $pid ) : '1,132';
    $bedrooms         = $acf ? get_field( 'bedrooms',            $pid ) : '3';
    $bathrooms        = $acf ? get_field( 'bathrooms',           $pid ) : '3';
    $stories          = $acf ? get_field( 'stories',             $pid ) : '2';
    $width            = $acf ? get_field( 'width',               $pid ) : "49'2\"";
    $depth            = $acf ? get_field( 'depth',               $pid ) : "60'8\"";
    $garage           = $acf ? get_field( 'garage',              $pid ) : 'Golf Cart Garage';
    $style            = $acf ? get_field( 'style',               $pid ) : 'Rustic · Mountain · Lake · Craftsman';
    $outdoor          = $acf ? get_field( 'outdoor',             $pid ) : 'Front, Screen, Rear Porch + Terrace';
    $roof             = $acf ? get_field( 'roof',                $pid ) : '11/12 Gable';
    $ceiling          = $acf ? get_field( 'ceiling',             $pid ) : "9' / Vaulted";
    $exterior         = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms = $acf ? get_field( 'additional_rooms',    $pid ) : 'Rec Room, Storage';
    $other_features   = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style        = $acf ? get_field( 'lot_style',           $pid ) : 'Narrow, Sloping';
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
    if ( ! $hero_url ) $hero_url = 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/small-cabin-home-plan-open-living-floor-plan-blowing-rock-cottage-13.jpg';

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
            array( 'full' => $hero_url, 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/small-cabin-home-plan-open-living-floor-plan-blowing-rock-cottage-13-300x200.jpg', 'alt' => 'Blowing Rock front exterior' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/small-cabin-home-plan-open-living-floor-plan-blowing-rock-cottage-10.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/small-cabin-home-plan-open-living-floor-plan-blowing-rock-cottage-10-300x200.jpg', 'alt' => 'Side porch view' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-vaulted-open-living-room.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-vaulted-open-living-room-300x167.jpg', 'alt' => 'Vaulted open living room' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-open-kitchen.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-open-kitchen-300x200.jpg', 'alt' => 'Open kitchen' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/small-cabin-house-plan-blowing-rock-sage-green-13.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/small-cabin-house-plan-blowing-rock-sage-green-13-300x200.jpg', 'alt' => 'Sage green exterior option' ),
        );
    }

    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft' ), '', $v ); };
    $sqft_display    = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display = $clean($main_floor) > 0 ? number_format( $clean($main_floor) ) : $main_floor;
    $lower_fl_display = $lower_floor ?: '1,132';
    $sqft_int = $clean($sqft) > 100 ? $clean($sqft) : 2533;
?>
<div class="brc-wrap">

<!-- BREADCRUMBS -->
<nav class="brc-breadcrumbs" aria-label="Breadcrumb">
  <div class="brc-container">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/home-plans/small-house-plans/')); ?>">Small House Plans</a><span>&#8250;</span>
    <strong><?php echo esc_html($plan_name); ?></strong>
  </div>
</nav>

<!-- HERO -->
<section class="brc-plan-hero">
  <div class="brc-container">
    <div class="brc-plan-hero__grid">
      <div class="brc-gallery brc-animate-in">
        <div class="brc-gallery__main" id="brcGalleryMain">
          <span class="brc-gallery__badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            Small Cabin Plan
          </span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> house plan — rustic small cabin with craftsman details, stone columns, covered front porch" id="brcMainImage" fetchpriority="high">
          <span class="brc-gallery__count">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            <?php echo count($gallery_images); ?>+ Photos
          </span>
        </div>
        <div class="brc-gallery__thumbs">
          <?php foreach ( $gallery_images as $idx => $img ) : ?>
          <div class="brc-gallery__thumb<?php echo $idx === 0 ? ' brc-active' : ''; ?>" onclick="brcSwapImage(this)" data-full="<?php echo esc_url($img['full']); ?>">
            <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <aside class="brc-purchase-card" aria-label="Plan purchase options">
        <div class="brc-purchase-card__header">
          <div class="brc-purchase-card__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            Fits Narrow Lots
          </div>
          <h1 class="brc-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
          <p class="brc-purchase-card__style"><?php echo esc_html($style); ?></p>
        </div>
        <div class="brc-purchase-card__specs">
          <div class="brc-spec-cell"><div class="brc-spec-cell__value"><?php echo esc_html($sqft_display); ?></div><div class="brc-spec-cell__label">Sq Ft</div></div>
          <div class="brc-spec-cell"><div class="brc-spec-cell__value"><?php echo esc_html($bedrooms); ?></div><div class="brc-spec-cell__label">Bedrooms</div></div>
          <div class="brc-spec-cell"><div class="brc-spec-cell__value"><?php echo esc_html($bathrooms); ?></div><div class="brc-spec-cell__label">Bathrooms</div></div>
          <div class="brc-spec-cell"><div class="brc-spec-cell__value"><?php echo esc_html($stories); ?></div><div class="brc-spec-cell__label">Stories</div></div>
        </div>
        <div class="brc-purchase-card__body">
          <label class="brc-price-option brc-selected" id="brcOptionPdf">
            <input type="radio" name="brc_plan_format" value="pdf" checked onchange="brcSelectOption('pdf')">
            <div class="brc-price-option__info"><div class="brc-price-option__format">PDF Plan Set</div><div class="brc-price-option__desc">Print-ready digital plans</div></div>
            <div class="brc-price-option__price"><?php echo esc_html($price_fmt); ?></div>
          </label>
          <label class="brc-price-option" id="brcOptionCad">
            <input type="radio" name="brc_plan_format" value="cad" onchange="brcSelectOption('cad')">
            <div class="brc-price-option__info"><div class="brc-price-option__format">CAD File</div><div class="brc-price-option__desc">Editable for your builder</div></div>
            <div class="brc-price-option__price"><?php echo esc_html($cad_price); ?></div>
          </label>
          <a href="<?php echo esc_url($buy_href); ?>" class="brc-btn-buy" id="brcBuyButton">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="m16 10-4 4-4-4"/></svg>
            Purchase This Plan
          </a>
          <div class="brc-trust-row">
            <span class="brc-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Secure Checkout</span>
            <span class="brc-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Instant Download</span>
          </div>
        </div>
        <div class="brc-purchase-card__footer">
          <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>">Need changes? Request a modification &#8594;</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- QUICK SPECS -->
<div class="brc-quick-specs">
  <div class="brc-container">
    <div class="brc-quick-specs__grid">
      <div class="brc-quick-spec"><svg class="brc-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg><div class="brc-quick-spec__value"><?php echo esc_html($sqft_display); ?> sq ft</div><div class="brc-quick-spec__label">Heated Area</div></div>
      <div class="brc-quick-spec"><svg class="brc-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg><div class="brc-quick-spec__value"><?php echo esc_html($width); ?> &times; <?php echo esc_html($depth); ?></div><div class="brc-quick-spec__label">Footprint</div></div>
      <div class="brc-quick-spec"><svg class="brc-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M4 20V8l8-6 8 6v12"/></svg><div class="brc-quick-spec__value">Main: <?php echo esc_html($main_fl_display); ?></div><div class="brc-quick-spec__label">Main Floor Sq Ft</div></div>
      <div class="brc-quick-spec"><svg class="brc-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg><div class="brc-quick-spec__value">Lower: <?php echo esc_html($lower_fl_display); ?></div><div class="brc-quick-spec__label">Walkout Basement</div></div>
      <div class="brc-quick-spec"><svg class="brc-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><div class="brc-quick-spec__value">From <?php echo esc_html($price_fmt); ?></div><div class="brc-quick-spec__label">Plan Price</div></div>
    </div>
  </div>
</div>

<!-- PLAN DESCRIPTION -->
<section class="brc-section" id="description">
  <div class="brc-container">
    <div class="brc-description-grid">
      <div class="brc-description__content">
        <?php if ( $plan_description ) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <h2>A Small Cabin That Lives Bigger Than Its Footprint</h2>
          <p>The <?php echo esc_html($plan_name); ?> is designed for people who want the character of a mountain cabin without giving up the room to actually live in it. At just under 50 feet wide, this plan fits comfortably on narrow and smaller lots &#8212; but its 2,533 square feet of heated space across two levels gives you a genuine home, not a compromise.</p>
          <p>The main floor is built around an open vaulted living room that connects directly to the kitchen and dining area. A double-sided stone fireplace anchors the space &#8212; visible from both the living room and the covered side screen porch, so you get firelight indoors and out. The master suite sits on this level with a vaulted ceiling, a bathroom with room for a clawfoot tub and walk-in shower, and direct porch access.</p>
          <p>Walk outside and you&#8217;ll find porches on nearly every side of the home: a covered front porch, a screened side porch, a rear porch, and a terrace patio at the walkout level. For a mountain or lake property, this means you&#8217;re never more than a few steps from the outdoors.</p>
          <p>The walkout basement adds two more bedrooms, each with its own bathroom, plus a recreation room with its own stone fireplace. A mechanical/storage room and golf cart garage round out the lower level. This natural separation between the main-level master and the lower bedrooms makes the plan well suited for families, guests, or rental use.</p>
          <p>Exterior framing supports 2x4 or 2x6 construction. The 11/12 gable roof pitch, craftsman-style columns, and rustic material details give the home its cabin character. Ceiling heights are 9 feet on the main level (vaulted in the living room and master) and 10 feet in the walkout basement.</p>
        <?php endif; ?>
      </div>

      <div class="brc-highlights-card">
        <h3 class="brc-highlights-card__title">Plan Highlights</h3>
        <div class="brc-highlight-item">
          <div class="brc-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
          <div class="brc-highlight-item__text"><strong>Vaulted Open Living</strong><span>Kitchen, dining, and living connected under one vaulted ceiling</span></div>
        </div>
        <div class="brc-highlight-item">
          <div class="brc-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
          <div class="brc-highlight-item__text"><strong>Double-Sided Fireplace</strong><span>Stone fireplace shared between living room and screen porch</span></div>
        </div>
        <div class="brc-highlight-item">
          <div class="brc-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
          <div class="brc-highlight-item__text"><strong>Walkout Basement</strong><span>1,132 sq ft lower level with rec room and 2 bedrooms</span></div>
        </div>
        <div class="brc-highlight-item">
          <div class="brc-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg></div>
          <div class="brc-highlight-item__text"><strong>Porches on Every Side</strong><span>Front, screened, rear, plus lower terrace patio</span></div>
        </div>
        <div class="brc-highlight-item">
          <div class="brc-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div>
          <div class="brc-highlight-item__text"><strong>Narrow Lot Friendly</strong><span>Under 50 feet wide &#8212; fits lots as narrow as 50&#8217;</span></div>
        </div>
        <div class="brc-highlight-item">
          <div class="brc-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
          <div class="brc-highlight-item__text"><strong>Master on Main</strong><span>Vaulted ceiling, clawfoot tub, walk-in shower</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- VISUAL STORY -->
<section class="brc-section brc-section--alt" id="interior">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">Step Inside</p>
      <h2 class="brc-section__title">Inside the <?php echo esc_html($plan_name); ?></h2>
      <p class="brc-section__subtitle">This plan has been built &#8212; here&#8217;s what the finished home looks and feels like, room by room.</p>
    </div>
    <div class="brc-story-grid">
      <div class="brc-story-card"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-vaulted-open-living-room.jpg" alt="Vaulted open living room with exposed beams and stone fireplace" loading="lazy"><div class="brc-story-card__overlay"><div class="brc-story-card__label">Vaulted Great Room</div><div class="brc-story-card__detail">Open to kitchen &middot; Stone fireplace &middot; Exposed beams</div></div></div>
      <div class="brc-story-card"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-open-kitchen.jpg" alt="Open kitchen with wood cabinets and farmhouse style" loading="lazy"><div class="brc-story-card__overlay"><div class="brc-story-card__label">Open Kitchen</div><div class="brc-story-card__detail">Connected to living and dining &middot; Natural light</div></div></div>
      <div class="brc-story-card"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-vaulted-living-room.jpg" alt="Vaulted living room full height" loading="lazy"><div class="brc-story-card__overlay"><div class="brc-story-card__label">Living Room Volume</div><div class="brc-story-card__detail">Full vaulted ceiling height</div></div></div>
      <div class="brc-story-card"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-master-bedroom-vaulted.jpg" alt="Vaulted master bedroom with wood ceiling and rustic details" loading="lazy"><div class="brc-story-card__overlay"><div class="brc-story-card__label">Master Bedroom</div><div class="brc-story-card__detail">Vaulted ceiling &middot; Main level &middot; Porch access</div></div></div>
      <div class="brc-story-card"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-cabin-home-plan-clawfoot-tun.jpg" alt="Master bathroom with clawfoot tub" loading="lazy"><div class="brc-story-card__overlay"><div class="brc-story-card__label">Master Bath</div><div class="brc-story-card__detail">Clawfoot tub &middot; Walk-in shower</div></div></div>
      <div class="brc-story-card"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/small-rustic-cabin-home-plan-fireplace.jpg" alt="Double-sided stone fireplace between living room and screen porch" loading="lazy"><div class="brc-story-card__overlay"><div class="brc-story-card__label">Double-Sided Fireplace</div><div class="brc-story-card__detail">Stone &middot; Shared with screen porch</div></div></div>
    </div>
  </div>
</section>

<!-- SPECS -->
<section class="brc-section" id="specs">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">Specifications</p>
      <h2 class="brc-section__title">Full Plan Details</h2>
      <p class="brc-section__subtitle">Everything you and your builder need to evaluate this plan for your lot and budget.</p>
    </div>
    <div class="brc-specs-grid">
      <div class="brc-specs-group">
        <h3 class="brc-specs-group__title">Living Area</h3>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Main Floor</span><span class="brc-spec-row__value"><?php echo esc_html($main_fl_display); ?> sq ft</span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Lower Floor</span><span class="brc-spec-row__value"><?php echo esc_html($lower_fl_display); ?> sq ft</span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Upper Floor</span><span class="brc-spec-row__value"><?php echo $upper_floor ? esc_html($upper_floor) : 'None'; ?></span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Total Heated</span><span class="brc-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Width</span><span class="brc-spec-row__value"><?php echo esc_html($width); ?></span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Depth</span><span class="brc-spec-row__value"><?php echo esc_html($depth); ?></span></div>
      </div>
      <div class="brc-specs-group">
        <h3 class="brc-specs-group__title">House Features</h3>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Bedrooms</span><span class="brc-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Bathrooms</span><span class="brc-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Stories</span><span class="brc-spec-row__value"><?php echo esc_html($stories); ?></span></div>
        <?php if ( $additional_rooms ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Additional Rooms</span><span class="brc-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div><?php endif; ?>
        <?php if ( $garage ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Garage</span><span class="brc-spec-row__value"><?php echo esc_html($garage); ?></span></div><?php endif; ?>
        <?php if ( $outdoor ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Outdoor Spaces</span><span class="brc-spec-row__value"><?php echo esc_html($outdoor); ?></span></div><?php endif; ?>
        <?php if ( $other_features ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Other Features</span><span class="brc-spec-row__value"><?php echo esc_html($other_features); ?></span></div><?php endif; ?>
      </div>
      <div class="brc-specs-group">
        <h3 class="brc-specs-group__title">Construction</h3>
        <?php if ( $roof ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Roof Pitch</span><span class="brc-spec-row__value"><?php echo esc_html($roof); ?></span></div><?php endif; ?>
        <?php if ( $exterior ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Exterior Framing</span><span class="brc-spec-row__value"><?php echo esc_html($exterior); ?></span></div><?php endif; ?>
        <?php if ( $ceiling ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Ceiling Height</span><span class="brc-spec-row__value"><?php echo esc_html($ceiling); ?></span></div><?php endif; ?>
        <div class="brc-spec-row"><span class="brc-spec-row__label">Foundation</span><span class="brc-spec-row__value">Walkout Basement</span></div>
        <?php if ( $lot_style ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Lot Style</span><span class="brc-spec-row__value"><?php echo esc_html($lot_style); ?></span></div><?php endif; ?>
        <?php if ( $style ) : ?><div class="brc-spec-row"><span class="brc-spec-row__label">Home Style</span><span class="brc-spec-row__value"><?php echo esc_html($style); ?></span></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- FLOOR PLANS -->
<section class="brc-section brc-section--alt" id="floorplans">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">Floor Plans</p>
      <h2 class="brc-section__title">Explore Both Levels</h2>
      <p class="brc-section__subtitle">Main level living with master suite, two bedrooms and recreation room on the walkout lower level.</p>
    </div>
    <?php if ( $floor_plans_wysiwyg ) : ?>
      <div class="brc-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans_wysiwyg); ?></div>
    <?php else : ?>
    <div class="brc-floor-plans-grid">
      <div class="brc-floor-plan-card">
        <div class="brc-floor-plan-card__image"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/Blowing-Rock-Cottage-Main-Level-floor-plan-724x1024.jpg" alt="<?php echo esc_attr($plan_name); ?> main floor plan — 1,401 sq ft" loading="lazy"></div>
        <div class="brc-floor-plan-card__info"><span class="brc-floor-plan-card__label">Main Floor</span><span class="brc-floor-plan-card__sqft"><?php echo esc_html($main_fl_display); ?> sq ft</span></div>
      </div>
      <div class="brc-floor-plan-card">
        <div class="brc-floor-plan-card__image"><img src="https://www.maxhouseplans.com/wp-content/uploads/2016/12/Blowing-Rock-Cottage-Lower-Level-floor-plan-764x1024.jpg" alt="<?php echo esc_attr($plan_name); ?> lower level floor plan — 1,132 sq ft walkout basement" loading="lazy"></div>
        <div class="brc-floor-plan-card__info"><span class="brc-floor-plan-card__label">Walkout Lower Level</span><span class="brc-floor-plan-card__sqft"><?php echo esc_html($lower_fl_display); ?> sq ft</span></div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- COST ESTIMATOR -->
<section class="brc-section" id="cost-estimator">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">Budget Planning</p>
      <h2 class="brc-section__title">Cost to Build Estimator</h2>
      <p class="brc-section__subtitle">Get a ballpark construction estimate for the <?php echo esc_html($plan_name); ?> based on 2026 national data.</p>
    </div>
    <div class="brc-estimator">
      <div class="brc-estimator__inner">
        <div class="brc-estimator__info">
          <p class="brc-estimator__overline">Plan Your Budget</p>
          <h3 class="brc-estimator__title">What Will This Cabin Cost to Build?</h3>
          <p class="brc-estimator__desc">This estimator uses 2026 national construction averages adjusted for region and finish quality. It covers the structure itself &#8212; framing, mechanicals, roofing, and finishes &#8212; but not land, permits, site work, or landscaping.</p>
          <p class="brc-estimator__desc">Because this plan uses a walkout basement on a sloping lot, foundation and site work costs can vary more than a flat-lot build. Steep access roads, rock excavation, and retaining walls are all factors your builder can assess for your specific property.</p>
          <div class="brc-estimator__note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Estimates based on <?php echo esc_html($sqft_display); ?> sq ft using 2026 industry data. This is a planning tool, not a quote. Always get a professional bid.</span>
          </div>
        </div>
        <div class="brc-estimator__calc">
          <div class="brc-calc-field">
            <label for="brcRegionSelect">Your Region</label>
            <select id="brcRegionSelect" class="brc-calc-select" onchange="brcUpdateEstimate()">
              <option value="southeast">Southeast (GA, NC, SC, TN, AL)</option>
              <option value="south_central">South Central (TX, OK, AR, LA)</option>
              <option value="midwest">Midwest (OH, IN, MI, IL, MO)</option>
              <option value="mountain_west">Mountain West (CO, UT, MT, ID)</option>
              <option value="northeast">Northeast (NY, NJ, PA, CT, MA)</option>
              <option value="pacific_nw">Pacific Northwest (WA, OR)</option>
              <option value="west_coast">West Coast (CA)</option>
            </select>
          </div>
          <div class="brc-calc-field">
            <label>Finish Level</label>
            <div class="brc-range-wrapper">
              <input type="range" id="brcFinishLevel" min="1" max="4" step="1" value="2" onchange="brcUpdateEstimate()" oninput="brcUpdateEstimate()">
              <div class="brc-range-labels"><span>Standard</span><span>Mid-Range</span><span>Custom</span><span>Premium</span></div>
              <div class="brc-range-current" id="brcFinishLabel">Mid-Range Build</div>
            </div>
          </div>
          <div class="brc-estimate-result">
            <div class="brc-estimate-result__label">Estimated Construction Cost</div>
            <div class="brc-estimate-result__value" id="brcEstimateValue">Calculating&#8230;</div>
            <div class="brc-estimate-result__range" id="brcEstimatePerSqft"></div>
            <div class="brc-estimate-result__breakdown">
              <div><div class="brc-breakdown-item__label">Materials (est. 45%)</div><div class="brc-breakdown-item__value" id="brcMaterialsCost">&#8211;</div></div>
              <div><div class="brc-breakdown-item__label">Labor (est. 35%)</div><div class="brc-breakdown-item__value" id="brcLaborCost">&#8211;</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="brc-section brc-section--alt" id="whats-included">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">Your Plan Set</p>
      <h2 class="brc-section__title">What&#8217;s Included</h2>
      <p class="brc-section__subtitle">Every plan set is complete and build-ready.</p>
    </div>
    <div class="brc-included-grid">
      <div class="brc-included-card"><div class="brc-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg></div><h3 class="brc-included-card__title">Elevations</h3><p class="brc-included-card__desc">Front, side, and rear at &frac14;&Prime; scale with material notes.</p></div>
      <div class="brc-included-card"><div class="brc-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><h3 class="brc-included-card__title">Floor Plans</h3><p class="brc-included-card__desc">Dimensioned plans for both main and lower levels.</p></div>
      <div class="brc-included-card"><div class="brc-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20"/><path d="M5 20V8l7-5 7 5v12"/></svg></div><h3 class="brc-included-card__title">Foundation Plan</h3><p class="brc-included-card__desc">Walkout basement foundation with footings and wall details.</p></div>
      <div class="brc-included-card"><div class="brc-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7"/><path d="M3 9v0l9 4 9-4"/></svg></div><h3 class="brc-included-card__title">Roof Plan</h3><p class="brc-included-card__desc">11/12 gable roof with pitches, ridges, and drainage.</p></div>
    </div>
  </div>
</section>

<!-- RELATED PLANS -->
<?php if ( ! empty($related_plans) && is_array($related_plans) ) : ?>
<section class="brc-section" id="related-plans">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">More to Explore</p>
      <h2 class="brc-section__title">Related House Plans</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--brc-space-xl);">
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
      <div style="background:var(--brc-white);border-radius:var(--brc-radius-lg);overflow:hidden;box-shadow:var(--brc-shadow-md);border:1px solid var(--brc-border);">
        <?php if ($r_img) : ?><div style="aspect-ratio:3/2;overflow:hidden;"><img src="<?php echo esc_url($r_img); ?>" alt="<?php echo esc_attr($r_name ?: get_the_title($r_id)); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div><?php endif; ?>
        <div style="padding:var(--brc-space-lg);">
          <div style="font-family:var(--brc-font-display);font-size:1.1rem;color:var(--brc-primary-dark);margin-bottom:4px;"><?php echo esc_html($r_name ?: get_the_title($r_id)); ?></div>
          <div style="font-size:0.82rem;color:var(--brc-text-light);"><?php echo esc_html(implode(' · ', $r_specs)); ?></div>
          <a href="<?php echo esc_url($r_link); ?>" style="display:inline-block;margin-top:var(--brc-space-md);font-size:0.875rem;font-weight:600;color:var(--brc-accent);">View Plan &#8594;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="brc-section<?php echo empty($related_plans) ? ' brc-section--alt' : ''; ?>" id="faq">
  <div class="brc-container">
    <div class="brc-section__header">
      <p class="brc-section__overline">Common Questions</p>
      <h2 class="brc-section__title">Frequently Asked Questions</h2>
      <p class="brc-section__subtitle">Answers about the <?php echo esc_html($plan_name); ?>, modifications, lot requirements, and the build process.</p>
    </div>
    <div class="brc-faq-list">
      <?php if ( ! empty($faqs) && is_array($faqs) ) :
            foreach ( $faqs as $faq ) :
              $q = isset($faq['question']) ? $faq['question'] : '';
              $a = isset($faq['answer'])   ? $faq['answer']   : '';
              if ( ! $q ) continue; ?>
      <div class="brc-faq-item">
        <button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false">
          <span><?php echo esc_html($q); ?></span>
          <span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="brc-faq-answer"><div class="brc-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
      </div>
      <?php endforeach; else : ?>
      <div class="brc-faq-item"><button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false"><span>What is included in the plan set?</span><span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="brc-faq-answer"><div class="brc-faq-answer__inner">Each set includes dimensioned floor plans for both levels, front/side/rear elevations with material notes, a foundation plan, and a roof plan. Available as PDF (<?php echo esc_html($price_fmt); ?>) or editable CAD files (<?php echo esc_html($cad_price); ?>). Electrical plans are available as a $350 add-on.</div></div></div>
      <div class="brc-faq-item"><button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false"><span>Can this cabin plan be modified?</span><span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="brc-faq-answer"><div class="brc-faq-answer__inner">Yes. We handle all modifications in-house. Common requests include adjusting the basement layout, changing porch configurations, adding a full garage, or modifying bedroom sizes. Contact us directly and we&#8217;ll provide a clear scope and price before any work starts.</div></div></div>
      <div class="brc-faq-item"><button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false"><span>What type of lot does this plan need?</span><span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="brc-faq-answer"><div class="brc-faq-answer__inner">The plan is designed for sloping lots and works well on narrow lots &#8212; under 50 feet wide. The walkout basement takes advantage of the slope. It&#8217;s well suited for mountain and lakefront properties.</div></div></div>
      <div class="brc-faq-item"><button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false"><span>How much does it cost to build this cabin plan?</span><span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="brc-faq-answer"><div class="brc-faq-answer__inner">Use the cost estimator above for a ballpark. For a 2,533 sq ft home in 2026, expect roughly $380,000&#8211;$633,250 for standard to mid-range construction. Always get a line-item bid from a local builder.</div></div></div>
      <div class="brc-faq-item"><button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false"><span>Is this plan suitable as a vacation rental?</span><span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="brc-faq-answer"><div class="brc-faq-answer__inner">Yes. The plan works well as a vacation home or short-term rental. The walkout basement with its own bedrooms, bathrooms, and recreation room provides natural separation between owner and guest space. The porches and cabin character add significant rental appeal in mountain and lake markets.</div></div></div>
      <div class="brc-faq-item"><button class="brc-faq-question" onclick="brcToggleFaq(this)" aria-expanded="false"><span>Does this plan include a garage?</span><span class="brc-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="brc-faq-answer"><div class="brc-faq-answer__inner">The standard plan includes a golf cart garage on the lower level. If you need a full-size attached or detached garage, we can add that as a modification.</div></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="brc-cta-section">
  <div class="brc-container">
    <h2 class="brc-cta__title">Ready to Build Your Mountain Cabin?</h2>
    <p class="brc-cta__text">Whether you&#8217;re ready to purchase the <?php echo esc_html($plan_name); ?> or want to talk through modifications, our family is here to help.</p>
    <div class="brc-cta__buttons">
      <a href="<?php echo esc_url($buy_href); ?>" class="brc-btn-cta brc-btn-cta--primary"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>Purchase This Plan</a>
      <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="brc-btn-cta brc-btn-cta--outline">Request a Modification</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="brc-btn-cta brc-btn-cta--outline">Talk With Our Family</a>
    </div>
  </div>
</section>

<!-- MOBILE BAR -->
<div class="brc-mobile-buy-bar">
  <div class="brc-mobile-buy-bar__inner">
    <div>
      <div class="brc-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
      <div class="brc-mobile-buy-bar__price">From <?php echo esc_html($price_fmt); ?></div>
    </div>
    <a href="<?php echo esc_url($buy_href); ?>" class="brc-btn-buy">Purchase Plan</a>
  </div>
</div>

</div><!-- .brc-wrap -->

<script>
(function(){
  var BRC_SQFT = <?php echo (int)$sqft_int; ?>;
  var brcRegions = {southeast:{low:155,mid:210},south_central:{low:145,mid:200},midwest:{low:150,mid:205},mountain_west:{low:180,mid:245},northeast:{low:205,mid:280},pacific_nw:{low:190,mid:260},west_coast:{low:225,mid:315}};
  var brcFinish = {1:{f:0.85,l:'Standard Build'},2:{f:1.0,l:'Mid-Range Build'},3:{f:1.30,l:'Custom Build'},4:{f:1.65,l:'Premium Custom Build'}};
  window.brcSwapImage = function(thumb) {
    var img = document.getElementById('brcMainImage'); if (img) img.src = thumb.dataset.full;
    document.querySelectorAll('.brc-gallery__thumb').forEach(function(t){t.classList.remove('brc-active');});
    thumb.classList.add('brc-active');
  };
  window.brcSelectOption = function(fmt) {
    document.querySelectorAll('.brc-price-option').forEach(function(o){o.classList.remove('brc-selected');});
    var el = document.getElementById(fmt==='pdf'?'brcOptionPdf':'brcOptionCad'); if(el) el.classList.add('brc-selected');
  };
  window.brcUpdateEstimate = function() {
    var rEl = document.getElementById('brcRegionSelect'), fEl = document.getElementById('brcFinishLevel');
    if (!rEl||!fEl) return;
    var rd = brcRegions[rEl.value], fm = brcFinish[parseInt(fEl.value,10)];
    var lbl = document.getElementById('brcFinishLabel'); if (lbl) lbl.textContent = fm.l;
    var lo = Math.round(rd.low*fm.f), hi = Math.round(rd.mid*fm.f);
    var loT = lo*BRC_SQFT, hiT = hi*BRC_SQFT;
    var fmt = function(n){return '$'+n.toLocaleString();}, fK = function(n){return '$'+Math.round(n/1000)+'K';};
    var set = function(id,v){var e=document.getElementById(id);if(e)e.textContent=v;};
    set('brcEstimateValue', fmt(loT)+' \u2013 '+fmt(hiT));
    set('brcEstimatePerSqft', '$'+lo+' \u2013 $'+hi+' per sq ft');
    set('brcMaterialsCost', fK(loT*0.45)+' \u2013 '+fK(hiT*0.45));
    set('brcLaborCost', fK(loT*0.35)+' \u2013 '+fK(hiT*0.35));
  };
  window.brcToggleFaq = function(btn) {
    var item = btn.parentElement, isOpen = item.classList.contains('brc-open');
    document.querySelectorAll('.brc-faq-item').forEach(function(f){
      f.classList.remove('brc-open');
      var q=f.querySelector('.brc-faq-question'); if(q) q.setAttribute('aria-expanded','false');
      var a=f.querySelector('.brc-faq-answer'); if(a) a.style.maxHeight='0';
    });
    if (!isOpen) {
      item.classList.add('brc-open'); btn.setAttribute('aria-expanded','true');
      var ans = item.querySelector('.brc-faq-answer'); if(ans) ans.style.maxHeight = ans.scrollHeight+'px';
    }
  };
  if (document.readyState==='loading') { document.addEventListener('DOMContentLoaded', brcUpdateEstimate); } else { brcUpdateEstimate(); }
})();
</script>
<?php
}

genesis();
