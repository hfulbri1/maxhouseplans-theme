<?php
/**
 * Template Name: Plan Page
 *
 * Full-width plan detail page for MaxHousePlans.com.
 * Includes: Hero, Trust Strip, Floor Plans, Plan Features, Build Cost Estimator,
 * Popular Modifications, What's Included, Social Proof, FAQ, Related Plans,
 * Designer, Sticky Bar.
 */

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function() { return 'full-width-content'; } );
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_plan_loop' );
add_action( 'wp_enqueue_scripts', 'mhp_plan_scripts' );

function mhp_plan_scripts() {
    wp_enqueue_script(
        'mhp-plan-page',
        get_stylesheet_directory_uri() . '/js/plan-page.js',
        array(),
        '2.0',
        true
    );

    // Enqueue per-plan CSS based on post slug
    if ( is_singular( 'plans' ) ) {
        $plan_slug = get_post_field( 'post_name', get_the_ID() );
        $plan_css  = get_stylesheet_directory() . '/css/plan-' . sanitize_title( $plan_slug ) . '.css';
        if ( file_exists( $plan_css ) ) {
            wp_enqueue_style(
                'mhp-plan-' . sanitize_title( $plan_slug ),
                get_stylesheet_directory_uri() . '/css/plan-' . sanitize_title( $plan_slug ) . '.css',
                array( 'mhp-style-new' ),
                filemtime( $plan_css )
            );
        }
    }
}

// Add body class for plan slug (per-plan CSS namespacing)
add_filter( 'body_class', function( $classes ) {
    if ( is_singular( 'plans' ) ) {
        $classes[] = 'plan-' . get_post_field( 'post_name', get_the_ID() );
    }
    return $classes;
} );

function mhp_plan_loop() {
    global $post;
    $pid = get_the_ID();

    /* ── ACF Fields ─────────────────────────────────────────── */
    $plan_name        = get_field( 'plan_name', $pid )        ?: get_the_title();
    $total_living     = get_field( 'total_living_area', $pid ) ?: '';
    $main_floor       = get_field( 'main_floor', $pid )        ?: '';
    $upper_floor      = get_field( 'upper_floor', $pid )       ?: '';
    $lower_floor      = get_field( 'lower_floor', $pid )       ?: '';
    $bedrooms         = get_field( 'bedrooms', $pid )          ?: '';
    $bathrooms        = get_field( 'bathrooms', $pid )         ?: '';
    $stories          = get_field( 'stories', $pid )           ?: '';
    $width            = get_field( 'width', $pid )             ?: '';
    $depth            = get_field( 'depth', $pid )             ?: '';
    $garage           = get_field( 'garage', $pid )            ?: '';
    $style            = get_field( 'style', $pid )             ?: '';
    $outdoor          = get_field( 'outdoor', $pid )           ?: '';
    $roof             = get_field( 'roof', $pid )              ?: '';
    $ceiling          = get_field( 'ceiling', $pid )           ?: '';
    $exterior         = get_field( 'exterior', $pid )          ?: '';
    $additional_rooms = get_field( 'additional_rooms', $pid )  ?: '';
    $other_features   = get_field( 'other_features', $pid )    ?: '';
    $lot_style        = get_field( 'lot_style', $pid )         ?: '';
    $plan_description = get_field( 'plan_description', $pid )  ?: '';
    $floor_plans_acf  = get_field( 'floor_plans', $pid )       ?: '';
    $floor_plans_out  = $floor_plans_acf ?: apply_filters( 'the_content', $post->post_content );
    $plan_image_id    = get_field( 'plan_image', $pid );
    $paypal           = get_field( 'paypal', $pid )            ?: '';
    $related_plans    = get_field( 'related_plans', $pid )     ?: array();
    $faqs_acf         = get_field( 'faqs', $pid )              ?: array();
    $price            = get_field( 'price', $pid )             ?: '';

    /* ── PayPal URL extraction ───────────────────────────────── */
    $paypal_url = '';
    if ( $paypal ) {
        if ( preg_match( '/name=["\']hosted_button_id["\']\s+value=["\']([^"\']+)["\']/', $paypal, $m ) ) {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
        } elseif ( preg_match( '/hosted_button_id.*?value=["\']([^"\']+)["\']/', $paypal, $m ) ) {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
        }
    }
    $buy_href = $paypal_url ? esc_url( $paypal_url ) : esc_url( home_url( '/contact-us/' ) );

    /* ── Hero / Gallery images ───────────────────────────────── */
    $hero_img_url   = $plan_image_id ? wp_get_attachment_image_url( $plan_image_id, 'large' ) : get_the_post_thumbnail_url( $pid, 'large' );
    $hero_img_url   = $hero_img_url ?: '';
    $hero_img_alt   = $plan_image_id ? esc_attr( get_post_meta( $plan_image_id, '_wp_attachment_image_alt', true ) ) : esc_attr( $plan_name );

    // Use WP featured image as hero (preserves SEO/alt text set in media library)
    $featured_img_url = get_the_post_thumbnail_url( $pid, 'large' );
    if ( $featured_img_url ) {
        $hero_img_url = $featured_img_url;
        $hero_img_alt = get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) ?: $plan_name;
    }

    // Get gallery images from post content (preserves existing gallery plugin + SEO)
    $post_content = get_post_field( 'post_content', $pid );
    $thumbs = array();
    if ( preg_match_all( '/\[gallery[^\]]*ids=["\']([^"\']*)["\']/i', $post_content, $gm ) ) {
        $ids = explode( ',', $gm[1][0] );
        foreach ( $ids as $att_id ) {
            $att_id = trim( $att_id );
            if ( ! $att_id ) continue;
            $thumbs[] = array(
                'full'  => wp_get_attachment_image_url( $att_id, 'large' ),
                'thumb' => wp_get_attachment_image_url( $att_id, 'medium' ),
                'alt'   => esc_attr( get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $att_id ) ),
            );
        }
    }
    // Fallback: use attached media if no gallery shortcode found
    if ( empty( $thumbs ) ) {
        $attached = get_attached_media( 'image', $pid );
        foreach ( $attached as $att ) {
            if ( $att->ID == get_post_thumbnail_id( $pid ) ) continue; // skip featured
            $thumbs[] = array(
                'full'  => wp_get_attachment_image_url( $att->ID, 'large' ),
                'thumb' => wp_get_attachment_image_url( $att->ID, 'medium' ),
                'alt'   => esc_attr( get_post_meta( $att->ID, '_wp_attachment_image_alt', true ) ?: $att->post_title ),
            );
        }
    }

    /* ── Price display ───────────────────────────────────────── */
    $price_display = $price ? '$' . number_format( (float) $price, 0, '.', ',' ) : '';

    /* ── Stat labels ─────────────────────────────────────────── */
    $sqft_total_display = $total_living ?: ( $main_floor ? (int)$main_floor + (int)$upper_floor + (int)$lower_floor : '' );

    /* ── Trust strip pills (dynamic from fields) ─────────────── */
    $trust_pills = array();
    if ( $outdoor )      $trust_pills[] = esc_html( $outdoor );
    if ( $lot_style )    $trust_pills[] = esc_html( $lot_style ) . ' Lot Ready';
    if ( $ceiling )      $trust_pills[] = esc_html( $ceiling ) . ' Ceilings';
    if ( $additional_rooms ) $trust_pills[] = esc_html( $additional_rooms );
    if ( $other_features )   $trust_pills[] = esc_html( $other_features );
    // Ensure at least some pills
    if ( empty( $trust_pills ) ) {
        $trust_pills = array( 'Permit-Ready Plans', 'Instant PDF Delivery', '25+ Years Experience', 'Talk to Max Directly' );
    }

    /* ── FAQ data ────────────────────────────────────────────── */
    $faqs = array();
    if ( ! empty( $faqs_acf ) ) {
        foreach ( $faqs_acf as $row ) {
            $faqs[] = array(
                'q' => $row['question'] ?? '',
                'a' => $row['answer']   ?? '',
            );
        }
    } else {
        $faqs = array(
            array(
                'q' => 'Will these plans pass permits in my state?',
                'a' => 'Plans meet standard residential building codes and pass in most states. Some states (NY, NJ, NV, parts of IL) require a local engineer\'s stamp. Contact your building department to confirm. Max can refer engineers in most regions.',
            ),
            array(
                'q' => 'Can I modify this plan?',
                'a' => 'Yes — most modifications are available. Common requests include adding a garage bay, reversing the plan, changing foundation type, or adjusting room sizes. Contact Max for a same-day quote.',
            ),
            array(
                'q' => 'What exactly is included?',
                'a' => 'Every plan set includes front/side/rear elevations at 1/4&quot; scale, complete dimensioned floor plans for all levels, foundation plan, and roof plan. Electrical plans are an add-on for $350.',
            ),
            array(
                'q' => 'How long does delivery take?',
                'a' => 'PDF plans are delivered instantly via email after purchase. CAD files within 1 business day.',
            ),
            array(
                'q' => 'Can I leave the basement unfinished and finish it later?',
                'a' => 'Absolutely. The lower level plans are included whether you finish now or later. Many buyers start with an unfinished basement to reduce initial costs.',
            ),
            array(
                'q' => 'Is this plan suitable for a flat lot?',
                'a' => 'Yes — with a slab or crawlspace foundation modification (from $300). Contact Max to discuss your lot and get the right foundation type specified.',
            ),
            array(
                'q' => 'Who do I contact if I have questions after buying?',
                'a' => 'Max directly. Email or call — his contact info is on the site. He\'s the designer and builder, not a call center.',
            ),
        );
    }

    /* ── Output ──────────────────────────────────────────────── */
    ?>
<style>
:root {
    --cream: #F7F4EF;
    --charcoal: #1C1C1C;
    --clay: #C4714A;
    --clay-light: #D4875F;
    --sage: #7A8C6E;
    --warm-gray: #8A8278;
    --warm-gray-light: #C5BFB8;
    --warm-gray-lighter: #E8E3DC;
    --white: #FFFFFF;
    --section-pad: clamp(60px, 8vw, 100px);
    --side-pad: clamp(20px, 5vw, 64px);
}
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
.mhp-plan-wrap { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--charcoal); overflow-x: hidden; -webkit-font-smoothing: antialiased; }
.mhp-plan-wrap img { max-width: 100%; height: auto; display: block; }

/* ── HERO ── */
.mhp-hero { display: grid; grid-template-columns: 75% 25%; min-height: 100svh; }
@media (max-width: 900px) { .mhp-hero { grid-template-columns: 1fr; min-height: auto; } }

.mhp-hero-gallery { position: relative; background: #1A1410; overflow: hidden; }
@media (max-width: 900px) { .mhp-hero-gallery { min-height: 60vw; } }

.mhp-hero-video-wrap { position: relative; width: 100%; height: calc(100% - 110px); overflow: hidden; }
@media (max-width: 900px) { .mhp-hero-video-wrap { height: calc(60vw - 80px); } }

#mhpHeroMainImg { width: 100%; height: 100%; object-fit: cover; display: block; transition: opacity 0.4s ease; }

.mhp-hero-badge { position: absolute; top: 20px; left: 20px; background: var(--clay); color: white; padding: 6px 13px; font-size: 10px; font-weight: 500; letter-spacing: 0.12em; text-transform: uppercase; z-index: 3; }

.mhp-social-proof { position: absolute; bottom: 14px; left: 14px; background: rgba(28,28,28,0.72); backdrop-filter: blur(8px); color: white; padding: 8px 14px; font-size: 11px; letter-spacing: 0.04em; display: flex; align-items: center; gap: 8px; z-index: 3; }
.mhp-social-proof-dot { width: 6px; height: 6px; background: #5CB85C; border-radius: 50%; flex-shrink: 0; animation: mhpPulse 2s infinite; }
@keyframes mhpPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.3)} }

.mhp-hero-thumbs { display: grid; grid-template-columns: repeat(6, 1fr); gap: 2px; height: 110px; background: #111; padding: 2px; }
@media (max-width: 900px) { .mhp-hero-thumbs { grid-template-columns: repeat(4, 1fr); height: 80px; } .mhp-hero-thumb:nth-child(n+5) { display: none; } }
.mhp-hero-thumb { overflow: hidden; cursor: pointer; position: relative; }
.mhp-hero-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s, opacity 0.2s; opacity: 0.6; }
.mhp-hero-thumb:hover img, .mhp-hero-thumb.active img { opacity: 1; transform: scale(1.05); }
.mhp-hero-thumb.active::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: var(--clay); }

.mhp-hero-content { display: flex; flex-direction: column; justify-content: center; padding: clamp(32px,5vw,64px) clamp(24px,4vw,48px); background: var(--cream); }

.mhp-breadcrumb { font-size: 11px; color: var(--warm-gray); letter-spacing: 0.06em; margin-bottom: 16px; text-transform: uppercase; }
.mhp-breadcrumb a { color: var(--clay); text-decoration: none; }

.mhp-plan-number { font-size: 11px; font-weight: 500; letter-spacing: 0.14em; text-transform: uppercase; color: var(--clay); margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
.mhp-plan-number::before { content: ''; width: 28px; height: 1px; background: var(--clay); }

.mhp-hero-title { font-family: 'DM Serif Display', serif; font-size: clamp(36px,4.5vw,58px); font-weight: 400; line-height: 1.05; color: var(--charcoal); margin-bottom: 8px; }
.mhp-hero-title em { font-style: italic; color: var(--clay); }

.mhp-hero-style-tag { font-size: 13px; color: var(--warm-gray); margin-bottom: 24px; line-height: 1.5; }

.mhp-hero-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
.mhp-stars { color: var(--clay); font-size: 14px; letter-spacing: 1px; }
.mhp-rating-text { font-size: 12px; color: var(--warm-gray); }

.mhp-hero-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: var(--warm-gray-lighter); border: 1px solid var(--warm-gray-lighter); margin-bottom: 14px; }
.mhp-stat { background: var(--cream); padding: 14px 8px; text-align: center; }
.mhp-stat-value { font-family: 'DM Serif Display', serif; font-size: 22px; color: var(--charcoal); display: block; line-height: 1; margin-bottom: 3px; }
.mhp-stat-label { font-size: 9px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--warm-gray); }

.mhp-sqft-breakdown { display: flex; gap: 14px; flex-wrap: wrap; padding: 11px 0; border-top: 1px solid var(--warm-gray-lighter); border-bottom: 1px solid var(--warm-gray-lighter); margin-bottom: 20px; }
.mhp-sqft-item { font-size: 11px; color: var(--warm-gray); }
.mhp-sqft-item strong { color: var(--charcoal); font-weight: 500; margin-left: 3px; }

.mhp-hero-price-row { display: flex; align-items: center; gap: 18px; margin-bottom: 14px; }
.mhp-price-main { display: flex; flex-direction: column; }
.mhp-price-label { font-size: 10px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--warm-gray); margin-bottom: 2px; }
.mhp-price-amount { font-family: 'DM Serif Display', serif; font-size: 38px; font-weight: 400; color: var(--charcoal); line-height: 1; }
.mhp-price-divider { width: 1px; height: 40px; background: var(--warm-gray-lighter); }
.mhp-price-secondary { font-size: 12px; color: var(--warm-gray); line-height: 1.6; }
.mhp-price-secondary a { color: var(--clay); text-decoration: none; font-size: 12px; }

.mhp-hero-actions { display: flex; gap: 10px; margin-bottom: 18px; }
.mhp-btn-primary { flex: 1; background: var(--clay); color: white; border: none; padding: 15px 18px; font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500; letter-spacing: 0.07em; text-transform: uppercase; cursor: pointer; transition: all 0.2s; text-align: center; text-decoration: none; display: inline-block; }
.mhp-btn-primary:hover { background: var(--clay-light); transform: translateY(-1px); color: white; }
.mhp-btn-secondary { background: transparent; color: var(--charcoal); border: 1px solid var(--warm-gray-lighter); padding: 15px 16px; font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 400; letter-spacing: 0.06em; text-transform: uppercase; cursor: pointer; transition: all 0.2s; white-space: nowrap; text-decoration: none; display: inline-block; }
.mhp-btn-secondary:hover { border-color: var(--charcoal); color: var(--charcoal); }

.mhp-hero-trust { display: flex; gap: 14px; flex-wrap: wrap; }
.mhp-trust-pill { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--warm-gray); }
.mhp-trust-pill svg { color: var(--sage); flex-shrink: 0; }

/* ── TRUST STRIP ── */
.mhp-trust-strip { background: var(--charcoal); padding: 16px var(--side-pad); display: flex; align-items: center; justify-content: center; gap: clamp(18px,3.5vw,44px); flex-wrap: wrap; }
.mhp-ts-item { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 400; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(247,244,239,0.6); white-space: nowrap; }
.mhp-ts-item svg { color: var(--clay); }

/* ── SECTIONS ── */
.mhp-section { padding: var(--section-pad) var(--side-pad); }
.mhp-section-inner { max-width: 1300px; margin: 0 auto; }
.mhp-section-eyebrow { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.mhp-eyebrow-line { width: 32px; height: 1px; background: var(--clay); flex-shrink: 0; }
.mhp-eyebrow-text { font-size: 10px; font-weight: 500; letter-spacing: 0.14em; text-transform: uppercase; color: var(--clay); }
.mhp-section-title { font-family: 'DM Serif Display', serif; font-size: clamp(28px,3.2vw,44px); font-weight: 400; line-height: 1.1; color: var(--charcoal); margin-bottom: clamp(28px,4.5vw,48px); }
.mhp-section-title em { font-style: italic; color: var(--clay); }

/* ── FLOOR PLANS ── */
.mhp-floor-section { background: white; }
.mhp-floor-plans-acf { font-size: 14px; color: var(--warm-gray); line-height: 1.75; }
.mhp-floor-plans-acf img { max-width: 100%; height: auto; }

.mhp-floor-nav { display: flex; margin-bottom: 36px; border-bottom: 1px solid var(--warm-gray-lighter); overflow-x: auto; -webkit-overflow-scrolling: touch; }
.mhp-floor-btn { padding: 13px 22px; font-size: 12px; font-weight: 500; letter-spacing: 0.06em; text-transform: uppercase; border: none; border-bottom: 2px solid transparent; margin-bottom: -1px; cursor: pointer; background: transparent; color: var(--warm-gray); transition: all 0.2s; white-space: nowrap; }
.mhp-floor-btn.active { color: var(--clay); border-bottom-color: var(--clay); }

.mhp-floor-content { display: none; }
.mhp-floor-content.active { display: grid; grid-template-columns: 1fr 300px; gap: 36px; align-items: start; }
@media (max-width: 900px) { .mhp-floor-content.active { grid-template-columns: 1fr; } }

.mhp-floor-img-wrap { border: 1px solid var(--warm-gray-lighter); padding: clamp(14px,2.5vw,28px); background: #FAFAF8; }
.mhp-floor-img-wrap img { width: 100%; height: auto; }

.mhp-floor-rooms { display: flex; flex-direction: column; gap: 1px; background: var(--warm-gray-lighter); border: 1px solid var(--warm-gray-lighter); }
.mhp-room-row { background: var(--cream); padding: 13px 15px; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; transition: background 0.15s; }
.mhp-room-row:hover { background: white; }
.mhp-room-name { font-family: 'DM Serif Display', serif; font-size: 16px; font-weight: 400; color: var(--charcoal); }
.mhp-room-feature { font-size: 11px; color: var(--sage); margin-top: 2px; }
.mhp-room-dim { font-size: 11px; color: var(--warm-gray); text-align: right; white-space: nowrap; flex-shrink: 0; }

/* ── FEATURES ── */
.mhp-features-section { background: var(--cream); }
.mhp-features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; background: var(--warm-gray-lighter); border: 1px solid var(--warm-gray-lighter); }
@media (max-width: 768px) { .mhp-features-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .mhp-features-grid { grid-template-columns: 1fr; } }
.mhp-feature-card { background: var(--cream); padding: clamp(18px,2.8vw,30px) clamp(16px,2.5vw,26px); transition: background 0.2s; }
.mhp-feature-card:hover { background: white; }
.mhp-feature-icon { width: 38px; height: 38px; background: var(--warm-gray-lighter); display: flex; align-items: center; justify-content: center; margin-bottom: 14px; color: var(--clay); }
.mhp-feature-title { font-family: 'DM Serif Display', serif; font-size: 18px; font-weight: 400; color: var(--charcoal); margin-bottom: 6px; }
.mhp-feature-desc { font-size: 13px; color: var(--warm-gray); line-height: 1.65; }

/* ── COST ESTIMATOR ── */
.mhp-cost-section { background: var(--charcoal); padding: var(--section-pad) var(--side-pad); }
.mhp-cost-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: clamp(36px,5.5vw,72px); align-items: center; }
@media (max-width: 768px) { .mhp-cost-inner { grid-template-columns: 1fr; } }
.mhp-cost-header .mhp-eyebrow-text { color: var(--clay); }
.mhp-cost-header .mhp-section-title { color: var(--cream); }
.mhp-cost-desc { font-size: 14px; color: rgba(247,244,239,0.55); line-height: 1.75; margin-bottom: 22px; }
.mhp-cost-features { display: flex; flex-direction: column; gap: 9px; }
.mhp-cost-feat { display: flex; align-items: center; gap: 9px; font-size: 12px; color: rgba(247,244,239,0.55); }
.mhp-cost-feat svg { color: var(--sage); flex-shrink: 0; }
.mhp-calc-box { background: rgba(247,244,239,0.05); border: 1px solid rgba(247,244,239,0.1); padding: clamp(22px,3.5vw,34px); }
.mhp-calc-row { margin-bottom: 18px; }
.mhp-calc-label { font-size: 10px; font-weight: 500; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(247,244,239,0.4); margin-bottom: 7px; }
.mhp-calc-select { width: 100%; background: rgba(247,244,239,0.07); border: 1px solid rgba(247,244,239,0.12); color: var(--cream); padding: 11px 13px; font-family: 'DM Sans', sans-serif; font-size: 14px; appearance: none; cursor: pointer; }
.mhp-calc-select option { background: #1C1C1C; }
.mhp-calc-result { border-top: 1px solid rgba(247,244,239,0.1); padding-top: 20px; }
.mhp-calc-result-label { font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(247,244,239,0.35); margin-bottom: 7px; }
.mhp-calc-range { font-family: 'DM Serif Display', serif; font-size: clamp(26px,2.8vw,34px); font-weight: 400; color: var(--cream); line-height: 1; margin-bottom: 8px; }
.mhp-calc-range em { color: var(--clay); font-style: normal; }
.mhp-calc-note { font-size: 11px; color: rgba(247,244,239,0.3); line-height: 1.6; }

/* ── MODIFICATIONS ── */
.mhp-mod-section { background: white; }
.mhp-mod-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; background: var(--warm-gray-lighter); border: 1px solid var(--warm-gray-lighter); }
@media (max-width: 768px) { .mhp-mod-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .mhp-mod-grid { grid-template-columns: 1fr; } }
.mhp-mod-card { background: white; padding: clamp(16px,2.8vw,26px) clamp(14px,2.2vw,22px); display: flex; gap: 13px; align-items: flex-start; transition: background 0.2s; cursor: pointer; }
.mhp-mod-card:hover { background: var(--cream); }
.mhp-mod-icon { width: 32px; height: 32px; background: var(--warm-gray-lighter); display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--clay); flex-shrink: 0; }
.mhp-mod-name { font-family: 'DM Serif Display', serif; font-size: 17px; font-weight: 400; color: var(--charcoal); margin-bottom: 3px; }
.mhp-mod-price { font-size: 11px; font-weight: 500; color: var(--clay); letter-spacing: 0.04em; margin-bottom: 5px; }
.mhp-mod-desc { font-size: 12px; color: var(--warm-gray); line-height: 1.55; }

/* ── WHAT'S INCLUDED ── */
.mhp-included-section { background: var(--cream); }
.mhp-included-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 28px; }
@media (max-width: 768px) { .mhp-included-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .mhp-included-grid { grid-template-columns: 1fr; } }
.mhp-included-icon { width: 42px; height: 42px; border: 1px solid var(--warm-gray-lighter); display: flex; align-items: center; justify-content: center; margin-bottom: 13px; color: var(--clay); }
.mhp-included-title { font-family: 'DM Serif Display', serif; font-size: 18px; font-weight: 400; color: var(--charcoal); margin-bottom: 5px; }
.mhp-included-desc { font-size: 12px; color: var(--warm-gray); line-height: 1.6; }

/* ── SOCIAL PROOF ── */
.mhp-proof-section { background: white; }
.mhp-proof-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media (max-width: 768px) { .mhp-proof-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .mhp-proof-grid { grid-template-columns: 1fr; } }
.mhp-proof-img-wrap { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; background: linear-gradient(135deg,#4A3A28,#6B5040); display: flex; align-items: center; justify-content: center; font-family: 'DM Serif Display',serif; font-style: italic; color: rgba(255,255,255,0.2); font-size: 12px; }
.mhp-proof-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.mhp-proof-info { padding: 16px 0; }
.mhp-proof-location { font-size: 10px; font-weight: 500; letter-spacing: 0.12em; text-transform: uppercase; color: var(--clay); margin-bottom: 7px; }
.mhp-proof-quote { font-family: 'DM Serif Display', serif; font-size: 16px; font-weight: 400; font-style: italic; color: var(--charcoal); line-height: 1.55; margin-bottom: 8px; }
.mhp-proof-author { font-size: 12px; color: var(--warm-gray); }

/* ── FAQ ── */
.mhp-faq-section { background: var(--cream); }
.mhp-faq-list { display: flex; flex-direction: column; gap: 1px; background: var(--warm-gray-lighter); border: 1px solid var(--warm-gray-lighter); }
.mhp-faq-item { background: var(--cream); }
.mhp-faq-q { width: 100%; background: none; border: none; padding: 20px 22px; display: flex; justify-content: space-between; align-items: center; gap: 16px; cursor: pointer; text-align: left; transition: background 0.15s; }
.mhp-faq-q:hover { background: white; }
.mhp-faq-q-text { font-family: 'DM Serif Display', serif; font-size: 18px; font-weight: 400; color: var(--charcoal); }
.mhp-faq-chevron { width: 20px; height: 20px; color: var(--clay); flex-shrink: 0; transition: transform 0.25s; }
.mhp-faq-item.open .mhp-faq-chevron { transform: rotate(180deg); }
.mhp-faq-a { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
.mhp-faq-item.open .mhp-faq-a { max-height: 400px; }
.mhp-faq-a-inner { padding: 0 22px 20px; font-size: 14px; color: var(--warm-gray); line-height: 1.75; }

/* ── RELATED PLANS ── */
.mhp-related-section { background: white; }
.mhp-related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media (max-width: 768px) { .mhp-related-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .mhp-related-grid { grid-template-columns: 1fr; } }
.mhp-related-card { background: var(--cream); border: 1px solid var(--warm-gray-lighter); overflow: hidden; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; display: block; color: inherit; }
.mhp-related-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(28,28,28,0.08); color: inherit; }
.mhp-related-img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
.mhp-related-info { padding: 16px; }
.mhp-related-style { font-size: 10px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--clay); margin-bottom: 5px; }
.mhp-related-name { font-family: 'DM Serif Display', serif; font-size: 19px; font-weight: 400; color: var(--charcoal); margin-bottom: 4px; }
.mhp-related-specs { font-size: 12px; color: var(--warm-gray); margin-bottom: 9px; }
.mhp-related-link { font-size: 11px; color: var(--clay); letter-spacing: 0.06em; text-transform: uppercase; text-decoration: none; display: inline-block; }

/* ── DESIGNER ── */
.mhp-designer-section { background: var(--charcoal); padding: 64px var(--side-pad); }
.mhp-designer-inner { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 72px 1fr auto; gap: 28px; align-items: center; }
@media (max-width: 768px) { .mhp-designer-inner { grid-template-columns: 72px 1fr; } .mhp-designer-cta { grid-column: 1 / -1; width: fit-content; } }
@media (max-width: 480px) { .mhp-designer-inner { grid-template-columns: 1fr; } .mhp-designer-avatar { display: none; } }
.mhp-designer-avatar { width: 72px; height: 72px; background: linear-gradient(135deg, var(--clay), #9A4A28); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'DM Serif Display', serif; font-size: 26px; color: white; font-weight: 400; flex-shrink: 0; }
.mhp-designer-name { font-family: 'DM Serif Display', serif; font-size: 22px; font-weight: 400; color: var(--cream); margin-bottom: 3px; }
.mhp-designer-title { font-size: 10px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--clay); margin-bottom: 8px; }
.mhp-designer-bio { font-size: 13px; color: rgba(247,244,239,0.5); line-height: 1.7; }
.mhp-designer-cta { background: transparent; color: var(--cream); border: 1px solid rgba(247,244,239,0.2); padding: 13px 20px; font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 400; letter-spacing: 0.07em; text-transform: uppercase; cursor: pointer; transition: all 0.2s; white-space: nowrap; text-decoration: none; display: inline-block; }
.mhp-designer-cta:hover { border-color: rgba(247,244,239,0.5); color: var(--cream); }

/* ── STICKY BAR ── */
.mhp-sticky-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999; background: rgba(28,28,28,0.97); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 12px var(--side-pad); display: flex; align-items: center; justify-content: space-between; gap: 16px; transform: translateY(100%); transition: transform 0.3s ease; border-top: 1px solid rgba(247,244,239,0.07); }
.mhp-sticky-bar.visible { transform: translateY(0); }
.mhp-sticky-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.mhp-sticky-name { font-family: 'DM Serif Display', serif; font-size: 18px; font-weight: 400; color: var(--cream); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mhp-sticky-stats { font-size: 11px; color: rgba(247,244,239,0.4); letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
@media (max-width: 600px) { .mhp-sticky-stats { display: none; } }
.mhp-sticky-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.mhp-sticky-price-block { text-align: right; }
.mhp-sticky-price-main { font-family: 'DM Serif Display', serif; font-size: 22px; color: var(--cream); line-height: 1; }
.mhp-sticky-price-sub { font-size: 9px; color: rgba(247,244,239,0.35); letter-spacing: 0.06em; text-transform: uppercase; }
.mhp-btn-sticky { background: var(--clay); color: white; border: none; padding: 12px 22px; font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500; letter-spacing: 0.07em; text-transform: uppercase; cursor: pointer; transition: background 0.2s; white-space: nowrap; text-decoration: none; display: inline-block; }
.mhp-btn-sticky:hover { background: var(--clay-light); color: white; }
.mhp-btn-sticky-ghost { background: transparent; color: rgba(247,244,239,0.6); border: 1px solid rgba(247,244,239,0.15); padding: 12px 16px; font-family: 'DM Sans', sans-serif; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap; text-decoration: none; display: inline-block; }
.mhp-btn-sticky-ghost:hover { border-color: rgba(247,244,239,0.4); color: var(--cream); }
@media (max-width: 480px) { .mhp-btn-sticky-ghost { display: none; } }

/* ── FADE IN ── */
.mhp-fade-in { opacity: 0; transform: translateY(16px); transition: opacity 0.6s ease, transform 0.6s ease; }
.mhp-fade-in.visible { opacity: 1; transform: translateY(0); }
.mhp-fade-d1 { transition-delay: 0.1s; }
.mhp-fade-d2 { transition-delay: 0.2s; }
</style>

<div class="mhp-plan-wrap">

<?php
/* ══════════════════════════════════════════════════
   1. HERO
══════════════════════════════════════════════════ */
$plan_style_label = $style ?: 'House Plan';
?>

<!-- HERO -->
<section class="mhp-hero" aria-label="Plan Hero">

  <!-- Gallery (75%) -->
  <div class="mhp-hero-gallery">
    <div class="mhp-hero-video-wrap">
      <?php if ( $hero_img_url ) : ?>
      <img
        id="mhpHeroMainImg"
        src="<?php echo esc_url( $hero_img_url ); ?>"
        alt="<?php echo $hero_img_alt ?: esc_attr( $plan_name ); ?>"
        loading="eager"
        width="800" height="540"
      />
      <?php else : ?>
      <img id="mhpHeroMainImg" src="" alt="<?php echo esc_attr( $plan_name ); ?>" loading="eager" width="800" height="540" style="background:#2A2018;" />
      <?php endif; ?>
      <!-- Prev/Next arrows -->
      <button class="mhp-gallery-arrow mhp-gallery-prev" id="mhpGalleryPrev" aria-label="Previous image">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="mhp-gallery-arrow mhp-gallery-next" id="mhpGalleryNext" aria-label="Next image">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
      <!-- Image counter -->
      <div class="mhp-gallery-counter" id="mhpGalleryCounter"><span id="mhpGalleryCurrent">1</span> / <span id="mhpGalleryTotal">1</span></div>
    </div>

    <?php if ( $plan_style_label ) : ?>
    <div class="mhp-hero-badge"><?php echo esc_html( $plan_style_label ); ?></div>
    <?php endif; ?>

    <div class="mhp-social-proof">
      <div class="mhp-social-proof-dot"></div>
      This plan has been purchased by dozens of homeowners
    </div>

    <!-- Thumbnails: show 6, overlay for rest opens FooBox gallery -->
    <div class="mhp-hero-thumbs">
      <?php if ( ! empty( $thumbs ) ) : ?>
        <?php $t_index = 0; $total_thumbs = count( $thumbs ); ?>
        <?php foreach ( $thumbs as $t ) : ?>
          <?php if ( $t_index < 5 || ( $t_index === 5 && $total_thumbs === 6 ) ) : ?>
          <div class="mhp-hero-thumb <?php echo $t_index === 0 ? 'active' : ''; ?>" data-full="<?php echo esc_url( $t['full'] ); ?>">
            <img src="<?php echo esc_url( $t['thumb'] ); ?>" alt="<?php echo esc_attr( $t['alt'] ); ?>" loading="lazy" />
          </div>
          <?php elseif ( $t_index === 5 && $total_thumbs > 6 ) : ?>
          <!-- 6th slot = "view all" overlay, links to first image for FooBox -->
          <div class="mhp-hero-thumb mhp-thumb-more" data-full="<?php echo esc_url( $t['full'] ); ?>">
            <img src="<?php echo esc_url( $t['thumb'] ); ?>" alt="<?php echo esc_attr( $t['alt'] ); ?>" loading="lazy" />
            <a href="<?php echo esc_url( $thumbs[0]['full'] ); ?>" class="mhp-thumb-more-overlay" data-foobox rel="mhp-gallery-<?php echo $pid; ?>">
              <span>+<?php echo $total_thumbs - 5; ?> more</span>
            </a>
            <?php foreach ( array_slice( $thumbs, 6 ) as $hidden_t ) : ?>
            <a href="<?php echo esc_url( $hidden_t['full'] ); ?>" data-foobox rel="mhp-gallery-<?php echo $pid; ?>" style="display:none;" aria-hidden="true">
              <img src="<?php echo esc_url( $hidden_t['thumb'] ); ?>" alt="<?php echo esc_attr( $hidden_t['alt'] ); ?>" />
            </a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        <?php $t_index++; endforeach; ?>
      <?php else : ?>
        <?php if ( $hero_img_url ) : ?>
        <div class="mhp-hero-thumb active" data-full="<?php echo esc_url( $hero_img_url ); ?>">
          <img src="<?php echo esc_url( $hero_img_url ); ?>" alt="<?php echo $hero_img_alt; ?>" loading="lazy" />
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Content Panel (25%) -->
  <div class="mhp-hero-content" id="mhp-buy">
    <p class="mhp-breadcrumb">
      <a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">House Plans</a>
      <?php if ( $style ) echo ' / <span>' . esc_html( $style ) . '</span>'; ?>
    </p>

    <p class="mhp-plan-number">
      <?php echo esc_html( 'Plan #' . $pid );
      if ( $style ) echo ' &middot; ' . esc_html( $style );
      ?>
    </p>

    <h1 class="mhp-hero-title"><?php echo wp_kses_post( $plan_name ); ?></h1>

    <?php if ( $plan_description ) : ?>
    <p class="mhp-hero-style-tag"><?php echo wp_strip_all_tags( wp_trim_words( $plan_description, 20 ) ); ?></p>
    <?php elseif ( $style ) : ?>
    <p class="mhp-hero-style-tag"><?php echo esc_html( $style ); ?><?php if ( $stories ) echo ' &middot; ' . esc_html( $stories ) . ' ' . _n( 'Story', 'Stories', (int) $stories ); ?></p>
    <?php endif; ?>

    <div class="mhp-hero-rating">
      <span class="mhp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
      <span class="mhp-rating-text">5.0 &nbsp;&middot;&nbsp; Verified Purchases</span>
    </div>

    <!-- Stats grid -->
    <div class="mhp-hero-stats">
      <?php if ( $bedrooms ) : ?>
      <div class="mhp-stat">
        <span class="mhp-stat-value"><?php echo esc_html( $bedrooms ); ?></span>
        <span class="mhp-stat-label">Bedrooms</span>
      </div>
      <?php endif; ?>
      <?php if ( $bathrooms ) : ?>
      <div class="mhp-stat">
        <span class="mhp-stat-value"><?php echo esc_html( $bathrooms ); ?></span>
        <span class="mhp-stat-label">Baths</span>
      </div>
      <?php endif; ?>
      <?php if ( $sqft_total_display ) : ?>
      <div class="mhp-stat">
        <span class="mhp-stat-value"><?php echo number_format( (int) $sqft_total_display ); ?></span>
        <span class="mhp-stat-label">Sq Ft</span>
      </div>
      <?php endif; ?>
      <?php if ( $garage ) : ?>
      <div class="mhp-stat">
        <span class="mhp-stat-value"><?php echo esc_html( $garage ); ?></span>
        <span class="mhp-stat-label">Car Garage</span>
      </div>
      <?php endif; ?>
    </div>

    <!-- Sq ft breakdown -->
    <div class="mhp-sqft-breakdown">
      <?php if ( $main_floor )  echo '<span class="mhp-sqft-item">Main:<strong>' . number_format( (int) $main_floor ) . '</strong></span>'; ?>
      <?php if ( $upper_floor ) echo '<span class="mhp-sqft-item">Upper:<strong>' . number_format( (int) $upper_floor ) . '</strong></span>'; ?>
      <?php if ( $lower_floor ) echo '<span class="mhp-sqft-item">Lower:<strong>' . number_format( (int) $lower_floor ) . '</strong></span>'; ?>
      <?php if ( $width )       echo '<span class="mhp-sqft-item">Width:<strong>' . esc_html( $width ) . '</strong></span>'; ?>
      <?php if ( $depth )       echo '<span class="mhp-sqft-item">Depth:<strong>' . esc_html( $depth ) . '</strong></span>'; ?>
    </div>

    <!-- Price -->
    <?php if ( $price_display ) : ?>
    <div class="mhp-hero-price-row">
      <div class="mhp-price-main">
        <span class="mhp-price-label">PDF Plans</span>
        <span class="mhp-price-amount"><?php echo esc_html( $price_display ); ?></span>
      </div>
      <div class="mhp-price-divider"></div>
      <div class="mhp-price-secondary">
        Instant download &middot; Permit-ready<br>
        <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Need CAD file? Ask Max &rarr;</a>
      </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="mhp-hero-actions">
      <a href="<?php echo $buy_href; ?>" class="mhp-btn-primary" target="_blank" rel="noopener">
        <?php if ( $price_display ) : ?>
          <span class="mhp-btn-price"><?php echo esc_html( $price_display ); ?></span>
          <span class="mhp-btn-label">Buy PDF Plans &rarr;</span>
        <?php else : ?>
          Buy PDF Plans &rarr;
        <?php endif; ?>
      </a>
      <a href="#mhp-faq" class="mhp-btn-secondary">Ask a Question</a>
    </div>

    <!-- Trust pills -->
    <div class="mhp-hero-trust">
      <div class="mhp-trust-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Instant PDF delivery
      </div>
      <div class="mhp-trust-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Permit-ready drawings
      </div>
      <div class="mhp-trust-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        25+ years experience
      </div>
      <div class="mhp-trust-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Talk to Max directly
      </div>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   2. TRUST STRIP
══════════════════════════════════════════════════ */
?>

<!-- TRUST STRIP -->
<div class="mhp-trust-strip">
  <?php foreach ( $trust_pills as $pill ) : ?>
  <div class="mhp-ts-item">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <?php echo esc_html( $pill ); ?>
  </div>
  <?php endforeach; ?>
</div>

<?php
/* ══════════════════════════════════════════════════
   3. FLOOR PLANS
══════════════════════════════════════════════════ */
?>

<!-- FLOOR PLANS -->
<section class="mhp-section mhp-floor-section" aria-label="Floor Plans">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">Every Room, Every Detail</span>
      </div>
      <h2 class="mhp-section-title">Explore the <em>Floor Plans</em></h2>
    </div>

    <?php if ( $floor_plans_acf ) : ?>
      <!-- ACF floor_plans wysiwyg content -->
      <div class="mhp-floor-plans-acf mhp-fade-in mhp-fade-d1">
        <?php echo wp_kses_post( $floor_plans_acf ); ?>
      </div>
    <?php else : ?>
      <!-- Fallback: tab layout with default floor levels -->
      <nav class="mhp-floor-nav mhp-fade-in mhp-fade-d1" aria-label="Floor level navigation">
        <?php if ( $main_floor )  echo '<button class="mhp-floor-btn active" onclick="mhpShowFloor(\'main\',this)">Main' . ( $main_floor ? ' &middot; ' . number_format( (int) $main_floor ) . ' sq ft' : '' ) . '</button>'; ?>
        <?php if ( $upper_floor ) echo '<button class="mhp-floor-btn" onclick="mhpShowFloor(\'upper\',this)">Upper' . ( $upper_floor ? ' &middot; ' . number_format( (int) $upper_floor ) . ' sq ft' : '' ) . '</button>'; ?>
        <?php if ( $lower_floor ) echo '<button class="mhp-floor-btn" onclick="mhpShowFloor(\'lower\',this)">Lower Level' . ( $lower_floor ? ' &middot; ' . number_format( (int) $lower_floor ) . ' sq ft' : '' ) . '</button>'; ?>
        <?php if ( ! $main_floor && ! $upper_floor && ! $lower_floor ) echo '<button class="mhp-floor-btn active">Floor Plan</button>'; ?>
      </nav>

      <?php if ( $main_floor ) : ?>
      <div id="mhp-floor-main" class="mhp-floor-content active mhp-fade-in mhp-fade-d2">
        <div class="mhp-floor-img-wrap">
          <?php echo get_the_post_thumbnail( $pid, 'large', array( 'alt' => esc_attr( $plan_name ) . ' main floor plan' ) ); ?>
        </div>
        <div class="mhp-floor-rooms">
          <div class="mhp-room-row"><div><div class="mhp-room-name">Main Level</div><div class="mhp-room-feature"><?php echo esc_html( number_format( (int) $main_floor ) ); ?> sq ft heated</div></div><div class="mhp-room-dim">Main</div></div>
          <?php if ( $ceiling ) : ?><div class="mhp-room-row"><div><div class="mhp-room-name">Ceiling Height</div><div class="mhp-room-feature"><?php echo esc_html( $ceiling ); ?></div></div><div class="mhp-room-dim">Main</div></div><?php endif; ?>
          <?php if ( $outdoor ) : ?><div class="mhp-room-row"><div><div class="mhp-room-name"><?php echo esc_html( $outdoor ); ?></div><div class="mhp-room-feature">Outdoor living</div></div><div class="mhp-room-dim">Outdoor</div></div><?php endif; ?>
          <?php if ( $garage )  : ?><div class="mhp-room-row"><div><div class="mhp-room-name"><?php echo esc_html( $garage ); ?>-Car Garage</div><div class="mhp-room-feature">Attached</div></div><div class="mhp-room-dim">Main</div></div><?php endif; ?>
          <?php if ( $additional_rooms ) : ?><div class="mhp-room-row"><div><div class="mhp-room-name"><?php echo esc_html( $additional_rooms ); ?></div><div class="mhp-room-feature">Additional space</div></div><div class="mhp-room-dim">Main</div></div><?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ( $upper_floor ) : ?>
      <div id="mhp-floor-upper" class="mhp-floor-content">
        <div class="mhp-floor-img-wrap">
          <p style="padding:20px; color:var(--warm-gray); font-size:14px;">Upper floor plan image — add via Media Library</p>
        </div>
        <div class="mhp-floor-rooms">
          <div class="mhp-room-row"><div><div class="mhp-room-name">Upper Level</div><div class="mhp-room-feature"><?php echo esc_html( number_format( (int) $upper_floor ) ); ?> sq ft heated</div></div><div class="mhp-room-dim">Upper</div></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ( $lower_floor ) : ?>
      <div id="mhp-floor-lower" class="mhp-floor-content">
        <div class="mhp-floor-img-wrap">
          <p style="padding:20px; color:var(--warm-gray); font-size:14px;">Lower level plan image — add via Media Library</p>
        </div>
        <div class="mhp-floor-rooms">
          <div class="mhp-room-row"><div><div class="mhp-room-name">Lower Level</div><div class="mhp-room-feature"><?php echo esc_html( number_format( (int) $lower_floor ) ); ?> sq ft</div></div><div class="mhp-room-dim">Lower</div></div>
          <div class="mhp-room-row"><div><div class="mhp-room-name">Storage</div><div class="mhp-room-feature">Leave unfinished to save on build cost</div></div><div class="mhp-room-dim">Lower</div></div>
        </div>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   4. PLAN FEATURES (6 cards)
══════════════════════════════════════════════════ */
// Build dynamic features from ACF fields
$feature_cards = array();

if ( $outdoor ) {
    $feature_cards[] = array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>',
        'title' => esc_html( $outdoor ),
        'desc' => 'Outdoor living designed for the way you actually spend your time — morning coffee, evening gatherings, and everything in between.',
    );
}
if ( $ceiling ) {
    $feature_cards[] = array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
        'title' => esc_html( $ceiling ) . ' Ceilings',
        'desc' => 'Generous ceiling heights throughout the main level create a sense of openness and allow natural light to flood every room.',
    );
}
if ( $lower_floor ) {
    $feature_cards[] = array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 22V8l10-6 10 6v14"/><path d="M12 22V12"/><path d="M6 12h12"/></svg>',
        'title' => 'Lower Level / Basement',
        'desc' => 'Finish the lower level for recreation, guests, and bonus living — or leave it unfinished to reduce initial build cost. Plans included either way.',
    );
}
if ( $additional_rooms ) {
    $feature_cards[] = array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>',
        'title' => esc_html( $additional_rooms ),
        'desc' => 'Flexible spaces that adapt to your lifestyle — home office, media room, playroom, or guest suite. It grows with your family.',
    );
}
if ( $lot_style ) {
    $feature_cards[] = array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
        'title' => esc_html( $lot_style ) . ' Lot Ready',
        'desc' => 'Designed for ' . esc_html( strtolower( $lot_style ) ) . ' lots — foundation and grading details are fully specified in the plan set.',
    );
}
// Always include Permit-Ready card
$feature_cards[] = array(
    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
    'title' => 'Permit-Ready Drawings',
    'desc' => 'Elevations, floor plans, foundation, and roof plan at 1/4&quot; scale — everything your builder and building department needs to break ground.',
);

// Pad to 6 with generic cards if needed
$generic_extras = array(
    array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>',
        'title' => 'Open Living Concept',
        'desc' => 'Kitchen, dining, and great room flow seamlessly. Built for how families actually gather and entertain.',
    ),
    array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'title' => 'Attached Garage',
        'desc' => 'Integrated garage with mud room access and built-in storage — practical living from day one.',
    ),
    array(
        'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'title' => 'Built to Last',
        'desc' => 'Designed by a builder with 25+ years of hands-on construction experience — details that work in the real world.',
    ),
);

while ( count( $feature_cards ) < 6 && ! empty( $generic_extras ) ) {
    $feature_cards[] = array_shift( $generic_extras );
}
$feature_cards = array_slice( $feature_cards, 0, 6 );
?>

<!-- PLAN FEATURES -->
<section class="mhp-section mhp-features-section" aria-label="Plan Features">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">What Makes It Special</span>
      </div>
      <h2 class="mhp-section-title">Designed for <em>How You Live</em></h2>
    </div>
    <div class="mhp-features-grid mhp-fade-in mhp-fade-d1">
      <?php foreach ( $feature_cards as $fc ) : ?>
      <div class="mhp-feature-card">
        <div class="mhp-feature-icon"><?php echo $fc['icon']; ?></div>
        <div class="mhp-feature-title"><?php echo $fc['title']; ?></div>
        <div class="mhp-feature-desc"><?php echo $fc['desc']; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   5. BUILD COST ESTIMATOR
══════════════════════════════════════════════════ */
$sqft_for_calc  = (int) ( $sqft_total_display ?: 2000 );
$basement_sqft  = (int) ( $lower_floor ?: 0 );
?>

<!-- BUILD COST ESTIMATOR -->
<section class="mhp-cost-section" aria-label="Build Cost Estimator">
  <div class="mhp-cost-inner">
    <div class="mhp-cost-header mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">Know Before You Build</span>
      </div>
      <h2 class="mhp-section-title">Build Cost <em>Estimator</em></h2>
      <p class="mhp-cost-desc">Real regional cost ranges based on current contractor rates — not generic national averages. Adjust for your region, finish level, and whether you're finishing the basement.</p>
      <div class="mhp-cost-features">
        <div class="mhp-cost-feat">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Based on <?php echo number_format( $sqft_for_calc ); ?> heated sq ft
        </div>
        <div class="mhp-cost-feat">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Excludes land, site prep &amp; utilities
        </div>
        <?php if ( $basement_sqft ) : ?>
        <div class="mhp-cost-feat">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Basement finish adds $60–$90/sq ft
        </div>
        <?php endif; ?>
        <div class="mhp-cost-feat">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Get firm quotes from local builders
        </div>
      </div>
    </div>

    <div class="mhp-calc-box mhp-fade-in mhp-fade-d1">
      <div class="mhp-calc-row">
        <div class="mhp-calc-label">Your Region</div>
        <select class="mhp-calc-select" id="mhpStateSelect" onchange="mhpUpdateCost()">
          <option value="se">Georgia / Southeast</option>
          <option value="sw">Texas / Southwest</option>
          <option value="ne">Northeast</option>
          <option value="mw">Midwest</option>
          <option value="pnw">Pacific Northwest</option>
          <option value="ca">California</option>
        </select>
      </div>
      <div class="mhp-calc-row">
        <div class="mhp-calc-label">Finish Level</div>
        <select class="mhp-calc-select" id="mhpFinishSelect" onchange="mhpUpdateCost()">
          <option value="standard">Standard Finishes</option>
          <option value="mid">Mid-Range Finishes</option>
          <option value="high">High-End Finishes</option>
        </select>
      </div>
      <?php if ( $basement_sqft ) : ?>
      <div class="mhp-calc-row">
        <div class="mhp-calc-label">Basement</div>
        <select class="mhp-calc-select" id="mhpBasementSelect" onchange="mhpUpdateCost()">
          <option value="no">Leave Unfinished (Save $)</option>
          <option value="yes">Finish the Basement</option>
        </select>
      </div>
      <?php endif; ?>
      <div class="mhp-calc-result">
        <div class="mhp-calc-result-label">Estimated Build Cost</div>
        <div class="mhp-calc-range" id="mhpCostRange"><em>Calculating&hellip;</em></div>
        <div class="mhp-calc-note">Estimates carry &plusmn;15% variance. Contact local builders for firm quotes before budgeting.</div>
      </div>
    </div>
  </div>
</section>
<script>
(function(){
  var MHP_SQFT = <?php echo (int) $sqft_for_calc; ?>;
  var MHP_BSQFT = <?php echo (int) $basement_sqft; ?>;
  var mhpRates = {
    se:{standard:[180,245],mid:[235,310],high:[310,420]},
    sw:{standard:[170,235],mid:[220,295],high:[290,395]},
    ne:{standard:[245,340],mid:[315,430],high:[415,560]},
    mw:{standard:[165,225],mid:[210,280],high:[275,375]},
    pnw:{standard:[225,310],mid:[290,395],high:[385,520]},
    ca:{standard:[285,390],mid:[370,500],high:[490,660]}
  };
  window.mhpUpdateCost = function() {
    var region = document.getElementById('mhpStateSelect').value;
    var finish = document.getElementById('mhpFinishSelect').value;
    var r = mhpRates[region][finish];
    var low = MHP_SQFT * r[0], high = MHP_SQFT * r[1];
    var bSel = document.getElementById('mhpBasementSelect');
    if (bSel && bSel.value === 'yes') { low += MHP_BSQFT * 60; high += MHP_BSQFT * 90; }
    function fmt(n) { return '$' + (Math.round(n/1000)*1000).toLocaleString(); }
    document.getElementById('mhpCostRange').innerHTML = '<em>' + fmt(low) + '</em> &ndash; ' + fmt(high);
  };
  document.addEventListener('DOMContentLoaded', function(){ mhpUpdateCost(); });
})();
</script>

<?php
/* ══════════════════════════════════════════════════
   6. POPULAR MODIFICATIONS
══════════════════════════════════════════════════ */
?>

<!-- POPULAR MODIFICATIONS -->
<section class="mhp-section mhp-mod-section" aria-label="Popular Modifications" id="mhp-cad">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">Make It Yours</span>
      </div>
      <h2 class="mhp-section-title">Popular <em>Modifications</em></h2>
    </div>
    <div class="mhp-mod-grid mhp-fade-in mhp-fade-d1">
      <div class="mhp-mod-card">
        <div class="mhp-mod-icon">&#8862;</div>
        <div>
          <div class="mhp-mod-name">3-Car Garage</div>
          <div class="mhp-mod-price">From $450</div>
          <div class="mhp-mod-desc">Add a third bay — popular with boat owners, lake-house builders, and multi-vehicle families.</div>
        </div>
      </div>
      <div class="mhp-mod-card">
        <div class="mhp-mod-icon">&#8635;</div>
        <div>
          <div class="mhp-mod-name">Mirror / Reverse Plan</div>
          <div class="mhp-mod-price">From $150</div>
          <div class="mhp-mod-desc">Flip the plan to orient the garage or entry to match your lot and street configuration.</div>
        </div>
      </div>
      <div class="mhp-mod-card">
        <div class="mhp-mod-icon">&#8593;</div>
        <div>
          <div class="mhp-mod-name">Finish the Basement</div>
          <div class="mhp-mod-price">From $550</div>
          <div class="mhp-mod-desc">Full drawings for rec room, bunk room, guest suite, and bath on the lower level.</div>
        </div>
      </div>
      <div class="mhp-mod-card">
        <div class="mhp-mod-icon">&#8962;</div>
        <div>
          <div class="mhp-mod-name">Slab Foundation</div>
          <div class="mhp-mod-price">From $300</div>
          <div class="mhp-mod-desc">Convert to slab for flat lots — removes the basement and reduces initial construction cost.</div>
        </div>
      </div>
      <div class="mhp-mod-card">
        <div class="mhp-mod-icon">&#9889;</div>
        <div>
          <div class="mhp-mod-name">Electrical Plans</div>
          <div class="mhp-mod-price">$350</div>
          <div class="mhp-mod-desc">Custom electrical drawings with switch, outlet, and fixture locations for your lifestyle.</div>
        </div>
      </div>
      <div class="mhp-mod-card">
        <div class="mhp-mod-icon">&#9633;</div>
        <div>
          <div class="mhp-mod-name">CAD File</div>
          <div class="mhp-mod-price">$1,500</div>
          <div class="mhp-mod-desc">Full editable CAD file for builders or architects who want to make their own modifications.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   7. WHAT'S INCLUDED
══════════════════════════════════════════════════ */
?>

<!-- WHAT'S INCLUDED -->
<section class="mhp-section mhp-included-section" aria-label="What's Included">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">Complete Plan Set</span>
      </div>
      <h2 class="mhp-section-title">Everything Your Builder <em>Needs</em></h2>
    </div>
    <div class="mhp-included-grid mhp-fade-in mhp-fade-d1">
      <div>
        <div class="mhp-included-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="12,2 2,7 12,12 22,7"/><polyline points="2,17 12,22 22,17"/><polyline points="2,12 12,17 22,12"/></svg>
        </div>
        <div class="mhp-included-title">All Elevations</div>
        <div class="mhp-included-desc">Front, side, and rear at 1/4&quot; scale with full dimensions, notes, and material callouts.</div>
      </div>
      <div>
        <div class="mhp-included-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        </div>
        <div class="mhp-included-title">All Floor Plans</div>
        <div class="mhp-included-desc">Complete dimensioned plans for all levels — every room fully detailed and dimensioned.</div>
      </div>
      <div>
        <div class="mhp-included-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="mhp-included-title">Foundation Plan</div>
        <div class="mhp-included-desc">Detailed foundation layout ready to submit for permit — slab, crawl, or basement.</div>
      </div>
      <div>
        <div class="mhp-included-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="12,2 19.07,7.5 16.18,15.5 7.82,15.5 4.93,7.5"/></svg>
        </div>
        <div class="mhp-included-title">Roof Plan</div>
        <div class="mhp-included-desc">Complete framing plan with ridges, valleys, and pitch notations for all roof sections.</div>
      </div>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   8. SOCIAL PROOF / HOMES BUILT
══════════════════════════════════════════════════ */
?>

<!-- SOCIAL PROOF -->
<section class="mhp-section mhp-proof-section" id="mhp-reviews" aria-label="Customer Reviews">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">Built &amp; Loved</span>
      </div>
      <h2 class="mhp-section-title">Homes Built From <em>This Plan</em></h2>
    </div>
    <div class="mhp-proof-grid mhp-fade-in mhp-fade-d1">
      <div>
        <div class="mhp-proof-img-wrap" style="aspect-ratio:4/3;background:linear-gradient(135deg,#4A3A28,#6B5040);">Customer photo — add yours</div>
        <div class="mhp-proof-info">
          <p class="mhp-proof-location">North Carolina &middot; 2024</p>
          <p class="mhp-proof-quote">"We built on a sloping lot and the plans were perfect. Max answered every single question personally."</p>
          <p class="mhp-proof-author">— The Caldwell Family &nbsp;&#9733;&#9733;&#9733;&#9733;&#9733;</p>
        </div>
      </div>
      <div>
        <div class="mhp-proof-img-wrap" style="aspect-ratio:4/3;background:linear-gradient(135deg,#3A4A38,#506048);">Customer photo — add yours</div>
        <div class="mhp-proof-info">
          <p class="mhp-proof-location">Tennessee &middot; 2023</p>
          <p class="mhp-proof-quote">"We added a modification and it was perfect for our lot. Max turned it around faster than I expected."</p>
          <p class="mhp-proof-author">— Sarah &amp; James Whitfield &nbsp;&#9733;&#9733;&#9733;&#9733;&#9733;</p>
        </div>
      </div>
      <div>
        <div class="mhp-proof-img-wrap" style="aspect-ratio:4/3;background:linear-gradient(135deg,#3A3848,#505060);">Customer photo — add yours</div>
        <div class="mhp-proof-info">
          <p class="mhp-proof-location">Georgia &middot; 2024</p>
          <p class="mhp-proof-quote">"My builder said these were some of the cleanest plan drawings he'd ever worked from. Worth every penny."</p>
          <p class="mhp-proof-author">— Robert Callahan, Builder &nbsp;&#9733;&#9733;&#9733;&#9733;&#9733;</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   9. FAQ / BEFORE YOU BUY
══════════════════════════════════════════════════ */
?>

<!-- FAQ -->
<section class="mhp-section mhp-faq-section" id="mhp-faq" aria-label="Frequently Asked Questions">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">Common Questions</span>
      </div>
      <h2 class="mhp-section-title">Before You <em>Buy</em></h2>
    </div>
    <div class="mhp-faq-list mhp-fade-in mhp-fade-d1">
      <?php foreach ( $faqs as $faq ) : ?>
      <div class="mhp-faq-item">
        <button class="mhp-faq-q" aria-expanded="false">
          <span class="mhp-faq-q-text"><?php echo esc_html( $faq['q'] ); ?></span>
          <svg class="mhp-faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="mhp-faq-a" role="region">
          <div class="mhp-faq-a-inner"><?php echo wp_kses_post( $faq['a'] ); ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   10. RELATED PLANS
══════════════════════════════════════════════════ */
?>

<!-- RELATED PLANS -->
<section class="mhp-section mhp-related-section" aria-label="Related House Plans">
  <div class="mhp-section-inner">
    <div class="mhp-fade-in">
      <div class="mhp-section-eyebrow">
        <div class="mhp-eyebrow-line"></div>
        <span class="mhp-eyebrow-text">You Might Also Like</span>
      </div>
      <h2 class="mhp-section-title">More <em>House Plans</em></h2>
    </div>
    <div class="mhp-related-grid mhp-fade-in mhp-fade-d1">
      <?php if ( ! empty( $related_plans ) ) : ?>
        <?php $count = 0; foreach ( $related_plans as $rel_post ) : if ( $count >= 3 ) break; ?>
          <?php
          $rel_id    = $rel_post->ID;
          $rel_title = get_the_title( $rel_id );
          $rel_url   = get_permalink( $rel_id );
          // Try multiple sizes - medium_large may not exist for older uploads
          $rel_img = get_the_post_thumbnail_url( $rel_id, 'large' );
          if ( ! $rel_img ) $rel_img = get_the_post_thumbnail_url( $rel_id, 'medium_large' );
          if ( ! $rel_img ) $rel_img = get_the_post_thumbnail_url( $rel_id, 'full' );
          $rel_style = get_field( 'style', $rel_id );
          $rel_bed   = get_field( 'bedrooms', $rel_id );
          $rel_bath  = get_field( 'bathrooms', $rel_id );
          $rel_sqft  = get_field( 'total_living_area', $rel_id );
          $rel_price = get_field( 'price', $rel_id );
          ?>
          <a class="mhp-related-card" href="<?php echo esc_url( $rel_url ); ?>">
            <?php if ( $rel_img ) : ?>
            <img class="mhp-related-img" src="<?php echo esc_url( $rel_img ); ?>" alt="<?php echo esc_attr( $rel_title ); ?>" loading="lazy" />
            <?php else : ?>
            <div class="mhp-related-img" style="background:var(--warm-gray-lighter);display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--warm-gray);">No image</div>
            <?php endif; ?>
            <div class="mhp-related-info">
              <?php if ( $rel_style ) echo '<p class="mhp-related-style">' . esc_html( $rel_style ) . '</p>'; ?>
              <p class="mhp-related-name"><?php echo esc_html( $rel_title ); ?></p>
              <p class="mhp-related-specs">
                <?php
                $specs = array();
                if ( $rel_bed )  $specs[] = $rel_bed . ' bed';
                if ( $rel_bath ) $specs[] = $rel_bath . ' bath';
                if ( $rel_sqft ) $specs[] = number_format( (int) $rel_sqft ) . ' sq ft';
                echo esc_html( implode( ' &middot; ', $specs ) );
                ?>
              </p>
              <?php if ( $rel_price ) echo '<p class="mhp-related-specs" style="font-weight:500;color:var(--charcoal);">$' . number_format( (float) $rel_price, 0, '.', ',' ) . '</p>'; ?>
              <span class="mhp-related-link">View Plan &rarr;</span>
            </div>
          </a>
        <?php $count++; endforeach; ?>
      <?php else : ?>
        <!-- Fallback static related plans -->
        <a class="mhp-related-card" href="<?php echo esc_url( home_url( '/home-plans/dog-trot-house-plan/' ) ); ?>">
          <div class="mhp-related-img" style="background:linear-gradient(135deg,#4A3A28,#6B5040);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;font-size:12px;color:rgba(255,255,255,0.3);">View Plan</div>
          <div class="mhp-related-info">
            <p class="mhp-related-style">Cottage &middot; Country</p>
            <p class="mhp-related-name">Dog Trot House Plan</p>
            <p class="mhp-related-specs">3 bed &middot; 2 bath &middot; Open Breezeway</p>
            <span class="mhp-related-link">View Plan &rarr;</span>
          </div>
        </a>
        <a class="mhp-related-card" href="<?php echo esc_url( home_url( '/home-plans/craftsman-cottage-house-plan-with-porches/' ) ); ?>">
          <div class="mhp-related-img" style="background:linear-gradient(135deg,#3A4A38,#506048);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;font-size:12px;color:rgba(255,255,255,0.3);">View Plan</div>
          <div class="mhp-related-info">
            <p class="mhp-related-style">Craftsman &middot; Cottage</p>
            <p class="mhp-related-name">Craftsman Cottage</p>
            <p class="mhp-related-specs">3 bed &middot; Multiple porches</p>
            <span class="mhp-related-link">View Plan &rarr;</span>
          </div>
        </a>
        <a class="mhp-related-card" href="<?php echo esc_url( home_url( '/home-plans/cottage-house-plan-with-porches/' ) ); ?>">
          <div class="mhp-related-img" style="background:linear-gradient(135deg,#3A3848,#505060);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;font-size:12px;color:rgba(255,255,255,0.3);">View Plan</div>
          <div class="mhp-related-info">
            <p class="mhp-related-style">Cottage &middot; One Story</p>
            <p class="mhp-related-name">Cottage with Porches</p>
            <p class="mhp-related-specs">One-story &middot; Optional garage</p>
            <span class="mhp-related-link">View Plan &rarr;</span>
          </div>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   11. DESIGNER SECTION
══════════════════════════════════════════════════ */
?>

<!-- DESIGNER -->
<section class="mhp-designer-section" aria-label="About the Designer">
  <div class="mhp-designer-inner">
    <div class="mhp-designer-avatar">M</div>
    <div>
      <div class="mhp-designer-title">Designed By</div>
      <div class="mhp-designer-name">Max Fulbright &mdash; Principal Designer</div>
      <p class="mhp-designer-bio">With 25+ years of hands-on building experience, I design every plan myself and work directly with you on modifications. No call centers, no middlemen. You'll talk to the person who drew the plans — and that's how I think it should be.</p>
    </div>
    <a class="mhp-designer-cta" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Ask Max a Question</a>
  </div>
</section>

<?php
/* ══════════════════════════════════════════════════
   12. STICKY BOTTOM BAR
══════════════════════════════════════════════════ */
$sticky_specs = array();
if ( $bedrooms )          $sticky_specs[] = $bedrooms . ' bed';
if ( $bathrooms )         $sticky_specs[] = $bathrooms . ' bath';
if ( $sqft_total_display ) $sticky_specs[] = number_format( (int) $sqft_total_display ) . ' sq ft';
if ( $outdoor )           $sticky_specs[] = $outdoor;
?>

<!-- STICKY BAR -->
<div class="mhp-sticky-bar" id="mhpStickyBar" aria-label="Sticky purchase bar">
  <div class="mhp-sticky-info">
    <span class="mhp-sticky-name"><?php echo esc_html( $plan_name ); ?></span>
    <?php if ( ! empty( $sticky_specs ) ) : ?>
    <span class="mhp-sticky-stats"><?php echo esc_html( implode( ' &middot; ', $sticky_specs ) ); ?></span>
    <?php endif; ?>
  </div>
  <div class="mhp-sticky-right">
    <?php if ( $price_display ) : ?>
    <div class="mhp-sticky-price-block">
      <div class="mhp-sticky-price-main"><?php echo esc_html( $price_display ); ?></div>
      <div class="mhp-sticky-price-sub">PDF Plans</div>
    </div>
    <?php endif; ?>
    <a class="mhp-btn-sticky-ghost" href="#mhp-faq">Questions?</a>
    <a class="mhp-btn-sticky" href="<?php echo $buy_href; ?>">Buy This Plan</a>
  </div>
</div>

</div><!-- .mhp-plan-wrap -->
    <?php
}

genesis();
