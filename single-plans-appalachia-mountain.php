<?php
/**
 * Template Name: Plan - Appalachia Mountain
 *
 * Dedicated plan template for the Appalachia Mountain house plan.
 * Slug: appalachia-mountain-house-plan
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-21
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_appalachia_loop' );
add_action( 'wp_head', 'mhp_appalachia_styles', 20 );
add_action( 'wp_head', 'mhp_appalachia_schema', 5 );
add_action( 'wp_head', 'mhp_appalachia_fonts', 1 );

function mhp_appalachia_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">' . "\n";
}

function mhp_appalachia_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid  = get_the_ID();
    $acf  = function_exists( 'get_field' );
    $name    = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $name    = $name ?: get_the_title();
    $sqft    = $acf ? get_field( 'total_living_area', $pid ) : '2100';
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
        'description' => 'A build-ready A-frame mountain house plan with open living area, walls of glass on front and rear, main-level master suite, outdoor fireplace, and optional finished basement with bunkrooms and recreation room. Ideal for mountain and lake lots.',
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
            array( '@type' => 'PropertyValue', 'name' => 'Style', 'value' => 'A-Frame, Mountain, Lake, Craftsman' ),
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

function mhp_appalachia_styles() {
?>
<style id="mhp-appalachia-styles">
:root {
  --color-primary: #2C3E2D; --color-primary-light: #3D5A3F; --color-primary-dark: #1A2A1B;
  --color-accent: #B8860B; --color-accent-light: #D4A843; --color-accent-warm: #C4913B;
  --color-cream: #FAF7F2; --color-cream-dark: #F0EBE3; --color-stone: #E8E2D8;
  --color-stone-dark: #D4CEC4; --color-text: #2B2B2B; --color-text-secondary: #5A5A5A;
  --color-text-light: #8A8A8A; --color-white: #FFFFFF; --color-border: #E0DAD0;
  --color-success: #3A7D44;
  --font-display: 'DM Serif Display', Georgia, serif;
  --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --space-xs: 0.25rem; --space-sm: 0.5rem; --space-md: 1rem; --space-lg: 1.5rem;
  --space-xl: 2rem; --space-2xl: 3rem; --space-3xl: 4rem; --space-4xl: 6rem;
  --max-width: 1280px;
  --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px; --radius-xl: 24px;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.06); --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
  --shadow-lg: 0 8px 30px rgba(0,0,0,0.10); --shadow-xl: 0 16px 50px rgba(0,0,0,0.12);
  --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
  --transition-fast: 0.2s var(--ease-out); --transition-base: 0.35s var(--ease-out);
  --transition-slow: 0.6s var(--ease-out);
}
.app-wrap *, .app-wrap *::before, .app-wrap *::after { box-sizing: border-box; }
.app-wrap { font-family: var(--font-body); color: var(--color-text); background: var(--color-cream); line-height: 1.7; font-size: 16px; overflow-x: hidden; }
.app-wrap img { max-width: 100%; height: auto; display: block; }
.app-wrap a { color: inherit; text-decoration: none; }
.app-wrap button { cursor: pointer; border: none; background: none; font-family: inherit; }
.app-container { max-width: var(--max-width); margin: 0 auto; padding: 0 var(--space-xl); }
@keyframes app-fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
@keyframes app-fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes app-scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.app-animate-in { opacity: 0; animation: app-fadeInUp 0.7s var(--ease-out) forwards; }
/* Breadcrumbs */
.app-breadcrumbs { padding: var(--space-lg) 0; font-size: 0.825rem; color: var(--color-text-light); }
.app-breadcrumbs a { color: var(--color-text-secondary); transition: color var(--transition-fast); }
.app-breadcrumbs a:hover { color: var(--color-accent); }
.app-breadcrumbs span { margin: 0 var(--space-sm); opacity: 0.5; }
/* Hero */
.app-plan-hero { padding-bottom: var(--space-4xl); }
.app-plan-hero__grid { display: grid; grid-template-columns: 1fr 420px; gap: var(--space-3xl); align-items: start; }
/* Hero Image */
.app-hero-image { animation: app-fadeIn 0.6s var(--ease-out) forwards; }
.app-hero-image__main { position: relative; border-radius: var(--radius-lg); overflow: hidden; background: var(--color-stone); aspect-ratio: 3/2; box-shadow: var(--shadow-lg); }
.app-hero-image__main img { width: 100%; height: 100%; object-fit: cover; }
.app-hero-image__badge { position: absolute; top: var(--space-lg); left: var(--space-lg); background: var(--color-primary); color: var(--color-white); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: var(--space-xs) var(--space-md); border-radius: 100px; z-index: 2; }
/* Foobox Gallery Grid */
.app-gallery-grid { margin-top: var(--space-lg); }
.app-gallery-grid .gallery { display: grid !important; grid-template-columns: repeat(9, 1fr); gap: 4px; float: none !important; width: 100% !important; }
.app-gallery-grid .gallery-item { float: none !important; width: auto !important; margin: 0 !important; }
.app-gallery-grid .gallery-item img { width: 100%; height: 60px; object-fit: cover; border-radius: 3px; border: none !important; display: block; transition: opacity var(--transition-fast); cursor: pointer; }
.app-gallery-grid .gallery-item img:hover { opacity: 0.85; }
/* Purchase Card */
.app-purchase-card { position: sticky; top: var(--space-xl); background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: hidden; animation: app-scaleIn 0.5s var(--ease-out) 0.2s forwards; opacity: 0; }
.app-purchase-card__header { padding: var(--space-xl) var(--space-xl) var(--space-lg); border-bottom: 1px solid var(--color-border); }
.app-purchase-card__plan-name { font-family: var(--font-display); font-size: 1.65rem; color: var(--color-primary-dark); line-height: 1.2; margin-bottom: var(--space-xs); }
.app-purchase-card__style { font-size: 0.825rem; color: var(--color-text-light); font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; }
.app-purchase-card__specs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; background: var(--color-border); }
.app-spec-cell { background: var(--color-white); padding: var(--space-md) var(--space-lg); text-align: center; }
.app-spec-cell__value { font-family: var(--font-display); font-size: 1.35rem; color: var(--color-primary); line-height: 1.2; }
.app-spec-cell__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-text-light); margin-top: 2px; font-weight: 600; }
.app-purchase-card__body { padding: var(--space-xl); }
.app-price-option { display: flex; align-items: center; padding: var(--space-md) var(--space-lg); border: 2px solid var(--color-border); border-radius: var(--radius-md); margin-bottom: var(--space-sm); cursor: pointer; transition: all var(--transition-fast); }
.app-price-option:hover { border-color: var(--color-accent-light); background: var(--color-cream); }
.app-price-option.app-selected { border-color: var(--color-accent); background: linear-gradient(135deg, rgba(184,134,11,0.04), rgba(184,134,11,0.02)); }
.app-price-option input[type="radio"] { appearance: none; -webkit-appearance: none; width: 20px; height: 20px; border: 2px solid var(--color-stone-dark); border-radius: 50%; margin-right: var(--space-md); flex-shrink: 0; transition: all var(--transition-fast); }
.app-price-option input[type="radio"]:checked { border-color: var(--color-accent); background: var(--color-accent); box-shadow: inset 0 0 0 3px var(--color-white); }
.app-price-option__info { flex: 1; }
.app-price-option__format { font-weight: 600; font-size: 0.9rem; }
.app-price-option__desc { font-size: 0.75rem; color: var(--color-text-light); margin-top: 1px; }
.app-price-option__price { font-family: var(--font-display); font-size: 1.25rem; color: var(--color-primary); }
.app-btn-buy { display: flex; align-items: center; justify-content: center; width: 100%; padding: var(--space-lg) var(--space-xl); background: var(--color-accent); color: var(--color-white); font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-top: var(--space-lg); transition: all var(--transition-fast); letter-spacing: 0.02em; gap: var(--space-sm); box-shadow: 0 4px 16px rgba(184,134,11,0.3); text-decoration: none; }
.app-btn-buy:hover { background: var(--color-accent-warm); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(184,134,11,0.35); color: var(--color-white); }
.app-btn-buy svg { width: 20px; height: 20px; }
.app-trust-row { display: flex; justify-content: center; gap: var(--space-xl); margin-top: var(--space-lg); padding-top: var(--space-lg); border-top: 1px solid var(--color-border); }
.app-trust-item { display: flex; align-items: center; gap: var(--space-xs); font-size: 0.75rem; color: var(--color-text-light); font-weight: 500; }
.app-trust-item svg { width: 16px; height: 16px; color: var(--color-success); flex-shrink: 0; }
.app-purchase-card__footer { padding: var(--space-md) var(--space-xl) var(--space-xl); text-align: center; }
.app-purchase-card__footer a { font-size: 0.825rem; color: var(--color-accent); font-weight: 600; }
/* Quick Specs */
.app-quick-specs { background: var(--color-primary); padding: var(--space-xl) 0; margin-bottom: var(--space-4xl); }
.app-quick-specs__grid { display: flex; justify-content: center; gap: var(--space-3xl); flex-wrap: wrap; }
.app-quick-spec { text-align: center; color: var(--color-white); }
.app-quick-spec__icon { width: 32px; height: 32px; margin: 0 auto var(--space-sm); opacity: 0.7; }
.app-quick-spec__value { font-family: var(--font-display); font-size: 1.5rem; line-height: 1.2; }
.app-quick-spec__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.65; font-weight: 600; margin-top: 2px; }
/* Sections */
.app-section { padding: var(--space-4xl) 0; }
.app-section--alt { background: var(--color-white); }
.app-section__header { text-align: center; margin-bottom: var(--space-3xl); }
.app-section__overline { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--color-accent); margin-bottom: var(--space-sm); }
.app-section__title { font-family: var(--font-display); font-size: 2.2rem; color: var(--color-primary-dark); line-height: 1.2; margin-bottom: var(--space-md); }
.app-section__subtitle { font-size: 1.05rem; color: var(--color-text-secondary); max-width: 640px; margin: 0 auto; line-height: 1.7; }
/* Description */
.app-description-grid { display: grid; grid-template-columns: 1fr 380px; gap: var(--space-3xl); align-items: start; }
.app-description__content h2 { font-family: var(--font-display); font-size: 2rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); line-height: 1.25; }
.app-description__content p { color: var(--color-text-secondary); margin-bottom: var(--space-lg); font-size: 1.02rem; }
/* Highlights */
.app-highlights-card { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-md); border: 1px solid var(--color-border); }
.app-highlights-card__title { font-family: var(--font-display); font-size: 1.15rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); padding-bottom: var(--space-md); border-bottom: 2px solid var(--color-accent); display: inline-block; }
.app-highlight-item { display: flex; align-items: flex-start; gap: var(--space-md); margin-bottom: var(--space-lg); }
.app-highlight-item:last-child { margin-bottom: 0; }
.app-highlight-item__icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--color-cream), var(--color-stone)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.app-highlight-item__icon svg { width: 18px; height: 18px; color: var(--color-primary); }
.app-highlight-item__text strong { display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-text); margin-bottom: 1px; }
.app-highlight-item__text span { font-size: 0.8rem; color: var(--color-text-light); }
/* Specs */
.app-specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-xl); }
.app-specs-group { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--color-border); }
.app-specs-group__title { font-family: var(--font-display); font-size: 1.1rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); padding-bottom: var(--space-sm); border-bottom: 2px solid var(--color-stone); }
.app-spec-row { display: flex; justify-content: space-between; align-items: baseline; padding: var(--space-sm) 0; border-bottom: 1px solid var(--color-cream-dark); }
.app-spec-row:last-child { border-bottom: none; }
.app-spec-row__label { font-size: 0.85rem; color: var(--color-text-secondary); }
.app-spec-row__value { font-size: 0.875rem; font-weight: 600; color: var(--color-text); text-align: right; }
/* Floor Plans */
.app-floor-plans__wysiwyg img { max-width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
/* Included */
.app-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-xl); }
.app-included-card { background: var(--color-cream); border-radius: var(--radius-lg); padding: var(--space-xl); text-align: center; border: 1px solid var(--color-border); transition: all var(--transition-base); }
.app-included-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--color-accent-light); }
.app-included-card__icon { width: 56px; height: 56px; background: var(--color-white); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg); box-shadow: var(--shadow-sm); }
.app-included-card__icon svg { width: 28px; height: 28px; color: var(--color-primary); }
.app-included-card__title { font-weight: 700; font-size: 0.95rem; margin-bottom: var(--space-sm); color: var(--color-primary-dark); }
.app-included-card__desc { font-size: 0.825rem; color: var(--color-text-light); line-height: 1.6; }
/* FAQ */
.app-faq-list { max-width: 820px; margin: 0 auto; }
.app-faq-item { background: var(--color-white); border-radius: var(--radius-md); margin-bottom: var(--space-md); border: 1px solid var(--color-border); overflow: hidden; }
.app-faq-item.app-open { box-shadow: var(--shadow-md); border-color: var(--color-accent-light); }
.app-faq-question { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: var(--space-lg) var(--space-xl); font-size: 0.95rem; font-weight: 600; text-align: left; color: var(--color-text); gap: var(--space-md); }
.app-faq-question__icon { width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: var(--color-cream); display: flex; align-items: center; justify-content: center; transition: all var(--transition-fast); }
.app-faq-item.app-open .app-faq-question__icon { background: var(--color-accent); transform: rotate(45deg); }
.app-faq-question__icon svg { width: 14px; height: 14px; color: var(--color-text-secondary); }
.app-faq-item.app-open .app-faq-question__icon svg { color: var(--color-white); }
.app-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s var(--ease-out); }
.app-faq-answer__inner { padding: 0 var(--space-xl) var(--space-xl); font-size: 0.9rem; color: var(--color-text-secondary); line-height: 1.8; }
/* CTA */
.app-cta-section { background: var(--color-primary); padding: var(--space-4xl) 0; text-align: center; color: var(--color-white); }
.app-cta__title { font-family: var(--font-display); font-size: 2.2rem; margin-bottom: var(--space-md); }
.app-cta__text { font-size: 1.05rem; opacity: 0.8; max-width: 560px; margin: 0 auto var(--space-xl); line-height: 1.7; }
.app-cta__buttons { display: flex; justify-content: center; gap: var(--space-lg); flex-wrap: wrap; }
.app-btn-cta { display: inline-flex; align-items: center; gap: var(--space-sm); padding: var(--space-md) var(--space-2xl); border-radius: var(--radius-md); font-weight: 700; font-size: 0.95rem; transition: all var(--transition-fast); }
.app-btn-cta--primary { background: var(--color-accent); color: var(--color-white); box-shadow: 0 4px 16px rgba(184,134,11,0.35); }
.app-btn-cta--primary:hover { background: var(--color-accent-warm); transform: translateY(-2px); color: var(--color-white); }
.app-btn-cta--outline { border: 2px solid rgba(255,255,255,0.3); color: var(--color-white); }
.app-btn-cta--outline:hover { border-color: var(--color-white); background: rgba(255,255,255,0.08); }
/* Mobile Buy Bar */
.app-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--color-white); border-top: 1px solid var(--color-border); padding: var(--space-md) var(--space-lg); z-index: 100; box-shadow: 0 -4px 20px rgba(0,0,0,0.1); }
.app-mobile-buy-bar__inner { display: flex; align-items: center; justify-content: space-between; max-width: var(--max-width); margin: 0 auto; }
.app-mobile-buy-bar__name { font-weight: 700; color: var(--color-primary-dark); font-size: 0.9rem; }
.app-mobile-buy-bar__price { font-family: var(--font-display); color: var(--color-accent); font-size: 1.1rem; }
.app-mobile-buy-bar .app-btn-buy { width: auto; padding: var(--space-sm) var(--space-xl); margin-top: 0; font-size: 0.875rem; }
/* Responsive */
@media (max-width: 1024px) {
  .app-plan-hero__grid { grid-template-columns: 1fr; }
  .app-purchase-card { position: relative; top: 0; }
  .app-description-grid { grid-template-columns: 1fr; }
  .app-specs-grid { grid-template-columns: 1fr 1fr; }
  .app-included-grid { grid-template-columns: repeat(2, 1fr); }
  .app-mobile-buy-bar { display: block; }
  .app-wrap { padding-bottom: 80px; }
}
@media (max-width: 768px) {
  .app-gallery-grid .gallery { grid-template-columns: repeat(5, 1fr); }
  .app-specs-grid { grid-template-columns: 1fr; }
  .app-included-grid { grid-template-columns: 1fr 1fr; }
  .app-section__title { font-size: 1.75rem; }
}
@media (max-width: 480px) {
  .app-container { padding: 0 var(--space-lg); }
  .app-gallery-grid .gallery { grid-template-columns: repeat(4, 1fr); }
  .app-cta__buttons { flex-direction: column; align-items: center; }
}
</style>
<?php
}

function mhp_appalachia_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    $plan_name           = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft                = $acf ? get_field( 'total_living_area',   $pid ) : '2,100';
    $main_floor          = $acf ? get_field( 'main_floor',          $pid ) : '';
    $upper_floor         = $acf ? get_field( 'upper_floor',         $pid ) : '';
    $lower_floor         = $acf ? get_field( 'lower_floor',         $pid ) : 'Optional Finish';
    $bedrooms            = $acf ? get_field( 'bedrooms',            $pid ) : '3';
    $bathrooms           = $acf ? get_field( 'bathrooms',           $pid ) : '3';
    $stories             = $acf ? get_field( 'stories',             $pid ) : '2';
    $width               = $acf ? get_field( 'width',               $pid ) : '';
    $depth               = $acf ? get_field( 'depth',               $pid ) : '';
    $garage              = $acf ? get_field( 'garage',              $pid ) : '';
    $style               = $acf ? get_field( 'style',               $pid ) : 'A-Frame · Mountain · Lake · Craftsman';
    $outdoor             = $acf ? get_field( 'outdoor',             $pid ) : 'Porches, Outdoor Fireplace';
    $roof                = $acf ? get_field( 'roof',                $pid ) : 'A-Frame';
    $ceiling             = $acf ? get_field( 'ceiling',             $pid ) : 'Vaulted';
    $exterior            = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms    = $acf ? get_field( 'additional_rooms',    $pid ) : 'Bunkrooms, Recreation Room, Wet Bar';
    $other_features      = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style           = $acf ? get_field( 'lot_style',           $pid ) : 'Sloping, Mountain, Lake';
    $plan_description    = $acf ? get_field( 'plan_description',    $pid ) : '';
    $floor_plans_wysiwyg = $acf ? get_field( 'floor_plans',         $pid ) : '';
    $paypal              = $acf ? get_field( 'paypal',              $pid ) : '';
    $price               = $acf ? get_field( 'price',               $pid ) : '1195';
    $related_plans       = $acf ? get_field( 'related_plans',       $pid ) : array();
    $faqs                = $acf ? get_field( 'faqs',                $pid ) : array();

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
    if ( ! $hero_url ) $hero_url = 'https://www.maxhouseplans.com/wp-content/uploads/2011/08/Appalchian-Mountain-House-Plan.jpg';

    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft' ), '', $v ); };
    $sqft_display     = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display  = $main_floor ? ( $clean($main_floor) > 0 ? number_format($clean($main_floor)) : $main_floor ) : '';
    $upper_fl_display = $upper_floor ? ( $clean($upper_floor) > 0 ? number_format($clean($upper_floor)) : $upper_floor ) : '';
    $footprint        = ( $width && $depth ) ? rtrim($width,"'") . "' × " . rtrim($depth,"'") . "'" : '';
    $sqft_int         = $clean($sqft) > 100 ? $clean($sqft) : 2100;

    // Get post content for gallery shortcode
    $post_content = get_post_field( 'post_content', $pid );
?>
<div class="app-wrap">

<!-- BREADCRUMBS -->
<nav class="app-breadcrumbs" aria-label="Breadcrumb">
  <div class="app-container">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/home-plans/mountain-house-plans/')); ?>">Mountain House Plans</a><span>&#8250;</span>
    <strong><?php echo esc_html($plan_name); ?></strong>
  </div>
</nav>

<!-- HERO -->
<section class="app-plan-hero">
  <div class="app-container">
    <div class="app-plan-hero__grid">

      <!-- Featured Image + Gallery -->
      <div class="app-hero-image app-animate-in">
        <div class="app-hero-image__main">
          <span class="app-hero-image__badge">A-Frame Mountain Plan</span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> — A-frame mountain house plan with walls of glass and outdoor fireplace" fetchpriority="high">
        </div>
        <?php if ( has_shortcode( $post_content, 'gallery' ) ) : ?>
        <div class="app-gallery-grid">
          <?php echo do_shortcode( '[gallery ' . implode( ' ', array_slice( explode( ' ', trim( str_replace( ['[gallery', ']'], '', strstr( strstr( $post_content, '[gallery' ), ']', true ) ) ) ), 1 ) ) . ']' ); ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Purchase Card -->
      <aside class="app-purchase-card" aria-label="Plan purchase options">
        <div class="app-purchase-card__header">
          <h1 class="app-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
          <p class="app-purchase-card__style"><?php echo esc_html($style); ?></p>
        </div>
        <div class="app-purchase-card__specs">
          <div class="app-spec-cell"><div class="app-spec-cell__value"><?php echo esc_html($sqft_display); ?></div><div class="app-spec-cell__label">Sq Ft</div></div>
          <div class="app-spec-cell"><div class="app-spec-cell__value"><?php echo esc_html($bedrooms); ?></div><div class="app-spec-cell__label">Bedrooms</div></div>
          <div class="app-spec-cell"><div class="app-spec-cell__value"><?php echo esc_html($bathrooms); ?></div><div class="app-spec-cell__label">Bathrooms</div></div>
          <div class="app-spec-cell"><div class="app-spec-cell__value"><?php echo esc_html($stories); ?></div><div class="app-spec-cell__label">Stories</div></div>
        </div>
        <div class="app-purchase-card__body">
          <label class="app-price-option app-selected" id="appOptionPdf">
            <input type="radio" name="app_plan_format" value="pdf" checked onchange="appSelectOption('pdf')">
            <div class="app-price-option__info"><div class="app-price-option__format">PDF Plan Set</div><div class="app-price-option__desc">Print-ready digital plans</div></div>
            <div class="app-price-option__price"><?php echo esc_html($price_fmt); ?></div>
          </label>
          <label class="app-price-option" id="appOptionCad">
            <input type="radio" name="app_plan_format" value="cad" onchange="appSelectOption('cad')">
            <div class="app-price-option__info"><div class="app-price-option__format">CAD File</div><div class="app-price-option__desc">Editable for your builder</div></div>
            <div class="app-price-option__price"><?php echo esc_html($cad_price); ?></div>
          </label>
          <a href="<?php echo esc_url($buy_href); ?>" class="app-btn-buy">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="m16 10-4 4-4-4"/></svg>
            Purchase This Plan
          </a>
          <div class="app-trust-row">
            <span class="app-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Secure Checkout</span>
            <span class="app-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Instant Download</span>
          </div>
        </div>
        <div class="app-purchase-card__footer">
          <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>">Need changes? Request a modification &#8594;</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- QUICK SPECS -->
<div class="app-quick-specs">
  <div class="app-container">
    <div class="app-quick-specs__grid">
      <div class="app-quick-spec">
        <svg class="app-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>
        <div class="app-quick-spec__value"><?php echo esc_html($sqft_display); ?> sq ft</div>
        <div class="app-quick-spec__label">Heated Area</div>
      </div>
      <?php if ($footprint) : ?>
      <div class="app-quick-spec">
        <svg class="app-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <div class="app-quick-spec__value"><?php echo esc_html($footprint); ?></div>
        <div class="app-quick-spec__label">Footprint</div>
      </div>
      <?php endif; ?>
      <?php if ($main_fl_display) : ?>
      <div class="app-quick-spec">
        <svg class="app-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M4 20V8l8-6 8 6v12"/></svg>
        <div class="app-quick-spec__value">Main: <?php echo esc_html($main_fl_display); ?></div>
        <div class="app-quick-spec__label">Main Floor Sq Ft</div>
      </div>
      <?php endif; ?>
      <div class="app-quick-spec">
        <svg class="app-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
        <div class="app-quick-spec__value">Optional Basement</div>
        <div class="app-quick-spec__label">Bunkrooms + Rec Room</div>
      </div>
      <div class="app-quick-spec">
        <svg class="app-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
        <div class="app-quick-spec__value">From <?php echo esc_html($price_fmt); ?></div>
        <div class="app-quick-spec__label">Plan Price</div>
      </div>
    </div>
  </div>
</div>

<!-- DESCRIPTION -->
<section class="app-section" id="description">
  <div class="app-container">
    <div class="app-description-grid">
      <div class="app-description__content">
        <?php if ( $plan_description ) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <h2>Classic A-Frame Design Built for Mountain and Lake Lots</h2>
          <p>The <?php echo esc_html($plan_name); ?> is a pure mountain plan. The A-frame roofline and walls of glass on both the front and rear aren&#8217;t stylistic choices &#8212; they&#8217;re how this home captures the landscape. Every seat in the main living area looks out through glass, and every level opens to covered outdoor space.</p>
          <p>The main level is built around an open living plan with the kitchen, dining, and great room flowing together. The master suite sits on this level and is deliberately separated from the two additional bedrooms, giving it privacy without requiring a second story. Ample porches and an outdoor fireplace extend the living space beyond the walls of the home.</p>
          <p>Below, the basement level can be left unfinished or built out to include bunkrooms, a recreation room with a wet bar, and additional storage &#8212; making this plan ideal for large families, lake retreats, or vacation rental properties where sleeping capacity matters.</p>
          <p>This plan is specifically designed for sloping mountain or lake lots where the rear elevation can take advantage of views. The A-frame profile handles snow loads well and gives the home a strong architectural identity from the road.</p>
        <?php endif; ?>
      </div>

      <div class="app-highlights-card">
        <h3 class="app-highlights-card__title">Plan Highlights</h3>
        <div class="app-highlight-item">
          <div class="app-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div>
          <div class="app-highlight-item__text"><strong>Classic A-Frame Roofline</strong><span>Dramatic profile, handles snow loads, strong curb appeal</span></div>
        </div>
        <div class="app-highlight-item">
          <div class="app-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div>
          <div class="app-highlight-item__text"><strong>Walls of Glass</strong><span>Front and rear glass maximizes mountain and lake views</span></div>
        </div>
        <div class="app-highlight-item">
          <div class="app-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
          <div class="app-highlight-item__text"><strong>Main-Level Master Suite</strong><span>Separated from guest bedrooms for maximum privacy</span></div>
        </div>
        <div class="app-highlight-item">
          <div class="app-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></div>
          <div class="app-highlight-item__text"><strong>Outdoor Fireplace</strong><span>Covered porch with fireplace extends the living season</span></div>
        </div>
        <div class="app-highlight-item">
          <div class="app-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
          <div class="app-highlight-item__text"><strong>Optional Finished Basement</strong><span>Bunkrooms, rec room, wet bar &#8212; finish to your needs</span></div>
        </div>
        <div class="app-highlight-item">
          <div class="app-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
          <div class="app-highlight-item__text"><strong>Rear View Design</strong><span>Built for sloping lots — every level faces the view</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SPECS -->
<section class="app-section app-section--alt" id="specs">
  <div class="app-container">
    <div class="app-section__header">
      <p class="app-section__overline">Specifications</p>
      <h2 class="app-section__title">Full Plan Details</h2>
      <p class="app-section__subtitle">Everything you and your builder need to evaluate this plan.</p>
    </div>
    <div class="app-specs-grid">
      <div class="app-specs-group">
        <h3 class="app-specs-group__title">Living Area</h3>
        <?php if ($main_fl_display) : ?><div class="app-spec-row"><span class="app-spec-row__label">Main Floor</span><span class="app-spec-row__value"><?php echo esc_html($main_fl_display); ?> sq ft</span></div><?php endif; ?>
        <?php if ($upper_fl_display) : ?><div class="app-spec-row"><span class="app-spec-row__label">Upper Floor</span><span class="app-spec-row__value"><?php echo esc_html($upper_fl_display); ?> sq ft</span></div><?php endif; ?>
        <div class="app-spec-row"><span class="app-spec-row__label">Lower / Basement</span><span class="app-spec-row__value"><?php echo esc_html($lower_floor); ?></span></div>
        <div class="app-spec-row"><span class="app-spec-row__label">Total Heated</span><span class="app-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div>
        <?php if ($width) : ?><div class="app-spec-row"><span class="app-spec-row__label">Width</span><span class="app-spec-row__value"><?php echo esc_html($width); ?></span></div><?php endif; ?>
        <?php if ($depth) : ?><div class="app-spec-row"><span class="app-spec-row__label">Depth</span><span class="app-spec-row__value"><?php echo esc_html($depth); ?></span></div><?php endif; ?>
      </div>
      <div class="app-specs-group">
        <h3 class="app-specs-group__title">House Features</h3>
        <div class="app-spec-row"><span class="app-spec-row__label">Bedrooms</span><span class="app-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div>
        <div class="app-spec-row"><span class="app-spec-row__label">Bathrooms</span><span class="app-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div>
        <div class="app-spec-row"><span class="app-spec-row__label">Stories</span><span class="app-spec-row__value"><?php echo esc_html($stories); ?></span></div>
        <?php if ($additional_rooms) : ?><div class="app-spec-row"><span class="app-spec-row__label">Additional Rooms</span><span class="app-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div><?php endif; ?>
        <?php if ($garage) : ?><div class="app-spec-row"><span class="app-spec-row__label">Garage</span><span class="app-spec-row__value"><?php echo esc_html($garage); ?></span></div><?php endif; ?>
        <?php if ($outdoor) : ?><div class="app-spec-row"><span class="app-spec-row__label">Outdoor Spaces</span><span class="app-spec-row__value"><?php echo esc_html($outdoor); ?></span></div><?php endif; ?>
      </div>
      <div class="app-specs-group">
        <h3 class="app-specs-group__title">Construction</h3>
        <?php if ($exterior) : ?><div class="app-spec-row"><span class="app-spec-row__label">Exterior Framing</span><span class="app-spec-row__value"><?php echo esc_html($exterior); ?></span></div><?php endif; ?>
        <?php if ($ceiling) : ?><div class="app-spec-row"><span class="app-spec-row__label">Ceiling Height</span><span class="app-spec-row__value"><?php echo esc_html($ceiling); ?></span></div><?php endif; ?>
        <?php if ($roof) : ?><div class="app-spec-row"><span class="app-spec-row__label">Roof Style</span><span class="app-spec-row__value"><?php echo esc_html($roof); ?></span></div><?php endif; ?>
        <?php if ($lot_style) : ?><div class="app-spec-row"><span class="app-spec-row__label">Lot Style</span><span class="app-spec-row__value"><?php echo esc_html($lot_style); ?></span></div><?php endif; ?>
        <?php if ($style) : ?><div class="app-spec-row"><span class="app-spec-row__label">Home Style</span><span class="app-spec-row__value"><?php echo esc_html($style); ?></span></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- FLOOR PLANS -->
<section class="app-section" id="floorplans">
  <div class="app-container">
    <div class="app-section__header">
      <p class="app-section__overline">Floor Plans</p>
      <h2 class="app-section__title">Explore the Layout</h2>
      <p class="app-section__subtitle">Main level open living with separated master suite, plus an optional finished basement for additional sleeping and recreation space.</p>
    </div>
    <?php if ( $floor_plans_wysiwyg ) : ?>
      <div class="app-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans_wysiwyg); ?></div>
    <?php else : ?>
      <p style="text-align:center;color:var(--color-text-secondary);">Floor plan drawings available in the purchased plan set.</p>
    <?php endif; ?>
  </div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="app-section app-section--alt" id="whats-included">
  <div class="app-container">
    <div class="app-section__header">
      <p class="app-section__overline">Your Plan Set</p>
      <h2 class="app-section__title">What&#8217;s Included</h2>
      <p class="app-section__subtitle">Complete and build-ready. Everything your builder needs.</p>
    </div>
    <div class="app-included-grid">
      <div class="app-included-card">
        <div class="app-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg></div>
        <h3 class="app-included-card__title">Elevations</h3>
        <p class="app-included-card__desc">Front, side, and rear at &#188;&#8221; scale with material notes.</p>
      </div>
      <div class="app-included-card">
        <div class="app-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
        <h3 class="app-included-card__title">Floor Plans</h3>
        <p class="app-included-card__desc">Dimensioned plans for all levels including optional basement.</p>
      </div>
      <div class="app-included-card">
        <div class="app-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M5 20V8l7-5 7 5v12"/></svg></div>
        <h3 class="app-included-card__title">Foundation Plan</h3>
        <p class="app-included-card__desc">Foundation layout with footing and wall dimensions.</p>
      </div>
      <div class="app-included-card">
        <div class="app-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7"/><path d="M3 9v0l9 4 9-4"/></svg></div>
        <h3 class="app-included-card__title">Roof Plan</h3>
        <p class="app-included-card__desc">A-frame roof plan with pitches and drainage detail.</p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="app-section" id="faq">
  <div class="app-container">
    <div class="app-section__header">
      <p class="app-section__overline">Common Questions</p>
      <h2 class="app-section__title">Frequently Asked Questions</h2>
    </div>
    <div class="app-faq-list">
      <?php if ( ! empty($faqs) && is_array($faqs) ) :
            foreach ( $faqs as $faq ) :
              $q = isset($faq['question']) ? $faq['question'] : '';
              $a = isset($faq['answer'])   ? $faq['answer']   : '';
              if ( ! $q ) continue; ?>
      <div class="app-faq-item">
        <button class="app-faq-question" onclick="appToggleFaq(this)" aria-expanded="false">
          <span><?php echo esc_html($q); ?></span>
          <span class="app-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="app-faq-answer"><div class="app-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
      </div>
      <?php endforeach; else : ?>
      <div class="app-faq-item"><button class="app-faq-question" onclick="appToggleFaq(this)" aria-expanded="false"><span>What is included in the plan set?</span><span class="app-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="app-faq-answer"><div class="app-faq-answer__inner">Each set includes dimensioned floor plans for all levels, front/side/rear elevations, a foundation plan, and a roof plan. Available as PDF (<?php echo esc_html($price_fmt); ?>) or editable CAD files (<?php echo esc_html($cad_price); ?>).</div></div></div>
      <div class="app-faq-item"><button class="app-faq-question" onclick="appToggleFaq(this)" aria-expanded="false"><span>Can this plan be modified?</span><span class="app-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="app-faq-answer"><div class="app-faq-answer__inner">Yes. We handle modifications in-house. Common requests include expanding the basement, adjusting bedroom layouts, or changing exterior materials. Contact us for a scope and quote.</div></div></div>
      <div class="app-faq-item"><button class="app-faq-question" onclick="appToggleFaq(this)" aria-expanded="false"><span>What kind of lot does this plan require?</span><span class="app-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="app-faq-answer"><div class="app-faq-answer__inner">This plan is designed for sloping mountain or lakefront lots where rear views are the priority. The A-frame profile works well on properties with significant grade change.</div></div></div>
      <div class="app-faq-item"><button class="app-faq-question" onclick="appToggleFaq(this)" aria-expanded="false"><span>Does this work as a vacation rental?</span><span class="app-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="app-faq-answer"><div class="app-faq-answer__inner">Yes &#8212; the A-frame style is extremely popular in the vacation rental market. The optional basement bunkroom setup increases sleeping capacity significantly, which directly impacts nightly rate potential.</div></div></div>
      <div class="app-faq-item"><button class="app-faq-question" onclick="appToggleFaq(this)" aria-expanded="false"><span>Does this plan require a structural engineer&#8217;s stamp?</span><span class="app-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="app-faq-answer"><div class="app-faq-answer__inner">Stock plans do not include a professional stamp. If your local building department requires one, you&#8217;ll need a licensed engineer or architect in your state to review and stamp the plans.</div></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="app-cta-section">
  <div class="app-container">
    <h2 class="app-cta__title">Ready to Build Your Mountain Home?</h2>
    <p class="app-cta__text">Whether you&#8217;re ready to purchase the <?php echo esc_html($plan_name); ?> or want to talk through modifications, our family is here to help.</p>
    <div class="app-cta__buttons">
      <a href="<?php echo esc_url($buy_href); ?>" class="app-btn-cta app-btn-cta--primary">Purchase This Plan</a>
      <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="app-btn-cta app-btn-cta--outline">Request a Modification</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="app-btn-cta app-btn-cta--outline">Talk With Our Family</a>
    </div>
  </div>
</section>

<!-- MOBILE BAR -->
<div class="app-mobile-buy-bar">
  <div class="app-mobile-buy-bar__inner">
    <div>
      <div class="app-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
      <div class="app-mobile-buy-bar__price">From <?php echo esc_html($price_fmt); ?></div>
    </div>
    <a href="<?php echo esc_url($buy_href); ?>" class="app-btn-buy">Purchase Plan</a>
  </div>
</div>

</div><!-- .app-wrap -->

<script>
(function(){
  window.appSelectOption = function(fmt) {
    document.querySelectorAll('.app-price-option').forEach(function(o){o.classList.remove('app-selected');});
    var el = document.getElementById(fmt==='pdf'?'appOptionPdf':'appOptionCad'); if(el) el.classList.add('app-selected');
  };
  window.appToggleFaq = function(btn) {
    var item = btn.parentElement, isOpen = item.classList.contains('app-open');
    document.querySelectorAll('.app-faq-item').forEach(function(f){
      f.classList.remove('app-open');
      var q=f.querySelector('.app-faq-question'); if(q) q.setAttribute('aria-expanded','false');
      var a=f.querySelector('.app-faq-answer'); if(a) a.style.maxHeight='0';
    });
    if (!isOpen) {
      item.classList.add('app-open'); btn.setAttribute('aria-expanded','true');
      var ans = item.querySelector('.app-faq-answer'); if(ans) ans.style.maxHeight = ans.scrollHeight+'px';
    }
  };
})();
</script>
<?php
}

genesis();
