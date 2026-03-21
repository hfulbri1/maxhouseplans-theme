<?php
/**
 * Template Name: Plan - Asheville Mountain
 *
 * Dedicated plan template for the Asheville Mountain house plan.
 * Applies to plan ID 7930 (slug: 3-story-open-mountain-house-floor-plan).
 *
 * Built by Vegeta for MaxHousePlans.com — 2026-03-19
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Genesis layout: full-width, no sidebar ──────────────────────────────────
add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );

// ── Replace Genesis loop with our custom output ─────────────────────────────
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_asheville_loop' );

// ── Inject CSS into <head> ───────────────────────────────────────────────────
add_action( 'wp_head', 'mhp_asheville_styles', 20 );

// ── Schema / structured data ─────────────────────────────────────────────────
add_action( 'wp_head', 'mhp_asheville_schema', 5 );

// ── Google Fonts ─────────────────────────────────────────────────────────────
add_action( 'wp_head', 'mhp_asheville_fonts', 1 );

// ============================================================================
// GOOGLE FONTS
// ============================================================================
function mhp_asheville_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">' . "\n";
}

// ============================================================================
// SCHEMA MARKUP
// ============================================================================
function mhp_asheville_schema() {
    if ( ! is_singular( 'plans' ) ) return;
    $pid  = get_the_ID();
    $acf  = function_exists( 'get_field' );
    $name        = $acf ? get_field( 'plan_name', $pid ) : get_the_title();
    $sqft        = $acf ? get_field( 'total_living_area', $pid ) : '2618';
    $beds        = $acf ? get_field( 'bedrooms', $pid ) : '4';
    $baths       = $acf ? get_field( 'bathrooms', $pid ) : '4.5';
    $stories     = $acf ? get_field( 'stories', $pid ) : '3';
    $main_fl     = $acf ? get_field( 'main_floor', $pid ) : '1760';
    $upper_fl    = $acf ? get_field( 'upper_floor', $pid ) : '858';
    $width       = $acf ? get_field( 'width', $pid ) : '59';
    $depth       = $acf ? get_field( 'depth', $pid ) : '60';
    $style       = $acf ? get_field( 'style', $pid ) : 'Craftsman, Mountain, Lake';
    $price       = $acf ? get_field( 'price', $pid ) : '1195';
    $price_num   = is_numeric( $price ) ? number_format( (float) $price, 2, '.', '' ) : '1195.00';
    $image_url   = get_the_post_thumbnail_url( $pid, 'full' );
    $permalink   = get_permalink( $pid );

    $product_schema = array(
        '@context'           => 'https://schema.org',
        '@type'              => 'Product',
        'name'               => $name . ' House Plan',
        'description'        => 'A build-ready ' . $stories . '-story mountain house plan featuring ' . $sqft . ' sq ft of heated living space, ' . $beds . ' bedrooms, ' . $baths . ' bathrooms, vaulted great room with exposed timber beams, wraparound porches, and walkout basement option.',
        'image'              => $image_url ? array( $image_url ) : array(),
        'brand'              => array( '@type' => 'Brand', 'name' => 'Max Fulbright Designs' ),
        'offers'             => array(
            array(
                '@type'         => 'Offer',
                'name'          => 'PDF Plan Set',
                'price'         => $price_num,
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock',
                'url'           => $permalink,
                'seller'        => array( '@type' => 'Organization', 'name' => 'Max House Plans' ),
            ),
            array(
                '@type'         => 'Offer',
                'name'          => 'CAD File',
                'price'         => number_format( (float) $price_num * 1.26, 2, '.', '' ),
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock',
                'url'           => $permalink,
            ),
        ),
        'additionalProperty' => array(
            array( '@type' => 'PropertyValue', 'name' => 'Bedrooms',           'value' => $beds ),
            array( '@type' => 'PropertyValue', 'name' => 'Bathrooms',          'value' => $baths ),
            array( '@type' => 'PropertyValue', 'name' => 'Stories',            'value' => $stories ),
            array( '@type' => 'PropertyValue', 'name' => 'Heated Square Feet', 'value' => $sqft ),
            array( '@type' => 'PropertyValue', 'name' => 'Main Floor',         'value' => $main_fl . ' sq ft' ),
            array( '@type' => 'PropertyValue', 'name' => 'Upper Floor',        'value' => $upper_fl . ' sq ft' ),
            array( '@type' => 'PropertyValue', 'name' => 'Width',              'value' => $width . ' ft' ),
            array( '@type' => 'PropertyValue', 'name' => 'Depth',              'value' => $depth . ' ft' ),
            array( '@type' => 'PropertyValue', 'name' => 'Style',              'value' => $style ),
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $product_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";

    // FAQ schema from ACF repeater
    $faqs = $acf ? get_field( 'faqs', $pid ) : array();
    if ( ! empty( $faqs ) && is_array( $faqs ) ) {
        $faq_entities = array();
        foreach ( $faqs as $faq ) {
            $q = isset( $faq['question'] ) ? $faq['question'] : '';
            $a = isset( $faq['answer'] )   ? wp_strip_all_tags( $faq['answer'] ) : '';
            if ( $q && $a ) {
                $faq_entities[] = array(
                    '@type'          => 'Question',
                    'name'           => $q,
                    'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ),
                );
            }
        }
        if ( ! empty( $faq_entities ) ) {
            $faq_schema = array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_entities,
            );
            echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        }
    }

    // Breadcrumb schema
    $bc_schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array(
            array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home',                'item' => home_url( '/' ) ),
            array( '@type' => 'ListItem', 'position' => 2, 'name' => 'House Plans',          'item' => home_url( '/house-plans/' ) ),
            array( '@type' => 'ListItem', 'position' => 3, 'name' => 'Mountain House Plans',  'item' => home_url( '/home-plans/mountain-house-plans/' ) ),
            array( '@type' => 'ListItem', 'position' => 4, 'name' => $name . ' House Plan' ),
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $bc_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

// ============================================================================
// STYLES — Full CSS inline in <head>
// ============================================================================
function mhp_asheville_styles() {
?>
<style id="mhp-asheville-styles">
:root {
  --color-primary: #2C3E2D;
  --color-primary-light: #3D5A3F;
  --color-primary-dark: #1A2A1B;
  --color-accent: #B8860B;
  --color-accent-light: #D4A843;
  --color-accent-warm: #C4913B;
  --color-cream: #FAF7F2;
  --color-cream-dark: #F0EBE3;
  --color-stone: #E8E2D8;
  --color-stone-dark: #D4CEC4;
  --color-text: #2B2B2B;
  --color-text-secondary: #5A5A5A;
  --color-text-light: #8A8A8A;
  --color-white: #FFFFFF;
  --color-border: #E0DAD0;
  --color-success: #3A7D44;
  --color-info: #4A7C9B;
  --font-display: 'DM Serif Display', Georgia, serif;
  --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --space-xs: 0.25rem; --space-sm: 0.5rem; --space-md: 1rem; --space-lg: 1.5rem;
  --space-xl: 2rem; --space-2xl: 3rem; --space-3xl: 4rem; --space-4xl: 6rem;
  --max-width: 1280px;
  --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px; --radius-xl: 24px;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
  --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
  --shadow-lg: 0 8px 30px rgba(0,0,0,0.10);
  --shadow-xl: 0 16px 50px rgba(0,0,0,0.12);
  --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
  --transition-fast: 0.2s var(--ease-out);
  --transition-base: 0.35s var(--ease-out);
  --transition-slow: 0.6s var(--ease-out);
}
.mhp-plan-wrap *, .mhp-plan-wrap *::before, .mhp-plan-wrap *::after { box-sizing: border-box; }
.mhp-plan-wrap { font-family: var(--font-body); color: var(--color-text); background: var(--color-cream); line-height: 1.7; font-size: 16px; overflow-x: hidden; }
.mhp-plan-wrap img { max-width: 100%; height: auto; display: block; }
.mhp-plan-wrap a { color: inherit; text-decoration: none; }
.mhp-plan-wrap button { cursor: pointer; border: none; background: none; font-family: inherit; }
.mhp-container { max-width: var(--max-width); margin: 0 auto; padding: 0 var(--space-xl); }
@keyframes mhp-fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
@keyframes mhp-fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes mhp-scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.mhp-animate-in { opacity: 0; animation: mhp-fadeInUp 0.7s var(--ease-out) forwards; }
/* Breadcrumbs */
.mhp-breadcrumbs { padding: var(--space-lg) 0; font-size: 0.825rem; color: var(--color-text-light); }
.mhp-breadcrumbs a { color: var(--color-text-secondary); transition: color var(--transition-fast); }
.mhp-breadcrumbs a:hover { color: var(--color-accent); }
.mhp-breadcrumbs span { margin: 0 var(--space-sm); opacity: 0.5; }
/* Hero */
.mhp-plan-hero { padding-bottom: var(--space-4xl); }
.mhp-plan-hero__grid { display: grid; grid-template-columns: 1fr 420px; gap: var(--space-3xl); align-items: start; }
/* Gallery */
.mhp-gallery { animation: mhp-fadeIn 0.6s var(--ease-out) forwards; }
.mhp-gallery__main { position: relative; border-radius: var(--radius-lg); overflow: hidden; background: var(--color-stone); aspect-ratio: 3/2; box-shadow: var(--shadow-lg); }
.mhp-gallery__main img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-slow); }
.mhp-gallery__main:hover img { transform: scale(1.02); }
.mhp-gallery__badge { position: absolute; top: var(--space-lg); left: var(--space-lg); background: var(--color-primary); color: var(--color-white); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: var(--space-xs) var(--space-md); border-radius: 100px; z-index: 2; }
.mhp-gallery__count { position: absolute; bottom: var(--space-lg); right: var(--space-lg); background: rgba(0,0,0,0.65); backdrop-filter: blur(8px); color: var(--color-white); font-size: 0.8rem; font-weight: 500; padding: var(--space-xs) var(--space-md); border-radius: 100px; z-index: 2; display: flex; align-items: center; gap: var(--space-xs); }
.mhp-gallery__count svg { width: 16px; height: 16px; }
.mhp-gallery__thumbs { display: grid; grid-template-columns: repeat(5, 1fr); gap: var(--space-sm); margin-top: var(--space-sm); }
.mhp-gallery__thumb { aspect-ratio: 3/2; border-radius: var(--radius-sm); overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all var(--transition-fast); opacity: 0.7; }
.mhp-gallery__thumb:hover, .mhp-gallery__thumb.mhp-active { opacity: 1; border-color: var(--color-accent); box-shadow: var(--shadow-md); }
.mhp-gallery__thumb img { width: 100%; height: 100%; object-fit: cover; }
/* Purchase Card */
.mhp-purchase-card { position: sticky; top: var(--space-xl); background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: hidden; animation: mhp-scaleIn 0.5s var(--ease-out) 0.2s forwards; opacity: 0; }
.mhp-purchase-card__header { padding: var(--space-xl) var(--space-xl) var(--space-lg); border-bottom: 1px solid var(--color-border); }
.mhp-purchase-card__plan-name { font-family: var(--font-display); font-size: 1.65rem; color: var(--color-primary-dark); line-height: 1.2; margin-bottom: var(--space-xs); }
.mhp-purchase-card__style { font-size: 0.825rem; color: var(--color-text-light); font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; }
.mhp-purchase-card__specs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; background: var(--color-border); }
.mhp-spec-cell { background: var(--color-white); padding: var(--space-md) var(--space-lg); text-align: center; }
.mhp-spec-cell__value { font-family: var(--font-display); font-size: 1.35rem; color: var(--color-primary); line-height: 1.2; }
.mhp-spec-cell__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-text-light); margin-top: 2px; font-weight: 600; }
.mhp-purchase-card__body { padding: var(--space-xl); }
.mhp-price-option { display: flex; align-items: center; padding: var(--space-md) var(--space-lg); border: 2px solid var(--color-border); border-radius: var(--radius-md); margin-bottom: var(--space-sm); cursor: pointer; transition: all var(--transition-fast); }
.mhp-price-option:hover { border-color: var(--color-accent-light); background: var(--color-cream); }
.mhp-price-option.mhp-selected { border-color: var(--color-accent); background: linear-gradient(135deg, rgba(184,134,11,0.04), rgba(184,134,11,0.02)); }
.mhp-price-option input[type="radio"] { appearance: none; -webkit-appearance: none; width: 20px; height: 20px; border: 2px solid var(--color-stone-dark); border-radius: 50%; margin-right: var(--space-md); flex-shrink: 0; transition: all var(--transition-fast); }
.mhp-price-option input[type="radio"]:checked { border-color: var(--color-accent); background: var(--color-accent); box-shadow: inset 0 0 0 3px var(--color-white); }
.mhp-price-option__info { flex: 1; }
.mhp-price-option__format { font-weight: 600; font-size: 0.9rem; color: var(--color-text); }
.mhp-price-option__desc { font-size: 0.75rem; color: var(--color-text-light); margin-top: 1px; }
.mhp-price-option__price { font-family: var(--font-display); font-size: 1.25rem; color: var(--color-primary); }
.mhp-btn-buy { display: flex; align-items: center; justify-content: center; width: 100%; padding: var(--space-lg) var(--space-xl); background: var(--color-accent); color: var(--color-white); font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-top: var(--space-lg); transition: all var(--transition-fast); letter-spacing: 0.02em; gap: var(--space-sm); box-shadow: 0 4px 16px rgba(184,134,11,0.3); text-decoration: none; }
.mhp-btn-buy:hover { background: var(--color-accent-warm); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(184,134,11,0.35); color: var(--color-white); }
.mhp-btn-buy svg { width: 20px; height: 20px; }
.mhp-trust-row { display: flex; justify-content: center; gap: var(--space-xl); margin-top: var(--space-lg); padding-top: var(--space-lg); border-top: 1px solid var(--color-border); }
.mhp-trust-item { display: flex; align-items: center; gap: var(--space-xs); font-size: 0.75rem; color: var(--color-text-light); font-weight: 500; }
.mhp-trust-item svg { width: 16px; height: 16px; color: var(--color-success); flex-shrink: 0; }
.mhp-purchase-card__footer { padding: var(--space-md) var(--space-xl) var(--space-xl); text-align: center; }
.mhp-purchase-card__footer a { font-size: 0.825rem; color: var(--color-accent); font-weight: 600; }
.mhp-purchase-card__footer a:hover { color: var(--color-accent-warm); }
/* Quick Specs Bar */
.mhp-quick-specs { background: var(--color-primary); padding: var(--space-xl) 0; margin-bottom: var(--space-4xl); position: relative; overflow: hidden; }
.mhp-quick-specs::before { content: ''; position: absolute; inset: 0; opacity: 0.5; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat; }
.mhp-quick-specs__grid { display: flex; justify-content: center; gap: var(--space-3xl); position: relative; z-index: 1; flex-wrap: wrap; }
.mhp-quick-spec { text-align: center; color: var(--color-white); }
.mhp-quick-spec__icon { width: 32px; height: 32px; margin: 0 auto var(--space-sm); opacity: 0.7; }
.mhp-quick-spec__value { font-family: var(--font-display); font-size: 1.5rem; line-height: 1.2; }
.mhp-quick-spec__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.65; font-weight: 600; margin-top: 2px; }
/* Sections */
.mhp-section { padding: var(--space-4xl) 0; }
.mhp-section--alt { background: var(--color-white); }
.mhp-section__header { text-align: center; margin-bottom: var(--space-3xl); }
.mhp-section__overline { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--color-accent); margin-bottom: var(--space-sm); }
.mhp-section__title { font-family: var(--font-display); font-size: 2.2rem; color: var(--color-primary-dark); line-height: 1.2; margin-bottom: var(--space-md); }
.mhp-section__subtitle { font-size: 1.05rem; color: var(--color-text-secondary); max-width: 640px; margin: 0 auto; line-height: 1.7; }
/* Description */
.mhp-description-grid { display: grid; grid-template-columns: 1fr 380px; gap: var(--space-3xl); align-items: start; }
.mhp-description__content h2 { font-family: var(--font-display); font-size: 2rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); line-height: 1.25; }
.mhp-description__content p { color: var(--color-text-secondary); margin-bottom: var(--space-lg); font-size: 1.02rem; }
/* Highlights Card */
.mhp-highlights-card { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-md); border: 1px solid var(--color-border); }
.mhp-highlights-card__title { font-family: var(--font-display); font-size: 1.15rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); padding-bottom: var(--space-md); border-bottom: 2px solid var(--color-accent); display: inline-block; }
.mhp-highlight-item { display: flex; align-items: flex-start; gap: var(--space-md); margin-bottom: var(--space-lg); }
.mhp-highlight-item:last-child { margin-bottom: 0; }
.mhp-highlight-item__icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--color-cream), var(--color-stone)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mhp-highlight-item__icon svg { width: 18px; height: 18px; color: var(--color-primary); }
.mhp-highlight-item__text strong { display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-text); margin-bottom: 1px; }
.mhp-highlight-item__text span { font-size: 0.8rem; color: var(--color-text-light); }
/* Specs Table */
.mhp-specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-xl); }
.mhp-specs-group { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--color-border); }
.mhp-specs-group__title { font-family: var(--font-display); font-size: 1.1rem; color: var(--color-primary-dark); margin-bottom: var(--space-lg); padding-bottom: var(--space-sm); border-bottom: 2px solid var(--color-stone); }
.mhp-spec-row { display: flex; justify-content: space-between; align-items: baseline; padding: var(--space-sm) 0; border-bottom: 1px solid var(--color-cream-dark); }
.mhp-spec-row:last-child { border-bottom: none; }
.mhp-spec-row__label { font-size: 0.85rem; color: var(--color-text-secondary); }
.mhp-spec-row__value { font-size: 0.875rem; font-weight: 600; color: var(--color-text); text-align: right; }
/* Floor Plans Wysiwyg */
.mhp-floor-plans__wysiwyg img { max-width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
/* Cost Estimator */
.mhp-estimator { background: var(--color-primary-dark); border-radius: var(--radius-xl); overflow: hidden; position: relative; color: var(--color-white); }
.mhp-estimator::before { content: ''; position: absolute; top: -50%; right: -20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(184,134,11,0.12) 0%, transparent 60%); pointer-events: none; }
.mhp-estimator__inner { display: grid; grid-template-columns: 1fr 1fr; position: relative; z-index: 1; }
.mhp-estimator__info { padding: var(--space-3xl); }
.mhp-estimator__overline { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--color-accent-light); margin-bottom: var(--space-md); }
.mhp-estimator__title { font-family: var(--font-display); font-size: 2rem; line-height: 1.2; margin-bottom: var(--space-lg); }
.mhp-estimator__desc { opacity: 0.75; line-height: 1.7; margin-bottom: var(--space-xl); font-size: 0.95rem; }
.mhp-estimator__disclaimer { font-size: 0.75rem; opacity: 0.5; line-height: 1.5; margin-top: var(--space-xl); }
.mhp-estimator__calc { padding: var(--space-3xl); background: rgba(255,255,255,0.04); border-left: 1px solid rgba(255,255,255,0.08); }
.mhp-calc-field { margin-bottom: var(--space-xl); }
.mhp-calc-field label { display: block; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: var(--space-sm); opacity: 0.8; }
.mhp-calc-select { width: 100%; padding: var(--space-md) var(--space-lg); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-md); color: var(--color-white); font-family: var(--font-body); font-size: 0.9rem; appearance: none; cursor: pointer; transition: all var(--transition-fast); background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ffffff' stroke-width='1.5' fill='none' opacity='0.5'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; }
.mhp-calc-select:hover { border-color: rgba(255,255,255,0.3); }
.mhp-calc-select option { background: var(--color-primary-dark); color: var(--color-white); }
.mhp-range-wrapper { position: relative; padding-top: var(--space-sm); }
.mhp-range-labels { display: flex; justify-content: space-between; margin-top: var(--space-xs); font-size: 0.7rem; opacity: 0.5; }
.mhp-estimator input[type="range"] { -webkit-appearance: none; appearance: none; width: 100%; height: 6px; border-radius: 3px; background: rgba(255,255,255,0.15); outline: none; }
.mhp-estimator input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: var(--color-accent); cursor: pointer; border: 3px solid var(--color-white); box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
.mhp-range-current { text-align: center; font-family: var(--font-display); font-size: 1.1rem; color: var(--color-accent-light); margin-top: var(--space-xs); }
.mhp-estimate-result { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-lg); padding: var(--space-xl); text-align: center; margin-top: var(--space-lg); }
.mhp-estimate-result__label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.65; font-weight: 600; margin-bottom: var(--space-sm); }
.mhp-estimate-result__value { font-family: var(--font-display); font-size: 2.2rem; color: var(--color-accent-light); line-height: 1.1; margin-bottom: var(--space-xs); }
.mhp-estimate-result__range { font-size: 0.825rem; opacity: 0.6; }
.mhp-estimate-result__breakdown { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-top: var(--space-lg); padding-top: var(--space-lg); border-top: 1px solid rgba(255,255,255,0.08); }
.mhp-breakdown-item__label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; opacity: 0.5; }
.mhp-breakdown-item__value { font-size: 1rem; font-weight: 600; margin-top: 2px; }
/* Included Grid */
.mhp-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-xl); }
.mhp-included-card { background: var(--color-cream); border-radius: var(--radius-lg); padding: var(--space-xl); text-align: center; border: 1px solid var(--color-border); transition: all var(--transition-base); }
.mhp-included-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--color-accent-light); }
.mhp-included-card__icon { width: 56px; height: 56px; background: var(--color-white); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg); box-shadow: var(--shadow-sm); }
.mhp-included-card__icon svg { width: 28px; height: 28px; color: var(--color-primary); }
.mhp-included-card__title { font-weight: 700; font-size: 0.95rem; margin-bottom: var(--space-sm); color: var(--color-primary-dark); }
.mhp-included-card__desc { font-size: 0.825rem; color: var(--color-text-light); line-height: 1.6; }
/* Related Plans */
.mhp-related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-xl); }
.mhp-related-card { background: var(--color-white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--color-border); transition: all var(--transition-base); }
.mhp-related-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl); }
.mhp-related-card__image { aspect-ratio: 3/2; overflow: hidden; }
.mhp-related-card__image img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-slow); }
.mhp-related-card:hover .mhp-related-card__image img { transform: scale(1.05); }
.mhp-related-card__info { padding: var(--space-lg); }
.mhp-related-card__name { font-family: var(--font-display); font-size: 1.1rem; color: var(--color-primary-dark); margin-bottom: var(--space-xs); }
.mhp-related-card__specs { font-size: 0.825rem; color: var(--color-text-light); }
.mhp-related-card__link { display: inline-block; margin-top: var(--space-md); font-size: 0.875rem; font-weight: 600; color: var(--color-accent); }
/* FAQ */
.mhp-faq-list { max-width: 820px; margin: 0 auto; }
.mhp-faq-item { background: var(--color-white); border-radius: var(--radius-md); margin-bottom: var(--space-md); border: 1px solid var(--color-border); overflow: hidden; transition: all var(--transition-fast); }
.mhp-faq-item:hover { border-color: var(--color-stone-dark); }
.mhp-faq-item.mhp-open { box-shadow: var(--shadow-md); border-color: var(--color-accent-light); }
.mhp-faq-question { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: var(--space-lg) var(--space-xl); font-size: 0.95rem; font-weight: 600; text-align: left; color: var(--color-text); transition: all var(--transition-fast); gap: var(--space-md); }
.mhp-faq-question:hover { color: var(--color-primary); }
.mhp-faq-question__icon { width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: var(--color-cream); display: flex; align-items: center; justify-content: center; transition: all var(--transition-fast); }
.mhp-faq-item.mhp-open .mhp-faq-question__icon { background: var(--color-accent); transform: rotate(45deg); }
.mhp-faq-question__icon svg { width: 14px; height: 14px; color: var(--color-text-secondary); }
.mhp-faq-item.mhp-open .mhp-faq-question__icon svg { color: var(--color-white); }
.mhp-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s var(--ease-out); }
.mhp-faq-answer__inner { padding: 0 var(--space-xl) var(--space-xl); font-size: 0.9rem; color: var(--color-text-secondary); line-height: 1.8; }
/* CTA */
.mhp-cta-section { background: var(--color-primary); padding: var(--space-4xl) 0; text-align: center; color: var(--color-white); position: relative; overflow: hidden; }
.mhp-cta-section::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.025'%3E%3Cpath d='m40 0 40 40-40 40L0 40z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat; }
.mhp-cta-section .mhp-container { position: relative; z-index: 1; }
.mhp-cta__title { font-family: var(--font-display); font-size: 2.2rem; margin-bottom: var(--space-md); }
.mhp-cta__text { font-size: 1.05rem; opacity: 0.8; max-width: 560px; margin: 0 auto var(--space-xl); line-height: 1.7; }
.mhp-cta__buttons { display: flex; justify-content: center; gap: var(--space-lg); flex-wrap: wrap; }
.mhp-btn-cta { display: inline-flex; align-items: center; gap: var(--space-sm); padding: var(--space-md) var(--space-2xl); border-radius: var(--radius-md); font-weight: 700; font-size: 0.95rem; transition: all var(--transition-fast); letter-spacing: 0.01em; }
.mhp-btn-cta--primary { background: var(--color-accent); color: var(--color-white); box-shadow: 0 4px 16px rgba(184,134,11,0.35); }
.mhp-btn-cta--primary:hover { background: var(--color-accent-warm); transform: translateY(-2px); color: var(--color-white); }
.mhp-btn-cta--outline { border: 2px solid rgba(255,255,255,0.3); color: var(--color-white); }
.mhp-btn-cta--outline:hover { border-color: var(--color-white); background: rgba(255,255,255,0.08); }
/* Mobile Buy Bar */
.mhp-mobile-buy-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--color-white); border-top: 1px solid var(--color-border); padding: var(--space-md) var(--space-lg); z-index: 100; box-shadow: 0 -4px 20px rgba(0,0,0,0.1); }
.mhp-mobile-buy-bar__inner { display: flex; align-items: center; justify-content: space-between; max-width: var(--max-width); margin: 0 auto; }
.mhp-mobile-buy-bar__name { font-weight: 700; color: var(--color-primary-dark); font-size: 0.9rem; }
.mhp-mobile-buy-bar__price { font-family: var(--font-display); color: var(--color-accent); font-size: 1.1rem; }
.mhp-mobile-buy-bar .mhp-btn-buy { width: auto; padding: var(--space-sm) var(--space-xl); margin-top: 0; font-size: 0.875rem; }
/* Responsive */
@media (max-width: 1024px) {
  .mhp-plan-hero__grid { grid-template-columns: 1fr; }
  .mhp-purchase-card { position: relative; top: 0; }
  .mhp-description-grid { grid-template-columns: 1fr; }
  .mhp-specs-grid { grid-template-columns: 1fr 1fr; }
  .mhp-estimator__inner { grid-template-columns: 1fr; }
  .mhp-included-grid { grid-template-columns: repeat(2, 1fr); }
  .mhp-related-grid { grid-template-columns: repeat(2, 1fr); }
  .mhp-mobile-buy-bar { display: block; }
  .mhp-plan-wrap { padding-bottom: 80px; }
}
@media (max-width: 768px) {
  :root { --space-4xl: 3.5rem; --space-3xl: 2.5rem; }
  .mhp-gallery__thumbs { grid-template-columns: repeat(4, 1fr); }
  .mhp-section__title { font-size: 1.75rem; }
  .mhp-specs-grid { grid-template-columns: 1fr; }
  .mhp-included-grid { grid-template-columns: 1fr; }
  .mhp-related-grid { grid-template-columns: 1fr; }
  .mhp-estimator__info, .mhp-estimator__calc { padding: var(--space-xl); }
  .mhp-estimator__title { font-size: 1.55rem; }
  .mhp-estimate-result__value { font-size: 1.8rem; }
  .mhp-cta__title { font-size: 1.75rem; }
}
@media (max-width: 480px) {
  .mhp-container { padding: 0 var(--space-lg); }
  .mhp-gallery__thumbs { grid-template-columns: repeat(3, 1fr); }
  .mhp-quick-specs__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-lg); }
  .mhp-cta__buttons { flex-direction: column; align-items: center; }
}
</style>
<?php
}

// ============================================================================
// MAIN LOOP
// ============================================================================
function mhp_asheville_loop() {
    $pid = get_the_ID();
    $acf = function_exists( 'get_field' );

    // ACF Fields
    $plan_name           = $acf ? get_field( 'plan_name',           $pid ) : '';
    $sqft                = $acf ? get_field( 'total_living_area',   $pid ) : '2,618';
    $main_floor          = $acf ? get_field( 'main_floor',          $pid ) : '1,760';
    $upper_floor         = $acf ? get_field( 'upper_floor',         $pid ) : '858';
    $lower_floor         = $acf ? get_field( 'lower_floor',         $pid ) : 'Unfinished';
    $bedrooms            = $acf ? get_field( 'bedrooms',            $pid ) : '4';
    $bathrooms           = $acf ? get_field( 'bathrooms',           $pid ) : '4.5';
    $stories             = $acf ? get_field( 'stories',             $pid ) : '3';
    $width               = $acf ? get_field( 'width',               $pid ) : "59'";
    $depth               = $acf ? get_field( 'depth',               $pid ) : "60'";
    $garage              = $acf ? get_field( 'garage',              $pid ) : 'Optional';
    $style               = $acf ? get_field( 'style',               $pid ) : 'Mountain \xc2\xb7 Lake \xc2\xb7 Craftsman';
    $outdoor             = $acf ? get_field( 'outdoor',             $pid ) : 'Porches, Gazebo';
    $roof                = $acf ? get_field( 'roof',                $pid ) : 'As Shown';
    $ceiling             = $acf ? get_field( 'ceiling',             $pid ) : "9' / Vaulted";
    $exterior            = $acf ? get_field( 'exterior',            $pid ) : '2x4 or 2x6';
    $additional_rooms    = $acf ? get_field( 'additional_rooms',    $pid ) : 'Loft';
    $other_features      = $acf ? get_field( 'other_features',      $pid ) : '';
    $lot_style           = $acf ? get_field( 'lot_style',           $pid ) : 'Sloping';
    $plan_description    = $acf ? get_field( 'plan_description',    $pid ) : '';
    $floor_plans_wysiwyg = $acf ? get_field( 'floor_plans',         $pid ) : '';
    $paypal              = $acf ? get_field( 'paypal',              $pid ) : '';
    $price               = $acf ? get_field( 'price',               $pid ) : '1195';
    $related_plans       = $acf ? get_field( 'related_plans',       $pid ) : array();
    $faqs                = $acf ? get_field( 'faqs',                $pid ) : array();

    // Derived
    $plan_name = $plan_name ?: get_the_title();
    $price_num = is_numeric( $price ) ? (float) $price : 1195;
    $price_fmt = '$' . number_format( $price_num, 0, '.', ',' );
    $cad_price = '$' . number_format( $price_num * 1.26, 0, '.', ',' );

    // PayPal URL
    $paypal_url = '';
    if ( $paypal ) {
        if ( preg_match( "/name=[\"']hosted_button_id[\"']\s+value=[\"']([^\"']+)[\"']/", $paypal, $m ) ) {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
        }
    }
    $buy_href = $paypal_url ?: ( get_permalink() . '#contact' );

    // Hero image
    $hero_url = get_the_post_thumbnail_url( $pid, 'full' );
    if ( ! $hero_url ) {
        $hero_url = 'https://www.maxhouseplans.com/wp-content/uploads/2025/01/house-plans-by-max-fulbright-designs-59.jpg';
    }

    // Gallery from post content shortcode
    $gallery_images = array();
    $post_content   = get_post_field( 'post_content', $pid );
    if ( has_shortcode( $post_content, 'gallery' ) ) {
        preg_match( '/\[gallery[^\]]+ids=["\']?([\d,]+)["\']?/i', $post_content, $gm );
        if ( ! empty( $gm[1] ) ) {
            foreach ( explode( ',', $gm[1] ) as $img_id ) {
                $img_id = (int) trim( $img_id );
                $full   = wp_get_attachment_image_src( $img_id, 'full' );
                $thumb  = wp_get_attachment_image_src( $img_id, 'thumbnail' );
                $alt    = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                if ( $full ) {
                    $gallery_images[] = array(
                        'full'  => $full[0],
                        'thumb' => $thumb ? $thumb[0] : $full[0],
                        'alt'   => $alt ?: $plan_name,
                    );
                }
            }
        }
    }
    if ( empty( $gallery_images ) ) {
        $thumb_url = get_the_post_thumbnail_url( $pid, 'thumbnail' ) ?: $hero_url;
        $gallery_images[] = array( 'full' => $hero_url, 'thumb' => $thumb_url, 'alt' => $plan_name . ' front exterior' );
    }

    // Formatting helpers
    $clean = function( $v ) { return (int) str_replace( array( ',', ' sq ft', ' sqft' ), '', $v ); };
    $sqft_display     = $clean($sqft) > 0 ? number_format( $clean($sqft) ) : $sqft;
    $main_fl_display  = $clean($main_floor) > 0 ? number_format( $clean($main_floor) ) : $main_floor;
    $upper_fl_display = $clean($upper_floor) > 0 ? number_format( $clean($upper_floor) ) : $upper_floor;
    $lower_fl_display = $lower_floor ?: 'Unfinished';
    $footprint        = ( $width && $depth ) ? rtrim($width,"'") . "' \xc3\x97 " . rtrim($depth,"'") . "'" : '';
    $sqft_int         = $clean($sqft) > 100 ? $clean($sqft) : 2618;
    $permalink        = get_permalink( $pid );
?>
<div class="mhp-plan-wrap">

<!-- BREADCRUMBS -->
<nav class="mhp-breadcrumbs" aria-label="Breadcrumb">
  <div class="mhp-container">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
    <span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a>
    <span>&#8250;</span>
    <a href="<?php echo esc_url(home_url('/home-plans/mountain-house-plans/')); ?>">Mountain House Plans</a>
    <span>&#8250;</span>
    <strong><?php echo esc_html($plan_name); ?></strong>
  </div>
</nav>

<!-- HERO -->
<section class="mhp-plan-hero">
  <div class="mhp-container">
    <div class="mhp-plan-hero__grid">

      <!-- Gallery -->
      <div class="mhp-gallery mhp-animate-in">
        <div class="mhp-gallery__main" id="mhpGalleryMain">
          <span class="mhp-gallery__badge">Popular Plan</span>
          <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($plan_name); ?> house plan" id="mhpMainImage" fetchpriority="high">
          <span class="mhp-gallery__count">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            <?php echo count($gallery_images); ?>+ Photos
          </span>
        </div>
        <div class="mhp-gallery__thumbs">
          <?php foreach ( $gallery_images as $idx => $img ) : ?>
          <div class="mhp-gallery__thumb<?php echo $idx === 0 ? ' mhp-active' : ''; ?>" onclick="mhpSwapImage(this)" data-full="<?php echo esc_url($img['full']); ?>">
            <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Purchase Card -->
      <aside class="mhp-purchase-card" aria-label="Plan purchase options">
        <div class="mhp-purchase-card__header">
          <h1 class="mhp-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
          <p class="mhp-purchase-card__style"><?php echo esc_html($style); ?></p>
        </div>
        <div class="mhp-purchase-card__specs">
          <div class="mhp-spec-cell">
            <div class="mhp-spec-cell__value"><?php echo esc_html($sqft_display); ?></div>
            <div class="mhp-spec-cell__label">Sq Ft</div>
          </div>
          <div class="mhp-spec-cell">
            <div class="mhp-spec-cell__value"><?php echo esc_html($bedrooms); ?></div>
            <div class="mhp-spec-cell__label">Bedrooms</div>
          </div>
          <div class="mhp-spec-cell">
            <div class="mhp-spec-cell__value"><?php echo esc_html($bathrooms); ?></div>
            <div class="mhp-spec-cell__label">Bathrooms</div>
          </div>
          <div class="mhp-spec-cell">
            <div class="mhp-spec-cell__value"><?php echo esc_html($stories); ?></div>
            <div class="mhp-spec-cell__label">Stories</div>
          </div>
        </div>
        <div class="mhp-purchase-card__body">
          <label class="mhp-price-option mhp-selected" id="mhpOptionPdf">
            <input type="radio" name="mhp_plan_format" value="pdf" checked onchange="mhpSelectOption('pdf')">
            <div class="mhp-price-option__info">
              <div class="mhp-price-option__format">PDF Plan Set</div>
              <div class="mhp-price-option__desc">Print-ready digital plans</div>
            </div>
            <div class="mhp-price-option__price"><?php echo esc_html($price_fmt); ?></div>
          </label>
          <label class="mhp-price-option" id="mhpOptionCad">
            <input type="radio" name="mhp_plan_format" value="cad" onchange="mhpSelectOption('cad')">
            <div class="mhp-price-option__info">
              <div class="mhp-price-option__format">CAD File</div>
              <div class="mhp-price-option__desc">Editable for your builder</div>
            </div>
            <div class="mhp-price-option__price"><?php echo esc_html($cad_price); ?></div>
          </label>
          <a href="<?php echo esc_url($buy_href); ?>" class="mhp-btn-buy" id="mhpBuyButton">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="m16 10-4 4-4-4"/></svg>
            Purchase This Plan
          </a>
          <div class="mhp-trust-row">
            <span class="mhp-trust-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Secure Checkout
            </span>
            <span class="mhp-trust-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              Instant Download
            </span>
          </div>
        </div>
        <div class="mhp-purchase-card__footer">
          <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>">Need changes? Request a modification &#8594;</a>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- QUICK SPECS BAR -->
<div class="mhp-quick-specs">
  <div class="mhp-container">
    <div class="mhp-quick-specs__grid">
      <div class="mhp-quick-spec">
        <svg class="mhp-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>
        <div class="mhp-quick-spec__value"><?php echo esc_html($sqft_display); ?> sq ft</div>
        <div class="mhp-quick-spec__label">Heated Area</div>
      </div>
      <?php if ($footprint) : ?>
      <div class="mhp-quick-spec">
        <svg class="mhp-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <div class="mhp-quick-spec__value"><?php echo esc_html($footprint); ?></div>
        <div class="mhp-quick-spec__label">Footprint</div>
      </div>
      <?php endif; ?>
      <?php if ($main_fl_display) : ?>
      <div class="mhp-quick-spec">
        <svg class="mhp-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M4 20V8l8-6 8 6v12"/><path d="M9 20v-6h6v6"/></svg>
        <div class="mhp-quick-spec__value">Main: <?php echo esc_html($main_fl_display); ?></div>
        <div class="mhp-quick-spec__label">Main Floor Sq Ft</div>
      </div>
      <?php endif; ?>
      <?php if ($upper_fl_display) : ?>
      <div class="mhp-quick-spec">
        <svg class="mhp-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
        <div class="mhp-quick-spec__value">Upper: <?php echo esc_html($upper_fl_display); ?></div>
        <div class="mhp-quick-spec__label">Upper Floor Sq Ft</div>
      </div>
      <?php endif; ?>
      <div class="mhp-quick-spec">
        <svg class="mhp-quick-spec__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <div class="mhp-quick-spec__value">From <?php echo esc_html($price_fmt); ?></div>
        <div class="mhp-quick-spec__label">Plan Price</div>
      </div>
    </div>
  </div>
</div>

<!-- PLAN DESCRIPTION -->
<section class="mhp-section" id="description">
  <div class="mhp-container">
    <div class="mhp-description-grid">
      <div class="mhp-description__content">
        <?php if ($plan_description) : ?>
          <?php echo wp_kses_post($plan_description); ?>
        <?php else : ?>
          <h2>A Mountain Home Designed Around the View</h2>
          <p>The <?php echo esc_html($plan_name); ?> is one of our most-requested floor plans &#8212; a three-story craftsman home designed specifically for sloping mountain and lake lots. Every major living space opens to the rear of the home, so the landscape becomes part of the room.</p>
        <?php endif; ?>
      </div>
      <div class="mhp-highlights-card">
        <h3 class="mhp-highlights-card__title">Plan Highlights</h3>
        <div class="mhp-highlight-item">
          <div class="mhp-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></div>
          <div class="mhp-highlight-item__text"><strong>Vaulted Great Room</strong><span>Two-story glass wall, exposed timbers, stone fireplace</span></div>
        </div>
        <div class="mhp-highlight-item">
          <div class="mhp-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
          <div class="mhp-highlight-item__text"><strong>Main-Level Master Suite</strong><span>Private covered porch, soaking tub, walk-in closet</span></div>
        </div>
        <div class="mhp-highlight-item">
          <div class="mhp-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
          <div class="mhp-highlight-item__text"><strong>Sloping Lot Design</strong><span>Walkout basement, rear views on every level</span></div>
        </div>
        <div class="mhp-highlight-item">
          <div class="mhp-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg></div>
          <div class="mhp-highlight-item__text"><strong>Wraparound Porches</strong><span>Covered porches on every level, rear gazebo with fireplace</span></div>
        </div>
        <div class="mhp-highlight-item">
          <div class="mhp-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
          <div class="mhp-highlight-item__text"><strong>Open Loft</strong><span>Connects upper bedrooms, overlooks great room</span></div>
        </div>
        <?php if ($garage) : ?>
        <div class="mhp-highlight-item">
          <div class="mhp-highlight-item__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
          <div class="mhp-highlight-item__text"><strong>Garage</strong><span><?php echo esc_html($garage); ?></span></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- DETAILED SPECS -->
<section class="mhp-section mhp-section--alt" id="specs">
  <div class="mhp-container">
    <div class="mhp-section__header">
      <p class="mhp-section__overline">Specifications</p>
      <h2 class="mhp-section__title">Full Plan Details</h2>
      <p class="mhp-section__subtitle">Everything you need to evaluate this plan for your build.</p>
    </div>
    <div class="mhp-specs-grid">
      <div class="mhp-specs-group">
        <h3 class="mhp-specs-group__title">Living Area</h3>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Main Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($main_fl_display); ?> sq ft</span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Upper Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($upper_fl_display); ?> sq ft</span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Lower Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($lower_fl_display); ?></span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Total Heated Area</span><span class="mhp-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Width</span><span class="mhp-spec-row__value"><?php echo esc_html($width); ?></span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Depth</span><span class="mhp-spec-row__value"><?php echo esc_html($depth); ?></span></div>
      </div>
      <div class="mhp-specs-group">
        <h3 class="mhp-specs-group__title">House Features</h3>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Bedrooms</span><span class="mhp-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Bathrooms</span><span class="mhp-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Stories</span><span class="mhp-spec-row__value"><?php echo esc_html($stories); ?></span></div>
        <?php if ($additional_rooms) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Additional Rooms</span><span class="mhp-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div>
        <?php endif; ?>
        <?php if ($garage) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Garage</span><span class="mhp-spec-row__value"><?php echo esc_html($garage); ?></span></div>
        <?php endif; ?>
        <?php if ($outdoor) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Outdoor Spaces</span><span class="mhp-spec-row__value"><?php echo esc_html($outdoor); ?></span></div>
        <?php endif; ?>
        <?php if ($other_features) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Other Features</span><span class="mhp-spec-row__value"><?php echo esc_html($other_features); ?></span></div>
        <?php endif; ?>
      </div>
      <div class="mhp-specs-group">
        <h3 class="mhp-specs-group__title">Construction</h3>
        <?php if ($exterior) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Exterior Framing</span><span class="mhp-spec-row__value"><?php echo esc_html($exterior); ?></span></div>
        <?php endif; ?>
        <?php if ($ceiling) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Ceiling Height</span><span class="mhp-spec-row__value"><?php echo esc_html($ceiling); ?></span></div>
        <?php endif; ?>
        <?php if ($style) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Home Style</span><span class="mhp-spec-row__value"><?php echo esc_html($style); ?></span></div>
        <?php endif; ?>
        <?php if ($lot_style) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Lot Style</span><span class="mhp-spec-row__value"><?php echo esc_html($lot_style); ?></span></div>
        <?php endif; ?>
        <?php if ($roof) : ?>
        <div class="mhp-spec-row"><span class="mhp-spec-row__label">Roof</span><span class="mhp-spec-row__value"><?php echo esc_html($roof); ?></span></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- FLOOR PLANS -->
<section class="mhp-section" id="floorplans">
  <div class="mhp-container">
    <div class="mhp-section__header">
      <p class="mhp-section__overline">Floor Plans</p>
      <h2 class="mhp-section__title">Explore the Layout</h2>
      <p class="mhp-section__subtitle">Each level is designed for clear circulation, natural light, and direct access to outdoor living.</p>
    </div>
    <?php if ($floor_plans_wysiwyg) : ?>
      <div class="mhp-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans_wysiwyg); ?></div>
    <?php else : ?>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:var(--space-xl);">
      <?php
      $fp_items = array(
        array('img'=>'https://www.maxhouseplans.com/wp-content/uploads/2011/08/mountain-house-floor-plan.png','label'=>'Main Floor','sqft'=>$main_fl_display.' sq ft'),
        array('img'=>'https://www.maxhouseplans.com/wp-content/uploads/2011/08/Upper-Level-Floorplan.png','label'=>'Upper Floor','sqft'=>$upper_fl_display.' sq ft'),
        array('img'=>'https://www.maxhouseplans.com/wp-content/uploads/2011/08/Lower-Level-Floor-Plan.png','label'=>'Lower Level','sqft'=>$lower_fl_display),
        array('img'=>'https://www.maxhouseplans.com/wp-content/uploads/2011/08/House-Roof-Plan.png','label'=>'Roof Plan','sqft'=>'Multi-Gable'),
      );
      foreach ($fp_items as $fp) : ?>
      <div style="background:var(--color-white);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-md);border:1px solid var(--color-border);">
        <div style="padding:var(--space-xl);background:var(--color-cream);">
          <img src="<?php echo esc_url($fp['img']); ?>" alt="<?php echo esc_attr($plan_name.' '.$fp['label']); ?>" loading="lazy" style="width:100%;border-radius:var(--radius-sm);">
        </div>
        <div style="padding:var(--space-lg) var(--space-xl);display:flex;justify-content:space-between;align-items:center;">
          <span style="font-weight:700;font-size:0.95rem;color:var(--color-primary-dark);"><?php echo esc_html($fp['label']); ?></span>
          <span style="font-size:0.825rem;color:var(--color-text-light);"><?php echo esc_html($fp['sqft']); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- COST ESTIMATOR -->
<section class="mhp-section mhp-section--alt" id="cost-estimator">
  <div class="mhp-container">
    <div class="mhp-section__header">
      <p class="mhp-section__overline">Budget Planning</p>
      <h2 class="mhp-section__title">Cost to Build Estimator</h2>
      <p class="mhp-section__subtitle">Get a ballpark construction estimate based on 2026 national data. Adjust for your region and finish level.</p>
    </div>
    <div class="mhp-estimator">
      <div class="mhp-estimator__inner">
        <div class="mhp-estimator__info">
          <p class="mhp-estimator__overline">Why We Include This</p>
          <h3 class="mhp-estimator__title">Understand Your Build Budget Before You Buy</h3>
          <p class="mhp-estimator__desc">One of the first questions every homeowner asks is &#8220;what will this cost to build?&#8221; This estimator uses 2026 national averages for residential construction, adjusted by region and finish quality. It covers the structure itself &#8212; framing, mechanicals, roofing, and finishes &#8212; but not land, permits, site work, or landscaping.</p>
          <p class="mhp-estimator__desc">Your actual cost will depend on your builder, your lot, and your local market. We always recommend getting a line-item bid from a contractor familiar with your area.</p>
          <p class="mhp-estimator__disclaimer">* Estimates are based on <?php echo esc_html($sqft_display); ?> sq ft of heated space using 2026 industry cost-per-square-foot data. This is a rough planning tool, not a quote. Always get a professional bid for your specific project.</p>
        </div>
        <div class="mhp-estimator__calc">
          <div class="mhp-calc-field">
            <label for="mhpRegionSelect">Your Region</label>
            <select id="mhpRegionSelect" class="mhp-calc-select" onchange="mhpUpdateEstimate()">
              <option value="southeast">Southeast (GA, NC, SC, TN, AL)</option>
              <option value="south_central">South Central (TX, OK, AR, LA)</option>
              <option value="midwest">Midwest (OH, IN, MI, IL, MO)</option>
              <option value="mountain_west">Mountain West (CO, UT, MT, ID)</option>
              <option value="northeast">Northeast (NY, NJ, PA, CT, MA)</option>
              <option value="pacific_nw">Pacific Northwest (WA, OR)</option>
              <option value="west_coast">West Coast (CA)</option>
            </select>
          </div>
          <div class="mhp-calc-field">
            <label>Finish Level</label>
            <div class="mhp-range-wrapper">
              <input type="range" id="mhpFinishLevel" min="1" max="4" step="1" value="2" onchange="mhpUpdateEstimate()" oninput="mhpUpdateEstimate()">
              <div class="mhp-range-labels"><span>Standard</span><span>Mid-Range</span><span>Custom</span><span>Premium</span></div>
              <div class="mhp-range-current" id="mhpFinishLabel">Mid-Range Build</div>
            </div>
          </div>
          <div class="mhp-estimate-result">
            <div class="mhp-estimate-result__label">Estimated Construction Cost</div>
            <div class="mhp-estimate-result__value" id="mhpEstimateValue">Calculating&#8230;</div>
            <div class="mhp-estimate-result__range" id="mhpEstimatePerSqft"></div>
            <div class="mhp-estimate-result__breakdown">
              <div class="mhp-breakdown-item">
                <div class="mhp-breakdown-item__label">Materials (est. 45%)</div>
                <div class="mhp-breakdown-item__value" id="mhpMaterialsCost">&#8211;</div>
              </div>
              <div class="mhp-breakdown-item">
                <div class="mhp-breakdown-item__label">Labor (est. 35%)</div>
                <div class="mhp-breakdown-item__value" id="mhpLaborCost">&#8211;</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="mhp-section" id="whats-included">
  <div class="mhp-container">
    <div class="mhp-section__header">
      <p class="mhp-section__overline">Your Plan Set</p>
      <h2 class="mhp-section__title">What&#8217;s Included</h2>
      <p class="mhp-section__subtitle">Every plan set we deliver is complete and build-ready. Here&#8217;s what you&#8217;ll receive.</p>
    </div>
    <div class="mhp-included-grid">
      <div class="mhp-included-card">
        <div class="mhp-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg></div>
        <h3 class="mhp-included-card__title">Elevations</h3>
        <p class="mhp-included-card__desc">Front, side, and rear elevations at &#188;&#8221; scale with material notes and dimensions.</p>
      </div>
      <div class="mhp-included-card">
        <div class="mhp-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
        <h3 class="mhp-included-card__title">Floor Plans</h3>
        <p class="mhp-included-card__desc">Fully dimensioned and detailed plans for every level of the home.</p>
      </div>
      <div class="mhp-included-card">
        <div class="mhp-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20"/><path d="M5 20V8l7-5 7 5v12"/></svg></div>
        <h3 class="mhp-included-card__title">Foundation Plan</h3>
        <p class="mhp-included-card__desc">Walkout basement foundation layout with footing and wall dimensions.</p>
      </div>
      <div class="mhp-included-card">
        <div class="mhp-included-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7"/><path d="M3 9v0l9 4 9-4"/></svg></div>
        <h3 class="mhp-included-card__title">Roof Plan</h3>
        <p class="mhp-included-card__desc">Complete roof plan with pitches, ridges, and drainage direction.</p>
      </div>
    </div>
  </div>
</section>

<!-- RELATED PLANS -->
<?php if (!empty($related_plans) && is_array($related_plans)) : ?>
<section class="mhp-section mhp-section--alt" id="related-plans">
  <div class="mhp-container">
    <div class="mhp-section__header">
      <p class="mhp-section__overline">More to Explore</p>
      <h2 class="mhp-section__title">Related House Plans</h2>
      <p class="mhp-section__subtitle">You might also like these plans from our collection.</p>
    </div>
    <div class="mhp-related-grid">
      <?php foreach ($related_plans as $rp) :
            if (!is_object($rp)) continue;
            $r_id    = $rp->ID;
            $r_name  = $acf ? get_field('plan_name',$r_id) : get_the_title($r_id);
            $r_sqft  = $acf ? get_field('total_living_area',$r_id) : '';
            $r_beds  = $acf ? get_field('bedrooms',$r_id) : '';
            $r_baths = $acf ? get_field('bathrooms',$r_id) : '';
            $r_img   = get_the_post_thumbnail_url($r_id,'plan-card');
            $r_link  = get_permalink($r_id);
            $r_specs = array_filter(array(
              $r_sqft  ? number_format((int)str_replace(',','',$r_sqft)).' sq ft' : '',
              $r_beds  ? $r_beds.' bed'  : '',
              $r_baths ? $r_baths.' bath': '',
            ));
      ?>
      <div class="mhp-related-card">
        <div class="mhp-related-card__image">
          <?php if ($r_img) : ?><img src="<?php echo esc_url($r_img); ?>" alt="<?php echo esc_attr($r_name ?: get_the_title($r_id)); ?>" loading="lazy">
          <?php else : ?><div style="background:var(--color-stone);min-height:200px;"></div><?php endif; ?>
        </div>
        <div class="mhp-related-card__info">
          <div class="mhp-related-card__name"><?php echo esc_html($r_name ?: get_the_title($r_id)); ?></div>
          <div class="mhp-related-card__specs"><?php echo esc_html(implode(' &#183; ', $r_specs)); ?></div>
          <a href="<?php echo esc_url($r_link); ?>" class="mhp-related-card__link">View Plan &#8594;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="mhp-section<?php echo empty($related_plans) ? ' mhp-section--alt' : ''; ?>" id="faq">
  <div class="mhp-container">
    <div class="mhp-section__header">
      <p class="mhp-section__overline">Common Questions</p>
      <h2 class="mhp-section__title">Frequently Asked Questions</h2>
      <p class="mhp-section__subtitle">Answers to the most common questions about this plan, modifications, and the build process.</p>
    </div>
    <div class="mhp-faq-list">
      <?php if (!empty($faqs) && is_array($faqs)) :
            foreach ($faqs as $faq) :
              $q = isset($faq['question']) ? $faq['question'] : '';
              $a = isset($faq['answer'])   ? $faq['answer']   : '';
              if (!$q) continue; ?>
      <div class="mhp-faq-item">
        <button class="mhp-faq-question" onclick="mhpToggleFaq(this)" aria-expanded="false">
          <span><?php echo esc_html($q); ?></span>
          <span class="mhp-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="mhp-faq-answer"><div class="mhp-faq-answer__inner"><?php echo wp_kses_post($a); ?></div></div>
      </div>
      <?php endforeach;
      else : // Static fallback FAQs ?>
      <div class="mhp-faq-item">
        <button class="mhp-faq-question" onclick="mhpToggleFaq(this)" aria-expanded="false">
          <span>What is included in the plan set?</span>
          <span class="mhp-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="mhp-faq-answer"><div class="mhp-faq-answer__inner">Each plan set includes dimensioned floor plans for all levels, front, side, and rear elevations with material notes, a foundation plan, and a roof plan. Plans are available as PDF (<?php echo esc_html($price_fmt); ?>) or editable CAD files (<?php echo esc_html($cad_price); ?>).</div></div>
      </div>
      <div class="mhp-faq-item">
        <button class="mhp-faq-question" onclick="mhpToggleFaq(this)" aria-expanded="false">
          <span>Can this plan be modified?</span>
          <span class="mhp-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="mhp-faq-answer"><div class="mhp-faq-answer__inner">Yes. We handle modifications in-house &#8212; from minor layout changes to significant redesigns. Contact our family directly to discuss what you need changed and get a clear quote before any work begins.</div></div>
      </div>
      <div class="mhp-faq-item">
        <button class="mhp-faq-question" onclick="mhpToggleFaq(this)" aria-expanded="false">
          <span>Is this plan designed for a sloping lot?</span>
          <span class="mhp-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="mhp-faq-answer"><div class="mhp-faq-answer__inner">Yes. The plan is specifically designed for a sloping lot with a walkout basement on the lower level. It works well for mountain or lakefront properties where rear views are a priority.</div></div>
      </div>
      <div class="mhp-faq-item">
        <button class="mhp-faq-question" onclick="mhpToggleFaq(this)" aria-expanded="false">
          <span>Does this plan require an engineer&#8217;s stamp?</span>
          <span class="mhp-faq-question__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        </button>
        <div class="mhp-faq-answer"><div class="mhp-faq-answer__inner">Stock plans do not include a professional stamp. If your local building department requires one, you will need a licensed structural engineer or architect in your state to review and stamp the plans.</div></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="mhp-cta-section">
  <div class="mhp-container">
    <h2 class="mhp-cta__title">Ready to Build Your Dream Home?</h2>
    <p class="mhp-cta__text">Whether you&#8217;re ready to purchase the <?php echo esc_html($plan_name); ?> plan or have questions about modifications, our family is here to help.</p>
    <div class="mhp-cta__buttons">
      <a href="<?php echo esc_url($buy_href); ?>" class="mhp-btn-cta mhp-btn-cta--primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
        Purchase This Plan
      </a>
      <a href="<?php echo esc_url(home_url('/home-plan-modifications/')); ?>" class="mhp-btn-cta mhp-btn-cta--outline">Request a Modification</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="mhp-btn-cta mhp-btn-cta--outline">Talk With Our Family</a>
    </div>
  </div>
</section>

<!-- MOBILE STICKY BAR -->
<div class="mhp-mobile-buy-bar">
  <div class="mhp-mobile-buy-bar__inner">
    <div>
      <div class="mhp-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
      <div class="mhp-mobile-buy-bar__price">From <?php echo esc_html($price_fmt); ?></div>
    </div>
    <a href="<?php echo esc_url($buy_href); ?>" class="mhp-btn-buy">Purchase Plan</a>
  </div>
</div>

</div><!-- .mhp-plan-wrap -->

<script>
(function(){
  window.mhpSwapImage = function(thumb) {
    var url = thumb.dataset.full;
    var img = document.getElementById('mhpMainImage');
    if (img) img.src = url;
    document.querySelectorAll('.mhp-gallery__thumb').forEach(function(t){ t.classList.remove('mhp-active'); });
    thumb.classList.add('mhp-active');
  };
  window.mhpSelectOption = function(fmt) {
    document.querySelectorAll('.mhp-price-option').forEach(function(o){ o.classList.remove('mhp-selected'); });
    var el = document.getElementById(fmt === 'pdf' ? 'mhpOptionPdf' : 'mhpOptionCad');
    if (el) el.classList.add('mhp-selected');
  };
  var MHP_SQFT = <?php echo (int)$sqft_int; ?>;
  var R = {
    southeast:{low:155,mid:210}, south_central:{low:145,mid:200}, midwest:{low:150,mid:205},
    mountain_west:{low:175,mid:240}, northeast:{low:200,mid:275},
    pacific_nw:{low:185,mid:255}, west_coast:{low:220,mid:310}
  };
  var F = {
    1:{f:0.85,l:'Standard Build'}, 2:{f:1.0,l:'Mid-Range Build'},
    3:{f:1.30,l:'Custom Build'}, 4:{f:1.65,l:'Premium Custom Build'}
  };
  window.mhpUpdateEstimate = function() {
    var rEl = document.getElementById('mhpRegionSelect');
    var fEl = document.getElementById('mhpFinishLevel');
    if (!rEl||!fEl) return;
    var rd = R[rEl.value]; var fm = F[parseInt(fEl.value,10)];
    var lbl = document.getElementById('mhpFinishLabel');
    if (lbl) lbl.textContent = fm.l;
    var lo = Math.round(rd.low*fm.f); var hi = Math.round(rd.mid*fm.f);
    var loT = lo*MHP_SQFT; var hiT = hi*MHP_SQFT;
    var fmt = function(n){return '$'+n.toLocaleString();};
    var fmtK = function(n){return '$'+Math.round(n/1000)+'K';};
    var set = function(id,v){var e=document.getElementById(id);if(e)e.textContent=v;};
    set('mhpEstimateValue', fmt(loT)+' \u2013 '+fmt(hiT));
    set('mhpEstimatePerSqft','$'+lo+' \u2013 $'+hi+' per sq ft');
    set('mhpMaterialsCost', fmtK(loT*0.45)+' \u2013 '+fmtK(hiT*0.45));
    set('mhpLaborCost', fmtK(loT*0.35)+' \u2013 '+fmtK(hiT*0.35));
  };
  window.mhpToggleFaq = function(btn) {
    var item = btn.parentElement;
    var isOpen = item.classList.contains('mhp-open');
    document.querySelectorAll('.mhp-faq-item').forEach(function(f){
      f.classList.remove('mhp-open');
      var q=f.querySelector('.mhp-faq-question'); if(q) q.setAttribute('aria-expanded','false');
      var a=f.querySelector('.mhp-faq-answer'); if(a) a.style.maxHeight='0';
    });
    if (!isOpen) {
      item.classList.add('mhp-open');
      btn.setAttribute('aria-expanded','true');
      var ans = item.querySelector('.mhp-faq-answer');
      if (ans) ans.style.maxHeight = ans.scrollHeight+'px';
    }
  };
  if (document.readyState==='loading') {
    document.addEventListener('DOMContentLoaded', mhpUpdateEstimate);
  } else { mhpUpdateEstimate(); }
})();
</script>
<?php
}

genesis();
