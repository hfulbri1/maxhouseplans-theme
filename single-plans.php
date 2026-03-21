<?php
/**
 * Universal Single Plan Template (v3.1 — 2026)
 *
 * @package MaxHousePlans
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('genesis_pre_get_option_site_layout', function ($layout) {
    if (is_singular('plans')) {
        return 'full-width-content';
    }

    return $layout;
});

if (!function_exists('mhp_get_plan_field')) {
    /**
     * Safe ACF getter with fallback.
     *
     * @param string $key     Field key.
     * @param int    $post_id Post ID.
     * @param mixed  $default Default.
     *
     * @return mixed
     */
    function mhp_get_plan_field($key, $post_id, $default = '') {
        if (function_exists('get_field')) {
            $value = get_field($key, $post_id);
            if ($value !== null && $value !== false && $value !== '') {
                return $value;
            }
        }

        return $default;
    }
}

if (!function_exists('mhp_plan_detect_style_category')) {
    /**
     * Detect style category + labels from style string.
     *
     * @param string $style Style field.
     *
     * @return array<string,string>
     */
    function mhp_plan_detect_style_category($style) {
        $style_lower = strtolower((string) $style);
        if (strpos($style_lower, 'mountain') !== false || strpos($style_lower, 'lake') !== false) {
            return array(
                'style_cat' => 'mountain',
                'badge'     => 'Mountain House Plan',
                'bc_cat'    => 'Mountain House Plans',
                'bc_url'    => home_url('/home-plans/mountain-house-plans/'),
            );
        }
        if (strpos($style_lower, 'farmhouse') !== false || strpos($style_lower, 'southern') !== false || strpos($style_lower, 'country') !== false) {
            return array(
                'style_cat' => 'farmhouse',
                'badge'     => 'Farmhouse Plan',
                'bc_cat'    => 'Farmhouse House Plans',
                'bc_url'    => home_url('/home-plans/farmhouse-house-plans/'),
            );
        }
        if (strpos($style_lower, 'cottage') !== false || strpos($style_lower, 'cabin') !== false || strpos($style_lower, 'bungalow') !== false) {
            return array(
                'style_cat' => 'cottage',
                'badge'     => 'Cottage Plan',
                'bc_cat'    => 'Cottage House Plans',
                'bc_url'    => home_url('/home-plans/cottage-house-plans/'),
            );
        }

        return array(
            'style_cat' => 'craftsman',
            'badge'     => 'Craftsman Plan',
            'bc_cat'    => 'House Plans',
            'bc_url'    => home_url('/house-plans/'),
        );
    }
}

if (!function_exists('mhp_money_0')) {
    /**
     * Format no-decimal USD.
     *
     * @param float $value Value.
     *
     * @return string
     */
    function mhp_money_0($value) {
        return '$' . number_format((float) $value, 0, '.', ',');
    }
}

if (!function_exists('mhp_plan_schema_output')) {
    /**
     * Product + Breadcrumb + FAQ schema.
     */
    function mhp_plan_schema_output() {
        if (!is_singular('plans')) {
            return;
        }

        $post_id   = get_the_ID();
        $plan_name = (string) mhp_get_plan_field('plan_name', $post_id, get_the_title($post_id));
        $style     = (string) mhp_get_plan_field('style', $post_id, '');
        $meta      = mhp_plan_detect_style_category($style);

        $price     = mhp_get_plan_field('price', $post_id, 1195);
        $price_num = is_numeric($price) ? (float) $price : 1195;

        $sqft      = (string) mhp_get_plan_field('total_living_area', $post_id, '');
        $beds      = (string) mhp_get_plan_field('bedrooms', $post_id, '');
        $baths     = (string) mhp_get_plan_field('bathrooms', $post_id, '');
        $desc_raw  = (string) mhp_get_plan_field('plan_description', $post_id, '');
        $desc      = wp_strip_all_tags($desc_raw);
        if ($desc === '') {
            $desc = sprintf('%s is a builder-minded %s plan designed by Max Fulbright to reduce wasted space and build cost.', $plan_name, $style !== '' ? $style : 'house');
        }

        $faqs = mhp_get_plan_field('faqs', $post_id, array());
        $faq_entities = array();
        if (is_array($faqs)) {
            foreach ($faqs as $faq) {
                $q = isset($faq['question']) ? trim((string) $faq['question']) : '';
                $a = isset($faq['answer']) ? trim((string) $faq['answer']) : '';
                if ($q === '' || $a === '') {
                    continue;
                }
                $faq_entities[] = array(
                    '@type' => 'Question',
                    'name'  => wp_strip_all_tags($q),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => wp_strip_all_tags($a),
                    ),
                );
            }
        }

        if (empty($faq_entities)) {
            $faq_entities = array(
                array('@type' => 'Question', 'name' => 'What is included in this plan package?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Each plan set includes core construction drawings such as floor plans, elevations, foundation details, and roof information.')),
                array('@type' => 'Question', 'name' => 'Can this plan be modified?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Yes. We provide in-house modification services to tailor the plan to your lot, lifestyle, and local requirements.')),
                array('@type' => 'Question', 'name' => 'How do I receive my plan after purchase?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Digital plan files are delivered after checkout confirmation.')),
                array('@type' => 'Question', 'name' => 'Do I need local engineering for permits?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Permit requirements vary by jurisdiction. Please confirm requirements with your local building department.')),
                array('@type' => 'Question', 'name' => 'Do you provide support during construction?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Yes. Our team supports clients through permitting questions and build-stage clarifications.')),
            );
        }

        $schema_product = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => $plan_name,
            'description' => $desc,
            'url'      => get_permalink($post_id),
            'image'    => get_the_post_thumbnail_url($post_id, 'large') ? array(get_the_post_thumbnail_url($post_id, 'large')) : array(),
            'brand'    => array('@type' => 'Brand', 'name' => 'MaxHousePlans'),
            'offers'   => array(
                array('@type' => 'Offer', 'name' => 'PDF Plan Set', 'priceCurrency' => 'USD', 'price' => number_format($price_num, 2, '.', ''), 'availability' => 'https://schema.org/InStock', 'url' => get_permalink($post_id)),
                array('@type' => 'Offer', 'name' => 'CAD + PDF Plan Set', 'priceCurrency' => 'USD', 'price' => number_format($price_num * 1.26, 2, '.', ''), 'availability' => 'https://schema.org/InStock', 'url' => get_permalink($post_id)),
            ),
            'additionalProperty' => array_filter(array(
                $sqft !== '' ? array('@type' => 'PropertyValue', 'name' => 'Heated Sq Ft', 'value' => $sqft) : null,
                $beds !== '' ? array('@type' => 'PropertyValue', 'name' => 'Bedrooms', 'value' => $beds) : null,
                $baths !== '' ? array('@type' => 'PropertyValue', 'name' => 'Bathrooms', 'value' => $baths) : null,
            )),
        );

        $schema_breadcrumb = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')),
                array('@type' => 'ListItem', 'position' => 2, 'name' => 'House Plans', 'item' => home_url('/house-plans/')),
                array('@type' => 'ListItem', 'position' => 3, 'name' => $meta['bc_cat'], 'item' => $meta['bc_url']),
                array('@type' => 'ListItem', 'position' => 4, 'name' => $plan_name, 'item' => get_permalink($post_id)),
            ),
        );

        $schema_faq = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq_entities,
        );

        echo "\n<script type=\"application/ld+json\">" . wp_json_encode($schema_product, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
        echo "<script type=\"application/ld+json\">" . wp_json_encode($schema_breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
        echo "<script type=\"application/ld+json\">" . wp_json_encode($schema_faq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
    }
}
add_action('wp_head', 'mhp_plan_schema_output', 40);

remove_action('genesis_loop', 'genesis_do_loop');

/**
 * Render plan template.
 */
function mhp_render_single_plan_v3() {
    if (!have_posts()) {
        return;
    }

    while (have_posts()) {
        the_post();

        $post_id = get_the_ID();

        $plan_name        = (string) mhp_get_plan_field('plan_name', $post_id, get_the_title($post_id));
        $total_living     = (string) mhp_get_plan_field('total_living_area', $post_id, '');
        $main_floor       = (string) mhp_get_plan_field('main_floor', $post_id, '');
        $upper_floor      = (string) mhp_get_plan_field('upper_floor', $post_id, '');
        $lower_floor      = (string) mhp_get_plan_field('lower_floor', $post_id, '');
        $bedrooms         = (string) mhp_get_plan_field('bedrooms', $post_id, '');
        $bathrooms        = (string) mhp_get_plan_field('bathrooms', $post_id, '');
        $stories          = (string) mhp_get_plan_field('stories', $post_id, '');
        $width            = (string) mhp_get_plan_field('width', $post_id, '');
        $depth            = (string) mhp_get_plan_field('depth', $post_id, '');
        $garage           = (string) mhp_get_plan_field('garage', $post_id, '');
        $style            = (string) mhp_get_plan_field('style', $post_id, '');
        $outdoor          = (string) mhp_get_plan_field('outdoor', $post_id, '');
        $roof             = (string) mhp_get_plan_field('roof', $post_id, '');
        $ceiling          = (string) mhp_get_plan_field('ceiling', $post_id, '');
        $exterior         = (string) mhp_get_plan_field('exterior', $post_id, '');
        $additional_rooms = (string) mhp_get_plan_field('additional_rooms', $post_id, '');
        $other_features   = (string) mhp_get_plan_field('other_features', $post_id, '');
        $lot_style        = (string) mhp_get_plan_field('lot_style', $post_id, '');
        $plan_description = (string) mhp_get_plan_field('plan_description', $post_id, '');
        $floor_plans      = (string) mhp_get_plan_field('floor_plans', $post_id, '');
        $paypal           = (string) mhp_get_plan_field('paypal', $post_id, '');
        $price            = mhp_get_plan_field('price', $post_id, 1195);
        $related_plans    = mhp_get_plan_field('related_plans', $post_id, array());
        $faqs             = mhp_get_plan_field('faqs', $post_id, array());

        $meta      = mhp_plan_detect_style_category($style);
        $style_cat = $meta['style_cat'];
        $badge     = $meta['badge'];
        $bc_cat    = $meta['bc_cat'];
        $bc_url    = $meta['bc_url'];

        $price_num = is_numeric($price) ? (float) $price : 1195;
        $price_fmt = '$' . number_format($price_num, 0, '.', ',');
        $cad_price = '$' . number_format($price_num * 1.26, 0, '.', ',');

        $paypal_url = '';
        if ($paypal && preg_match('/name=["\']hosted_button_id["\']\s+value=["\']([^"\']+)["\']/', $paypal, $m)) {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . urlencode($m[1]);
        }
        $buy_href = $paypal_url ?: (get_permalink() . '#contact');

        $sqft_int      = (int) preg_replace('/[^0-9]/', '', $total_living);
        $sqft_display  = $sqft_int > 0 ? number_format($sqft_int) : '—';
        $main_display  = is_numeric($main_floor) ? number_format((float) $main_floor) : ($main_floor !== '' ? $main_floor : '—');
        $upper_display = is_numeric($upper_floor) ? number_format((float) $upper_floor) : $upper_floor;
        $lower_display = is_numeric($lower_floor) ? number_format((float) $lower_floor) : $lower_floor;

        $post_content = (string) get_post_field('post_content', $post_id);
        $gallery_html = '';
        if (preg_match('/\[gallery[^\]]*\]/', $post_content, $gallery_match)) {
            $gallery_html = do_shortcode($gallery_match[0]);
        }

        $foundation = $lower_floor !== '' ? 'Walkout Basement' : 'Slab / Crawl (per local requirements)';

        if (!is_array($faqs) || empty($faqs)) {
            $faqs = array(
                array('question' => 'What files do I receive after purchase?', 'answer' => 'Your plan package includes the core drawing set needed for pricing and construction planning, including floor plans, elevations, foundation information, and roof details.'),
                array('question' => 'Can this plan be modified for my family or lot?', 'answer' => 'Yes. We provide in-house modification services and can tailor this design to your lot constraints, local preferences, and lifestyle needs.'),
                array('question' => 'How long does delivery take?', 'answer' => 'Digital plans are delivered after payment confirmation. If you need help selecting the right format, we can guide you before purchase.'),
                array('question' => 'Will this plan work for permitting in my area?', 'answer' => 'Building requirements vary by jurisdiction. We recommend confirming local requirements early and coordinating with your local engineer where required.'),
                array('question' => 'Do you offer support during construction?', 'answer' => 'Yes. We answer plan questions and support you through permitting and build-stage clarifications.'),
            );
        }

        if ($plan_description === '') {
            $style_human = $style !== '' ? $style : ucfirst($style_cat);
            $plan_description =
                '<p>' . esc_html($plan_name) . ' is a builder-minded ' . esc_html($style_human) . ' home plan designed to feel larger than its footprint. At approximately ' . esc_html($sqft_display) . ' heated square feet, every area was shaped to reduce wasted space and keep construction straightforward.</p>' .
                '<p>The main level is organized for daily life: intuitive circulation, practical sightlines, and living spaces that connect naturally. With ' . esc_html($bedrooms !== '' ? $bedrooms : 'well-planned') . ' bedrooms and ' . esc_html($bathrooms !== '' ? $bathrooms : 'comfortable') . ' baths, the layout balances privacy and shared family space.</p>' .
                '<p>Signature details like ' . esc_html($outdoor !== '' ? $outdoor : 'purposeful outdoor living') . ', ' . esc_html($ceiling !== '' ? $ceiling : 'well-proportioned ceilings') . ', and ' . esc_html($additional_rooms !== '' ? $additional_rooms : 'flexible bonus spaces') . ' bring personality while staying practical for real-world building and long-term value.</p>' .
                '<p>This plan is ideal for homeowners looking for a home that feels custom without unnecessary complexity—especially on ' . esc_html($lot_style !== '' ? $lot_style : 'a variety of lot conditions') . '. It is architecture shaped by hands-on building experience, not catalog filler.</p>';
        }

        $floor_images = array();
        foreach (array('floor_plan_1', 'floor_plan_2', 'floor_plan_3') as $field_key) {
            $img = mhp_get_plan_field($field_key, $post_id, null);
            if (is_array($img) && !empty($img['url'])) {
                $floor_images[] = array(
                    'url' => (string) $img['url'],
                    'alt' => !empty($img['alt']) ? (string) $img['alt'] : ($plan_name . ' floor plan'),
                );
            } elseif (is_numeric($img)) {
                $url = wp_get_attachment_image_url((int) $img, 'large');
                if ($url) {
                    $floor_images[] = array('url' => $url, 'alt' => $plan_name . ' floor plan');
                }
            }
        }
        ?>

        <article class="mhp-plan-wrap mhp-style--<?php echo esc_attr($style_cat); ?>" data-style-cat="<?php echo esc_attr($style_cat); ?>">
            <div class="mhp-container">
                <nav class="mhp-breadcrumbs" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    <span>›</span>
                    <a href="<?php echo esc_url(home_url('/house-plans/')); ?>">House Plans</a>
                    <span>›</span>
                    <a href="<?php echo esc_url($bc_url); ?>"><?php echo esc_html($bc_cat); ?></a>
                    <span>›</span>
                    <span aria-current="page"><?php echo esc_html($plan_name); ?></span>
                </nav>

                <section class="mhp-plan-hero">
                    <div class="mhp-plan-hero__grid">
                        <div class="mhp-hero-image">
                            <div class="mhp-hero-image__main">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('full', array(
                                        'loading' => 'eager',
                                        'fetchpriority' => 'high',
                                        'decoding' => 'async',
                                        'alt' => esc_attr($plan_name . ' house plan — ' . ($style !== '' ? $style : $badge)),
                                    ));
                                }
                                ?>
                                <div class="mhp-hero-image__badge"><?php echo esc_html($badge); ?></div>
                            </div>

                            <?php if ($gallery_html !== '') : ?>
                                <div class="mhp-gallery-grid">
                                    <?php echo do_shortcode($gallery_match[0]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside class="mhp-purchase-card">
                            <header class="mhp-purchase-card__header">
                                <h1 class="mhp-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
                                <p class="mhp-purchase-card__style"><?php echo esc_html($style !== '' ? $style : $badge); ?></p>

                                <div class="mhp-purchase-card__specs">
                                    <div class="mhp-spec-cell mhp-spec-cell--hero">
                                        <div class="mhp-spec-cell__value"><?php echo esc_html($sqft_display); ?></div>
                                        <div class="mhp-spec-cell__label">Total Living Area</div>
                                    </div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($bedrooms !== '' ? $bedrooms : '—'); ?></div><div class="mhp-spec-cell__label">Bedrooms</div></div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($bathrooms !== '' ? $bathrooms : '—'); ?></div><div class="mhp-spec-cell__label">Bathrooms</div></div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($stories !== '' ? $stories : '—'); ?></div><div class="mhp-spec-cell__label">Stories</div></div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html(($width !== '' && $depth !== '') ? ($width . ' × ' . $depth) : ($garage !== '' ? $garage : '—')); ?></div><div class="mhp-spec-cell__label"><?php echo esc_html(($width !== '' && $depth !== '') ? 'Width × Depth' : 'Garage'); ?></div></div>
                                </div>
                            </header>

                            <div class="mhp-purchase-card__body">
                                <div class="mhp-price-option mhp-selected" id="mhpOptPdf" data-format="pdf">
                                    <input type="radio" name="mhpFmt" checked>
                                    <span class="mhp-price-option__info"><strong class="mhp-price-option__format">PDF Plan Set</strong><small class="mhp-price-option__desc">Instant digital delivery</small></span>
                                    <span class="mhp-price-option__price"><?php echo esc_html($price_fmt); ?></span>
                                </div>
                                <div class="mhp-price-option" id="mhpOptCad" data-format="cad">
                                    <input type="radio" name="mhpFmt">
                                    <span class="mhp-price-option__info"><strong class="mhp-price-option__format">CAD + PDF Set</strong><small class="mhp-price-option__desc">Editable source + PDF</small></span>
                                    <span class="mhp-price-option__price"><?php echo esc_html($cad_price); ?></span>
                                </div>

                                <a class="mhp-btn-buy" href="<?php echo esc_url($buy_href); ?>">Purchase This Plan</a>

                                <div class="mhp-trust-row">
                                    <span class="mhp-trust-item"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2l8 3v6c0 5.25-3.44 9.56-8 11-4.56-1.44-8-5.75-8-11V5l8-3z" stroke="currentColor" stroke-width="1.8"/></svg>Secure Checkout</span>
                                    <span class="mhp-trust-item"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 7L9 18l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Instant Download</span>
                                </div>
                            </div>

                            <footer class="mhp-purchase-card__footer">
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>">Need changes? Request a modification →</a>
                            </footer>
                        </aside>
                    </div>
                </section>
            </div>

            <section class="mhp-authority-strip mhp-reveal" aria-label="Max Fulbright Authority">
                <div class="mhp-container">
                    <div class="mhp-authority-strip__grid">
                        <div class="mhp-authority-strip__col">
                            <h3>Max Fulbright</h3>
                            <p class="mhp-authority-strip__eyebrow">Engineer · Builder · Designer</p>
                            <p class="mhp-authority-strip__meta">25+ Years Experience</p>
                            <div class="mhp-authority-strip__line"></div>
                            <p>Every plan was designed with hands-on building knowledge — to save space, reduce construction costs, and work in the real world.</p>
                        </div>
                        <div class="mhp-authority-strip__col">
                            <p class="mhp-authority-strip__eyebrow">Why We’re Different</p>
                            <blockquote>“Not a plan mill. Not a catalog. A real designer who has built what he draws. A family business that answers the phone.”</blockquote>
                            <p>Every plan in our collection came from a real design problem: a specific lot, a specific family, a specific budget. That’s what you’re buying — not a generic stock plan, but 25 years of building experience.</p>
                        </div>
                        <div class="mhp-authority-strip__col mhp-authority-process">
                            <p class="mhp-authority-strip__eyebrow">After You Purchase</p>
                            <span><strong>Purchase Your Plan</strong> — PDF or CAD delivered instantly</span>
                            <span><strong>Request Modifications</strong> — we handle changes in-house</span>
                            <span><strong>Permitting Support</strong> — we answer your questions</span>
                            <span><strong>Build Support</strong> — we’re here during construction</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mhp-quick-specs-bar">
                <div class="mhp-container">
                    <div class="mhp-quick-specs__grid">
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__value"><?php echo esc_html($sqft_display); ?></div><div class="mhp-quick-spec__label">Heated Sq Ft</div></div>
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__value"><?php echo esc_html(($width !== '' && $depth !== '') ? ($width . ' × ' . $depth) : '—'); ?></div><div class="mhp-quick-spec__label">Footprint</div></div>
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__value"><?php echo esc_html($main_display); ?></div><div class="mhp-quick-spec__label">Main Floor</div></div>
                        <?php if ($lower_display !== '') : ?><div class="mhp-quick-spec"><div class="mhp-quick-spec__value"><?php echo esc_html($lower_display); ?></div><div class="mhp-quick-spec__label">Lower Level</div></div><?php endif; ?>
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__value"><?php echo esc_html($price_fmt); ?></div><div class="mhp-quick-spec__label">From</div></div>
                    </div>
                </div>
            </section>

            <div class="mhp-container">
                <section class="mhp-section mhp-reveal">
                    <div class="mhp-description-grid">
                        <div class="mhp-description__content">
                            <p class="mhp-section__overline">The Plan</p>
                            <?php echo wp_kses_post(wpautop($plan_description)); ?>
                        </div>
                        <aside class="mhp-highlights-card">
                            <h3 class="mhp-highlights-card__title">Plan Highlights</h3>
                            <div class="mhp-highlight-item"><span class="mhp-highlight-item__icon"></span><div class="mhp-highlight-item__text"><strong>Style</strong><span><?php echo esc_html($style !== '' ? $style : 'Signature House Plan'); ?></span></div></div>
                            <div class="mhp-highlight-item"><span class="mhp-highlight-item__icon"></span><div class="mhp-highlight-item__text"><strong>Outdoor</strong><span><?php echo esc_html($outdoor !== '' ? $outdoor : 'Porch & living spaces available'); ?></span></div></div>
                            <div class="mhp-highlight-item"><span class="mhp-highlight-item__icon"></span><div class="mhp-highlight-item__text"><strong>Lot</strong><span><?php echo esc_html($lot_style !== '' ? $lot_style : 'Flexible lot compatibility'); ?></span></div></div>
                            <div class="mhp-highlight-item"><span class="mhp-highlight-item__icon"></span><div class="mhp-highlight-item__text"><strong>Ceilings</strong><span><?php echo esc_html($ceiling !== '' ? $ceiling : 'Varied ceiling character'); ?></span></div></div>
                            <div class="mhp-highlight-item"><span class="mhp-highlight-item__icon"></span><div class="mhp-highlight-item__text"><strong>Bonus Rooms</strong><span><?php echo esc_html($additional_rooms !== '' ? $additional_rooms : 'Flexible bonus room options'); ?></span></div></div>
                            <div class="mhp-highlight-item"><span class="mhp-highlight-item__icon"></span><div class="mhp-highlight-item__text"><strong>Garage</strong><span><?php echo esc_html($garage !== '' ? $garage : 'No Garage Included'); ?></span></div></div>
                        </aside>
                    </div>
                </section>

                <section class="mhp-section mhp-section--alt mhp-reveal">
                    <header class="mhp-section__header">
                        <p class="mhp-section__overline">Specifications</p>
                        <h2 class="mhp-section__title">Complete Plan Details</h2>
                        <p class="mhp-section__subtitle">Builder-grade information presented clearly so you can compare, budget, and move with confidence.</p>
                    </header>
                    <div class="mhp-specs-grid">
                        <div class="mhp-specs-group">
                            <h3 class="mhp-specs-group__title">Living Area</h3>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Main Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($main_display); ?></span></div>
                            <?php if ($upper_display !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Upper Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($upper_display); ?></span></div><?php endif; ?>
                            <?php if ($lower_display !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Lower Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($lower_display); ?></span></div><?php endif; ?>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Total Heated</span><span class="mhp-spec-row__value"><?php echo esc_html($sqft_display); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Width</span><span class="mhp-spec-row__value"><?php echo esc_html($width !== '' ? $width : '—'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Depth</span><span class="mhp-spec-row__value"><?php echo esc_html($depth !== '' ? $depth : '—'); ?></span></div>
                        </div>
                        <div class="mhp-specs-group">
                            <h3 class="mhp-specs-group__title">House Features</h3>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Bedrooms</span><span class="mhp-spec-row__value"><?php echo esc_html($bedrooms !== '' ? $bedrooms : '—'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Bathrooms</span><span class="mhp-spec-row__value"><?php echo esc_html($bathrooms !== '' ? $bathrooms : '—'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Stories</span><span class="mhp-spec-row__value"><?php echo esc_html($stories !== '' ? $stories : '—'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Additional Rooms</span><span class="mhp-spec-row__value"><?php echo esc_html($additional_rooms !== '' ? $additional_rooms : '—'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Garage</span><span class="mhp-spec-row__value"><?php echo esc_html($garage !== '' ? $garage : 'No Garage Included'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Outdoor Spaces</span><span class="mhp-spec-row__value"><?php echo esc_html($outdoor !== '' ? $outdoor : '—'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Other Features</span><span class="mhp-spec-row__value"><?php echo esc_html($other_features !== '' ? $other_features : '—'); ?></span></div>
                        </div>
                        <div class="mhp-specs-group">
                            <h3 class="mhp-specs-group__title">Construction</h3>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Exterior Framing</span><span class="mhp-spec-row__value"><?php echo esc_html($exterior !== '' ? $exterior : 'Wood framing'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Ceiling Height</span><span class="mhp-spec-row__value"><?php echo esc_html($ceiling !== '' ? $ceiling : 'Per plan notes'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Roof Style</span><span class="mhp-spec-row__value"><?php echo esc_html($roof !== '' ? $roof : 'Per elevations'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Foundation</span><span class="mhp-spec-row__value"><?php echo esc_html($foundation); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Lot Style</span><span class="mhp-spec-row__value"><?php echo esc_html($lot_style !== '' ? $lot_style : 'Standard lot'); ?></span></div>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Home Style</span><span class="mhp-spec-row__value"><?php echo esc_html($style !== '' ? $style : ucfirst($style_cat)); ?></span></div>
                        </div>
                    </div>
                </section>

                <section class="mhp-section mhp-reveal">
                    <header class="mhp-section__header">
                        <p class="mhp-section__overline">Floor Plans</p>
                        <h2 class="mhp-section__title">Explore the Layout</h2>
                    </header>
                    <?php if ($floor_plans !== '') : ?>
                        <div class="mhp-floor-plans__wysiwyg"><?php echo wp_kses_post($floor_plans); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($floor_images)) : ?>
                        <div class="mhp-floor-plans-grid">
                            <?php foreach ($floor_images as $img) : ?>
                                <div class="mhp-floor-plan-card">
                                    <div class="mhp-floor-plan-card__image"><img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($floor_plans === '') : ?>
                        <p>Complete floor plans for all levels included in your plan set.</p>
                    <?php endif; ?>
                </section>

                <section class="mhp-section mhp-section--alt mhp-reveal">
                    <header class="mhp-section__header">
                        <p class="mhp-section__overline">Budget Planning</p>
                        <h2 class="mhp-section__title">Cost to Build Estimator</h2>
                        <p class="mhp-section__subtitle">Get a ballpark estimate before you commit.</p>
                    </header>
                    <div class="mhp-estimator">
                        <div class="mhp-estimator__inner">
                            <div class="mhp-estimator__info">
                                <p>The #1 question every plan buyer has — and nobody answers it on the page. Use this to plan your budget before you buy.</p>
                                <p>Site work, permits, and land are not included. Get a local builder bid for your specific property.</p>
                            </div>
                            <div class="mhp-estimator__calc">
                                <label for="mhpRegion">Region</label>
                                <select id="mhpRegion">
                                    <option value="southeast">Southeast</option>
                                    <option value="south_central">South Central</option>
                                    <option value="midwest">Midwest</option>
                                    <option value="mountain_west">Mountain West</option>
                                    <option value="northeast">Northeast</option>
                                    <option value="pacific_nw">Pacific Northwest</option>
                                    <option value="west_coast">West Coast</option>
                                </select>
                                <label for="mhpFinish">Finish Level: <span id="mhpFinishLabel">Mid-Range Build</span></label>
                                <input id="mhpFinish" type="range" min="1" max="4" step="1" value="2">
                                <div class="mhp-estimator__result">
                                    <div><strong id="mhpEstRange">$0 – $0</strong><span id="mhpEstPsf">$0 – $0 per sq ft</span></div>
                                    <div><small>Materials</small><span id="mhpMat">$0K – $0K</span></div>
                                    <div><small>Labor</small><span id="mhpLab">$0K – $0K</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mhp-section mhp-reveal">
                    <header class="mhp-section__header">
                        <p class="mhp-section__overline">What’s Included</p>
                        <h2 class="mhp-section__title">Everything You Need to Build</h2>
                    </header>
                    <div class="mhp-included-grid">
                        <article class="mhp-included-card"><div class="mhp-included-card__title">Elevations</div><div class="mhp-included-card__desc">Exterior views that define proportions, materials, and curb appeal.</div></article>
                        <article class="mhp-included-card"><div class="mhp-included-card__title">Floor Plans</div><div class="mhp-included-card__desc">Room-by-room layout with dimensions and circulation intent.</div></article>
                        <article class="mhp-included-card"><div class="mhp-included-card__title">Foundation Plan</div><div class="mhp-included-card__desc">Structural base information for coordination with your local team.</div></article>
                        <article class="mhp-included-card"><div class="mhp-included-card__title">Roof Plan</div><div class="mhp-included-card__desc">Roof framing direction, slopes, and key geometry details.</div></article>
                    </div>
                </section>

                <?php if (is_array($related_plans) && !empty($related_plans)) : ?>
                    <section class="mhp-section mhp-section--alt mhp-reveal">
                        <header class="mhp-section__header">
                            <p class="mhp-section__overline">More to Explore</p>
                            <h2 class="mhp-section__title">Related House Plans</h2>
                        </header>
                        <div class="mhp-related-grid">
                            <?php foreach ($related_plans as $related) : ?>
                                <?php if (!($related instanceof WP_Post)) { continue; } ?>
                                <?php
                                $rid = $related->ID;
                                $r_name = (string) mhp_get_plan_field('plan_name', $rid, get_the_title($rid));
                                $r_sqft = (string) mhp_get_plan_field('total_living_area', $rid, '');
                                $r_bed  = (string) mhp_get_plan_field('bedrooms', $rid, '');
                                $r_bath = (string) mhp_get_plan_field('bathrooms', $rid, '');
                                ?>
                                <article class="mhp-related-card">
                                    <a href="<?php echo esc_url(get_permalink($rid)); ?>">
                                        <div class="mhp-related-card__image"><?php echo get_the_post_thumbnail($rid, 'large', array('loading' => 'lazy', 'decoding' => 'async')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                        <div class="mhp-related-card__info">
                                            <h3 class="mhp-related-card__name"><?php echo esc_html($r_name); ?></h3>
                                            <p class="mhp-related-card__specs"><?php echo esc_html(trim(($r_sqft !== '' ? $r_sqft . ' sq ft · ' : '') . ($r_bed !== '' ? $r_bed . ' bed · ' : '') . ($r_bath !== '' ? $r_bath . ' bath' : ''))); ?></p>
                                            <span class="mhp-related-card__link">View Plan →</span>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="mhp-section mhp-reveal" id="contact">
                    <header class="mhp-section__header">
                        <p class="mhp-section__overline">FAQ</p>
                        <h2 class="mhp-section__title">Questions Buyers Ask Before They Build</h2>
                    </header>
                    <div class="mhp-faq-list">
                        <?php foreach ($faqs as $index => $faq) : ?>
                            <?php
                            $q = isset($faq['question']) ? trim((string) $faq['question']) : '';
                            $a = isset($faq['answer']) ? trim((string) $faq['answer']) : '';
                            if ($q === '' || $a === '') { continue; }
                            ?>
                            <article class="mhp-faq-item">
                                <button class="mhp-faq-question" type="button" aria-expanded="false">
                                    <span><?php echo esc_html($q); ?></span>
                                    <span class="mhp-faq-question__icon">+</span>
                                </button>
                                <div class="mhp-faq-answer">
                                    <div class="mhp-faq-answer__inner"><?php echo wp_kses_post(wpautop($a)); ?></div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="mhp-cta-section mhp-reveal">
                    <div class="mhp-container">
                        <h2 class="mhp-cta__title">Ready to Build Your Dream Home?</h2>
                        <p class="mhp-cta__text"><?php echo esc_html($plan_name); ?> was designed to be beautiful on paper and practical on the jobsite. Choose your format and take the next step.</p>
                        <div class="mhp-cta__buttons">
                            <a class="mhp-btn-cta mhp-btn-cta--primary" href="<?php echo esc_url($buy_href); ?>">Purchase This Plan</a>
                            <a class="mhp-btn-cta mhp-btn-cta--outline" href="<?php echo esc_url(home_url('/contact/')); ?>">Request a Modification</a>
                            <a class="mhp-btn-cta mhp-btn-cta--outline" href="<?php echo esc_url(home_url('/contact/')); ?>">Talk With Our Family</a>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mhp-mobile-buy-bar">
                <div class="mhp-mobile-buy-bar__inner">
                    <div class="mhp-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div>
                    <div class="mhp-mobile-buy-bar__price"><?php echo esc_html($price_fmt); ?></div>
                    <a class="mhp-btn-buy" href="<?php echo esc_url($buy_href); ?>">Buy</a>
                </div>
            </div>

            <span id="mhpSqftData" data-sqft="<?php echo (int) $sqft_int; ?>" style="display:none"></span>
        </article>

        <script>
        (function(){
          if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function(entries){
              entries.forEach(function(e){
                if (e.isIntersecting) { e.target.classList.add('mhp-revealed'); io.unobserve(e.target); }
              });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.mhp-reveal').forEach(function(el){ io.observe(el); });
          } else {
            document.querySelectorAll('.mhp-reveal').forEach(function(el){ el.classList.add('mhp-revealed'); });
          }
          window.mhpSelect = function(fmt) {
            document.querySelectorAll('.mhp-price-option').forEach(function(o){ o.classList.remove('mhp-selected'); var r=o.querySelector('input[type="radio"]'); if(r){ r.checked=false; } });
            var el = document.getElementById(fmt === 'pdf' ? 'mhpOptPdf' : 'mhpOptCad');
            if (el) { el.classList.add('mhp-selected'); var rr=el.querySelector('input[type="radio"]'); if(rr){ rr.checked=true; } }
          };
          document.querySelectorAll('.mhp-price-option').forEach(function(opt){
            opt.addEventListener('click', function(){ mhpSelect(opt.getAttribute('data-format') || 'pdf'); });
          });
          window.mhpFaq = function(btn) {
            var item = btn.parentElement, isOpen = item.classList.contains('mhp-open');
            document.querySelectorAll('.mhp-faq-item').forEach(function(f){
              f.classList.remove('mhp-open');
              var q = f.querySelector('.mhp-faq-question'); if (q) q.setAttribute('aria-expanded','false');
              var a = f.querySelector('.mhp-faq-answer'); if (a) a.style.maxHeight = '0';
            });
            if (!isOpen) {
              item.classList.add('mhp-open'); btn.setAttribute('aria-expanded','true');
              var ans = item.querySelector('.mhp-faq-answer'); if (ans) ans.style.maxHeight = ans.scrollHeight + 'px';
            }
          };
          document.querySelectorAll('.mhp-faq-question').forEach(function(btn){
            btn.addEventListener('click', function(){ mhpFaq(btn); });
          });
          var sqftEl = document.getElementById('mhpSqftData');
          var SQFT = sqftEl ? parseInt(sqftEl.dataset.sqft, 10) : 0;
          var R = {
            southeast:{lo:155,hi:210}, south_central:{lo:145,hi:200}, midwest:{lo:150,hi:205},
            mountain_west:{lo:180,hi:245}, northeast:{lo:205,hi:280},
            pacific_nw:{lo:190,hi:260}, west_coast:{lo:225,hi:315}
          };
          var F = { 1:{f:0.85,l:'Standard'}, 2:{f:1.0,l:'Mid-Range'}, 3:{f:1.3,l:'Custom'}, 4:{f:1.65,l:'Premium Custom'} };
          window.mhpCalc = function() {
            var rEl = document.getElementById('mhpRegion'), fEl = document.getElementById('mhpFinish');
            if (!rEl || !fEl || !SQFT) return;
            var r = R[rEl.value], f = F[parseInt(fEl.value, 10)];
            var lo = Math.round(r.lo * f.f) * SQFT, hi = Math.round(r.hi * f.f) * SQFT;
            var s = function(id, v){ var e=document.getElementById(id); if(e) e.textContent=v; };
            s('mhpFinishLabel', f.l + ' Build');
            s('mhpEstRange', '$' + lo.toLocaleString() + ' – $' + hi.toLocaleString());
            s('mhpEstPsf', '$' + Math.round(r.lo*f.f) + ' – $' + Math.round(r.hi*f.f) + ' per sq ft');
            s('mhpMat', '$' + Math.round(lo*0.45/1000) + 'K – $' + Math.round(hi*0.45/1000) + 'K');
            s('mhpLab', '$' + Math.round(lo*0.35/1000) + 'K – $' + Math.round(hi*0.35/1000) + 'K');
          };
          var regionEl = document.getElementById('mhpRegion');
          var finishEl = document.getElementById('mhpFinish');
          if (regionEl) { regionEl.addEventListener('change', mhpCalc); }
          if (finishEl) { finishEl.addEventListener('input', mhpCalc); }
          if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', mhpCalc); } else { mhpCalc(); }
        })();
        </script>
        <?php
    }
}
add_action('genesis_loop', 'mhp_render_single_plan_v3');

genesis();

