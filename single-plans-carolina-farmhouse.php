<?php
/**
 * Template Name: Plan - Carolina Farmhouse
 *
 * Dedicated plan template for the Carolina Farmhouse house plan.
 * Applies to plan slug: carolina-farmhouse
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-19
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_carolina_loop' );
add_action( 'wp_head', 'mhp_carolina_styles', 20 );
add_action( 'wp_head', 'mhp_carolina_schema', 5 );
add_action( 'wp_head', 'mhp_carolina_fonts', 1 );

function mhp_carolina_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">' . "\n";
}

function mhp_carolina_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );
    $name    = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $name    = $name ?: get_the_title();
    $sqft    = $acf ? get_field( 'total_living_area', $pid ) : '2589';
    $beds    = $acf ? get_field( 'bedrooms', $pid ) : '4';
    $baths   = $acf ? get_field( 'bathrooms', $pid ) : '3.5';
    $stories = $acf ? get_field( 'stories', $pid ) : '3';
    $price   = $acf ? get_field( 'price', $pid ) : '1195';
    $price_num = is_numeric( $price ) ? number_format( (float) $price, 2, '.', '' ) : '1195.00';
    $image_url = get_the_post_thumbnail_url( $pid, 'full' );
    $permalink = get_permalink( $pid );

    $product = array(
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name' => $name . ' House Plan',
        'description' => 'A 4-bedroom modern farmhouse house plan with wraparound porch, open living, master bedroom on main level, walkout basement option, and 2-car garage. Designed by Max Fulbright Designs.',
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
            array( '@type' => 'PropertyValue', 'name' => 'Style', 'value' => 'Modern Farmhouse, Southern Living' ),
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
        array( '@type' => 'ListItem', 'position' => 3, 'name' => $name . ' House Plan' ),
    ) );
    echo '<script type="application/ld+json">' . wp_json_encode( $bc, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

function mhp_carolina_styles() {
?>
<style id="mhp-carolina-styles">
:root {
  --cf-cream: #F7F3EE; --cf-warm-white: #FDFAF6; --cf-slate: #2C3035;
  --cf-slate-mid: #4A5059; --cf-slate-light: #7A8390;
  --cf-sage: #6B7C69; --cf-sage-light: #EBF0E8;
  --cf-rust: #B85C38; --cf-rust-dark: #93431F;
  --cf-tan: #C9A97A; --cf-tan-light: #F0E6D6;
  --cf-border: #DDD6CC; --cf-shadow-soft: 0 4px 24px rgba(44,48,53,0.08);
  --cf-shadow-card: 0 2px 12px rgba(44,48,53,0.06);
  --cf-radius: 6px; --cf-radius-lg: 12px;
  --cf-font-display: 'Playfair Display', Georgia, serif;
  --cf-font-body: 'Jost', sans-serif;
  --cf-max-w: 1240px; --cf-transition: 0.22s ease;
  --cf-space-xs: 0.25rem; --cf-space-sm: 0.5rem; --cf-space-md: 1rem;
  --cf-space-lg: 1.5rem; --cf-space-xl: 2rem; --cf-space-2xl: 3rem;
  --cf-space-3xl: 4rem; --cf-space-4xl: 6rem;
}
.cf-wrap *, .cf-wrap *::before, .cf-wrap *::after { box-sizing: border-box; margin: 0; padding: 0; }
.cf-wrap { font-family: var(--cf-font-body); background: var(--cf-warm-white); color: var(--cf-slate); line-height: 1.65; -webkit-font-smoothing: antialiased; font-size: 16px; }
.cf-wrap img { display: block; max-width: 100%; height: auto; }
.cf-wrap a { color: inherit; text-decoration: none; }
.cf-container { max-width: var(--cf-max-w); margin: 0 auto; padding: 0 24px; }
@keyframes cf-fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
.cf-breadcrumb { background: var(--cf-cream); border-bottom: 1px solid var(--cf-border); padding: 10px 0; }
.cf-breadcrumb__list { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.cf-breadcrumb__list li { font-size: 0.8rem; color: var(--cf-slate-light); font-weight: 400; }
.cf-breadcrumb__list li a { color: var(--cf-sage); }
.cf-breadcrumb__list li a:hover { text-decoration: underline; }
.cf-breadcrumb__list li::after { content: '/'; margin-left: 6px; }
.cf-breadcrumb__list li:last-child::after { display: none; }
.cf-breadcrumb__list li:last-child { color: var(--cf-slate); font-weight: 500; }
.cf-plan-hero { background: var(--cf-cream); padding: 48px 0 0; }
.cf-plan-hero__grid { display: grid; grid-template-columns: 1fr 400px; gap: 48px; align-items: start; }
.cf-plan-title-block { margin-bottom: 20px; }
.cf-plan-style-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
.cf-tag { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 10px; border-radius: 30px; border: 1px solid var(--cf-border); color: var(--cf-slate-mid); background: var(--cf-warm-white); }
.cf-tag--sage { background: var(--cf-sage-light); color: var(--cf-sage); border-color: var(--cf-sage-light); }
.cf-tag--rust { background: #FCEEE8; color: var(--cf-rust); border-color: #F2C9B8; }
.cf-plan-title { font-family: var(--cf-font-display); font-size: clamp(2rem, 4vw, 2.9rem); font-weight: 700; line-height: 1.15; color: var(--cf-slate); margin-bottom: 10px; }
.cf-plan-subtitle { font-size: 1rem; color: var(--cf-slate-mid); font-weight: 300; max-width: 560px; line-height: 1.6; }
.cf-plan-stats-bar { display: flex; gap: 0; background: var(--cf-slate); border-radius: var(--cf-radius-lg); overflow: hidden; margin: 24px 0; }
.cf-stat-item { flex: 1; text-align: center; padding: 18px 12px; border-right: 1px solid rgba(255,255,255,0.08); }
.cf-stat-item:last-child { border-right: none; }
.cf-stat-item__value { font-family: var(--cf-font-display); font-size: 1.6rem; font-weight: 700; color: var(--cf-cream); line-height: 1; margin-bottom: 4px; }
.cf-stat-item__label { font-size: 0.7rem; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--cf-slate-light); }
.cf-gallery-wrapper { position: relative; }
.cf-gallery-main { position: relative; overflow: hidden; border-radius: var(--cf-radius-lg); background: var(--cf-cream); cursor: zoom-in; }
.cf-gallery-main img { width: 100%; aspect-ratio: 4/3; object-fit: cover; transition: transform 0.5s ease; }
.cf-gallery-main:hover img { transform: scale(1.02); }
.cf-gallery-badge { position: absolute; top: 16px; left: 16px; background: var(--cf-slate); color: var(--cf-cream); font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 12px; border-radius: 30px; z-index: 2; }
.cf-gallery-thumbs { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-top: 8px; }
.cf-gallery-thumb { border-radius: var(--cf-radius); overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: border-color var(--cf-transition); }
.cf-gallery-thumb:hover, .cf-gallery-thumb.cf-active { border-color: var(--cf-rust); }
.cf-gallery-thumb img { width: 100%; aspect-ratio: 4/3; object-fit: cover; }
.cf-buy-card { position: sticky; top: 80px; background: #fff; border-radius: var(--cf-radius-lg); border: 1px solid var(--cf-border); box-shadow: var(--cf-shadow-soft); overflow: hidden; }
.cf-buy-card__top { background: var(--cf-slate); padding: 22px 24px; }
.cf-buy-card__plan-name { font-family: var(--cf-font-display); font-size: 1.15rem; color: var(--cf-cream); margin-bottom: 4px; }
.cf-buy-card__plan-id { font-size: 0.75rem; color: var(--cf-slate-light); letter-spacing: 0.08em; }
.cf-buy-card__body { padding: 22px 24px; }
.cf-price-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: var(--cf-radius); margin-bottom: 10px; cursor: pointer; border: 2px solid var(--cf-border); transition: border-color var(--cf-transition), background var(--cf-transition); }
.cf-price-row:hover { border-color: var(--cf-rust); background: #fef9f6; }
.cf-price-row.cf-selected { border-color: var(--cf-rust); background: #fef9f6; }
.cf-price-row__left { display: flex; align-items: center; gap: 10px; }
.cf-price-radio { width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--cf-border); flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: border-color var(--cf-transition); }
.cf-price-row.cf-selected .cf-price-radio { border-color: var(--cf-rust); }
.cf-price-radio::after { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--cf-rust); opacity: 0; transition: opacity var(--cf-transition); }
.cf-price-row.cf-selected .cf-price-radio::after { opacity: 1; }
.cf-price-row__format { font-weight: 600; font-size: 0.9rem; color: var(--cf-slate); }
.cf-price-row__desc { font-size: 0.75rem; color: var(--cf-slate-light); }
.cf-price-row__price { font-family: var(--cf-font-display); font-size: 1.3rem; font-weight: 700; color: var(--cf-slate); }
.cf-btn-buy { display: block; width: 100%; background: var(--cf-rust); color: #fff; font-family: var(--cf-font-body); font-size: 0.95rem; font-weight: 600; letter-spacing: 0.04em; text-align: center; padding: 15px 24px; border-radius: var(--cf-radius); border: none; cursor: pointer; transition: background var(--cf-transition), transform var(--cf-transition); margin-top: 14px; text-decoration: none; }
.cf-btn-buy:hover { background: var(--cf-rust-dark); transform: translateY(-1px); color: #fff; }
.cf-buy-card__trust { display: flex; gap: 6px; flex-direction: column; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--cf-border); }
.cf-trust-line { display: flex; align-items: center; gap: 8px; font-size: 0.78rem; color: var(--cf-slate-mid); }
.cf-trust-icon { font-size: 0.9rem; flex-shrink: 0; }
.cf-buy-card__question { margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--cf-border); text-align: center; }
.cf-buy-card__question p { font-size: 0.8rem; color: var(--cf-slate-mid); margin-bottom: 8px; }
.cf-btn-outline { display: inline-block; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; padding: 9px 18px; border-radius: var(--cf-radius); border: 1.5px solid var(--cf-sage); color: var(--cf-sage); transition: background var(--cf-transition), color var(--cf-transition); }
.cf-btn-outline:hover { background: var(--cf-sage); color: #fff; }
/* PLAN BODY */
.cf-plan-body { display: grid; grid-template-columns: 1fr 400px; gap: 48px; align-items: start; padding: 48px 0 72px; }
.cf-section-label { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--cf-rust); margin-bottom: 6px; }
.cf-section-title { font-family: var(--cf-font-display); font-size: 1.65rem; font-weight: 700; color: var(--cf-slate); line-height: 1.2; margin-bottom: 16px; }
.cf-desc-section { margin-bottom: 52px; }
.cf-desc-lead { font-size: 1.05rem; line-height: 1.75; color: var(--cf-slate-mid); margin-bottom: 16px; }
.cf-desc-body { font-size: 0.9375rem; line-height: 1.75; color: var(--cf-slate-mid); }
.cf-desc-body + .cf-desc-body { margin-top: 12px; }
/* SPEC TABLE */
.cf-spec-section { margin-bottom: 52px; }
.cf-spec-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; border: 1px solid var(--cf-border); border-radius: var(--cf-radius-lg); overflow: hidden; }
.cf-spec-group { padding: 24px 28px; }
.cf-spec-group:nth-child(odd) { background: var(--cf-cream); }
.cf-spec-group:nth-child(even) { background: var(--cf-warm-white); border-left: 1px solid var(--cf-border); }
.cf-spec-group h3 { font-family: var(--cf-font-display); font-size: 0.95rem; font-weight: 600; color: var(--cf-slate); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid var(--cf-border); }
.cf-spec-list li { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; font-size: 0.875rem; padding: 5px 0; border-bottom: 1px solid rgba(0,0,0,0.04); }
.cf-spec-list li:last-child { border-bottom: none; }
.cf-spec-key { color: var(--cf-slate-light); font-weight: 400; }
.cf-spec-val { font-weight: 600; color: var(--cf-slate); text-align: right; }
/* FLOOR PLANS */
.cf-floorplan-section { margin-bottom: 52px; }
.cf-floorplan-tabs { display: flex; gap: 0; border: 1px solid var(--cf-border); border-radius: var(--cf-radius); overflow: hidden; width: fit-content; margin-bottom: 24px; }
.cf-fp-tab { padding: 9px 20px; font-size: 0.82rem; font-weight: 600; letter-spacing: 0.04em; cursor: pointer; border: none; background: var(--cf-cream); color: var(--cf-slate-mid); border-right: 1px solid var(--cf-border); transition: background var(--cf-transition), color var(--cf-transition); }
.cf-fp-tab:last-child { border-right: none; }
.cf-fp-tab.cf-active, .cf-fp-tab:hover { background: var(--cf-slate); color: var(--cf-cream); }
.cf-fp-panel { display: none; }
.cf-fp-panel.cf-active { display: block; }
.cf-fp-panel img { border-radius: var(--cf-radius-lg); border: 1px solid var(--cf-border); width: 100%; background: #fff; padding: 12px; }
.cf-fp-note { font-size: 0.8rem; color: var(--cf-slate-light); margin-top: 12px; font-style: italic; }
/* FEATURES */
.cf-features-section { background: var(--cf-sage-light); border-radius: var(--cf-radius-lg); padding: 36px 40px; margin-bottom: 52px; }
.cf-features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; }
.cf-feature-item { display: flex; align-items: flex-start; gap: 10px; font-size: 0.875rem; color: var(--cf-slate-mid); }
.cf-feature-check { width: 20px; height: 20px; flex-shrink: 0; background: var(--cf-sage); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.65rem; margin-top: 1px; }
/* INCLUDED */
.cf-included-section { margin-bottom: 52px; }
.cf-included-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 20px; }
.cf-included-card { background: var(--cf-cream); border: 1px solid var(--cf-border); border-radius: var(--cf-radius-lg); padding: 20px 22px; }
.cf-included-card h4 { font-family: var(--cf-font-display); font-size: 0.975rem; font-weight: 600; color: var(--cf-slate); margin-bottom: 6px; }
.cf-included-card p { font-size: 0.825rem; color: var(--cf-slate-mid); line-height: 1.55; }
.cf-included-icon { font-size: 1.5rem; margin-bottom: 8px; }
/* MOD BANNER */
.cf-mod-banner { background: var(--cf-slate); border-radius: var(--cf-radius-lg); padding: 36px 40px; display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 52px; }
.cf-mod-banner h3 { font-family: var(--cf-font-display); font-size: 1.35rem; color: var(--cf-cream); margin-bottom: 6px; }
.cf-mod-banner p { font-size: 0.875rem; color: var(--cf-slate-light); max-width: 460px; }
.cf-btn-mod { flex-shrink: 0; background: var(--cf-tan); color: var(--cf-slate); font-weight: 700; font-size: 0.85rem; letter-spacing: 0.06em; text-transform: uppercase; padding: 13px 24px; border-radius: var(--cf-radius); white-space: nowrap; transition: background var(--cf-transition); }
.cf-btn-mod:hover { background: #d6b88a; }
/* FAQ */
.cf-faq-section { margin-bottom: 52px; }
.cf-faq-list { margin-top: 20px; }
.cf-faq-item { border-bottom: 1px solid var(--cf-border); }
.cf-faq-question { display: flex; justify-content: space-between; align-items: center; width: 100%; background: none; border: none; cursor: pointer; padding: 18px 4px; text-align: left; gap: 16px; }
.cf-faq-question span { font-size: 0.9375rem; font-weight: 600; color: var(--cf-slate); }
.cf-faq-icon { width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; border: 1.5px solid var(--cf-border); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: var(--cf-slate-light); font-weight: 300; transition: background var(--cf-transition), color var(--cf-transition), border-color var(--cf-transition); line-height: 1; }
.cf-faq-item.cf-open .cf-faq-icon { background: var(--cf-rust); border-color: var(--cf-rust); color: #fff; }
.cf-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
.cf-faq-answer__inner { padding: 0 4px 18px; font-size: 0.875rem; color: var(--cf-slate-mid); line-height: 1.7; }
/* NOTES */
.cf-notes-section { background: var(--cf-tan-light); border: 1px solid #e5d5c0; border-left: 4px solid var(--cf-tan); border-radius: var(--cf-radius-lg); padding: 28px 32px; margin-bottom: 52px; }
.cf-notes-section h3 { font-family: var(--cf-font-display); font-size: 1rem; margin-bottom: 14px; color: var(--cf-slate); }
.cf-notes-list { display: flex; flex-direction: column; gap: 8px; }
.cf-notes-list li { font-size: 0.84rem; color: var(--cf-slate-mid); padding-left: 14px; position: relative; line-height: 1.55; }
.cf-notes-list li::before { content: '–'; position: absolute; left: 0; color: var(--cf-tan); }
/* SIDEBAR */
.cf-plan-sidebar { display: flex; flex-direction: column; gap: 24px; }
.cf-mini-spec-card { background: var(--cf-cream); border: 1px solid var(--cf-border); border-radius: var(--cf-radius-lg); padding: 22px 24px; }
.cf-mini-spec-card h4 { font-family: var(--cf-font-display); font-size: 0.95rem; font-weight: 600; color: var(--cf-slate); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid var(--cf-border); }
.cf-mini-spec-card ul { list-style: none; }
.cf-mini-spec-card li { display: flex; justify-content: space-between; gap: 10px; font-size: 0.84rem; padding: 6px 0; border-bottom: 1px solid rgba(0,0,0,0.04); }
.cf-mini-spec-card li:last-child { border-bottom: none; }
.cf-mini-spec-card li .k { color: var(--cf-slate-light); }
.cf-mini-spec-card li .v { font-weight: 600; }
.cf-designer-card { background: var(--cf-slate); border-radius: var(--cf-radius-lg); padding: 24px; color: var(--cf-cream); }
.cf-designer-card__eyebrow { font-size: 0.68rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--cf-tan); margin-bottom: 8px; }
.cf-designer-card__name { font-family: var(--cf-font-display); font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; }
.cf-designer-card__bio { font-size: 0.82rem; line-height: 1.6; color: rgba(247,243,238,0.7); margin-bottom: 16px; }
.cf-designer-card__cta { display: block; text-align: center; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: var(--cf-cream); font-size: 0.8rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; padding: 10px 16px; border-radius: var(--cf-radius); transition: background var(--cf-transition); }
.cf-designer-card__cta:hover { background: rgba(255,255,255,0.18); }
/* RELATED */
.cf-related-section { background: var(--cf-cream); border-top: 1px solid var(--cf-border); padding: 56px 0; }
.cf-related-section .cf-section-title { text-align: center; margin-bottom: 32px; }
.cf-related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.cf-related-card { background: #fff; border-radius: var(--cf-radius-lg); border: 1px solid var(--cf-border); overflow: hidden; transition: box-shadow var(--cf-transition), transform var(--cf-transition); text-decoration: none; display: block; }
.cf-related-card:hover { box-shadow: var(--cf-shadow-soft); transform: translateY(-3px); }
.cf-related-card img { width: 100%; aspect-ratio: 16/10; object-fit: cover; }
.cf-related-card__body { padding: 16px 18px; }
.cf-related-card__title { font-family: var(--cf-font-display); font-size: 1rem; font-weight: 600; color: var(--cf-slate); margin-bottom: 4px; }
.cf-related-card__meta { font-size: 0.8rem; color: var(--cf-slate-light); margin-bottom: 10px; }
.cf-related-card__link { font-size: 0.78rem; font-weight: 600; color: var(--cf-rust); letter-spacing: 0.06em; text-transform: uppercase; }
/* MOBILE BAR */
.cf-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 90; background: var(--cf-slate); border-top: 2px solid var(--cf-rust); padding: 12px 20px; align-items: center; justify-content: space-between; gap: 12px; }
.cf-mobile-buy-bar__name { font-size: 0.8rem; font-weight: 600; color: var(--cf-cream); }
.cf-mobile-buy-bar__price { font-size: 1.1rem; font-weight: 700; color: var(--cf-tan); }
.cf-mobile-buy-bar .cf-btn-buy { margin: 0; width: auto; padding: 12px 24px; font-size: 0.85rem; }
@media (max-width: 1024px) {
  .cf-plan-hero__grid, .cf-plan-body { grid-template-columns: 1fr; }
  .cf-buy-card { position: static; }
  .cf-spec-grid { grid-template-columns: 1fr; }
  .cf-spec-group:nth-child(even) { border-left: none; border-top: 1px solid var(--cf-border); }
  .cf-features-grid, .cf-included-grid { grid-template-columns: 1fr; }
  .cf-related-grid { grid-template-columns: 1fr; }
  .cf-mod-banner { flex-direction: column; text-align: center; }
  .cf-gallery-thumbs { grid-template-columns: repeat(4, 1fr); }
  .cf-mobile-buy-bar { display: flex; }
  body { padding-bottom: 72px; }
}
@media (max-width: 768px) {
  .cf-plan-title { font-size: 1.75rem; }
  .cf-gallery-thumbs { grid-template-columns: repeat(3, 1fr); }
  .cf-plan-stats-bar { flex-wrap: wrap; }
  .cf-stat-item { flex: 0 0 50%; border-bottom: 1px solid rgba(255,255,255,0.08); }
}
</style>
<?php
}

function mhp_carolina_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    $plan_name        = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft             = $acf ? get_field( 'total_living_area',   $pid ) : '2,589';
    $main_floor       = $acf ? get_field( 'main_floor',          $pid ) : '1,927';
    $upper_floor      = $acf ? get_field( 'upper_floor',         $pid ) : '662';
    $lower_floor      = $acf ? get_field( 'lower_floor',         $pid ) : '1,927';
    $bedrooms         = $acf ? get_field( 'bedrooms',            $pid ) : '4';
    $bathrooms        = $acf ? get_field( 'bathrooms',           $pid ) : '3.5';
    $stories          = $acf ? get_field( 'stories',             $pid ) : '3';
    $width            = $acf ? get_field( 'width',               $pid ) : "68'10\"";
    $depth            = $acf ? get_field( 'depth',               $pid ) : "67'10\"";
    $garage           = $acf ? get_field( 'garage',              $pid ) : '2-Car';
    $style            = $acf ? get_field( 'style',               $pid ) : 'Modern Farmhouse · Country · Southern Living';
    $outdoor          = $acf ? get_field( 'outdoor',             $pid ) : 'Wraparound Porch, Screen Porch, Deck';
    $roof             = $acf ? get_field( 'roof',                $pid ) : "11'2";
    $ceiling          = $acf ? get_field( 'ceiling',             $pid ) : "10' Main / 9' Upper";
    $exterior         = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms = $acf ? get_field( 'additional_rooms',    $pid ) : 'Mud Room, Office, Foyer, Pantry, Bunk, Rec';
    $other_features   = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style        = $acf ? get_field( 'lot_style',           $pid ) : 'Sloping';
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
    if ( ! $hero_url ) $hero_url = 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-8.jpg';

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
            array( 'full' => $hero_url, 'thumb' => $hero_url, 'alt' => $plan_name . ' front exterior' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-6.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-6.jpg', 'alt' => $plan_name . ' side view' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-3.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-3.jpg', 'alt' => $plan_name . ' porch detail' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-20.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-farmhouse-style-house-plan-max-fulbright-designs-20.jpg', 'alt' => $plan_name . ' interior living' ),
            array( 'full' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-modern-farmhouse-with-wraparound-porch-blueprints.jpg', 'thumb' => 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/four-bedroom-modern-farmhouse-with-wraparound-porch-blueprints.jpg', 'alt' => 'Wraparound porch blueprints' ),
        );
    }

    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft', '.' ), '', $v ); };
    $sqft_display    = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display = $clean($main_floor) > 0 ? number_format( $clean($main_floor) ) : $main_floor;
    $upper_fl_display = $clean($upper_floor) > 0 ? number_format( $clean($upper_floor) ) : $upper_floor;
    $lower_fl_display = $lower_floor ?: '1,927';
    $sqft_int = $clean($sqft) > 100 ? $clean($sqft) : 2589;
?>
<div class="cf-wrap">

<!-- BREADCRUMB -->
<nav class="cf-breadcrumb" aria-label="Breadcrumb">
  <div class="cf-container">
    <ol class="cf-breadcrumb__list">
      <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
      <li><a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a></li>
      <li><?php echo esc_html($plan_name); ?></li>
    </ol>
  </div>
</nav>

<!-- PLAN HERO -->
<section class="cf-plan-hero" aria-labelledby="cf-plan-title">
  <div class="cf-container">
    <div class="cf-plan-hero__grid">

      <!-- Gallery -->
      <div class="cf-gallery-wrapper">
        <div class="cf-gallery-main" id="cfGalleryMain">
          <span class="cf-gallery-badge">4 Bed &middot; <?php echo esc_html($bathrooms); ?> Bath</span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> &ndash; front elevation view with wraparound porch" id="cfMainPhoto" fetchpriority="high">
        </div>
        <div class="cf-gallery-thumbs">
          <?php foreach ( $gallery_images as $idx => $img ) : ?>
          <div class="cf-gallery-thumb<?php echo $idx === 0 ? ' cf-active' : ''; ?>" onclick="cfSetPhoto('<?php echo esc_js($img['full']); ?>',this)" style="cursor:pointer;">
            <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Buy Card & Title -->
      <div>
        <div class="cf-plan-title-block">
          <div class="cf-plan-style-tags">
            <span class="cf-tag cf-tag--sage">Modern Farmhouse</span>
            <span class="cf-tag cf-tag--sage">Southern Living</span>
            <span class="cf-tag cf-tag--rust">Wraparound Porch</span>
            <span class="cf-tag">Walkout Basement</span>
          </div>
          <h1 class="cf-plan-title" id="cf-plan-title"><?php echo esc_html($plan_name); ?></h1>
          <p class="cf-plan-subtitle">A build-ready <?php echo esc_html($bedrooms); ?>-bedroom modern farmhouse with open living, master on main, wraparound porch, and optional walkout basement. Designed by Max Fulbright Designs.</p>
        </div>

        <div class="cf-plan-stats-bar">
          <div class="cf-stat-item"><div class="cf-stat-item__value"><?php echo esc_html($bedrooms); ?></div><div class="cf-stat-item__label">Bedrooms</div></div>
          <div class="cf-stat-item"><div class="cf-stat-item__value"><?php echo esc_html($bathrooms); ?></div><div class="cf-stat-item__label">Bathrooms</div></div>
          <div class="cf-stat-item"><div class="cf-stat-item__value"><?php echo esc_html($sqft_display); ?></div><div class="cf-stat-item__label">Heated Sq. Ft.</div></div>
          <div class="cf-stat-item"><div class="cf-stat-item__value"><?php echo esc_html($stories); ?></div><div class="cf-stat-item__label">Stories</div></div>
        </div>

        <div class="cf-buy-card" id="cfBuyCard">
          <div class="cf-buy-card__top">
            <div class="cf-buy-card__plan-name"><?php echo esc_html($plan_name); ?></div>
            <div class="cf-buy-card__plan-id">Immediate Digital Delivery</div>
          </div>
          <div class="cf-buy-card__body">
            <div class="cf-price-row cf-selected" onclick="cfSelectPlan(this,'<?php echo esc_js($price_fmt); ?>')">
              <div class="cf-price-row__left">
                <div class="cf-price-radio"></div>
                <div><div class="cf-price-row__format">PDF Plan Set</div><div class="cf-price-row__desc">Printable, full-resolution PDF</div></div>
              </div>
              <div class="cf-price-row__price"><?php echo esc_html($price_fmt); ?></div>
            </div>
            <div class="cf-price-row" onclick="cfSelectPlan(this,'<?php echo esc_js($cad_price); ?>')">
              <div class="cf-price-row__left">
                <div class="cf-price-radio"></div>
                <div><div class="cf-price-row__format">CAD File</div><div class="cf-price-row__desc">Editable DWG/CAD format</div></div>
              </div>
              <div class="cf-price-row__price"><?php echo esc_html($cad_price); ?></div>
            </div>
            <a href="<?php echo esc_url($buy_href); ?>" class="cf-btn-buy" id="cfBuyButton">Purchase Plan</a>
            <div class="cf-buy-card__trust">
              <div class="cf-trust-line"><span class="cf-trust-icon">🔒</span> Secure checkout &middot; All major cards accepted</div>
              <div class="cf-trust-line"><span class="cf-trust-icon">📦</span> Instant digital delivery after purchase</div>
              <div class="cf-trust-line"><span class="cf-trust-icon">✏️</span> Modifications available &mdash; just ask</div>
            </div>
            <div class="cf-buy-card__question">
              <p>Questions before buying?</p>
              <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="cf-btn-outline">Talk With Our Team</a>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /grid -->
  </div>
</section>

<!-- PLAN BODY -->
<main class="cf-container">
  <div class="cf-plan-body">

    <!-- MAIN COLUMN -->
    <div>

      <!-- Description -->
      <section class="cf-desc-section">
        <p class="cf-section-label">About This Plan</p>
        <h2 class="cf-section-title">Built for the Way Families Actually Live</h2>
        <?php if ( $plan_description ) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <p class="cf-desc-lead">The <?php echo esc_html($plan_name); ?> brings together everything families ask for &#8212; open common spaces, a private master suite on the main level, room to grow upstairs, and the option to finish the basement when the time is right.</p>
          <p class="cf-desc-body">The wraparound porch isn&#8217;t just a style choice &#8212; it&#8217;s a functional outdoor room that connects the front of the home to the back, perfect for Carolina summers and evening gatherings. Inside, the main floor flows naturally between the living room, dining area, and kitchen, giving the home the kind of open feel that works for everyday life and larger get-togethers alike.</p>
          <p class="cf-desc-body" style="margin-top:12px;">The master suite sits on the main level with a bathroom and a walk-in closet sized for real use. Upstairs, two bedrooms and a bonus room offer flexibility for a growing family, a home office, or a private retreat. The lower level can stay unfinished to keep construction costs down, or be completed for a bunk room, recreation room, and guest space &#8212; all with the same square footage as the main floor.</p>
          <p class="cf-desc-body" style="margin-top:12px;">This plan is designed for sloped lots, making it a strong fit for properties with natural grade change or wooded settings where a daylight basement adds views and value.</p>
        <?php endif; ?>
      </section>

      <!-- Spec Table -->
      <section class="cf-spec-section">
        <p class="cf-section-label">Plan Specifications</p>
        <h2 class="cf-section-title">Full Plan Details</h2>
        <div class="cf-spec-grid">
          <div class="cf-spec-group">
            <h3>Living Area</h3>
            <ul class="cf-spec-list">
              <li><span class="cf-spec-key">Main Floor</span><span class="cf-spec-val"><?php echo esc_html($main_fl_display); ?> sq. ft.</span></li>
              <li><span class="cf-spec-key">Upper Floor</span><span class="cf-spec-val"><?php echo esc_html($upper_fl_display); ?> sq. ft.</span></li>
              <li><span class="cf-spec-key">Lower Floor</span><span class="cf-spec-val"><?php echo esc_html($lower_fl_display); ?> sq. ft.</span></li>
              <li><span class="cf-spec-key">Total Heated Area</span><span class="cf-spec-val"><?php echo esc_html($sqft_display); ?> sq. ft.</span></li>
            </ul>
          </div>
          <div class="cf-spec-group">
            <h3>Dimensions</h3>
            <ul class="cf-spec-list">
              <li><span class="cf-spec-key">Width</span><span class="cf-spec-val"><?php echo esc_html($width); ?></span></li>
              <li><span class="cf-spec-key">Depth</span><span class="cf-spec-val"><?php echo esc_html($depth); ?></span></li>
              <li><span class="cf-spec-key">Stories</span><span class="cf-spec-val"><?php echo esc_html($stories); ?></span></li>
              <li><span class="cf-spec-key">Lot Type</span><span class="cf-spec-val"><?php echo esc_html($lot_style); ?></span></li>
            </ul>
          </div>
          <div class="cf-spec-group">
            <h3>Rooms</h3>
            <ul class="cf-spec-list">
              <li><span class="cf-spec-key">Bedrooms</span><span class="cf-spec-val"><?php echo esc_html($bedrooms); ?></span></li>
              <li><span class="cf-spec-key">Bathrooms</span><span class="cf-spec-val"><?php echo esc_html($bathrooms); ?></span></li>
              <li><span class="cf-spec-key">Garage</span><span class="cf-spec-val"><?php echo esc_html($garage); ?></span></li>
              <?php if ( $additional_rooms ) : ?><li><span class="cf-spec-key">Additional Rooms</span><span class="cf-spec-val"><?php echo esc_html($additional_rooms); ?></span></li><?php endif; ?>
            </ul>
          </div>
          <div class="cf-spec-group">
            <h3>Construction</h3>
            <ul class="cf-spec-list">
              <li><span class="cf-spec-key">Exterior Framing</span><span class="cf-spec-val"><?php echo esc_html($exterior); ?></span></li>
              <li><span class="cf-spec-key">Ceiling Height</span><span class="cf-spec-val"><?php echo esc_html($ceiling); ?></span></li>
              <?php if ( $roof ) : ?><li><span class="cf-spec-key">Roof Pitch</span><span class="cf-spec-val"><?php echo esc_html($roof); ?></span></li><?php endif; ?>
              <?php if ( $style ) : ?><li><span class="cf-spec-key">Style</span><span class="cf-spec-val"><?php echo esc_html($style); ?></span></li><?php endif; ?>
            </ul>
          </div>
          <?php if ( $outdoor ) : ?>
          <div class="cf-spec-group">
            <h3>Outdoor Spaces</h3>
            <ul class="cf-spec-list">
              <li><span class="cf-spec-key">Porches &amp; Decks</span><span class="cf-spec-val"><?php echo esc_html($outdoor); ?></span></li>
            </ul>
          </div>
          <?php endif; ?>
          <?php if ( $other_features ) : ?>
          <div class="cf-spec-group">
            <h3>Other Features</h3>
            <ul class="cf-spec-list"><li><span class="cf-spec-key"><?php echo esc_html($other_features); ?></span></li></ul>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- Floor Plans -->
      <section class="cf-floorplan-section">
        <p class="cf-section-label">Floor Plans</p>
        <h2 class="cf-section-title">Three-Level Layout</h2>
        <?php if ( $floor_plans_wysiwyg ) : ?>
          <?php echo wp_kses_post($floor_plans_wysiwyg); ?>
        <?php else : ?>
        <div class="cf-floorplan-tabs">
          <button class="cf-fp-tab cf-active" onclick="cfSwitchTab(this,'cfFpMain')">Main Level</button>
          <button class="cf-fp-tab" onclick="cfSwitchTab(this,'cfFpUpper')">Upper Level</button>
          <button class="cf-fp-tab" onclick="cfSwitchTab(this,'cfFpLower')">Lower Level</button>
        </div>
        <div id="cfFpMain" class="cf-fp-panel cf-active">
          <img src="https://www.maxhouseplans.com/wp-content/uploads/2019/04/4-bedroom-modern-farmhouse-house-plan-1024x998.jpg" alt="<?php echo esc_attr($plan_name); ?> main level floor plan &#8212; <?php echo esc_attr($main_fl_display); ?> sq ft with open living, master suite, and wraparound porch" loading="lazy">
        </div>
        <div id="cfFpUpper" class="cf-fp-panel">
          <img src="https://www.maxhouseplans.com/wp-content/uploads/2019/04/modern-farmhouse-house-plan-upper-level-2-bedrooms-1024x1009.jpg" alt="<?php echo esc_attr($plan_name); ?> upper level floor plan &#8212; <?php echo esc_attr($upper_fl_display); ?> sq ft with two bedrooms and bonus room" loading="lazy">
        </div>
        <div id="cfFpLower" class="cf-fp-panel">
          <img src="https://www.maxhouseplans.com/wp-content/uploads/2019/04/modern-farmhouse-house-plan-walkout-basement-1024x946.jpg" alt="<?php echo esc_attr($plan_name); ?> walkout basement floor plan &#8212; <?php echo esc_attr($lower_fl_display); ?> sq ft with recreation room and bunk room" loading="lazy">
        </div>
        <p class="cf-fp-note">Floor plans may vary slightly from site depictions. We make occasional plan improvements that may not be immediately reflected here.</p>
        <?php endif; ?>
      </section>

      <!-- Features -->
      <section class="cf-features-section">
        <p class="cf-section-label">What Makes This Plan Work</p>
        <h2 class="cf-section-title" style="margin-bottom:0;">Key Design Features</h2>
        <div class="cf-features-grid">
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Master bedroom on the main level &#8212; no stairs required for daily living</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Wraparound porch connects front and side of home, ideal for outdoor living</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Open kitchen, dining, and living layout &#8212; designed for real family use</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Basement can be left unfinished at build time to reduce initial cost</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>10&#8217; ceilings on main level for an open, airy feel throughout</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Mud room and pantry &#8212; practical spaces that homes actually need</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Bonus room upstairs for an office, playroom, or flex space</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Designed for sloped lots &#8212; takes advantage of natural grade for the basement</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>2-car garage with room for storage</span></div>
          <div class="cf-feature-item"><div class="cf-feature-check">&#10003;</div><span>Screened porch and deck for three-season outdoor living</span></div>
        </div>
      </section>

      <!-- What's Included -->
      <section class="cf-included-section">
        <p class="cf-section-label">Plan Set Contents</p>
        <h2 class="cf-section-title">What&#8217;s Included in Every Set</h2>
        <div class="cf-included-grid">
          <div class="cf-included-card"><div class="cf-included-icon">&#128204;</div><h4>Elevations</h4><p>Front, side, and rear elevations at &frac14;&Prime; scale with notes, dimensions, and recommended exterior materials.</p></div>
          <div class="cf-included-card"><div class="cf-included-icon">&#128506;&#65039;</div><h4>Floor Plans</h4><p>Complete dimensioned and detailed floor plans for all three levels &#8212; main, upper, and lower.</p></div>
          <div class="cf-included-card"><div class="cf-included-icon">&#127959;</div><h4>Foundation Plan</h4><p>Detailed lower level / foundation plan showing structural layout and basement configuration.</p></div>
          <div class="cf-included-card"><div class="cf-included-icon">&#127968;</div><h4>Roof Plan</h4><p>Complete roof plan showing ridge lines, valleys, and framing geometry for the farmhouse roofline.</p></div>
        </div>
        <p style="font-size:0.82rem;color:var(--cf-slate-light);margin-top:14px;">Electrical plans are not included in the standard set. Custom electrical plans are available for $350. All sales are final once fulfillment begins.</p>
      </section>

      <!-- Modification Banner -->
      <div class="cf-mod-banner">
        <div>
          <h3>Need Something Changed?</h3>
          <p>We offer custom modifications on all of our plans &#8212; room additions, layout changes, exterior adjustments, or anything else your build requires. Our team has modified hundreds of plans and can walk you through the process.</p>
        </div>
        <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="cf-btn-mod">Request a Modification</a>
      </div>

      <!-- FAQ -->
      <section class="cf-faq-section">
        <p class="cf-section-label">Common Questions</p>
        <h2 class="cf-section-title">Frequently Asked Questions</h2>
        <div class="cf-faq-list">
          <?php if ( ! empty($faqs) && is_array($faqs) ) :
                foreach ( $faqs as $faq ) :
                  $q = isset($faq['question']) ? $faq['question'] : '';
                  $a = isset($faq['answer'])   ? $faq['answer']   : '';
                  if ( ! $q ) continue; ?>
          <div class="cf-faq-item">
            <button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false">
              <span><?php echo esc_html($q); ?></span>
              <div class="cf-faq-icon" aria-hidden="true">+</div>
            </button>
            <div class="cf-faq-answer"><div class="cf-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
          </div>
          <?php endforeach; else : ?>
          <div class="cf-faq-item"><button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false"><span>Can the basement be left unfinished?</span><div class="cf-faq-icon" aria-hidden="true">+</div></button><div class="cf-faq-answer"><div class="cf-faq-answer__inner">Yes. Leaving the lower level unfinished is a straightforward way to reduce upfront construction costs. The space is already planned for future finishing &#8212; add a recreation room, bunk room, and guest bathroom when your budget and timeline allow.</div></div></div>
          <div class="cf-faq-item"><button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false"><span>Can this plan be modified?</span><div class="cf-faq-icon" aria-hidden="true">+</div></button><div class="cf-faq-answer"><div class="cf-faq-answer__inner">Yes. Max Fulbright Designs handles custom modifications &#8212; everything from room size adjustments and layout changes to exterior material choices or garage orientation. Contact us to discuss your specific needs and we&#8217;ll give you a clear quote.</div></div></div>
          <div class="cf-faq-item"><button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false"><span>What is the total heated square footage?</span><div class="cf-faq-icon" aria-hidden="true">+</div></button><div class="cf-faq-answer"><div class="cf-faq-answer__inner">The heated area is <?php echo esc_html($sqft_display); ?> sq. ft. &#8212; <?php echo esc_html($main_fl_display); ?> sq. ft. on the main floor plus <?php echo esc_html($upper_fl_display); ?> sq. ft. on the upper level. The lower level adds up to <?php echo esc_html($lower_fl_display); ?> sq. ft. of optional finished space, which is not counted in the standard heated total.</div></div></div>
          <div class="cf-faq-item"><button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false"><span>Will I need an engineer to stamp these plans?</span><div class="cf-faq-icon" aria-hidden="true">+</div></button><div class="cf-faq-answer"><div class="cf-faq-answer__inner">Stock plans do not include a professional stamp. Requirements vary by location &#8212; check with your local building department before purchase if you&#8217;re unsure about your area&#8217;s requirements.</div></div></div>
          <div class="cf-faq-item"><button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false"><span>What does the PDF vs. CAD option include?</span><div class="cf-faq-icon" aria-hidden="true">+</div></button><div class="cf-faq-answer"><div class="cf-faq-answer__inner">The PDF set is a full-resolution, print-ready set of plan sheets &#8212; everything your builder needs to submit for permit and begin construction. The CAD file provides the same plans in DWG format, which a designer or engineer can open and modify directly. The CAD option is recommended if you plan to make significant modifications.</div></div></div>
          <div class="cf-faq-item"><button class="cf-faq-question" onclick="cfToggleFaq(this)" aria-expanded="false"><span>Is this plan suitable for a sloped lot?</span><div class="cf-faq-icon" aria-hidden="true">+</div></button><div class="cf-faq-answer"><div class="cf-faq-answer__inner">Yes &#8212; the <?php echo esc_html($plan_name); ?> is specifically designed for sloping lots. The walkout basement takes advantage of the natural grade to provide a daylight lower level with direct outdoor access.</div></div></div>
          <?php endif; ?>
        </div>
      </section>

      <!-- Notes -->
      <section class="cf-notes-section">
        <h3>Important Information Before Purchase</h3>
        <ul class="cf-notes-list">
          <li>All sales on house plans and modifications are final once the order has started the fulfillment process.</li>
          <li>Plans are designed to conform to local codes where the original house was constructed. You may need additional documentation for your specific region.</li>
          <li>Some areas require review by a licensed structural engineer. Check with your building department.</li>
          <li>Electrical plans are not included in the standard set. Custom electrical plans are available for $350.</li>
          <li>Floor plans and elevations may vary slightly from website depictions as we make ongoing plan improvements.</li>
        </ul>
      </section>

    </div><!-- /plan-main -->

    <!-- SIDEBAR -->
    <aside class="cf-plan-sidebar">

      <div class="cf-mini-spec-card">
        <h4>Plan at a Glance</h4>
        <ul>
          <li><span class="k">Plan Name</span><span class="v"><?php echo esc_html($plan_name); ?></span></li>
          <li><span class="k">Bedrooms</span><span class="v"><?php echo esc_html($bedrooms); ?></span></li>
          <li><span class="k">Bathrooms</span><span class="v"><?php echo esc_html($bathrooms); ?></span></li>
          <li><span class="k">Heated Sq. Ft.</span><span class="v"><?php echo esc_html($sqft_display); ?></span></li>
          <li><span class="k">Stories</span><span class="v"><?php echo esc_html($stories); ?> (incl. basement)</span></li>
          <li><span class="k">Garage</span><span class="v"><?php echo esc_html($garage); ?></span></li>
          <li><span class="k">Master</span><span class="v">Main Level</span></li>
          <li><span class="k">Lot</span><span class="v"><?php echo esc_html($lot_style); ?></span></li>
          <li><span class="k">Style</span><span class="v"><?php echo esc_html($style); ?></span></li>
          <li><span class="k">Width × Depth</span><span class="v"><?php echo esc_html($width); ?> × <?php echo esc_html($depth); ?></span></li>
          <li><span class="k">Ceiling (Main)</span><span class="v"><?php echo esc_html($ceiling); ?></span></li>
          <li><span class="k">Framing</span><span class="v"><?php echo esc_html($exterior); ?></span></li>
        </ul>
      </div>

      <div class="cf-designer-card">
        <div class="cf-designer-card__eyebrow">Designed By</div>
        <div class="cf-designer-card__name">Max Fulbright Designs</div>
        <p class="cf-designer-card__bio">A family-owned residential design business with 35+ years of combined design and real-world building experience. Every plan is drawn by people who have actually built homes &#8212; not a plan marketplace.</p>
        <a href="<?php echo esc_url(home_url('/about-us/')); ?>" class="cf-designer-card__cta">About Our Team</a>
      </div>

      <?php if ( ! empty($related_plans) && is_array($related_plans) ) : ?>
      <div class="cf-mini-spec-card">
        <h4>Related Plans</h4>
        <ul>
          <?php foreach ( $related_plans as $rp ) :
                if ( ! is_object($rp) ) continue;
                $r_id   = $rp->ID;
                $r_name = $acf ? get_field('plan_name',$r_id) : get_the_title($r_id);
                $r_link = get_permalink($r_id); ?>
          <li><span class="k"><a href="<?php echo esc_url($r_link); ?>" style="color:var(--cf-sage)"><?php echo esc_html($r_name ?: get_the_title($r_id)); ?></a></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

    </aside>

  </div><!-- /plan-body -->
</main>

<!-- RELATED PLANS (static fallback) -->
<section class="cf-related-section">
  <div class="cf-container">
    <p class="cf-section-label" style="text-align:center;">More Plans</p>
    <h2 class="cf-section-title">You May Also Like</h2>
    <div class="cf-related-grid">
      <a href="<?php echo esc_url(home_url('/home-plans/dog-trot-house-plan/')); ?>" class="cf-related-card">
        <img src="https://www.maxhouseplans.com/wp-content/uploads/2025/01/house-plans-by-max-fulbright-designs-28-350x216.jpg" alt="Dog Trot House Plan" loading="lazy">
        <div class="cf-related-card__body">
          <div class="cf-related-card__title">Dog Trot House Plan</div>
          <div class="cf-related-card__meta">Farmhouse &middot; Southern</div>
          <div class="cf-related-card__link">View Plan &#8594;</div>
        </div>
      </a>
      <a href="<?php echo esc_url(home_url('/home-plans/craftsman-cottage-house-plan-with-porches/')); ?>" class="cf-related-card">
        <img src="https://www.maxhouseplans.com/wp-content/uploads/2025/01/house-plans-by-max-fulbright-designs-31-350x216.jpg" alt="Craftsman Cottage House Plan with Porches" loading="lazy">
        <div class="cf-related-card__body">
          <div class="cf-related-card__title">Craftsman Cottage with Porches</div>
          <div class="cf-related-card__meta">Craftsman &middot; Cottage</div>
          <div class="cf-related-card__link">View Plan &#8594;</div>
        </div>
      </a>
      <a href="<?php echo esc_url(home_url('/home-plans/cottage-house-plan-with-porches/')); ?>" class="cf-related-card">
        <img src="https://www.maxhouseplans.com/wp-content/uploads/2025/01/house-plans-by-max-fulbright-designs-14-350x216.jpg" alt="Cottage House Plan with Porches" loading="lazy">
        <div class="cf-related-card__body">
          <div class="cf-related-card__title">Cottage House Plan with Porches</div>
          <div class="cf-related-card__meta">Cottage &middot; Southern Living</div>
          <div class="cf-related-card__link">View Plan &#8594;</div>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- MOBILE BAR -->
<div class="cf-mobile-buy-bar" id="cfMobileBuyBar">
  <div class="cf-mobile-buy-bar__info">
    <div class="cf-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
    <div class="cf-mobile-buy-bar__price" id="cfMobilePrice"><?php echo esc_html($price_fmt); ?> &ndash; PDF</div>
  </div>
  <a href="<?php echo esc_url($buy_href); ?>" class="cf-btn-buy" style="padding:12px 20px;font-size:0.85rem;width:auto;">Purchase Plan</a>
</div>

</div><!-- .cf-wrap -->

<script>
(function(){
  window.cfSetPhoto = function(src, thumb) {
    var img = document.getElementById('cfMainPhoto'); if (img) img.src = src;
    document.querySelectorAll('.cf-gallery-thumb').forEach(function(t){t.classList.remove('cf-active');});
    if (thumb) thumb.classList.add('cf-active');
  };
  window.cfSelectPlan = function(row, price) {
    document.querySelectorAll('.cf-price-row').forEach(function(r){r.classList.remove('cf-selected');});
    row.classList.add('cf-selected');
    var mp = document.getElementById('cfMobilePrice');
    if (mp) mp.textContent = price + ' \u2013 ' + row.querySelector('.cf-price-row__format').textContent;
  };
  window.cfSwitchTab = function(btn, panelId) {
    document.querySelectorAll('.cf-fp-tab').forEach(function(t){t.classList.remove('cf-active');});
    document.querySelectorAll('.cf-fp-panel').forEach(function(p){p.classList.remove('cf-active');});
    btn.classList.add('cf-active');
    var panel = document.getElementById(panelId); if (panel) panel.classList.add('cf-active');
  };
  window.cfToggleFaq = function(btn) {
    var item = btn.closest('.cf-faq-item');
    var answer = item.querySelector('.cf-faq-answer');
    var icon = item.querySelector('.cf-faq-icon');
    var isOpen = item.classList.contains('cf-open');
    document.querySelectorAll('.cf-faq-item').forEach(function(i){
      i.classList.remove('cf-open');
      var a=i.querySelector('.cf-faq-answer'); if(a) a.style.maxHeight='0';
      var ic=i.querySelector('.cf-faq-icon'); if(ic) ic.textContent='+';
      var b=i.querySelector('.cf-faq-question'); if(b) b.setAttribute('aria-expanded','false');
    });
    if (!isOpen) {
      item.classList.add('cf-open');
      if (answer) answer.style.maxHeight = answer.scrollHeight + 'px';
      if (icon) icon.textContent = '\u2212';
      btn.setAttribute('aria-expanded','true');
    }
  };
})();
</script>
<?php
}

genesis();
