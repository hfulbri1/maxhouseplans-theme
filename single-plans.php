<?php
/**
 * Template Name: Plan Page
 *
 * MaxHousePlans — Single Plan Template (v2)
 * Design reference: carolina-farmhouse-v2.html
 * Colors: Charcoal #1C1C1C · Clay #C4714A · Cream #F7F4EF
 * Last updated: 2026-03-18 — full rebuild by Vegeta
 */

// ---- Layout: no sidebar, full width ----
add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', function () { return 'full-width-content'; } );

// ---- Inject plan-page CSS via wp_head ----
add_action( 'wp_head', 'mhp_plan_v2_styles' );
function mhp_plan_v2_styles() {
	?>
<style id="mhp-plan-page-styles">
/* ============================================================
   MHP PLAN PAGE v2 — carolina-farmhouse design system
   Charcoal #1C1C1C · Clay #C4714A · Cream #F7F4EF
   Mobile-first: base = mobile, min-width for desktop
   ============================================================ */
:root {
	--mhp-cream:       #F7F4EF;
	--mhp-charcoal:    #1C1C1C;
	--mhp-clay:        #C4714A;
	--mhp-clay-light:  #D4875F;
	--mhp-sage:        #7A8C6E;
	--mhp-warm-gray:   #8A8278;
	--mhp-warm-gray-l: #C5BFB8;
	--mhp-warm-gray-ll:#E8E3DC;
	--mhp-white:       #FFFFFF;
	--mhp-side-pad:    clamp(16px, 5vw, 64px);
	--mhp-section-pad: clamp(52px, 7vw, 96px);
}

/* --- Google Fonts (plan page only) --- */
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap');

/* ---- HERO ---- */
.mhp-hero {
	display: grid;
	grid-template-columns: 1fr;
	min-height: 100svh;
	padding-top: 61px; /* fixed nav offset */
	background: var(--mhp-cream);
}
@media (min-width: 900px) {
	.mhp-hero {
		grid-template-columns: 75% 25%;
		min-height: 100svh;
	}
}

/* ---- Gallery (left) ---- */
.mhp-gallery {
	position: relative;
	background: #1A1410;
	overflow: hidden;
	display: flex;
	flex-direction: column;
	min-height: 56vw;
}
@media (min-width: 900px) {
	.mhp-gallery {
		min-height: auto;
		height: calc(100svh - 61px);
		position: sticky;
		top: 61px;
	}
}

.mhp-gallery-main {
	flex: 1;
	overflow: hidden;
	position: relative;
}
.mhp-gallery-main img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	display: block;
	transition: opacity 0.3s ease;
}
.mhp-gallery-placeholder {
	width: 100%;
	height: 100%;
	min-height: 260px;
	background: linear-gradient(135deg, #2A1E14, #4A3828);
	display: flex;
	align-items: center;
	justify-content: center;
	font-family: 'Cormorant Garamond', serif;
	font-style: italic;
	color: rgba(247,244,239,0.3);
	font-size: 15px;
}

.mhp-gallery-thumbs {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 2px;
	height: 80px;
	background: #111;
	padding: 2px;
	flex-shrink: 0;
}
@media (min-width: 900px) {
	.mhp-gallery-thumbs {
		grid-template-columns: repeat(6, 1fr);
		height: 100px;
	}
}

.mhp-gallery-thumb {
	overflow: hidden;
	cursor: pointer;
	position: relative;
}
.mhp-gallery-thumb img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	display: block;
	opacity: 0.55;
	transition: opacity 0.2s, transform 0.3s;
}
.mhp-gallery-thumb:hover img,
.mhp-gallery-thumb.active img {
	opacity: 1;
	transform: scale(1.05);
}
.mhp-gallery-thumb.active::after {
	content: '';
	position: absolute;
	bottom: 0; left: 0; right: 0;
	height: 2px;
	background: var(--mhp-clay);
}

/* ---- Plan Info Panel (right) ---- */
.mhp-plan-info {
	display: flex;
	flex-direction: column;
	justify-content: center;
	padding: clamp(28px, 4vw, 52px) clamp(20px, 4vw, 44px);
	background: var(--mhp-cream);
}
@media (min-width: 900px) {
	.mhp-plan-info {
		overflow-y: auto;
		max-height: calc(100svh - 61px);
	}
}

.mhp-plan-breadcrumb {
	font-size: 11px;
	color: var(--mhp-warm-gray);
	letter-spacing: 0.06em;
	text-transform: uppercase;
	margin-bottom: 18px;
}
.mhp-plan-breadcrumb a {
	color: var(--mhp-clay);
	text-decoration: none;
}
.mhp-plan-breadcrumb a:hover { text-decoration: underline; }

.mhp-plan-eyebrow {
	font-size: 11px;
	font-weight: 500;
	letter-spacing: 0.14em;
	text-transform: uppercase;
	color: var(--mhp-clay);
	margin-bottom: 10px;
	display: flex;
	align-items: center;
	gap: 10px;
}
.mhp-plan-eyebrow::before {
	content: '';
	width: 24px;
	height: 1px;
	background: var(--mhp-clay);
	flex-shrink: 0;
}

.mhp-plan-title {
	font-family: 'Cormorant Garamond', serif;
	font-size: clamp(36px, 5vw, 58px);
	font-weight: 300;
	line-height: 1.0;
	color: var(--mhp-charcoal);
	margin-bottom: 16px;
	margin-top: 0;
}
.mhp-plan-title em {
	font-style: italic;
	color: var(--mhp-clay);
}

.mhp-plan-subtitle {
	font-size: 13px;
	color: var(--mhp-warm-gray);
	line-height: 1.5;
	margin-bottom: 20px;
}

/* Spec grid */
.mhp-specs-grid {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 1px;
	background: var(--mhp-warm-gray-ll);
	border: 1px solid var(--mhp-warm-gray-ll);
	margin-bottom: 14px;
}
.mhp-spec {
	background: var(--mhp-cream);
	padding: 14px 8px;
	text-align: center;
}
.mhp-spec-value {
	font-family: 'Cormorant Garamond', serif;
	font-size: 22px;
	font-weight: 500;
	color: var(--mhp-charcoal);
	display: block;
	line-height: 1;
	margin-bottom: 3px;
}
.mhp-spec-label {
	font-size: 9px;
	font-weight: 500;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	color: var(--mhp-warm-gray);
}

/* Sqft breakdown */
.mhp-sqft-breakdown {
	display: flex;
	gap: 14px;
	flex-wrap: wrap;
	padding: 10px 0;
	border-top: 1px solid var(--mhp-warm-gray-ll);
	border-bottom: 1px solid var(--mhp-warm-gray-ll);
	margin-bottom: 20px;
	font-size: 11px;
	color: var(--mhp-warm-gray);
}
.mhp-sqft-item strong {
	color: var(--mhp-charcoal);
	font-weight: 500;
	margin-left: 3px;
}

/* Price */
.mhp-price-row {
	display: flex;
	align-items: center;
	gap: 16px;
	margin-bottom: 14px;
}
.mhp-price-label {
	font-size: 10px;
	font-weight: 500;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	color: var(--mhp-warm-gray);
	display: block;
	margin-bottom: 2px;
}
.mhp-price-amount {
	font-family: 'Cormorant Garamond', serif;
	font-size: 40px;
	font-weight: 400;
	color: var(--mhp-charcoal);
	line-height: 1;
	display: block;
}
.mhp-price-divider {
	width: 1px;
	height: 40px;
	background: var(--mhp-warm-gray-ll);
	flex-shrink: 0;
}
.mhp-price-secondary {
	font-size: 12px;
	color: var(--mhp-warm-gray);
	line-height: 1.6;
}

/* BUY button */
.mhp-btn-buy {
	display: block;
	width: 100%;
	background: var(--mhp-clay);
	color: #fff;
	border: none;
	padding: 15px 20px;
	font-family: 'DM Sans', sans-serif;
	font-size: 13px;
	font-weight: 500;
	letter-spacing: 0.07em;
	text-transform: uppercase;
	cursor: pointer;
	text-align: center;
	text-decoration: none;
	transition: background 0.2s, transform 0.15s;
	margin-bottom: 10px;
	line-height: 1.4;
}
.mhp-btn-buy:hover {
	background: var(--mhp-clay-light);
	transform: translateY(-1px);
	color: #fff;
	text-decoration: none;
}

.mhp-payment-note {
	font-size: 11px;
	color: var(--mhp-warm-gray);
	display: flex;
	align-items: center;
	gap: 5px;
	margin-bottom: 16px;
}

/* What's Included */
.mhp-whats-included {
	border-top: 1px solid var(--mhp-warm-gray-ll);
	padding-top: 16px;
}
.mhp-included-heading {
	font-size: 10px;
	font-weight: 500;
	letter-spacing: 0.12em;
	text-transform: uppercase;
	color: var(--mhp-warm-gray);
	margin-bottom: 10px;
}
.mhp-included-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	gap: 7px;
}
.mhp-included-list li {
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 12px;
	color: var(--mhp-warm-gray);
}
.mhp-included-list li svg { color: var(--mhp-sage); flex-shrink: 0; }

/* ---- STICKY BAR ---- */
.mhp-sticky-bar {
	position: fixed;
	bottom: 0; left: 0; right: 0;
	z-index: 199;
	background: rgba(28,28,28,0.97);
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	padding: 12px var(--mhp-side-pad);
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 14px;
	transform: translateY(100%);
	transition: transform 0.3s ease;
	border-top: 1px solid rgba(247,244,239,0.07);
}
.mhp-sticky-bar.mhp-visible { transform: translateY(0); }

.mhp-sticky-info {
	display: flex;
	flex-direction: column;
	gap: 2px;
	min-width: 0;
	flex: 1;
}
.mhp-sticky-name {
	font-family: 'Cormorant Garamond', serif;
	font-size: 18px;
	font-weight: 400;
	color: #F7F4EF;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.mhp-sticky-stats {
	font-size: 11px;
	color: rgba(247,244,239,0.4);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
@media (max-width: 600px) { .mhp-sticky-stats { display: none; } }

.mhp-sticky-right {
	display: flex;
	align-items: center;
	gap: 10px;
	flex-shrink: 0;
}
.mhp-sticky-price-block { text-align: right; }
.mhp-sticky-price-main {
	font-family: 'Cormorant Garamond', serif;
	font-size: 21px;
	color: #F7F4EF;
	line-height: 1;
}
.mhp-sticky-price-sub {
	font-size: 9px;
	color: rgba(247,244,239,0.35);
	letter-spacing: 0.06em;
	text-transform: uppercase;
}
.mhp-btn-sticky {
	background: var(--mhp-clay);
	color: #fff;
	border: none;
	padding: 11px 20px;
	font-family: 'DM Sans', sans-serif;
	font-size: 12px;
	font-weight: 500;
	letter-spacing: 0.07em;
	text-transform: uppercase;
	cursor: pointer;
	transition: background 0.2s;
	white-space: nowrap;
	text-decoration: none;
	display: inline-block;
}
.mhp-btn-sticky:hover { background: var(--mhp-clay-light); color: #fff; text-decoration: none; }

.mhp-btn-sticky-ghost {
	background: transparent;
	color: rgba(247,244,239,0.6);
	border: 1px solid rgba(247,244,239,0.15);
	padding: 11px 14px;
	font-family: 'DM Sans', sans-serif;
	font-size: 12px;
	cursor: pointer;
	transition: all 0.2s;
	white-space: nowrap;
}
.mhp-btn-sticky-ghost:hover { border-color: rgba(247,244,239,0.4); color: #F7F4EF; }
@media (max-width: 480px) { .mhp-btn-sticky-ghost { display: none; } }

/* ---- SECTIONS ---- */
.mhp-section {
	padding: var(--mhp-section-pad) var(--mhp-side-pad);
}
.mhp-section--cream { background: var(--mhp-cream); }
.mhp-section--white { background: #fff; }
.mhp-section--charcoal { background: var(--mhp-charcoal); }

.mhp-section-inner {
	max-width: 1280px;
	margin: 0 auto;
}

.mhp-section-eyebrow {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 10px;
}
.mhp-eyebrow-line {
	width: 28px;
	height: 1px;
	background: var(--mhp-clay);
	flex-shrink: 0;
}
.mhp-eyebrow-text {
	font-size: 10px;
	font-weight: 500;
	letter-spacing: 0.14em;
	text-transform: uppercase;
	color: var(--mhp-clay);
}

.mhp-section-title {
	font-family: 'Cormorant Garamond', serif;
	font-size: clamp(28px, 3.5vw, 44px);
	font-weight: 300;
	line-height: 1.1;
	color: var(--mhp-charcoal);
	margin-bottom: clamp(28px, 4vw, 48px);
	margin-top: 0;
}
.mhp-section-title em { font-style: italic; }
.mhp-section--charcoal .mhp-section-title { color: var(--mhp-cream); }

/* ---- Description ---- */
.mhp-plan-description-content {
	font-size: 15px;
	color: var(--mhp-warm-gray);
	line-height: 1.8;
	max-width: 780px;
}
.mhp-plan-description-content p { margin-bottom: 1.2em; }
.mhp-plan-description-content h2,
.mhp-plan-description-content h3 {
	font-family: 'Cormorant Garamond', serif;
	font-weight: 400;
	color: var(--mhp-charcoal);
	margin-bottom: 0.5em;
	margin-top: 1.4em;
}

/* ---- Floor Plans ---- */
.mhp-floor-plans-content {
	/* Output the ACF wysiwyg as-is; let the gallery plugin style its own content */
	font-size: 14px;
	color: var(--mhp-charcoal);
	line-height: 1.7;
}
.mhp-floor-plans-content img { max-width: 100%; height: auto; }
.mhp-floor-plans-content p { margin-bottom: 1em; }

/* ---- Specs Table ---- */
.mhp-specs-table {
	width: 100%;
	border-collapse: collapse;
	font-size: 14px;
}
.mhp-specs-table th,
.mhp-specs-table td {
	padding: 13px 16px;
	text-align: left;
	border-bottom: 1px solid var(--mhp-warm-gray-ll);
}
.mhp-specs-table th {
	font-size: 11px;
	font-weight: 500;
	letter-spacing: 0.06em;
	text-transform: uppercase;
	color: var(--mhp-warm-gray);
	width: 40%;
	background: var(--mhp-cream);
}
.mhp-specs-table td {
	color: var(--mhp-charcoal);
	background: #fff;
}
.mhp-specs-table tr:last-child th,
.mhp-specs-table tr:last-child td { border-bottom: none; }
@media (max-width: 600px) {
	.mhp-specs-table th,
	.mhp-specs-table td { padding: 10px 12px; font-size: 13px; }
}

/* ---- FAQ ---- */
.mhp-faq-list {
	display: flex;
	flex-direction: column;
	gap: 1px;
	background: var(--mhp-warm-gray-ll);
	border: 1px solid var(--mhp-warm-gray-ll);
}
.mhp-faq-item { background: var(--mhp-cream); }
.mhp-faq-question {
	width: 100%;
	background: none;
	border: none;
	padding: 18px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 14px;
	cursor: pointer;
	text-align: left;
	transition: background 0.15s;
}
.mhp-faq-question:hover { background: #fff; }
.mhp-faq-q-text {
	font-family: 'Cormorant Garamond', serif;
	font-size: 17px;
	font-weight: 400;
	color: var(--mhp-charcoal);
}
.mhp-faq-chevron {
	width: 18px; height: 18px;
	color: var(--mhp-clay);
	flex-shrink: 0;
	transition: transform 0.25s;
}
.mhp-faq-item.mhp-open .mhp-faq-chevron { transform: rotate(180deg); }
.mhp-faq-answer {
	max-height: 0;
	overflow: hidden;
	transition: max-height 0.35s ease;
}
.mhp-faq-item.mhp-open .mhp-faq-answer { max-height: 400px; }
.mhp-faq-answer-inner {
	padding: 0 20px 18px;
	font-size: 14px;
	color: var(--mhp-warm-gray);
	line-height: 1.75;
}

/* ---- Related Plans ---- */
.mhp-related-grid {
	display: grid;
	grid-template-columns: 1fr;
	gap: 20px;
}
@media (min-width: 600px) { .mhp-related-grid { grid-template-columns: 1fr 1fr; } }
@media (min-width: 900px) { .mhp-related-grid { grid-template-columns: repeat(3, 1fr); } }

.mhp-related-card {
	background: #fff;
	border: 1px solid var(--mhp-warm-gray-ll);
	overflow: hidden;
	transition: transform 0.2s, box-shadow 0.2s;
}
.mhp-related-card:hover {
	transform: translateY(-3px);
	box-shadow: 0 10px 28px rgba(28,28,28,0.08);
}
.mhp-related-img {
	width: 100%;
	aspect-ratio: 4/3;
	object-fit: cover;
	display: block;
}
.mhp-related-info { padding: 16px; }
.mhp-related-style {
	font-size: 10px;
	font-weight: 500;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	color: var(--mhp-clay);
	margin-bottom: 5px;
}
.mhp-related-name {
	font-family: 'Cormorant Garamond', serif;
	font-size: 19px;
	font-weight: 400;
	color: var(--mhp-charcoal);
	margin-bottom: 4px;
	margin-top: 0;
}
.mhp-related-name a { color: inherit; text-decoration: none; }
.mhp-related-name a:hover { color: var(--mhp-clay); }
.mhp-related-specs {
	font-size: 12px;
	color: var(--mhp-warm-gray);
	margin-bottom: 8px;
}
.mhp-related-price {
	font-size: 13px;
	font-weight: 500;
	color: var(--mhp-charcoal);
	margin-bottom: 4px;
}
.mhp-related-link {
	font-size: 11px;
	color: var(--mhp-clay);
	letter-spacing: 0.06em;
	text-transform: uppercase;
	text-decoration: none;
	display: inline-block;
}
.mhp-related-link:hover { text-decoration: underline; }

/* ---- Scroll fade animations ---- */
.mhp-fade {
	opacity: 0;
	transform: translateY(16px);
	transition: opacity 0.6s ease, transform 0.6s ease;
}
.mhp-fade.mhp-visible { opacity: 1; transform: translateY(0); }
.mhp-fade-d1 { transition-delay: 0.1s; }
.mhp-fade-d2 { transition-delay: 0.2s; }
</style>
	<?php
}

// ---- Inject JSON-LD schema via wp_head ----
add_action( 'wp_head', 'mhp_plan_v2_schema', 5 );
function mhp_plan_v2_schema() {
	if ( ! is_singular() ) { return; }
	$pid = get_the_ID();
	if ( ! function_exists( 'get_field' ) ) { return; }

	$plan_name    = get_field( 'plan_name', $pid ) ?: get_the_title( $pid );
	$plan_img_raw = get_field( 'plan_image', $pid );
	$plan_img_url = '';
	if ( $plan_img_raw ) {
		if ( is_array( $plan_img_raw ) ) {
			$plan_img_url = $plan_img_raw['url'] ?? '';
		} else {
			$src = wp_get_attachment_image_src( (int) $plan_img_raw, 'full' );
			if ( $src ) { $plan_img_url = $src[0]; }
		}
	}
	if ( empty( $plan_img_url ) ) {
		$thumb_id = get_post_thumbnail_id( $pid );
		if ( $thumb_id ) {
			$src = wp_get_attachment_image_src( $thumb_id, 'full' );
			if ( $src ) { $plan_img_url = $src[0]; }
		}
	}

	// Price
	$price_raw = get_field( 'price', $pid );
	$price_num  = '';
	if ( $price_raw ) {
		$price_num = number_format( (float) $price_raw, 2, '.', '' );
	} else {
		$paypal = get_field( 'paypal', $pid );
		if ( $paypal && preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?)\s*USD/i', $paypal, $m ) ) {
			$price_num = preg_replace( '/[^0-9.]/', '', $m[1] );
		}
	}

	// FAQs
	$faqs      = get_field( 'faqs', $pid );
	$faq_items = array();
	if ( ! empty( $faqs ) && is_array( $faqs ) ) {
		foreach ( $faqs as $faq ) {
			$q = $faq['question'] ?? '';
			$a = $faq['answer']   ?? '';
			if ( $q && $a ) {
				$faq_items[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $q ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $a ),
					),
				);
			}
		}
	}

	// Terms for breadcrumb
	$home_url   = home_url( '/' );
	$plans_url  = home_url( '/house-plans/' );
	$plan_url   = get_permalink( $pid );
	$plan_terms = get_the_terms( $pid, 'home-plans_categories' );

	$graph = array(
		array(
			'@type'       => 'Product',
			'name'        => $plan_name . ' House Plan',
			'description' => wp_strip_all_tags( get_field( 'plan_description', $pid ) ?: get_the_excerpt() ),
			'image'       => $plan_img_url ? array( $plan_img_url ) : array(),
			'brand'       => array( '@type' => 'Brand', 'name' => 'Max Fulbright Designs' ),
			'offers'      => array(
				array(
					'@type'        => 'Offer',
					'name'         => 'PDF House Plans',
					'price'        => $price_num ?: '0',
					'priceCurrency'=> 'USD',
					'availability' => 'https://schema.org/InStock',
					'url'          => $plan_url,
					'seller'       => array( '@type' => 'Organization', 'name' => 'MaxHousePlans' ),
				),
			),
		),
		array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home_url ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'House Plans', 'item' => $plans_url ),
				array( '@type' => 'ListItem', 'position' => 3, 'name' => $plan_name ),
			),
		),
	);

	if ( ! empty( $faq_items ) ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => $faq_items,
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

// ---- Replace Genesis loop ----
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_plan_loop_v2' );

function mhp_plan_loop_v2() {
	if ( ! have_posts() ) { return; }
	while ( have_posts() ) {
		the_post();
		$pid = get_the_ID();
		$acf = function_exists( 'get_field' );

		// ---- ACF field retrieval ----
		$plan_name        = $acf ? get_field( 'plan_name',          $pid ) : '';
		$plan_name        = $plan_name ?: get_the_title( $pid );
		$sqft             = $acf ? get_field( 'total_living_area',   $pid ) : '';
		$main_floor       = $acf ? get_field( 'main_floor',          $pid ) : '';
		$upper_floor      = $acf ? get_field( 'upper_floor',         $pid ) : '';
		$lower_floor      = $acf ? get_field( 'lower_floor',         $pid ) : '';
		$bedrooms         = $acf ? get_field( 'bedrooms',            $pid ) : '';
		$bathrooms        = $acf ? get_field( 'bathrooms',           $pid ) : '';
		$stories          = $acf ? get_field( 'stories',             $pid ) : '';
		$garage           = $acf ? get_field( 'garage',              $pid ) : '';
		$width            = $acf ? get_field( 'width',               $pid ) : '';
		$depth            = $acf ? get_field( 'depth',               $pid ) : '';
		$outdoor          = $acf ? get_field( 'outdoor',             $pid ) : '';
		$roof             = $acf ? get_field( 'roof',                $pid ) : '';
		$ceiling          = $acf ? get_field( 'ceiling',             $pid ) : '';
		$exterior         = $acf ? get_field( 'exterior',            $pid ) : '';
		$lot_style        = $acf ? get_field( 'lot_style',           $pid ) : '';
		$additional_rooms = $acf ? get_field( 'additional_rooms',    $pid ) : '';
		$other_features   = $acf ? get_field( 'other_features',      $pid ) : '';
		$style_field      = $acf ? get_field( 'style',               $pid ) : '';
		$plan_description = $acf ? get_field( 'plan_description',    $pid ) : '';
		$floor_plans      = $acf ? get_field( 'floor_plans',         $pid ) : '';
		$plan_image_raw   = $acf ? get_field( 'plan_image',          $pid ) : null;
		$paypal           = $acf ? get_field( 'paypal',              $pid ) : '';
		$related_plans    = $acf ? get_field( 'related_plans',       $pid ) : array();
		$faqs             = $acf ? get_field( 'faqs',                $pid ) : array();

		// ---- PayPal direct URL extraction ----
		$paypal_url = '';
		if ( $paypal ) {
			if ( preg_match( '/hosted_button_id.*?value=["\']([^"\']+)["\']/', $paypal, $m ) ) {
				$paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
			}
			if ( empty( $paypal_url ) && preg_match( '/action=["\']([^"\']+)["\']/', $paypal, $m ) ) {
				$paypal_url = $m[1];
			}
		}

		// ---- Price extraction ----
		$price_display = '';
		$price_raw     = $acf ? get_field( 'price', $pid ) : '';
		if ( $price_raw ) {
			$price_display = '$' . number_format( (float) $price_raw, 0 ) . ' PDF';
		} elseif ( $paypal ) {
			if ( preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?)\s*USD/i', $paypal, $m ) ) {
				$price_display = '$' . $m[1] . ' PDF';
			}
		}

		// ---- Hero image ----
		$hero_img_url = '';
		$hero_img_alt = esc_attr( $plan_name );

		// 1. plan_image ACF field
		if ( $plan_image_raw ) {
			if ( is_array( $plan_image_raw ) ) {
				$hero_img_url = $plan_image_raw['url'] ?? '';
				$hero_img_alt = esc_attr( $plan_image_raw['alt'] ?? $plan_name );
			} else {
				$src = wp_get_attachment_image_src( (int) $plan_image_raw, 'large' );
				if ( $src ) { $hero_img_url = $src[0]; }
			}
		}
		// 2. Fallback: featured image
		if ( empty( $hero_img_url ) ) {
			$thumb_id = get_post_thumbnail_id( $pid );
			if ( $thumb_id ) {
				$src = wp_get_attachment_image_src( $thumb_id, 'large' );
				if ( $src ) { $hero_img_url = $src[0]; }
			}
		}

		// ---- Gallery thumbnails ----
		$gallery_images = array();
		if ( $hero_img_url ) {
			$gallery_images[] = array(
				'url' => $hero_img_url,
				'alt' => $hero_img_alt,
				'id'  => get_post_thumbnail_id( $pid ) ?: 0,
			);
		}
		// Get attached images (up to 5 extras)
		$thumb_id_main = get_post_thumbnail_id( $pid );
		$plan_img_id   = ( $plan_image_raw && ! is_array( $plan_image_raw ) ) ? (int) $plan_image_raw : 0;
		$exclude_ids   = array_filter( array( $thumb_id_main, $plan_img_id ) );
		$attached      = get_posts( array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_parent'    => $pid,
			'post_status'    => 'inherit',
			'posts_per_page' => 5,
			'exclude'        => $exclude_ids,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
		foreach ( $attached as $att ) {
			$att_src = wp_get_attachment_image_src( $att->ID, 'medium_large' );
			$att_thumb_src = wp_get_attachment_image_src( $att->ID, 'thumbnail' );
			if ( $att_src ) {
				$gallery_images[] = array(
					'url' => $att_src[0],
					'alt' => esc_attr( $att->post_excerpt ?: $plan_name ),
					'id'  => $att->ID,
				);
			}
		}

		// ---- Plan style/category ----
		$plan_style = '';
		if ( $style_field ) {
			$plan_style = $style_field;
		} else {
			$plan_terms = get_the_terms( $pid, 'home-plans_categories' );
			if ( $plan_terms && ! is_wp_error( $plan_terms ) ) {
				$plan_style = implode( ' &middot; ', wp_list_pluck( $plan_terms, 'name' ) );
			}
		}

		// ---- Breadcrumb term ----
		$bc_term     = null;
		$bc_terms    = get_the_terms( $pid, 'home-plans_categories' );
		if ( $bc_terms && ! is_wp_error( $bc_terms ) ) {
			$bc_term = reset( $bc_terms );
		}

		// ---- Plan title split (first word charcoal, rest clay italic) ----
		$name_parts = explode( ' ', trim( $plan_name ), 2 );
		$name_first = $name_parts[0];
		$name_rest  = isset( $name_parts[1] ) ? $name_parts[1] : '';

		// ---- Sqft breakdown (skip "None" values) ----
		$sqft_breakdown = array();
		if ( $main_floor  && strtolower( trim( $main_floor  ) ) !== 'none' ) { $sqft_breakdown['Main']  = $main_floor; }
		if ( $upper_floor && strtolower( trim( $upper_floor ) ) !== 'none' ) { $sqft_breakdown['Upper'] = $upper_floor; }
		if ( $lower_floor && strtolower( trim( $lower_floor ) ) !== 'none' ) { $sqft_breakdown['Lower'] = $lower_floor; }
		if ( $width )  { $sqft_breakdown['Width'] = $width; }
		if ( $depth )  { $sqft_breakdown['Depth'] = $depth; }

		$plan_permalink = get_permalink( $pid );
		?>

		<!-- ==================== HERO ==================== -->
		<section class="mhp-hero" aria-label="<?php echo esc_attr( $plan_name ); ?> Overview">

			<!-- LEFT: Gallery (75%) -->
			<div class="mhp-gallery" aria-label="Plan photo gallery">

				<!-- Main image -->
				<div class="mhp-gallery-main">
					<?php if ( $hero_img_url ) : ?>
						<img
							id="mhpHeroMainImg"
							src="<?php echo esc_url( $hero_img_url ); ?>"
							alt="<?php echo $hero_img_alt; ?>"
							loading="eager"
							fetchpriority="high"
						/>
					<?php else : ?>
						<div class="mhp-gallery-placeholder">Photo coming soon</div>
					<?php endif; ?>
				</div><!-- /.mhp-gallery-main -->

				<!-- Thumbnail strip -->
				<?php if ( count( $gallery_images ) > 1 ) : ?>
					<div class="mhp-gallery-thumbs" role="list" aria-label="Plan images">
						<?php foreach ( $gallery_images as $i => $gimg ) :
							$thumb_display_url = $gimg['url'];
							if ( $gimg['id'] ) {
								$t = wp_get_attachment_image_src( $gimg['id'], 'thumbnail' );
								if ( $t ) { $thumb_display_url = $t[0]; }
							}
						?>
							<div
								class="mhp-gallery-thumb <?php echo $i === 0 ? 'active' : ''; ?>"
								role="listitem"
								tabindex="0"
								data-full="<?php echo esc_url( $gimg['url'] ); ?>"
								data-alt="<?php echo esc_attr( $gimg['alt'] ); ?>"
								aria-label="View photo <?php echo $i + 1; ?>"
							>
								<img
									src="<?php echo esc_url( $thumb_display_url ); ?>"
									alt="<?php echo esc_attr( $gimg['alt'] ); ?>"
									loading="lazy"
								/>
							</div>
						<?php endforeach; ?>
					</div><!-- /.mhp-gallery-thumbs -->
				<?php endif; ?>

			</div><!-- /.mhp-gallery -->

			<!-- RIGHT: Plan Info Panel (25%) -->
			<div class="mhp-plan-info" id="mhp-buy">

				<!-- Breadcrumb -->
				<p class="mhp-plan-breadcrumb">
					<a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">House Plans</a>
					<?php if ( $bc_term ) : ?>
						<span aria-hidden="true"> / </span>
						<a href="<?php echo esc_url( get_term_link( $bc_term ) ); ?>"><?php echo esc_html( $bc_term->name ); ?></a>
					<?php endif; ?>
				</p>

				<!-- Eyebrow / Badge -->
				<?php if ( $plan_style ) : ?>
					<div class="mhp-plan-eyebrow"><?php echo esc_html( $plan_style ); ?></div>
				<?php endif; ?>

				<!-- H1 Title -->
				<h1 class="mhp-plan-title">
					<?php echo esc_html( $name_first ); ?>
					<?php if ( $name_rest ) : ?><br><em><?php echo esc_html( $name_rest ); ?></em><?php endif; ?>
				</h1>

				<!-- Subtitle -->
				<?php if ( $sqft || $bedrooms ) : ?>
					<p class="mhp-plan-subtitle">
						<?php
						$sub_parts = array();
						if ( $bedrooms ) { $sub_parts[] = $bedrooms . '-Bedroom'; }
						if ( $sqft )     { $sub_parts[] = esc_html( $sqft ) . ' sq ft'; }
						if ( $lot_style ){ $sub_parts[] = esc_html( $lot_style ) . ' Lot'; }
						echo implode( ' &middot; ', $sub_parts );
						?>
					</p>
				<?php endif; ?>

				<!-- Spec Grid -->
				<div class="mhp-specs-grid" aria-label="Plan specifications">
					<?php if ( $sqft ) : ?>
						<div class="mhp-spec">
							<span class="mhp-spec-value"><?php echo esc_html( $sqft ); ?></span>
							<span class="mhp-spec-label">Sq Ft</span>
						</div>
					<?php endif; ?>
					<?php if ( $bedrooms ) : ?>
						<div class="mhp-spec">
							<span class="mhp-spec-value"><?php echo esc_html( $bedrooms ); ?></span>
							<span class="mhp-spec-label">Beds</span>
						</div>
					<?php endif; ?>
					<?php if ( $bathrooms ) : ?>
						<div class="mhp-spec">
							<span class="mhp-spec-value"><?php echo esc_html( $bathrooms ); ?></span>
							<span class="mhp-spec-label">Baths</span>
						</div>
					<?php endif; ?>
					<?php if ( $stories ) : ?>
						<div class="mhp-spec">
							<span class="mhp-spec-value"><?php echo esc_html( $stories ); ?></span>
							<span class="mhp-spec-label">Stories</span>
						</div>
					<?php endif; ?>
				</div><!-- /.mhp-specs-grid -->

				<!-- Sqft breakdown -->
				<?php if ( ! empty( $sqft_breakdown ) ) : ?>
					<div class="mhp-sqft-breakdown">
						<?php foreach ( $sqft_breakdown as $label => $val ) : ?>
							<span class="mhp-sqft-item"><?php echo esc_html( $label ); ?>:<strong><?php echo esc_html( $val ); ?></strong></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- Price -->
				<?php if ( $price_display ) : ?>
					<div class="mhp-price-row">
						<div>
							<span class="mhp-price-label">PDF Plans</span>
							<span class="mhp-price-amount"><?php echo esc_html( $price_display ); ?></span>
						</div>
						<div class="mhp-price-divider"></div>
						<div class="mhp-price-secondary">
							Instant download<br>Permit-ready drawings
						</div>
					</div>
				<?php endif; ?>

				<!-- BUY Button — direct PayPal link -->
				<?php if ( $paypal_url ) : ?>
					<a
						href="<?php echo esc_url( $paypal_url ); ?>"
						class="mhp-btn-buy"
						target="_blank"
						rel="noopener noreferrer"
						aria-label="Buy <?php echo esc_attr( $plan_name ); ?> — <?php echo esc_attr( $price_display ); ?>"
					>
						Buy Plan<?php echo $price_display ? ' — ' . esc_html( $price_display ) : ''; ?>
					</a>
					<p class="mhp-payment-note">
						<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
						Secure checkout via PayPal &nbsp;&middot;&nbsp; Instant PDF delivery
					</p>
				<?php elseif ( $paypal ) : ?>
					<!-- Fallback: hidden form + styled button (if URL extraction failed) -->
					<div style="display:none;" aria-hidden="true" id="mhp-paypal-form-<?php echo $pid; ?>">
						<?php echo $paypal; ?>
					</div>
					<button
						class="mhp-btn-buy"
						onclick="(function(){var f=document.querySelector('#mhp-paypal-form-<?php echo $pid; ?> form');if(f)f.submit();})()"
						aria-label="Buy <?php echo esc_attr( $plan_name ); ?>"
					>
						Buy Plan<?php echo $price_display ? ' — ' . esc_html( $price_display ) : ''; ?>
					</button>
					<p class="mhp-payment-note">
						<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
						Secure checkout via PayPal &nbsp;&middot;&nbsp; Instant PDF delivery
					</p>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="mhp-btn-buy">
						Get This Plan
					</a>
				<?php endif; ?>

				<!-- What's Included checklist -->
				<div class="mhp-whats-included">
					<p class="mhp-included-heading">What's Included</p>
					<ul class="mhp-included-list" role="list">
						<li>
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
							PDF plan set — instant delivery
						</li>
						<li>
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
							All floor plans — fully dimensioned
						</li>
						<li>
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
							Front, side &amp; rear elevations
						</li>
						<li>
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
							Foundation &amp; roof plan
						</li>
						<li>
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
							Permit-ready — talk to Max directly
						</li>
					</ul>
				</div><!-- /.mhp-whats-included -->

			</div><!-- /.mhp-plan-info -->
		</section><!-- /.mhp-hero -->

		<!-- ==================== STICKY BUY BAR ==================== -->
		<div id="mhpStickyBar" class="mhp-sticky-bar" role="complementary" aria-label="Quick purchase bar">
			<div class="mhp-sticky-info">
				<span class="mhp-sticky-name"><?php echo esc_html( $plan_name ); ?></span>
				<span class="mhp-sticky-stats">
					<?php
					$sticky_parts = array();
					if ( $bedrooms )  { $sticky_parts[] = $bedrooms . ' bed'; }
					if ( $bathrooms ) { $sticky_parts[] = $bathrooms . ' bath'; }
					if ( $sqft )      { $sticky_parts[] = esc_html( $sqft ) . ' sq ft'; }
					echo implode( ' &nbsp;&middot;&nbsp; ', $sticky_parts );
					?>
				</span>
			</div>
			<div class="mhp-sticky-right">
				<?php if ( $price_display ) : ?>
					<div class="mhp-sticky-price-block">
						<div class="mhp-sticky-price-main"><?php echo esc_html( $price_display ); ?></div>
						<div class="mhp-sticky-price-sub">PDF Plans</div>
					</div>
				<?php endif; ?>
				<button
					class="mhp-btn-sticky-ghost"
					onclick="document.getElementById('mhp-buy').scrollIntoView({behavior:'smooth'})"
					type="button"
				>Questions?</button>
				<?php if ( $paypal_url ) : ?>
					<a
						href="<?php echo esc_url( $paypal_url ); ?>"
						class="mhp-btn-sticky"
						target="_blank"
						rel="noopener noreferrer"
					>Buy This Plan</a>
				<?php else : ?>
					<button
						class="mhp-btn-sticky"
						onclick="document.getElementById('mhp-buy').scrollIntoView({behavior:'smooth'})"
						type="button"
					>Buy This Plan</button>
				<?php endif; ?>
			</div>
		</div><!-- /.mhp-sticky-bar -->

		<!-- ==================== DESCRIPTION ==================== -->
		<?php if ( $plan_description ) : ?>
			<section class="mhp-section mhp-section--cream" aria-label="Plan description">
				<div class="mhp-section-inner">
					<div class="mhp-section-eyebrow mhp-fade">
						<span class="mhp-eyebrow-line"></span>
						<span class="mhp-eyebrow-text">About This Plan</span>
					</div>
					<h2 class="mhp-section-title mhp-fade">Plan <em>Overview</em></h2>
					<div class="mhp-plan-description-content mhp-fade mhp-fade-d1">
						<?php echo wp_kses_post( $plan_description ); ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ==================== FLOOR PLANS ==================== -->
		<?php if ( $floor_plans ) : ?>
			<section class="mhp-section mhp-section--white" aria-label="Floor plans">
				<div class="mhp-section-inner">
					<div class="mhp-section-eyebrow mhp-fade">
						<span class="mhp-eyebrow-line"></span>
						<span class="mhp-eyebrow-text">Every Room, Every Detail</span>
					</div>
					<h2 class="mhp-section-title mhp-fade">Explore the <em>Floor Plans</em></h2>
					<div class="mhp-floor-plans-content mhp-fade mhp-fade-d1">
						<?php echo wp_kses_post( $floor_plans ); ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ==================== FULL SPECS TABLE ==================== -->
		<?php
		$spec_rows = array();
		if ( $sqft )             { $spec_rows['Total Living Area']  = esc_html( $sqft ); }
		if ( $main_floor  && strtolower( trim( $main_floor  ) ) !== 'none' ) { $spec_rows['Main Floor']       = esc_html( $main_floor ); }
		if ( $upper_floor && strtolower( trim( $upper_floor ) ) !== 'none' ) { $spec_rows['Upper Floor']      = esc_html( $upper_floor ); }
		if ( $lower_floor && strtolower( trim( $lower_floor ) ) !== 'none' ) { $spec_rows['Lower Floor']      = esc_html( $lower_floor ); }
		if ( $bedrooms )         { $spec_rows['Bedrooms']           = esc_html( $bedrooms ); }
		if ( $bathrooms )        { $spec_rows['Bathrooms']          = esc_html( $bathrooms ); }
		if ( $stories )          { $spec_rows['Stories']            = esc_html( $stories ); }
		if ( $garage )           { $spec_rows['Garage']             = esc_html( $garage ); }
		if ( $width )            { $spec_rows['Width']              = esc_html( $width ); }
		if ( $depth )            { $spec_rows['Depth']              = esc_html( $depth ); }
		if ( $outdoor )          { $spec_rows['Outdoor Spaces']     = esc_html( $outdoor ); }
		if ( $roof )             { $spec_rows['Roof Style']         = esc_html( $roof ); }
		if ( $ceiling )          { $spec_rows['Ceiling Height']     = esc_html( $ceiling ); }
		if ( $exterior )         { $spec_rows['Exterior']           = esc_html( $exterior ); }
		if ( $lot_style )        { $spec_rows['Lot Style']          = esc_html( $lot_style ); }
		if ( $additional_rooms ) { $spec_rows['Additional Rooms']   = esc_html( $additional_rooms ); }
		if ( $other_features )   { $spec_rows['Other Features']     = esc_html( $other_features ); }

		if ( ! empty( $spec_rows ) ) :
		?>
			<section class="mhp-section mhp-section--cream" aria-label="Full specifications">
				<div class="mhp-section-inner">
					<div class="mhp-section-eyebrow mhp-fade">
						<span class="mhp-eyebrow-line"></span>
						<span class="mhp-eyebrow-text">Full Details</span>
					</div>
					<h2 class="mhp-section-title mhp-fade">Plan <em>Specifications</em></h2>
					<table class="mhp-specs-table mhp-fade mhp-fade-d1" aria-label="Plan specifications table">
						<tbody>
							<?php foreach ( $spec_rows as $label => $value ) : ?>
								<tr>
									<th scope="row"><?php echo $label; ?></th>
									<td><?php echo $value; ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</section>
		<?php endif; ?>

		<!-- ==================== FAQ ==================== -->
		<?php if ( ! empty( $faqs ) && is_array( $faqs ) ) : ?>
			<section class="mhp-section mhp-section--white" id="mhp-faq" aria-label="Frequently asked questions">
				<div class="mhp-section-inner">
					<div class="mhp-section-eyebrow mhp-fade">
						<span class="mhp-eyebrow-line"></span>
						<span class="mhp-eyebrow-text">Common Questions</span>
					</div>
					<h2 class="mhp-section-title mhp-fade">Before You <em>Buy</em></h2>
					<div class="mhp-faq-list mhp-fade mhp-fade-d1">
						<?php foreach ( $faqs as $faq ) :
							$q = isset( $faq['question'] ) ? $faq['question'] : '';
							$a = isset( $faq['answer'] )   ? $faq['answer']   : '';
							if ( ! $q ) { continue; }
							$uid = 'mhpfaq-' . sanitize_title( $q );
						?>
							<div class="mhp-faq-item">
								<button
									class="mhp-faq-question"
									aria-expanded="false"
									aria-controls="<?php echo esc_attr( $uid ); ?>"
									type="button"
								>
									<span class="mhp-faq-q-text"><?php echo esc_html( $q ); ?></span>
									<svg class="mhp-faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
								</button>
								<div class="mhp-faq-answer" id="<?php echo esc_attr( $uid ); ?>" role="region">
									<div class="mhp-faq-answer-inner">
										<?php echo wp_kses_post( $a ); ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ==================== RELATED PLANS ==================== -->
		<?php if ( ! empty( $related_plans ) && is_array( $related_plans ) ) : ?>
			<section class="mhp-section mhp-section--cream" aria-label="Related house plans">
				<div class="mhp-section-inner">
					<div class="mhp-section-eyebrow mhp-fade">
						<span class="mhp-eyebrow-line"></span>
						<span class="mhp-eyebrow-text">You Might Also Like</span>
					</div>
					<h2 class="mhp-section-title mhp-fade">More <em>House Plans</em></h2>
					<div class="mhp-related-grid mhp-fade mhp-fade-d1">
						<?php
						$shown = 0;
						foreach ( $related_plans as $rel_raw ) :
							if ( $shown >= 3 ) { break; }
							// related_plans can be array of post objects OR array of IDs
							$rel_id = is_object( $rel_raw ) ? $rel_raw->ID : (int) $rel_raw;
							if ( ! $rel_id ) { continue; }
							$rel_post = get_post( $rel_id );
							if ( ! $rel_post ) { continue; }

							$rel_name  = $acf ? get_field( 'plan_name', $rel_id ) : '';
							$rel_name  = $rel_name ?: get_the_title( $rel_id );
							$rel_sqft  = $acf ? get_field( 'total_living_area', $rel_id ) : '';
							$rel_bed   = $acf ? get_field( 'bedrooms',          $rel_id ) : '';
							$rel_bath  = $acf ? get_field( 'bathrooms',         $rel_id ) : '';
							$rel_url   = get_permalink( $rel_id );
							$rel_thumb = get_the_post_thumbnail_url( $rel_id, 'medium_large' );

							// Price
							$rel_price_display = '';
							$rel_price_raw = $acf ? get_field( 'price', $rel_id ) : '';
							if ( $rel_price_raw ) {
								$rel_price_display = '$' . number_format( (float) $rel_price_raw, 0 );
							} else {
								$rel_paypal = $acf ? get_field( 'paypal', $rel_id ) : '';
								if ( $rel_paypal && preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?)\s*USD/i', $rel_paypal, $rm ) ) {
									$rel_price_display = '$' . $rm[1];
								}
							}

							// Style
							$rel_terms = get_the_terms( $rel_id, 'home-plans_categories' );
							$rel_style = '';
							if ( $rel_terms && ! is_wp_error( $rel_terms ) ) {
								$rel_style = esc_html( reset( $rel_terms )->name );
							}
							$shown++;
						?>
							<article class="mhp-related-card">
								<?php if ( $rel_thumb ) : ?>
									<a href="<?php echo esc_url( $rel_url ); ?>" tabindex="-1" aria-hidden="true">
										<img
											class="mhp-related-img"
											src="<?php echo esc_url( $rel_thumb ); ?>"
											alt="<?php echo esc_attr( $rel_name ); ?>"
											loading="lazy"
										/>
									</a>
								<?php endif; ?>
								<div class="mhp-related-info">
									<?php if ( $rel_style ) : ?>
										<p class="mhp-related-style"><?php echo $rel_style; ?></p>
									<?php endif; ?>
									<h3 class="mhp-related-name">
										<a href="<?php echo esc_url( $rel_url ); ?>"><?php echo esc_html( $rel_name ); ?></a>
									</h3>
									<?php if ( $rel_bed || $rel_sqft ) : ?>
										<p class="mhp-related-specs">
											<?php
											$rp = array();
											if ( $rel_bed )  { $rp[] = $rel_bed  . ' bed'; }
											if ( $rel_bath ) { $rp[] = $rel_bath . ' bath'; }
											if ( $rel_sqft ) { $rp[] = esc_html( $rel_sqft ) . ' sq ft'; }
											echo implode( ' &middot; ', $rp );
											?>
										</p>
									<?php endif; ?>
									<?php if ( $rel_price_display ) : ?>
										<p class="mhp-related-price"><?php echo esc_html( $rel_price_display ); ?></p>
									<?php endif; ?>
									<a href="<?php echo esc_url( $rel_url ); ?>" class="mhp-related-link">View Plan &rarr;</a>
								</div>
							</article>
						<?php endforeach; ?>
					</div><!-- /.mhp-related-grid -->
				</div>
			</section>
		<?php endif; ?>

		<!-- ==================== JAVASCRIPT ==================== -->
		<script>
		(function () {
			'use strict';

			/* --- Gallery switcher --- */
			var heroMainImg = document.getElementById('mhpHeroMainImg');
			var thumbs = document.querySelectorAll('.mhp-gallery-thumb');
			if (heroMainImg && thumbs.length) {
				thumbs.forEach(function (thumb) {
					function activateThumb() {
						var fullUrl = thumb.getAttribute('data-full');
						var altText = thumb.getAttribute('data-alt');
						if (fullUrl) {
							heroMainImg.style.opacity = '0.4';
							setTimeout(function () {
								heroMainImg.src = fullUrl;
								if (altText) { heroMainImg.alt = altText; }
								heroMainImg.style.opacity = '1';
							}, 180);
						}
						thumbs.forEach(function (t) { t.classList.remove('active'); });
						thumb.classList.add('active');
					}
					thumb.addEventListener('click', activateThumb);
					thumb.addEventListener('keydown', function (e) {
						if (e.key === 'Enter' || e.key === ' ') {
							e.preventDefault();
							activateThumb();
						}
					});
				});
			}

			/* --- Sticky bar (show after scrolling past hero) --- */
			var stickyBar = document.getElementById('mhpStickyBar');
			if (stickyBar) {
				window.addEventListener('scroll', function () {
					stickyBar.classList.toggle('mhp-visible', window.scrollY > 500);
				}, { passive: true });
			}

			/* --- FAQ accordion --- */
			document.querySelectorAll('.mhp-faq-question').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var item = btn.closest('.mhp-faq-item');
					var isOpen = item.classList.contains('mhp-open');
					// Close all
					document.querySelectorAll('.mhp-faq-item.mhp-open').forEach(function (i) {
						i.classList.remove('mhp-open');
						i.querySelector('.mhp-faq-question').setAttribute('aria-expanded', 'false');
					});
					// Toggle clicked
					if (!isOpen) {
						item.classList.add('mhp-open');
						btn.setAttribute('aria-expanded', 'true');
					}
				});
			});

			/* --- Scroll fade-in --- */
			var fadeEls = document.querySelectorAll('.mhp-fade');
			if ('IntersectionObserver' in window) {
				var obs = new IntersectionObserver(function (entries) {
					entries.forEach(function (e) {
						if (e.isIntersecting) {
							e.target.classList.add('mhp-visible');
							obs.unobserve(e.target);
						}
					});
				}, { threshold: 0.06 });
				fadeEls.forEach(function (el) { obs.observe(el); });
			} else {
				fadeEls.forEach(function (el) { el.classList.add('mhp-visible'); });
			}
		})();
		</script>

	<?php } // end while
}

genesis();
