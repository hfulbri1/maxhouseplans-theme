<?php
/**
 * Single Plan Template V4 (MaxHousePlans)
 *
 * @package maxhouseplans
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter(
	'genesis_site_layout',
	static function () {
		return 'full-width-content';
	}
);

remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_v4_render' );
add_action( 'wp_head', 'mhp_v4_schema', 5 );
add_action( 'wp_enqueue_scripts', 'mhp_v4_assets' );

/**
 * Enqueue V4 assets only on plan singular pages.
 */
function mhp_v4_assets() {
	if ( ! is_singular( 'plans' ) ) {
		return;
	}

	$css_path = get_stylesheet_directory() . '/css/mhp-plan-v4.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'mhp-plan-v4',
			get_stylesheet_directory_uri() . '/css/mhp-plan-v4.css',
			array( 'mhp-google-fonts' ),
			filemtime( $css_path )
		);
	}
}

/**
 * Template renderer.
 */
function mhp_v4_render() {
	$post_id = get_the_ID();

	$get = static function ( $key, $fallback = '' ) use ( $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			$val = get_field( $key, $post_id );
			if ( null !== $val && '' !== $val ) {
				return $val;
			}
		}
		return $fallback;
	};

	$plan_name = (string) $get( 'plan_name', get_the_title( $post_id ) );
	$total_living_area = (string) $get( 'total_living_area', '' );
	$main_floor = (string) $get( 'main_floor', '' );
	$upper_floor = (string) $get( 'upper_floor', '' );
	$lower_floor = (string) $get( 'lower_floor', '' );
	$bedrooms = (string) $get( 'bedrooms', '' );
	$bathrooms = (string) $get( 'bathrooms', '' );
	$stories = (string) $get( 'stories', '' );
	$width = (string) $get( 'width', '' );
	$depth = (string) $get( 'depth', '' );
	$garage = (string) $get( 'garage', '' );
	$style = (string) $get( 'style', '' );
	$outdoor = (string) $get( 'outdoor', '' );
	$roof = (string) $get( 'roof', '' );
	$ceiling = (string) $get( 'ceiling', '' );
	$exterior = (string) $get( 'exterior', '' );
	$additional_rooms = (string) $get( 'additional_rooms', '' );
	$other_features = (string) $get( 'other_features', '' );
	$lot_style = (string) $get( 'lot_style', '' );
	$plan_description = (string) $get( 'plan_description', '' );
	$floor_plans_content = (string) $get( 'floor_plans', '' );
	$paypal = (string) $get( 'paypal', '' );
	$price = $get( 'price', '' );
	$related_plans = $get( 'related_plans', array() );
	$faqs = $get( 'faqs', array() );

	$price_num = is_numeric( $price ) ? (float) $price : 1195;
	$price_fmt = '$' . number_format( $price_num, 0, '.', ',' );
	$cad_fmt = '$' . number_format( $price_num * 1.26, 0, '.', ',' );
	$paypal_url = '';
	if ( $paypal && preg_match( '/name=["\']hosted_button_id["\']\s+value=["\']([^"\']+)["\']/', $paypal, $m ) ) {
		$paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode( $m[1] );
	}
	$buy_href = $paypal_url ? $paypal_url : ( get_permalink( $post_id ) . '#contact' );

	$sl = strtolower( $style );
	if ( str_contains( $sl, 'mountain' ) || str_contains( $sl, 'lake' ) ) {
		$cat = 'mountain';
		$badge = 'Mountain House Plan';
		$bc_cat = 'Mountain House Plans';
		$bc_url = home_url( '/home-plans/mountain-house-plans/' );
	} elseif ( str_contains( $sl, 'farmhouse' ) || str_contains( $sl, 'southern' ) || str_contains( $sl, 'country' ) ) {
		$cat = 'farmhouse';
		$badge = 'Farmhouse Plan';
		$bc_cat = 'Farmhouse House Plans';
		$bc_url = home_url( '/home-plans/farmhouse-house-plans/' );
	} elseif ( str_contains( $sl, 'cottage' ) || str_contains( $sl, 'cabin' ) || str_contains( $sl, 'bungalow' ) ) {
		$cat = 'cottage';
		$badge = 'Cottage Plan';
		$bc_cat = 'Cottage House Plans';
		$bc_url = home_url( '/home-plans/cottage-house-plans/' );
	} else {
		$cat = 'craftsman';
		$badge = 'Craftsman Plan';
		$bc_cat = 'House Plans';
		$bc_url = home_url( '/house-plans/' );
	}

	// Gallery parsing from shortcode ids.
	$gallery_items = array();
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( preg_match( '/\[gallery[^\]]+ids=["\']?([\d,]+)/i', $content, $gm ) ) {
		$ids = array_filter( array_map( 'absint', explode( ',', $gm[1] ) ) );
		foreach ( $ids as $aid ) {
			$large = wp_get_attachment_image_src( $aid, 'large' );
			$full = wp_get_attachment_image_src( $aid, 'full' );
			if ( ! $large || ! $full ) {
				continue;
			}
			$gallery_items[] = array(
				'large' => $large[0],
				'full'  => $full[0],
				'alt'   => get_post_meta( $aid, '_wp_attachment_image_alt', true ) ? get_post_meta( $aid, '_wp_attachment_image_alt', true ) : $plan_name . ' - house plan elevation',
			);
		}
	}

	if ( empty( $gallery_items ) && has_post_thumbnail( $post_id ) ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		$large = wp_get_attachment_image_src( $thumb_id, 'large' );
		$full = wp_get_attachment_image_src( $thumb_id, 'full' );
		if ( $large && $full ) {
			$gallery_items[] = array(
				'large' => $large[0],
				'full'  => $full[0],
				'alt'   => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : $plan_name . ' - featured exterior',
			);
		}
	}

	if ( empty( $gallery_items ) ) {
		$placeholder = 'https://via.placeholder.com/1200x900?text=House+Plan';
		$gallery_items[] = array(
			'large' => $placeholder,
			'full'  => $placeholder,
			'alt'   => $plan_name,
		);
	}

	$gallery_count = count( $gallery_items );
	while ( count( $gallery_items ) < 4 ) {
		$gallery_items[] = $gallery_items[0];
	}
	$gallery_main = $gallery_items[0];
	$gallery_side = array_slice( $gallery_items, 1, 3 );

	$sqft_int = (int) preg_replace( '/[^\d]/', '', $total_living_area );
	$footprint = trim( $width . ( $width && $depth ? ' × ' : '' ) . $depth );

	$desc_html = '';
	if ( $plan_description ) {
		$desc_html = wp_kses_post( $plan_description );
	} else {
		$desc_html .= '<p>This ' . esc_html( strtolower( $style ? $style : 'custom' ) ) . ' home plan is designed around approximately ' . esc_html( $total_living_area ? $total_living_area : '2,000+' ) . ' square feet of efficient, buildable living space with a warm, timeless feel.</p>';
		$desc_html .= '<p>The main level is crafted for everyday comfort with open sight lines, practical room flow, and ceiling treatments that make the home feel larger while keeping the footprint smart.</p>';
		$desc_html .= '<p>Outdoor living and feature moments are integrated from the start — porches, gathering spaces, and builder-friendly details that create value without wasting square footage.</p>';
		$desc_html .= '<p>Whether your lot is ' . esc_html( strtolower( $lot_style ? $lot_style : 'sloped or level' ) ) . ', this plan is made for real-world construction and families who want a home that lives as good as it looks.</p>';
	}

	$floor_plan_images = array();
	foreach ( array( 'floor_plan_1', 'floor_plan_2', 'floor_plan_3' ) as $fp_key ) {
		$img = $get( $fp_key, null );
		if ( is_array( $img ) && ! empty( $img['url'] ) ) {
			$floor_plan_images[] = $img;
		} elseif ( is_numeric( $img ) ) {
			$url = wp_get_attachment_image_url( (int) $img, 'large' );
			if ( $url ) {
				$floor_plan_images[] = array( 'url' => $url, 'alt' => $plan_name . ' floor plan' );
			}
		}
	}

	if ( empty( $faqs ) || ! is_array( $faqs ) ) {
		$faqs = array(
			array( 'question' => 'What formats are included with my purchase?', 'answer' => 'Choose PDF for immediate plan delivery, or CAD if your builder needs editable files.' ),
			array( 'question' => 'Can I request modifications?', 'answer' => 'Yes. MaxHousePlans handles modifications in-house so changes stay true to the design intent.' ),
			array( 'question' => 'Are these plans permit-ready?', 'answer' => 'Plans are designed for construction and permitting, though local engineering and code requirements may vary by jurisdiction.' ),
			array( 'question' => 'How quickly will I receive my plans?', 'answer' => 'Digital plan files are delivered instantly after checkout.' ),
			array( 'question' => 'Do you offer support during construction?', 'answer' => 'Yes. Our team is available to answer plan questions during permitting and throughout the build.' ),
		);
	}
	?>
	<div class="mhp mhp-plan--<?php echo esc_attr( $cat ); ?>">
		<div class="mhp-inner">
			<nav class="mhp-bc" aria-label="Breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span class="mhp-bc__sep">/</span>
				<a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">House Plans</a><span class="mhp-bc__sep">/</span>
				<a href="<?php echo esc_url( $bc_url ); ?>"><?php echo esc_html( $bc_cat ); ?></a><span class="mhp-bc__sep">/</span>
				<span><?php echo esc_html( $plan_name ); ?></span>
			</nav>

			<section class="mhp-hero">
				<div class="mhp-bento mhp-bento-grid">
					<a href="<?php echo esc_url( $gallery_main['full'] ); ?>" class="mhp-bento-main" data-foobox>
						<img src="<?php echo esc_url( $gallery_main['large'] ); ?>" alt="<?php echo esc_attr( $gallery_main['alt'] ); ?>" fetchpriority="high" loading="eager">
						<span class="mhp-bento-badge"><?php echo esc_html( $badge ); ?></span>
						<span class="mhp-bento-count">View All Photos (<?php echo (int) $gallery_count; ?>)</span>
					</a>
					<div class="mhp-bento-side">
						<?php foreach ( $gallery_side as $idx => $g ) : ?>
							<a href="<?php echo esc_url( $g['full'] ); ?>" data-foobox>
								<img src="<?php echo esc_url( $g['large'] ); ?>" alt="<?php echo esc_attr( $g['alt'] ? $g['alt'] : $plan_name . ' gallery image ' . ( $idx + 2 ) ); ?>" loading="lazy">
							</a>
						<?php endforeach; ?>
					</div>
				</div>

				<aside class="mhp-card" id="buy">
					<div class="mhp-card__head">
						<h1 class="mhp-card__name"><?php echo esc_html( $plan_name ); ?></h1>
						<div class="mhp-card__style"><?php echo esc_html( $style ? $style : 'Custom Home Plan' ); ?></div>
					</div>
					<div class="mhp-specs">
						<div class="mhp-spec-hero">
							<div class="mhp-spec-hero__val"><?php echo esc_html( $total_living_area ? $total_living_area : '2,000+' ); ?></div>
							<div class="mhp-spec-hero__lbl">Heated Living Area</div>
						</div>
						<div class="mhp-spec-cell"><div class="mhp-spec-cell__val"><?php echo esc_html( $bedrooms ? $bedrooms : '-' ); ?></div><div class="mhp-spec-cell__lbl">Bedrooms</div></div>
						<div class="mhp-spec-cell"><div class="mhp-spec-cell__val"><?php echo esc_html( $bathrooms ? $bathrooms : '-' ); ?></div><div class="mhp-spec-cell__lbl">Bathrooms</div></div>
						<div class="mhp-spec-cell"><div class="mhp-spec-cell__val"><?php echo esc_html( $stories ? $stories : '-' ); ?></div><div class="mhp-spec-cell__lbl">Stories</div></div>
						<div class="mhp-spec-cell"><div class="mhp-spec-cell__val"><?php echo esc_html( $footprint ? $footprint : ( $garage ? $garage : '-' ) ); ?></div><div class="mhp-spec-cell__lbl">Width × Depth</div></div>
					</div>

					<div class="mhp-card__body">
						<label class="mhp-price-opt mhp-active" id="mhpPdf" onclick="mhpSelect('pdf')">
							<input type="radio" checked aria-label="PDF plan package">
							<span class="mhp-price-opt__info"><span class="mhp-price-opt__name">PDF Set</span><span class="mhp-price-opt__desc">Instant digital download</span></span>
							<span class="mhp-price-opt__price"><?php echo esc_html( $price_fmt ); ?></span>
						</label>
						<label class="mhp-price-opt" id="mhpCad" onclick="mhpSelect('cad')">
							<input type="radio" aria-label="CAD plan package">
							<span class="mhp-price-opt__info"><span class="mhp-price-opt__name">CAD + PDF Set</span><span class="mhp-price-opt__desc">Editable CAD files included</span></span>
							<span class="mhp-price-opt__price"><?php echo esc_html( $cad_fmt ); ?></span>
						</label>

						<a class="mhp-btn-buy" href="<?php echo esc_url( $buy_href ); ?>">Buy This Plan</a>
						<div class="mhp-trust">
							<div class="mhp-trust__item">Secure Checkout</div>
							<div class="mhp-trust__item">Instant Download</div>
						</div>
					</div>
					<div class="mhp-card__foot"><a href="#contact" class="mhp-card__mod">Need changes? Request a modification →</a></div>
				</aside>
			</section>
		</div>

		<section class="mhp-authority mhp-reveal">
			<div class="mhp-inner mhp-authority__grid">
				<div class="mhp-auth-col">
					<div class="mhp-auth-col__name">Max Fulbright</div>
					<div class="mhp-auth-col__rule"></div>
					<div class="mhp-auth-col__role">Engineer · Builder · Designer</div>
					<div class="mhp-auth-col__yrs">25+ Years Experience</div>
					<div class="mhp-auth-col__quote">“Every plan designed with hands-on building knowledge — to save wasted space and cut construction costs.”</div>
				</div>
				<div class="mhp-auth-col">
					<div class="mhp-auth-col__overline">Why We're Different</div>
					<div class="mhp-auth-col__body">
						<p>Not a plan mill. Not a catalog. A real designer who has built what he draws. A family business that answers the phone and stands behind every plan.</p>
						<p>You're not buying a generic stock plan. You're buying 25 years of building experience — every dimension, every sight line, every detail considered.</p>
					</div>
				</div>
				<div class="mhp-auth-col">
					<div class="mhp-auth-col__overline">After You Purchase</div>
					<ol class="mhp-auth-process">
						<li><span class="mhp-auth-num">1</span><span class="mhp-auth-step"><span class="mhp-auth-step__name">Get Your Plans</span><span class="mhp-auth-step__detail">PDF or CAD delivered to your inbox instantly</span></span></li>
						<li><span class="mhp-auth-num">2</span><span class="mhp-auth-step"><span class="mhp-auth-step__name">Request Changes</span><span class="mhp-auth-step__detail">Modifications handled in-house, same team</span></span></li>
						<li><span class="mhp-auth-num">3</span><span class="mhp-auth-step"><span class="mhp-auth-step__name">Permit Support</span><span class="mhp-auth-step__detail">We answer questions through permitting</span></span></li>
						<li><span class="mhp-auth-num">4</span><span class="mhp-auth-step"><span class="mhp-auth-step__name">Build Support</span><span class="mhp-auth-step__detail">We're here when construction starts</span></span></li>
					</ol>
				</div>
			</div>
		</section>

		<section class="mhp-qsbar">
			<div class="mhp-inner">
				<div class="mhp-qsbar__row">
					<div class="mhp-qsbar__item"><div class="mhp-qsbar__val"><?php echo esc_html( $total_living_area ? $total_living_area : '—' ); ?></div><div class="mhp-qsbar__lbl">Heated Sq Ft</div></div>
					<div class="mhp-qsbar__item"><div class="mhp-qsbar__val"><?php echo esc_html( $footprint ? $footprint : '—' ); ?></div><div class="mhp-qsbar__lbl">Footprint</div></div>
					<div class="mhp-qsbar__item"><div class="mhp-qsbar__val"><?php echo esc_html( $main_floor ? $main_floor : '—' ); ?></div><div class="mhp-qsbar__lbl">Main Floor</div></div>
					<?php if ( $lower_floor ) : ?><div class="mhp-qsbar__item"><div class="mhp-qsbar__val"><?php echo esc_html( $lower_floor ); ?></div><div class="mhp-qsbar__lbl">Lower Level</div></div><?php endif; ?>
					<div class="mhp-qsbar__item"><div class="mhp-qsbar__val"><?php echo esc_html( $price_fmt ); ?></div><div class="mhp-qsbar__lbl">From</div></div>
				</div>
			</div>
		</section>

		<section class="mhp-section mhp-reveal">
			<div class="mhp-inner mhp-desc-grid">
				<div class="mhp-desc__body">
					<div class="mhp-overline">The Plan</div>
					<h2 class="mhp-h2"><?php echo esc_html( $plan_name . ( $style ? ' — ' . $style : '' ) ); ?></h2>
					<?php echo $desc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<aside class="mhp-highlights">
					<div class="mhp-highlights__title">Plan Highlights</div>
					<?php
					$highlights = array(
						'Style'       => $style ? $style : 'Timeless custom design',
						'Outdoor'     => $outdoor ? $outdoor : 'Porch and outdoor living ready',
						'Lot'         => $lot_style ? $lot_style : 'Works well on versatile lots',
						'Ceilings'    => $ceiling ? $ceiling : 'Designed for openness and volume',
						'Bonus Rooms' => $additional_rooms ? $additional_rooms : 'Flexible additional spaces included',
						'Garage'      => $garage ? $garage : 'No Garage Included',
					);
					foreach ( $highlights as $k => $v ) :
						?>
						<div class="mhp-hi"><span class="mhp-hi__icon"></span><span><strong class="mhp-hi__strong"><?php echo esc_html( $k ); ?></strong><span class="mhp-hi__detail"><?php echo esc_html( $v ); ?></span></span></div>
					<?php endforeach; ?>
				</aside>
			</div>
		</section>

		<section class="mhp-section mhp-section--dark mhp-reveal">
			<div class="mhp-inner">
				<header class="mhp-sec-head mhp-sec-head--center">
					<div class="mhp-overline">Specifications</div>
					<h2 class="mhp-h2">Complete Plan Details</h2>
					<p class="mhp-subtitle">Everything your builder needs at a glance — organized for faster planning and cleaner estimating.</p>
				</header>
				<div class="mhp-spec-cards">
					<div class="mhp-spec-card"><h3 class="mhp-spec-card__title">Living Area</h3>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Total Living Area</span><span class="mhp-spec-row__val"><?php echo esc_html( $total_living_area ? $total_living_area : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Main Floor</span><span class="mhp-spec-row__val"><?php echo esc_html( $main_floor ? $main_floor : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Upper Floor</span><span class="mhp-spec-row__val"><?php echo esc_html( $upper_floor ? $upper_floor : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Lower Floor</span><span class="mhp-spec-row__val"><?php echo esc_html( $lower_floor ? $lower_floor : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Width × Depth</span><span class="mhp-spec-row__val"><?php echo esc_html( $footprint ? $footprint : '—' ); ?></span></div>
					</div>
					<div class="mhp-spec-card"><h3 class="mhp-spec-card__title">House Features</h3>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Bedrooms</span><span class="mhp-spec-row__val"><?php echo esc_html( $bedrooms ? $bedrooms : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Bathrooms</span><span class="mhp-spec-row__val"><?php echo esc_html( $bathrooms ? $bathrooms : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Stories</span><span class="mhp-spec-row__val"><?php echo esc_html( $stories ? $stories : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Garage</span><span class="mhp-spec-row__val"><?php echo esc_html( $garage ? $garage : 'None' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Outdoor Features</span><span class="mhp-spec-row__val"><?php echo esc_html( $outdoor ? $outdoor : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Additional Rooms</span><span class="mhp-spec-row__val"><?php echo esc_html( $additional_rooms ? $additional_rooms : '—' ); ?></span></div>
					</div>
					<div class="mhp-spec-card"><h3 class="mhp-spec-card__title">Construction</h3>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Roof</span><span class="mhp-spec-row__val"><?php echo esc_html( $roof ? $roof : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Ceiling</span><span class="mhp-spec-row__val"><?php echo esc_html( $ceiling ? $ceiling : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Exterior</span><span class="mhp-spec-row__val"><?php echo esc_html( $exterior ? $exterior : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Lot Type</span><span class="mhp-spec-row__val"><?php echo esc_html( $lot_style ? $lot_style : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Style</span><span class="mhp-spec-row__val"><?php echo esc_html( $style ? $style : '—' ); ?></span></div>
						<div class="mhp-spec-row"><span class="mhp-spec-row__lbl">Other Features</span><span class="mhp-spec-row__val"><?php echo esc_html( $other_features ? $other_features : '—' ); ?></span></div>
					</div>
				</div>
			</div>
		</section>

		<section class="mhp-section mhp-reveal">
			<div class="mhp-inner">
				<header class="mhp-sec-head"><div class="mhp-overline">Explore the Layout</div><h2 class="mhp-h2">Floor Plans</h2></header>
				<?php if ( $floor_plans_content ) : ?>
					<div class="mhp-desc__body"><?php echo wp_kses_post( $floor_plans_content ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $floor_plan_images ) ) : ?>
					<div class="mhp-related-grid">
						<?php foreach ( $floor_plan_images as $img ) : ?>
							<div class="mhp-rel-card"><div class="mhp-rel-card__img"><img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $img['alt'] ) ? $img['alt'] : $plan_name . ' floor plan image' ); ?>" loading="lazy"></div></div>
						<?php endforeach; ?>
					</div>
				<?php elseif ( ! $floor_plans_content ) : ?>
					<p style="text-align:center;font-style:italic;">Complete floor plans for all levels are included in your plan set.</p>
				<?php endif; ?>
			</div>
		</section>

		<section class="mhp-estimator mhp-reveal">
			<div class="mhp-inner">
				<header class="mhp-sec-head"><div class="mhp-overline">Cost to Build Estimator</div><h2 class="mhp-h2">Estimate Your Build Budget</h2><p class="mhp-subtitle">Use this quick estimator to understand likely build ranges in your region.</p></header>
				<div class="mhp-est-grid">
					<div>
						<p class="mhp-est-desc">Construction pricing varies based on region, finish selections, site conditions, and market labor. This estimator gives a realistic planning range based on your plan size and desired finish level.</p>
						<p class="mhp-est-disclaimer">Disclaimer: Site work, utilities, permits, and local engineering are not included and vary by location.</p>
					</div>
					<div class="mhp-calc-box">
						<label class="mhp-calc-label" for="mhpRegion">Region</label>
						<select id="mhpRegion" class="mhp-calc-select" onchange="mhpCalc()">
							<option value="southeast">Southeast</option><option value="south_central">South Central</option><option value="midwest">Midwest</option><option value="mountain_west">Mountain West</option><option value="northeast">Northeast</option><option value="pacific_nw">Pacific Northwest</option><option value="west_coast">West Coast</option>
						</select>
						<div class="mhp-range-wrap">
							<label class="mhp-calc-label" for="mhpFinish">Finish Level</label>
							<input id="mhpFinish" class="mhp-range" type="range" min="1" max="4" value="2" oninput="mhpCalc()">
							<div class="mhp-range-labels"><span>Standard</span><span>Premium</span></div>
							<div class="mhp-finish-lbl" id="mhpFinishLabel">Mid-Range Build</div>
						</div>
						<div class="mhp-est-result">
							<div class="mhp-est-result__lbl">Estimated Range</div>
							<div class="mhp-est-result__val" id="mhpEstRange">—</div>
							<div class="mhp-est-result__psf" id="mhpEstPsf">—</div>
							<div class="mhp-est-breakdown"><div><div class="mhp-est-breakdown__lbl">Materials</div><div class="mhp-est-breakdown__val" id="mhpMat">—</div></div><div><div class="mhp-est-breakdown__lbl">Labor</div><div class="mhp-est-breakdown__val" id="mhpLab">—</div></div></div>
						</div>
					</div>
				</div>
				<span id="mhpSqft" data-v="<?php echo (int) $sqft_int; ?>" hidden></span>
			</div>
		</section>

		<section class="mhp-section mhp-section--alt mhp-reveal">
			<div class="mhp-inner">
				<header class="mhp-sec-head mhp-sec-head--center"><div class="mhp-overline">Plan Set</div><h2 class="mhp-h2">What's Included</h2></header>
				<div class="mhp-included">
					<div class="mhp-inc-card"><div class="mhp-inc-card__icon"></div><div class="mhp-inc-card__title">Elevations</div><div class="mhp-inc-card__desc">All exterior views with critical dimensions and visual references.</div></div>
					<div class="mhp-inc-card"><div class="mhp-inc-card__icon"></div><div class="mhp-inc-card__title">Floor Plans</div><div class="mhp-inc-card__desc">Detailed layout plans for each level, room flow, and structural intent.</div></div>
					<div class="mhp-inc-card"><div class="mhp-inc-card__icon"></div><div class="mhp-inc-card__title">Foundation Plan</div><div class="mhp-inc-card__desc">Foundation strategy aligned with buildability and efficiency.</div></div>
					<div class="mhp-inc-card"><div class="mhp-inc-card__icon"></div><div class="mhp-inc-card__title">Roof Plan</div><div class="mhp-inc-card__desc">Roof structure and geometry for accurate framing and estimating.</div></div>
				</div>
			</div>
		</section>

		<?php if ( ! empty( $related_plans ) && is_array( $related_plans ) ) : ?>
		<section class="mhp-section mhp-reveal">
			<div class="mhp-inner">
				<header class="mhp-sec-head"><div class="mhp-overline">You May Also Like</div><h2 class="mhp-h2">Related Plans</h2></header>
				<div class="mhp-related-grid">
					<?php foreach ( $related_plans as $rel ) : ?>
						<?php $rid = is_object( $rel ) ? $rel->ID : (int) $rel; ?>
						<article class="mhp-rel-card">
							<a class="mhp-rel-card__img" href="<?php echo esc_url( get_permalink( $rid ) ); ?>"><?php echo get_the_post_thumbnail( $rid, 'medium_large', array( 'loading' => 'lazy' ) ); ?></a>
							<div class="mhp-rel-card__body">
								<div class="mhp-rel-card__name"><?php echo esc_html( get_the_title( $rid ) ); ?></div>
								<div class="mhp-rel-card__specs"><?php echo esc_html( get_field( 'total_living_area', $rid ) ? get_field( 'total_living_area', $rid ) : 'House Plan' ); ?></div>
								<a class="mhp-rel-card__link" href="<?php echo esc_url( get_permalink( $rid ) ); ?>">View Plan →</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<section class="mhp-section mhp-section--alt mhp-reveal">
			<div class="mhp-inner">
				<header class="mhp-sec-head mhp-sec-head--center"><div class="mhp-overline">FAQ</div><h2 class="mhp-h2">Frequently Asked Questions</h2></header>
				<div class="mhp-faq-list">
					<?php foreach ( $faqs as $i => $faq ) :
						$q = isset( $faq['question'] ) ? $faq['question'] : ( isset( $faq['q'] ) ? $faq['q'] : '' );
						$a = isset( $faq['answer'] ) ? $faq['answer'] : ( isset( $faq['a'] ) ? $faq['a'] : '' );
						?>
						<div class="mhp-faq-item<?php echo 0 === $i ? ' mhp-faq--open' : ''; ?>">
							<button class="mhp-faq-btn" onclick="mhpFaq(this)" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>"><span><?php echo esc_html( $q ); ?></span><span class="mhp-faq-icon">+</span></button>
							<div class="mhp-faq-answer" style="max-height:<?php echo 0 === $i ? '220px' : '0'; ?>;"><div class="mhp-faq-inner"><?php echo wp_kses_post( wpautop( $a ) ); ?></div></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="mhp-cta mhp-reveal" id="contact">
			<div class="mhp-inner">
				<h2 class="mhp-cta__title">Ready to Build Your Dream Home?</h2>
				<p class="mhp-cta__text">Start with <?php echo esc_html( $plan_name ); ?> and get expert-backed design support from a team that actually builds what it designs.</p>
				<div class="mhp-cta__btns">
					<a href="<?php echo esc_url( $buy_href ); ?>" class="mhp-btn mhp-btn--gold">Buy This Plan</a>
					<a href="#contact" class="mhp-btn mhp-btn--outline">Request Modifications</a>
					<a href="tel:+1" class="mhp-btn mhp-btn--outline">Talk to Our Team</a>
				</div>
			</div>
		</section>

		<div class="mhp-mobile-bar">
			<div class="mhp-mobile-bar__inner">
				<div class="mhp-mobile-bar__name"><?php echo esc_html( $plan_name ); ?></div>
				<div class="mhp-mobile-bar__price"><?php echo esc_html( $price_fmt ); ?></div>
				<a class="mhp-btn-buy" href="<?php echo esc_url( $buy_href ); ?>">Buy</a>
			</div>
		</div>
	</div>

	<script>
	(function(){
	  if('IntersectionObserver' in window){
	    var io=new IntersectionObserver(function(entries){
	      entries.forEach(function(e){ if(e.isIntersecting){e.target.classList.add('mhp-revealed');io.unobserve(e.target);} });
	    },{threshold:0.08,rootMargin:'0px 0px -30px 0px'});
	    document.querySelectorAll('.mhp-reveal').forEach(function(el){io.observe(el);});
	  } else {
	    document.querySelectorAll('.mhp-reveal').forEach(function(el){el.classList.add('mhp-revealed');});
	  }
	  window.mhpSelect=function(f){
	    document.querySelectorAll('.mhp-price-opt').forEach(function(o){o.classList.remove('mhp-active');});
	    var el=document.getElementById(f==='pdf'?'mhpPdf':'mhpCad'); if(el) el.classList.add('mhp-active');
	  };
	  window.mhpFaq=function(btn){
	    var item=btn.parentElement,open=item.classList.contains('mhp-faq--open');
	    document.querySelectorAll('.mhp-faq-item').forEach(function(f){
	      f.classList.remove('mhp-faq--open');
	      var q=f.querySelector('.mhp-faq-btn'); if(q) q.setAttribute('aria-expanded','false');
	      var a=f.querySelector('.mhp-faq-answer'); if(a) a.style.maxHeight='0';
	    });
	    if(!open){
	      item.classList.add('mhp-faq--open'); btn.setAttribute('aria-expanded','true');
	      var ans=item.querySelector('.mhp-faq-answer'); if(ans) ans.style.maxHeight=ans.scrollHeight+'px';
	    }
	  };
	  var sqftEl=document.getElementById('mhpSqft');
	  var SQ=sqftEl?parseInt(sqftEl.dataset.v,10):0;
	  var R={southeast:{lo:155,hi:210},south_central:{lo:145,hi:200},midwest:{lo:150,hi:205},mountain_west:{lo:180,hi:245},northeast:{lo:205,hi:280},pacific_nw:{lo:190,hi:260},west_coast:{lo:225,hi:315}};
	  var F={1:{f:0.85,l:'Standard'},2:{f:1.0,l:'Mid-Range'},3:{f:1.3,l:'Custom'},4:{f:1.65,l:'Premium Custom'}};
	  window.mhpCalc=function(){
	    var rEl=document.getElementById('mhpRegion'),fEl=document.getElementById('mhpFinish');
	    if(!rEl||!fEl||!SQ) return;
	    var r=R[rEl.value],f=F[parseInt(fEl.value,10)];
	    var lo=Math.round(r.lo*f.f)*SQ,hi=Math.round(r.hi*f.f)*SQ;
	    var s=function(id,v){var e=document.getElementById(id);if(e)e.textContent=v;};
	    s('mhpFinishLabel',f.l+' Build');
	    s('mhpEstRange','$'+lo.toLocaleString()+' – $'+hi.toLocaleString());
	    s('mhpEstPsf','$'+Math.round(r.lo*f.f)+' – $'+Math.round(r.hi*f.f)+' per sq ft');
	    s('mhpMat','$'+Math.round(lo*.45/1000)+'K – $'+Math.round(hi*.45/1000)+'K');
	    s('mhpLab','$'+Math.round(lo*.35/1000)+'K – $'+Math.round(hi*.35/1000)+'K');
	  };
	  document.readyState==='loading'?document.addEventListener('DOMContentLoaded',mhpCalc):mhpCalc();
	})();
	</script>
	<?php
}

/**
 * Schema output.
 */
function mhp_v4_schema() {
	if ( ! is_singular( 'plans' ) ) {
		return;
	}
	$post_id = get_queried_object_id();
	$plan_name = function_exists( 'get_field' ) && get_field( 'plan_name', $post_id ) ? get_field( 'plan_name', $post_id ) : get_the_title( $post_id );
	$desc = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 28, '…' );
	$image = get_the_post_thumbnail_url( $post_id, 'large' );
	$style = function_exists( 'get_field' ) ? (string) get_field( 'style', $post_id ) : '';
	$price = function_exists( 'get_field' ) ? get_field( 'price', $post_id ) : '';
	$price_num = is_numeric( $price ) ? (float) $price : 1195;
	$cad = $price_num * 1.26;
	$faqs = function_exists( 'get_field' ) ? get_field( 'faqs', $post_id ) : array();

	$sl = strtolower( $style );
	if ( str_contains( $sl, 'mountain' ) || str_contains( $sl, 'lake' ) ) {
		$bc_cat = 'Mountain House Plans';
		$bc_url = home_url( '/home-plans/mountain-house-plans/' );
	} elseif ( str_contains( $sl, 'farmhouse' ) || str_contains( $sl, 'southern' ) || str_contains( $sl, 'country' ) ) {
		$bc_cat = 'Farmhouse House Plans';
		$bc_url = home_url( '/home-plans/farmhouse-house-plans/' );
	} elseif ( str_contains( $sl, 'cottage' ) || str_contains( $sl, 'cabin' ) || str_contains( $sl, 'bungalow' ) ) {
		$bc_cat = 'Cottage House Plans';
		$bc_url = home_url( '/home-plans/cottage-house-plans/' );
	} else {
		$bc_cat = 'House Plans';
		$bc_url = home_url( '/house-plans/' );
	}

	$specs = array(
		'total_living_area' => function_exists( 'get_field' ) ? (string) get_field( 'total_living_area', $post_id ) : '',
		'bedrooms'          => function_exists( 'get_field' ) ? (string) get_field( 'bedrooms', $post_id ) : '',
		'bathrooms'         => function_exists( 'get_field' ) ? (string) get_field( 'bathrooms', $post_id ) : '',
		'stories'           => function_exists( 'get_field' ) ? (string) get_field( 'stories', $post_id ) : '',
		'width'             => function_exists( 'get_field' ) ? (string) get_field( 'width', $post_id ) : '',
		'depth'             => function_exists( 'get_field' ) ? (string) get_field( 'depth', $post_id ) : '',
		'garage'            => function_exists( 'get_field' ) ? (string) get_field( 'garage', $post_id ) : '',
		'style'             => $style,
	);

	$product = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Product',
		'name'     => $plan_name,
		'description' => $desc,
		'image'    => $image ? array( $image ) : array(),
		'offers'   => array(
			array(
				'@type' => 'Offer',
				'priceCurrency' => 'USD',
				'price' => number_format( $price_num, 2, '.', '' ),
				'name'  => 'PDF Plan Set',
				'availability' => 'https://schema.org/InStock',
				'url' => get_permalink( $post_id ),
			),
			array(
				'@type' => 'Offer',
				'priceCurrency' => 'USD',
				'price' => number_format( $cad, 2, '.', '' ),
				'name'  => 'CAD + PDF Plan Set',
				'availability' => 'https://schema.org/InStock',
				'url' => get_permalink( $post_id ),
			),
		),
		'additionalProperty' => array_map(
			static function ( $k, $v ) {
				return array( '@type' => 'PropertyValue', 'name' => $k, 'value' => $v ? $v : 'N/A' );
			},
			array_keys( $specs ),
			array_values( $specs )
		),
	);

	$breadcrumb = array(
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => array(
			array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
			array( '@type' => 'ListItem', 'position' => 2, 'name' => 'House Plans', 'item' => home_url( '/house-plans/' ) ),
			array( '@type' => 'ListItem', 'position' => 3, 'name' => $bc_cat, 'item' => $bc_url ),
			array( '@type' => 'ListItem', 'position' => 4, 'name' => $plan_name, 'item' => get_permalink( $post_id ) ),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $product ) . '</script>';
	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb ) . '</script>';

	if ( ! empty( $faqs ) && is_array( $faqs ) ) {
		$faq_schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'FAQPage',
			'mainEntity' => array(),
		);
		foreach ( $faqs as $f ) {
			$q = isset( $f['question'] ) ? wp_strip_all_tags( $f['question'] ) : '';
			$a = isset( $f['answer'] ) ? wp_strip_all_tags( $f['answer'] ) : '';
			if ( $q && $a ) {
				$faq_schema['mainEntity'][] = array(
					'@type' => 'Question',
					'name' => $q,
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text' => $a,
					),
				);
			}
		}
		if ( ! empty( $faq_schema['mainEntity'] ) ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema ) . '</script>';
		}
	}
}

genesis();
