<?php
/**
 * Template Name: Plan - Mountain Home with Walkout Basement
 *
 * Dedicated plan template for the Mountain Home House Plan with Walkout Basement.
 * Slug: mountain-home-house-plan-with-walkout-basement
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-21
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_mhwb_loop' );
add_action( 'wp_head', 'mhp_mhwb_styles', 20 );
add_action( 'wp_head', 'mhp_mhwb_schema', 5 );
add_action( 'wp_head', 'mhp_mhwb_fonts', 1 );

function mhp_mhwb_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">' . "\n";
}

function mhp_mhwb_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid  = get_the_ID();
    $acf  = function_exists( 'get_field' );
    $name    = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $name    = $name ?: get_the_title();
    $sqft    = $acf ? get_field( 'total_living_area', $pid ) : '2500';
    $beds    = $acf ? get_field( 'bedrooms', $pid ) : '4';
    $baths   = $acf ? get_field( 'bathrooms', $pid ) : '3';
    $stories = $acf ? get_field( 'stories', $pid ) : '2';
    $price   = $acf ? get_field( 'price', $pid ) : '1195';
    $price_num = is_numeric( $price ) ? number_format( (float) $price, 2, '.', '' ) : '1195.00';
    $image_url = get_the_post_thumbnail_url( $pid, 'full' );
    $permalink = get_permalink( $pid );

    $product = array(
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name'  => $name . ' House Plan',
        'description' => 'A build-ready mountain home plan with walkout basement, open main-level living, master suite on main, covered porches, and mountain or lake lot design. Ideal for sloping properties with rear views.',
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
            array( '@type' => 'PropertyValue', 'name' => 'Style', 'value' => 'Mountain, Craftsman, Rustic' ),
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

function mhp_mhwb_styles() {
?>
<style id="mhp-mhwb-styles">
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
}
.mhwb-wrap *, .mhwb-wrap *::before, .mhwb-wrap *::after { box-sizing: border-box; }
.mhwb-wrap { font-family: var(--font-body); color: var(--color-text); background: var(--color-cream); line-height: 1.7; font-size: 16px; overflow-x: hidden; }
.mhwb-wrap img { max-width: 100%; height: auto; display: block; }
.mhwb-wrap a { color: inherit; text-decoration: none; }
.mhwb-wrap button { cursor: pointer; border: none; background: none; font-family: inherit; }
.mhwb-container { max-width: var(--max-width); margin: 0 auto; padding: 0 var(--space-xl); }
@keyframes mhwb-fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
@keyframes mhwb-scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.mhwb-animate-in { opacity: 0; animation: mhwb-fadeInUp 0.7s var(--ease-out) forwards; }
/* Breadcrumbs */
.mhwb-breadcrumbs { padding: var(--space-lg) 0; font-size: 0.825rem; color: var(--color-text-light); }
.mhwb-breadcrumbs a { color: var(--color-text-secondary); transition: color var(--transition-fast); }
.mhwb-breadcrumbs a:hover { color: var(--color-accent); }
.mhwb-breadcrumbs span { margin: 0 var(--space-sm); opacity: 0.5; }
/* Hero */
.mhwb-plan-hero { padding-bottom: var(--space-4xl); }
.mhwb-plan-hero__grid { display: grid; grid-template-columns: 1fr 420px; gap: var(--space-3xl); align-items: start; }
/* Hero Image */
.mhwb-hero-image { }
.mhwb-hero-image__main { position: relative; border-radius: var(--radius-lg); overflow: hidden; background: var(--color-stone); aspect-ratio: 3/2; box-shadow: var(--shadow-lg); }
.mhwb-hero-image__main img { width: 100%; height: 100%; object-fit: cover; }
.mhwb-hero-image__badge { position: absolute; top: var(--space-lg); left: var(--space-lg); background: var(--color-primary); color: var(--color-white); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: var(--space-xs) var(--space-md); border-radius: 100px; z-index: 2; }
/* Gallery */
.mhwb-gallery-grid { margin-top: var(--space-lg); }
.mhwb-gallery-grid .gallery { display: grid !important; grid-template-columns: repeat(9, 1fr); gap: 4px; float: none !important; width: 100% !important; }
.mhwb-gallery-grid .gallery-item { float: none !important; width: auto !important; margin: 0 !important; }
.mhwb-gallery-grid .gallery-item img { width: 100%; height: 60px; object-fit: cover; border-radius: 3px; border: none !important; display: block; cursor: pointer; transition: opacity var(--transition-fast); }
.mhwb-gallery-grid .gallery-item img:hover { opacity: 0.85; }
/* Purchase Card */
.mhwb-purchase-card { position: sticky; top: var(--space-xl); background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: hidden; animation: mhwb-scaleIn 0.5s var(--ease-out) 0.2s forwards; opacity: 0; }
.mhwb-purchase-card__header { padding: var(--space-xl) var(--space-xl) var(--space-lg); border-bottom: 1px solid var(--color-border); }
.mhwb-purchase-card__plan-name { font-family: var(--font-display); font-size: 1.65rem; color: var(--color-primary-dark); line-height: 1.2; margin-bottom: var(--space-xs); }
.mhwb-purchase-card__style { font-size: 0.825rem; color: var(--color-text-light); font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; }
.mhwb-purchase-card__specs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; background: var(--color-border); }
.mhwb-spec-cell { background: var(--color-white); padding: var(--space-md) var(--space-lg); text-align: center; }
.mhwb-spec-cell__value { font-family: var(--font-display); font-size: 1.35rem; color: var(--color-primary); line-height: 1.2; }
.mhwb-spec-cell__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-text-light); margin-top: 2px; font-weight: 600; }
.mhwb-purchase-card__body { padding: var(--space-xl); }
.mhwb-price-option { display: flex; align-items: center; padding: var(--space-md) var(--space-lg); border: 2px solid var(--color-border); border-radius: var(--radius-md); margin-bottom: var(--space-sm); cursor: pointer; transition: all var(--transition-fast); }
.mhwb-price-option:hover { border-color: var(--color-accent-light); background: var(--color-cream); }
.mhwb-price-option.mhwb-selected { border-color: var(--color-accent); background: linear-gradient(135deg, rgba(184,134,11,0.04), rgba(184,134,11,0.02)); }
.mhwb-price-option input[type="radio"] { appearance: none; -webkit-appearance: none; width: 20px; height: 20px; border: 2px solid var(--color-stone-dark); border-radius: 50%; margin-right: var(--space-md); flex-shrink: 0; transition: all var(--transition-fast); }
.mhwb-price-option input[type="radio"]:checked { border-color: var(--color-accent); background: var(--color-accent); box-shadow: inset 0 0 0 3px var(--color-white); }
.mhwb-price-option__info { flex: 1; }
.mhwb-price-option__format { font-weight: 600; font-size: 0.9rem; }
.mhwb-price-option__desc { font-size: 0.75rem; color: var(--color-text-light); margin-top: 1px; }
.mhwb-price-option__price { font-family: var(--font-display); font-size: 1.25rem; color: var(--color-primary); }
.mhwb-btn-buy { display: flex; align-items: center; justify-content: center; width: 100%; padding: var(--space-lg) var(--space-xl); background: var(--color-accent); color: var(--color-white); font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-top: var(--space-lg); transition: all var(--transition-fast); gap: var(--space-sm); box-shadow: 0 4px 16px rgba(184,134,11,0.3); text-decoration: none; }
.mhwb-btn-buy:hover { background: var(--color-accent-warm); transform: translateY(-1px); color: var(--color-white); }
.mhwb-btn-buy svg { width: 20px; height: 20px; }
.mhwb-trust-row { display: flex; justify-content: center; gap: var(--space-xl); margin-top: var(--space-lg); padding-top: var(--space-lg); border-top: 1px solid var(--color-border); }
.mhwb-trust-item { display: flex; align-items: center; gap: var(--space-xs); font-size: 0.75rem; color: var(--color-text-light); font-weight: 500; }
.mhwb-trust-item svg { width: 16px; height: 16px; color: var(--color-success); }
.mhwb-purchase-card__footer { padding: var(--space-md) var(--space-xl) var(--space-xl); text-align: center; }
.mhwb-purchase-card__footer a { font-size: 0.825rem; color: var(--color-accent); font-weight: 600; }
/* Quick Specs */
.mhwb-quick-specs { background: var(--color-primary); padding: var(--space-xl) 0; margin-bottom: var(--space-4xl); }
.mhwb-quick-specs__grid { display: flex; justify-content: center; gap: var(--space-3xl); flex-wrap: wrap; }
.mhwb-quick-spec { text-align: center; color: var(--color-white); }
.mhwb-quick-spec__icon { width: 32px; height: 32px; margin: 0 auto var(--space-sm); opacity: 0.7; }
.mhwb-quick-spec__value { font-family: var(--font-display); font-size: 1.5rem; line-height: 1.2; }
.mhwb-quick-spec__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.65; font-weight: 600; margin-top: 2px; }
/* Sections */
.mhwb-section { padding: var(--space-4xl) 0; }
.mhwb-section--alt { background: var(--color-white); }
.mhwb-section__header { text-align: center; margin-bottom: var(--space-3xl); }
.mhwb-section__overline { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--color-accent); margin-bottom: var(--space-sm); }
.mhwb-section__title { font-family: var(--font-display); font-size: 2.2rem; color: var(--color-primary-dark); line-height: 1.2; margin-bottom: var(--space-md); }
.mhwb-section__subtitle { font-size: 1.05rem; color: var(--color-text-secondary); max-width: 640px; margin: 0 auto; line-height: 1.7; }
/* Description */
.mhwb-description-grid { display: grid; grid-template-columns: 1fr 380px; gap: var(--space-3xl); align-items: start; }
.mhwb-description__content h2 { font-family: var(--font-display); font-size: 2rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); line-height: 1.25; }
.mhwb-description__content p { color: var(--color-text-secondary); margin-bottom: var(--space-lg); font-size: 1.02rem; }
/* Highlights */
.mhwb-highlights-card { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-md); border: 1px solid var(--color-border); }
.mhwb-highlights-card__title { font-family: var(--font-display); font-size: 1.15rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); padding-bottom: var(--space-md); border-bottom: 2px solid var(--color-accent); display: inline-block; }
.mhwb-highlight-item { display: flex; align-items: flex-start; gap: var(--space-md); margin-bottom: var(--space-lg); }
.mhwb-highlight-item:last-child { margin-bottom: 0; }
.mhwb-highlight-item__icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--color-cream), var(--color-stone)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mhwb-highlight-item__icon svg { width: 18px; height: 18px; color: var(--color-primary); }
.mhwb-highlight-item__text strong { display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-text); margin-bottom: 1px; }
.mhwb-highlight-item__text span { font-size: 0.8rem; color: var(--color-text-light); }
/* Specs */
.mhwb-specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-xl); }
.mhwb-specs-group { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--color-border); }
.mhwb-specs-group__title { font-family: var(--font-display); font-size: 1.1rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); padding-bottom: var(--space-sm); border-bottom: 2px solid var(--color-stone); }
.mhwb-spec-row { display: flex; justify-content: space-between; align-items: baseline; padding: var(--space-sm) 0; border-bottom: 1px solid var(--color-cream-dark); }
.mhwb-spec-row:last-child { border-bottom: none; }
.mhwb-spec-row__label { font-size: 0.85rem; color: var(--color-text-secondary); }
.mhwb-spec-row__value { font-size: 0.875rem; font-weight: 600; color: var(--color-text); text-align: right; }
/* Floor Plans */
.mhwb-floor-plans__wysiwyg img { max-width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
/* Included */
.mhwb-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-xl); }
.mhwb-included-card { background: var(--color-cream); border-radius: var(--radius-lg); padding: var(--space-xl); text-align: center; border: 1px solid var(--color-border); transition: all var(--transition-base); }
.mhwb-included-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--color-accent-light); }
.mhwb-included-card__icon { width: 56px; height: 56px; background: var(--color-white); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg); box-shadow: var(--shadow-sm); }
.mhwb-included-card__icon svg { width: 28px; height: 28px; color: var(--color-primary); }
.mhwb-included-card__title { font-weight: 700; font-size: 0.95rem; margin-bottom: var(--space-sm); color: var(--color-primary-dark); }
.mhwb-included-card__desc { font-size: 0.825rem; color: var(--color-text-light); line-height: 1.6; }
/* FAQ */
.mhwb-faq-list { max-width: 820px; margin: 0 auto; }
.mhwb-faq-item { background: var(--color-white); border-radius: var(--radius-md); margin-bottom: var(--space-md); border: 1px solid var(--color-border); overflow: hidden; }
.mhwb-faq-item.mhwb-open { box-shadow: var(--shadow-md); border-color: var(--color-accent-light); }
.mhwb-faq-question { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: var(--space-lg) var(--space-xl); font-size: 0.95rem; font-weight: 600; text-align: left; color: var(--color-text); gap: var(--space-md); }
.mhwb-faq-question__icon { width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: var(--color-cream); display: flex; align-items: center; justify-content: center; transition: all var(--transition-fast); }
.mhwb-faq-item.mhwb-open .mhwb-faq-question__icon { background: var(--color-accent); transform: rotate(45deg); }
.mhwb-faq-question__icon svg { width: 14px; height: 14px; color: var(--color-text-secondary); }
.mhwb-faq-item.mhwb-open .mhwb-faq-question__icon svg { color: var(--color-white); }
.mhwb-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s var(--ease-out); }
.mhwb-faq-answer__inner { padding: 0 var(--space-xl) var(--space-xl); font-size: 0.9rem; color: var(--color-text-secondary); line-height: 1.8; }
/* CTA */
.mhwb-cta-section { background: var(--color-primary); padding: var(--space-4xl) 0; text-align: center; color: var(--color-white); }
.mhwb-cta__title { font-family: var(--font-display); font-size: 2.2rem; margin-bottom: var(--space-md); }
.mhwb-cta__text { font-size: 1.05rem; opacity: 0.8; max-width: 560px; margin: 0 auto var(--space-xl); line-height: 1.7; }
.mhwb-cta__buttons { display: flex; justify-content: center; gap: var(--space-lg); flex-wrap: wrap; }
.mhwb-btn-cta { display: inline-flex; align-items: center; gap: var(--space-sm); padding: var(--space-md) var(--space-2xl); border-radius: var(--radius-md); font-weight: 700; font-size: 0.95rem; transition: all var(--transition-fast); }
.mhwb-btn-cta--primary { background: var(--color-accent); color: var(--color-white); box-shadow: 0 4px 16px rgba(184,134,11,0.35); }
.mhwb-btn-cta--primary:hover { background: var(--color-accent-warm); transform: translateY(-2px); color: var(--color-white); }
.mhwb-btn-cta--outline { border: 2px solid rgba(255,255,255,0.3); color: var(--color-white); }
.mhwb-btn-cta--outline:hover { border-color: var(--color-white); background: rgba(255,255,255,0.08); }
/* Mobile Buy Bar */
.mhwb-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--color-white); border-top: 1px solid var(--color-border); padding: var(--space-md) var(--space-lg); z-index: 100; box-shadow: 0 -4px 20px rgba(0,0,0,0.1); }
.mhwb-mobile-buy-bar__inner { display: flex; align-items: center; justify-content: space-between; max-width: var(--max-width); margin: 0 auto; }
.mhwb-mobile-buy-bar__name { font-weight: 700; color: var(--color-primary-dark); font-size: 0.9rem; }
.mhwb-mobile-buy-bar__price { font-family: var(--font-display); color: var(--color-accent); font-size: 1.1rem; }
.mhwb-mobile-buy-bar .mhwb-btn-buy { width: auto; padding: var(--space-sm) var(--space-xl); margin-top: 0; font-size: 0.875rem; }
/* Responsive */
@media (max-width: 1024px) {
  .mhwb-plan-hero__grid { grid-template-columns: 1fr; }
  .mhwb-purchase-card { position: relative; top: 0; }
  .mhwb-description-grid { grid-template-columns: 1fr; }
  .mhwb-specs-grid { grid-template-columns: 1fr 1fr; }
  .mhwb-included-grid { grid-template-columns: repeat(2, 1fr); }
  .mhwb-mobile-buy-bar { display: block; }
  .mhwb-wrap { padding-bottom: 80px; }
}
@media (max-width: 768px) {
  .mhwb-gallery-grid .gallery { grid-template-columns: repeat(5, 1fr); }
  .mhwb-specs-grid { grid-template-columns: 1fr; }
  .mhwb-included-grid { grid-template-columns: 1fr 1fr; }
  .mhwb-section__title { font-size: 1.75rem; }
}
@media (max-width: 480px) {
  .mhwb-container { padding: 0 var(--space-lg); }
  .mhwb-gallery-grid .gallery { grid-template-columns: repeat(4, 1fr); }
  .mhwb-cta__buttons { flex-direction: column; align-items: center; }
}
</style>
<?php
}

function mhp_mhwb_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    $plan_name           = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft                = $acf ? get_field( 'total_living_area',   $pid ) : '2,500';
    $main_floor          = $acf ? get_field( 'main_floor',          $pid ) : '';
    $upper_floor         = $acf ? get_field( 'upper_floor',         $pid ) : '';
    $lower_floor         = $acf ? get_field( 'lower_floor',         $pid ) : 'Walkout Basement';
    $bedrooms            = $acf ? get_field( 'bedrooms',            $pid ) : '4';
    $bathrooms           = $acf ? get_field( 'bathrooms',           $pid ) : '3';
    $stories             = $acf ? get_field( 'stories',             $pid ) : '2';
    $width               = $acf ? get_field( 'width',               $pid ) : '';
    $depth               = $acf ? get_field( 'depth',               $pid ) : '';
    $garage              = $acf ? get_field( 'garage',              $pid ) : '';
    $style               = $acf ? get_field( 'style',               $pid ) : 'Mountain · Craftsman · Rustic';
    $outdoor             = $acf ? get_field( 'outdoor',             $pid ) : 'Covered Porches';
    $roof                = $acf ? get_field( 'roof',                $pid ) : '';
    $ceiling             = $acf ? get_field( 'ceiling',             $pid ) : "9' / Vaulted";
    $exterior            = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms    = $acf ? get_field( 'additional_rooms',    $pid ) : '';
    $other_features      = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style           = $acf ? get_field( 'lot_style',           $pid ) : 'Sloping';
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
    $hero_url = get_the_post_thumbnail_url( $pid, 'large' ) ?: 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/mountain-home-house-plan-with-walkout-basement-2.jpg';

    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft' ), '', $v ); };
    $sqft_display     = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display  = $main_floor ? ( $clean($main_floor) > 0 ? number_format($clean($main_floor)) : $main_floor ) : '';
    $upper_fl_display = $upper_floor ? ( $clean($upper_floor) > 0 ? number_format($clean($upper_floor)) : $upper_floor ) : '';
    $footprint        = ( $width && $depth ) ? rtrim($width,"'") . "' × " . rtrim($depth,"'") . "'" : '';

    $post_content = get_post_field( 'post_content', $pid );
?>
<div class="mhwb-wrap">

<nav class="mhwb-breadcrumbs" aria-label="Breadcrumb">
  <div class="mhwb-container">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a><span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/home-plans/mountain-house-plans/')); ?>">Mountain House Plans</a><span>&#8250;</span>
    <strong><?php echo esc_html($plan_name); ?></strong>
  </div>
</nav>

<section class="mhwb-plan-hero">
  <div class="mhwb-container">
    <div class="mhwb-plan-hero__grid">
      <div class="mhwb-hero-image mhwb-animate-in">
        <div class="mhwb-hero-image__main">
          <span class="mhwb-hero-image__badge">Walkout Basement Design</span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> — mountain home with walkout basement, covered porches, craftsman exterior" fetchpriority="high">
        </div>
        <?php if ( has_shortcode( $post_content, 'gallery' ) ) : ?>
        <div class="mhwb-gallery-grid">
          <?php
          preg_match( '/\[gallery[^\]]*\]/', $post_content, $gm );
          if ( ! empty($gm[0]) ) echo do_shortcode( $gm[0] );
          ?>
        </div>
        <?php endif; ?>
      </div>

      <aside class="mhwb-purchase-card">
        <div class="mhwb-purchase-card__header">
          <h1 class="mhwb-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
          <p class="mhwb-purchase-card__style"><?php echo esc_html($style); ?></p>
        </div>
        <div class="mhwb-purchase-card__specs">
          <div class="mhwb-spec-cell"><div class="mhwb-spec-cell__value"><?php echo esc_html($sqft_display); ?></div><div class="mhwb-spec-cell__label">Sq Ft</div></div>
          <div class="mhwb-spec-cell"><div class="mhwb-spec-cell__value"><?php echo esc_html($bedrooms); ?></div><div class="mhwb-spec-cell__label">Bedrooms</div></div>
          <div class="mhwb-spec-cell"><div class="mhwb-spec-cell__value"><?php echo esc_html($bathrooms); ?></div><div class="mhwb-spec-cell__label">Bathrooms</div></div>
          <div class="mhwb-spec-cell"><div class="mhwb-spec-cell__value"><?php echo esc_html($stories); ?></div><div class="mhwb-spec-cell__label">Stories</div></div>
        </div>
        <div class="mhwb-purchase-card__body">
          <label class="mhwb-price-option mhwb-selected" id="mhwbOptionPdf">
            <input type="radio" name="mhwb_format" value="pdf" checked onchange="mhwbSelect('pdf')">
            <div class="mhwb-price-option__info"><div class="mhwb-price-option__format">PDF Plan Set</div><div class="mhwb-price-option__desc">Print-ready digital plans</div></div>
            <div class="mhwb-price-option__price"><?php echo esc_html($price_fmt); ?></div>
          </label>
          <label class="mhwb-price-option" id="mhwbOptionCad">
            <input type="radio" name="mhwb_format" value="cad" onchange="mhwbSelect('cad')">
            <div class="mhwb-price-option__info"><div class="mhwb-price-option__format">CAD File</div><div class="mhwb-price-option__desc">Editable for your builder</div></div>
            <div class="mhwb-price-option__price"><?php echo esc_html($cad_price); ?></div>
          </label>
          <a href="<?php echo esc_url($buy_href); ?>" class="mhwb-btn-buy">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="m16 10-4 4-4-4"/></svg>
            Purchase This Plan
          </a>
          <div class="mhwb-trust-row">
            <span class="mhwb-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Secure Checkout</span>
            <span class="mhwb-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Instant Download</span>
          </div>
        </div>
        <div class="mhwb-purchase-card__footer">
          <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>">Need changes? Request a modification &#8594;</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<div class="mhwb-quick-specs">
  <div class="mhwb-container">
    <div class="mhwb-quick-specs__grid">
      <div class="mhwb-quick-spec">
        <svg class="mhwb-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>
        <div class="mhwb-quick-spec__value"><?php echo esc_html($sqft_display); ?> sq ft</div>
        <div class="mhwb-quick-spec__label">Heated Area</div>
      </div>
      <?php if ($footprint) : ?>
      <div class="mhwb-quick-spec">
        <svg class="mhwb-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <div class="mhwb-quick-spec__value"><?php echo esc_html($footprint); ?></div>
        <div class="mhwb-quick-spec__label">Footprint</div>
      </div>
      <?php endif; ?>
      <?php if ($main_fl_display) : ?>
      <div class="mhwb-quick-spec">
        <svg class="mhwb-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M4 20V8l8-6 8 6v12"/></svg>
        <div class="mhwb-quick-spec__value">Main: <?php echo esc_html($main_fl_display); ?></div>
        <div class="mhwb-quick-spec__label">Main Floor Sq Ft</div>
      </div>
      <?php endif; ?>
      <div class="mhwb-quick-spec">
        <svg class="mhwb-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
        <div class="mhwb-quick-spec__value">Walkout Basement</div>
        <div class="mhwb-quick-spec__label">Lower Level</div>
      </div>
      <div class="mhwb-quick-spec">
        <svg class="mhwb-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
        <div class="mhwb-quick-spec__value">From <?php echo esc_html($price_fmt); ?></div>
        <div class="mhwb-quick-spec__label">Plan Price</div>
      </div>
    </div>
  </div>
</div>

<section class="mhwb-section" id="description">
  <div class="mhwb-container">
    <div class="mhwb-description-grid">
      <div class="mhwb-description__content">
        <?php if ( $plan_description ) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <h2>A Mountain Home Built for the Slope of Your Lot</h2>
          <p>The <?php echo esc_html($plan_name); ?> is designed from the ground up to take advantage of a sloping lot. Rather than fighting the terrain, this plan works with it &#8212; letting the walkout basement become a full-function living level while every major room above faces the rear view.</p>
          <p>The main level centers around an open living area with vaulted ceilings connecting the kitchen, dining, and great room. The master suite sits on this level with direct access to covered porch space and clear separation from the secondary bedrooms. The craftsman-style exterior with covered porches gives the home strong curb appeal from the road while keeping the focus on views from the rear.</p>
          <p>The walkout basement expands the home significantly. Whether you finish it as additional bedrooms, a recreation room, home theater, or flex space, the lower level walks out to grade &#8212; giving it its own outdoor connection and natural light that most basement designs lack entirely.</p>
          <p>This is a plan built for mountain and lake properties where the landscape is the whole point. Every decision &#8212; the orientation, the porches, the window placement &#8212; was made to make the most of wherever you put it.</p>
        <?php endif; ?>
      </div>
      <div class="mhwb-highlights-card">
        <h3 class="mhwb-highlights-card__title">Plan Highlights</h3>
        <div class="mhwb-highlight-item">
          <div class="mhwb-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
          <div class="mhwb-highlight-item__text"><strong>Walkout Basement</strong><span>Full lower level walks out to grade — not a dark hole</span></div>
        </div>
        <div class="mhwb-highlight-item">
          <div class="mhwb-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
          <div class="mhwb-highlight-item__text"><strong>Main-Level Master Suite</strong><span>Private, porch-connected, separated from guest rooms</span></div>
        </div>
        <div class="mhwb-highlight-item">
          <div class="mhwb-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg></div>
          <div class="mhwb-highlight-item__text"><strong>Open Vaulted Living</strong><span>Kitchen, dining, and great room open under vaulted ceiling</span></div>
        </div>
        <div class="mhwb-highlight-item">
          <div class="mhwb-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg></div>
          <div class="mhwb-highlight-item__text"><strong>Covered Porches</strong><span>Outdoor living built into every level of the home</span></div>
        </div>
        <div class="mhwb-highlight-item">
          <div class="mhwb-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7"/><path d="M3 9v0l9 4 9-4"/></svg></div>
          <div class="mhwb-highlight-item__text"><strong>Sloping Lot Ready</strong><span>Designed specifically for mountain and lakefront grades</span></div>
        </div>
        <?php if ($garage) : ?>
        <div class="mhwb-highlight-item">
          <div class="mhwb-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/></svg></div>
          <div class="mhwb-highlight-item__text"><strong>Garage</strong><span><?php echo esc_html($garage); ?></span></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="mhwb-section mhwb-section--alt" id="specs">
  <div class="mhwb-container">
    <div class="mhwb-section__header">
      <p class="mhwb-section__overline">Specifications</p>
      <h2 class="mhwb-section__title">Full Plan Details</h2>
      <p class="mhwb-section__subtitle">Everything you need to evaluate this plan for your lot and budget.</p>
    </div>
    <div class="mhwb-specs-grid">
      <div class="mhwb-specs-group">
        <h3 class="mhwb-specs-group__title">Living Area</h3>
        <?php if ($main_fl_display) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Main Floor</span><span class="mhwb-spec-row__value"><?php echo esc_html($main_fl_display); ?> sq ft</span></div><?php endif; ?>
        <?php if ($upper_fl_display) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Upper Floor</span><span class="mhwb-spec-row__value"><?php echo esc_html($upper_fl_display); ?> sq ft</span></div><?php endif; ?>
        <div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Lower / Basement</span><span class="mhwb-spec-row__value"><?php echo esc_html($lower_floor); ?></span></div>
        <div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Total Heated</span><span class="mhwb-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div>
        <?php if ($width) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Width</span><span class="mhwb-spec-row__value"><?php echo esc_html($width); ?></span></div><?php endif; ?>
        <?php if ($depth) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Depth</span><span class="mhwb-spec-row__value"><?php echo esc_html($depth); ?></span></div><?php endif; ?>
      </div>
      <div class="mhwb-specs-group">
        <h3 class="mhwb-specs-group__title">House Features</h3>
        <div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Bedrooms</span><span class="mhwb-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div>
        <div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Bathrooms</span><span class="mhwb-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div>
        <div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Stories</span><span class="mhwb-spec-row__value"><?php echo esc_html($stories); ?></span></div>
        <?php if ($additional_rooms) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Additional Rooms</span><span class="mhwb-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div><?php endif; ?>
        <?php if ($garage) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Garage</span><span class="mhwb-spec-row__value"><?php echo esc_html($garage); ?></span></div><?php endif; ?>
        <?php if ($outdoor) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Outdoor Spaces</span><span class="mhwb-spec-row__value"><?php echo esc_html($outdoor); ?></span></div><?php endif; ?>
        <?php if ($other_features) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Other Features</span><span class="mhwb-spec-row__value"><?php echo esc_html($other_features); ?></span></div><?php endif; ?>
      </div>
      <div class="mhwb-specs-group">
        <h3 class="mhwb-specs-group__title">Construction</h3>
        <?php if ($exterior) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Exterior Framing</span><span class="mhwb-spec-row__value"><?php echo esc_html($exterior); ?></span></div><?php endif; ?>
        <?php if ($ceiling) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Ceiling Height</span><span class="mhwb-spec-row__value"><?php echo esc_html($ceiling); ?></span></div><?php endif; ?>
        <?php if ($roof) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Roof</span><span class="mhwb-spec-row__value"><?php echo esc_html($roof); ?></span></div><?php endif; ?>
        <div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Foundation</span><span class="mhwb-spec-row__value">Walkout Basement</span></div>
        <?php if ($lot_style) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Lot Style</span><span class="mhwb-spec-row__value"><?php echo esc_html($lot_style); ?></span></div><?php endif; ?>
        <?php if ($style) : ?><div class="mhwb-spec-row"><span class="mhwb-spec-row__label">Home Style</span><span class="mhwb-spec-row__value"><?php echo esc_html($style); ?></span></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if ( $floor_plans_wysiwyg ) : ?>
<section class="mhwb-section" id="floorplans">
  <div class="mhwb-container">
    <div class="mhwb-section__header">
      <p class="mhwb-section__overline">Floor Plans</p>
      <h2 class="mhwb-section__title">Explore the Layout</h2>
      <p class="mhwb-section__subtitle">Main level living with walkout basement below — every level connected to the outdoors.</p>
    </div>
    <div class="mhwb-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans_wysiwyg); ?></div>
  </div>
</section>
<?php endif; ?>

<section class="mhwb-section<?php echo $floor_plans_wysiwyg ? ' mhwb-section--alt' : ''; ?>" id="whats-included">
  <div class="mhwb-container">
    <div class="mhwb-section__header">
      <p class="mhwb-section__overline">Your Plan Set</p>
      <h2 class="mhwb-section__title">What&#8217;s Included</h2>
      <p class="mhwb-section__subtitle">Complete and build-ready. Everything your builder needs to get started.</p>
    </div>
    <div class="mhwb-included-grid">
      <div class="mhwb-included-card"><div class="mhwb-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg></div><h3 class="mhwb-included-card__title">Elevations</h3><p class="mhwb-included-card__desc">Front, side, and rear at &#188;&#8221; scale with material notes and dimensions.</p></div>
      <div class="mhwb-included-card"><div class="mhwb-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><h3 class="mhwb-included-card__title">Floor Plans</h3><p class="mhwb-included-card__desc">Dimensioned plans for main level and walkout basement.</p></div>
      <div class="mhwb-included-card"><div class="mhwb-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M5 20V8l7-5 7 5v12"/></svg></div><h3 class="mhwb-included-card__title">Foundation Plan</h3><p class="mhwb-included-card__desc">Walkout basement foundation with footing and wall dimensions.</p></div>
      <div class="mhwb-included-card"><div class="mhwb-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7"/><path d="M3 9v0l9 4 9-4"/></svg></div><h3 class="mhwb-included-card__title">Roof Plan</h3><p class="mhwb-included-card__desc">Complete roof plan with pitches, ridges, and drainage.</p></div>
    </div>
  </div>
</section>

<section class="mhwb-section mhwb-section--alt" id="faq">
  <div class="mhwb-container">
    <div class="mhwb-section__header">
      <p class="mhwb-section__overline">Common Questions</p>
      <h2 class="mhwb-section__title">Frequently Asked Questions</h2>
    </div>
    <div class="mhwb-faq-list">
      <?php if ( ! empty($faqs) && is_array($faqs) ) :
            foreach ( $faqs as $faq ) :
              $q = isset($faq['question']) ? $faq['question'] : '';
              $a = isset($faq['answer'])   ? $faq['answer']   : '';
              if ( ! $q ) continue; ?>
      <div class="mhwb-faq-item">
        <button class="mhwb-faq-question" onclick="mhwbFaq(this)" aria-expanded="false">
          <span><?php echo esc_html($q); ?></span>
          <span class="mhwb-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="mhwb-faq-answer"><div class="mhwb-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
      </div>
      <?php endforeach; else : ?>
      <div class="mhwb-faq-item"><button class="mhwb-faq-question" onclick="mhwbFaq(this)" aria-expanded="false"><span>What is included in the plan set?</span><span class="mhwb-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="mhwb-faq-answer"><div class="mhwb-faq-answer__inner">Each set includes dimensioned floor plans for all levels, front/side/rear elevations, a foundation plan, and a roof plan. PDF (<?php echo esc_html($price_fmt); ?>) or CAD files (<?php echo esc_html($cad_price); ?>).</div></div></div>
      <div class="mhwb-faq-item"><button class="mhwb-faq-question" onclick="mhwbFaq(this)" aria-expanded="false"><span>Can this plan be modified?</span><span class="mhwb-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="mhwb-faq-answer"><div class="mhwb-faq-answer__inner">Yes &#8212; all modifications are handled in-house. Common requests include reconfiguring the basement, adding garage space, or adjusting bedroom sizes. Contact us for a scope and price.</div></div></div>
      <div class="mhwb-faq-item"><button class="mhwb-faq-question" onclick="mhwbFaq(this)" aria-expanded="false"><span>What kind of lot does this plan require?</span><span class="mhwb-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="mhwb-faq-answer"><div class="mhwb-faq-answer__inner">This plan is designed for sloping lots &#8212; mountain or lakefront properties where the grade drops from front to rear. The walkout basement takes advantage of that slope to create a full second level of usable space.</div></div></div>
      <div class="mhwb-faq-item"><button class="mhwb-faq-question" onclick="mhwbFaq(this)" aria-expanded="false"><span>Does this plan require an engineer&#8217;s stamp?</span><span class="mhwb-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="mhwb-faq-answer"><div class="mhwb-faq-answer__inner">Stock plans do not include a professional stamp. If your local building department requires one, you&#8217;ll need a licensed engineer or architect in your state to review and stamp the plans.</div></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="mhwb-cta-section">
  <div class="mhwb-container">
    <h2 class="mhwb-cta__title">Ready to Build on Your Mountain Lot?</h2>
    <p class="mhwb-cta__text">Purchase the <?php echo esc_html($plan_name); ?> today or reach out about modifications &#8212; we&#8217;re a family business and we&#8217;re here to help.</p>
    <div class="mhwb-cta__buttons">
      <a href="<?php echo esc_url($buy_href); ?>" class="mhwb-btn-cta mhwb-btn-cta--primary">Purchase This Plan</a>
      <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="mhwb-btn-cta mhwb-btn-cta--outline">Request a Modification</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="mhwb-btn-cta mhwb-btn-cta--outline">Talk With Our Family</a>
    </div>
  </div>
</section>

<div class="mhwb-mobile-buy-bar">
  <div class="mhwb-mobile-buy-bar__inner">
    <div>
      <div class="mhwb-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
      <div class="mhwb-mobile-buy-bar__price">From <?php echo esc_html($price_fmt); ?></div>
    </div>
    <a href="<?php echo esc_url($buy_href); ?>" class="mhwb-btn-buy">Purchase Plan</a>
  </div>
</div>

</div>

<script>
(function(){
  window.mhwbSelect = function(fmt) {
    document.querySelectorAll('.mhwb-price-option').forEach(function(o){o.classList.remove('mhwb-selected');});
    var el = document.getElementById(fmt==='pdf'?'mhwbOptionPdf':'mhwbOptionCad'); if(el) el.classList.add('mhwb-selected');
  };
  window.mhwbFaq = function(btn) {
    var item = btn.parentElement, isOpen = item.classList.contains('mhwb-open');
    document.querySelectorAll('.mhwb-faq-item').forEach(function(f){
      f.classList.remove('mhwb-open');
      var q=f.querySelector('.mhwb-faq-question'); if(q) q.setAttribute('aria-expanded','false');
      var a=f.querySelector('.mhwb-faq-answer'); if(a) a.style.maxHeight='0';
    });
    if (!isOpen) {
      item.classList.add('mhwb-open'); btn.setAttribute('aria-expanded','true');
      var ans = item.querySelector('.mhwb-faq-answer'); if(ans) ans.style.maxHeight = ans.scrollHeight+'px';
    }
  };
})();
</script>
<?php
}

genesis();
