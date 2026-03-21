<?php
/**
 * Universal Single Plan Template (v2)
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

if (!function_exists('mhp_plan_detect_style_category')) {
    /**
     * Detect style category + labels from style string.
     *
     * @param string $style Style field.
     * @return array<string,string>
     */
    function mhp_plan_detect_style_category($style) {
        $style_lower = strtolower((string) $style);

        if (strpos($style_lower, 'mountain') !== false || strpos($style_lower, 'lake') !== false) {
            return array(
                'style_cat'      => 'mountain',
                'badge_text'     => 'Mountain House Plan',
                'breadcrumb_cat' => 'Mountain House Plans',
                'breadcrumb_url' => home_url('/home-plans/mountain-house-plans/'),
            );
        }

        if (strpos($style_lower, 'farmhouse') !== false || strpos($style_lower, 'southern') !== false || strpos($style_lower, 'country') !== false) {
            return array(
                'style_cat'      => 'farmhouse',
                'badge_text'     => 'Farmhouse Plan',
                'breadcrumb_cat' => 'Farmhouse House Plans',
                'breadcrumb_url' => home_url('/home-plans/farmhouse-house-plans/'),
            );
        }

        if (strpos($style_lower, 'cottage') !== false || strpos($style_lower, 'cabin') !== false || strpos($style_lower, 'bungalow') !== false) {
            return array(
                'style_cat'      => 'cottage',
                'badge_text'     => 'Cottage Plan',
                'breadcrumb_cat' => 'Cottage House Plans',
                'breadcrumb_url' => home_url('/home-plans/cottage-house-plans/'),
            );
        }

        return array(
            'style_cat'      => 'craftsman',
            'badge_text'     => 'Craftsman Plan',
            'breadcrumb_cat' => 'House Plans',
            'breadcrumb_url' => home_url('/house-plans/'),
        );
    }
}

if (!function_exists('mhp_plan_money')) {
    /**
     * Format money.
     *
     * @param float $value Value.
     * @return string
     */
    function mhp_plan_money($value) {
        return '$' . number_format((float) $value, 2);
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

        $post_id       = get_the_ID();
        $plan_name     = get_field('plan_name', $post_id) ?: get_the_title($post_id);
        $style         = (string) get_field('style', $post_id);
        $style_meta    = mhp_plan_detect_style_category($style);
        $price_pdf     = (float) get_field('price', $post_id);
        $price_cad     = $price_pdf > 0 ? round($price_pdf * 1.26, 2) : 0;
        $bedrooms      = (string) get_field('bedrooms', $post_id);
        $bathrooms     = (string) get_field('bathrooms', $post_id);
        $sqft          = (string) get_field('total_living_area', $post_id);
        $faqs          = get_field('faqs', $post_id);
        $desc          = wp_strip_all_tags((string) get_field('plan_description', $post_id));
        $permalink     = get_permalink($post_id);
        $image         = get_the_post_thumbnail_url($post_id, 'large');

        if ($desc === '') {
            $desc = sprintf(
                '%s by Max Fulbright is a %s home plan engineered to eliminate wasted space and reduce construction costs.',
                $plan_name,
                $style !== '' ? $style : 'custom'
            );
        }

        $offers = array();
        if ($price_pdf > 0) {
            $offers[] = array(
                '@type'         => 'Offer',
                'name'          => 'PDF Plan Set',
                'priceCurrency' => 'USD',
                'price'         => number_format($price_pdf, 2, '.', ''),
                'availability'  => 'https://schema.org/InStock',
                'url'           => $permalink,
            );
        }
        if ($price_cad > 0) {
            $offers[] = array(
                '@type'         => 'Offer',
                'name'          => 'CAD + PDF Plan Set',
                'priceCurrency' => 'USD',
                'price'         => number_format($price_cad, 2, '.', ''),
                'availability'  => 'https://schema.org/InStock',
                'url'           => $permalink,
            );
        }

        $faq_entities = array();
        if (is_array($faqs)) {
            foreach ($faqs as $faq) {
                $q = isset($faq['question']) ? trim((string) $faq['question']) : '';
                $a = isset($faq['answer']) ? trim((string) $faq['answer']) : '';
                if ($q === '' || $a === '') {
                    continue;
                }
                $faq_entities[] = array(
                    '@type'          => 'Question',
                    'name'           => wp_strip_all_tags($q),
                    'acceptedAnswer' => array('@type' => 'Answer', 'text' => wp_kses_post($a)),
                );
            }
        }

        if (empty($faq_entities)) {
            $faq_entities = array(
                array('@type' => 'Question', 'name' => 'What is included in my plan set?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Typical sets include floor plans, elevations, foundation, roof plan, sections, and key construction details.')),
                array('@type' => 'Question', 'name' => 'Can I modify this plan?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Yes. We can make changes for your lot, local code, and lifestyle priorities.')),
                array('@type' => 'Question', 'name' => 'How is the plan delivered?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Digital plans are delivered after checkout confirmation.')),
                array('@type' => 'Question', 'name' => 'Do I need local engineering?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Many jurisdictions require local engineering review. Confirm with your building department.')),
                array('@type' => 'Question', 'name' => 'Do you offer build support?', 'acceptedAnswer' => array('@type' => 'Answer', 'text' => 'Yes. We provide plan clarification and support through permitting and construction.')),
            );
        }

        $schema_product = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => $plan_name,
            'description' => $desc,
            'image'    => $image ? array($image) : array(),
            'url'      => $permalink,
            'brand'    => array('@type' => 'Brand', 'name' => 'MaxHousePlans'),
            'offers'   => $offers,
            'additionalProperty' => array_filter(array(
                $sqft !== '' ? array('@type' => 'PropertyValue', 'name' => 'Heated Sq Ft', 'value' => $sqft) : null,
                $bedrooms !== '' ? array('@type' => 'PropertyValue', 'name' => 'Bedrooms', 'value' => $bedrooms) : null,
                $bathrooms !== '' ? array('@type' => 'PropertyValue', 'name' => 'Bathrooms', 'value' => $bathrooms) : null,
            )),
        );

        $schema_breadcrumb = array(
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')),
                array('@type' => 'ListItem', 'position' => 2, 'name' => 'House Plans', 'item' => home_url('/house-plans/')),
                array('@type' => 'ListItem', 'position' => 3, 'name' => $style_meta['breadcrumb_cat'], 'item' => $style_meta['breadcrumb_url']),
                array('@type' => 'ListItem', 'position' => 4, 'name' => $plan_name, 'item' => $permalink),
            ),
        );

        $schema_faq = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
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
 * Render universal single plan template.
 */
function mhp_render_single_plan_v2() {
    if (!have_posts()) {
        return;
    }

    while (have_posts()) {
        the_post();

        $post_id = get_the_ID();

        $plan_name        = get_field('plan_name', $post_id) ?: get_the_title($post_id);
        $total_living     = (string) get_field('total_living_area', $post_id);
        $main_floor       = (string) get_field('main_floor', $post_id);
        $upper_floor      = (string) get_field('upper_floor', $post_id);
        $lower_floor      = (string) get_field('lower_floor', $post_id);
        $bedrooms         = (string) get_field('bedrooms', $post_id);
        $bathrooms        = (string) get_field('bathrooms', $post_id);
        $stories          = (string) get_field('stories', $post_id);
        $width            = (string) get_field('width', $post_id);
        $depth            = (string) get_field('depth', $post_id);
        $garage           = (string) get_field('garage', $post_id);
        $style            = (string) get_field('style', $post_id);
        $outdoor          = (string) get_field('outdoor', $post_id);
        $roof             = (string) get_field('roof', $post_id);
        $ceiling          = (string) get_field('ceiling', $post_id);
        $exterior         = (string) get_field('exterior', $post_id);
        $additional_rooms = (string) get_field('additional_rooms', $post_id);
        $other_features   = (string) get_field('other_features', $post_id);
        $lot_style        = (string) get_field('lot_style', $post_id);
        $plan_description = (string) get_field('plan_description', $post_id);
        $floor_plans      = (string) get_field('floor_plans', $post_id);
        $paypal           = (string) get_field('paypal', $post_id);
        $base_price       = (float) get_field('price', $post_id);
        $related_plans    = get_field('related_plans', $post_id);
        $faqs             = get_field('faqs', $post_id);

        $style_meta       = mhp_plan_detect_style_category($style);
        $style_cat        = $style_meta['style_cat'];
        $badge_text       = $style_meta['badge_text'];
        $breadcrumb_cat   = $style_meta['breadcrumb_cat'];
        $breadcrumb_url   = $style_meta['breadcrumb_url'];

        $pdf_price        = $base_price > 0 ? $base_price : 0;
        $cad_price        = $pdf_price > 0 ? round($pdf_price * 1.26, 2) : 0;

        $sqft_display     = $total_living !== '' ? number_format((float) $total_living) : '';
        $main_display     = $main_floor !== '' ? number_format((float) $main_floor) : '';
        $upper_display    = $upper_floor !== '' ? number_format((float) $upper_floor) : '';
        $lower_display    = $lower_floor !== '' ? number_format((float) $lower_floor) : '';

        $hosted_button_id = '';
        if ($paypal !== '' && preg_match('/name=["\']hosted_button_id["\']\s+value=["\']([^"\']+)["\']/', $paypal, $m)) {
            $hosted_button_id = $m[1];
        }

        $buy_url = !empty($hosted_button_id) ? 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . rawurlencode($hosted_button_id) : home_url('/contact/');

        $post_content    = (string) get_post_field('post_content', $post_id);
        $gallery_markup  = '';
        if (shortcode_exists('gallery') && preg_match('/\[gallery[^\]]*\]/', $post_content, $gallery_match)) {
            $gallery_markup = do_shortcode($gallery_match[0]);
        }

        if ($plan_description === '') {
            $plan_description = wp_kses_post(
                '<p>' . esc_html($plan_name) . ' was designed by Max Fulbright with one goal: remove wasted space and simplify construction decisions from day one. The footprint is efficient, the circulation is intentional, and every square foot has a job.</p>' .
                '<p>This ' . esc_html($style !== '' ? $style : ucfirst($style_cat)) . ' plan offers approximately ' . esc_html($sqft_display !== '' ? $sqft_display : 'well-balanced') . ' square feet with ' . esc_html($bedrooms !== '' ? $bedrooms : 'multiple') . ' bedrooms and ' . esc_html($bathrooms !== '' ? $bathrooms : 'well-appointed') . ' baths. It is created for families who want high design value without overbuilding.</p>' .
                '<p>From first sketch to permit set, the design reflects builder logic: cleaner spans, practical material use, and details that support smoother field execution. This is not catalog filler. It is build-minded architecture.</p>' .
                '<p>Engineer turned builder turned designer. Every plan designed to save wasted space and cut construction costs. A family business that cares about your home from start to finish. Real people. Real plans.</p>'
            );
        }

        $highlights = array();
        if ($style !== '') {
            $highlights[] = array('label' => 'Architectural Style', 'value' => $style);
        }
        if ($outdoor !== '') {
            $highlights[] = array('label' => 'Outdoor Living', 'value' => $outdoor);
        }
        if ($ceiling !== '') {
            $highlights[] = array('label' => 'Ceiling Strategy', 'value' => $ceiling);
        }
        if ($lot_style !== '') {
            $highlights[] = array('label' => 'Lot Compatibility', 'value' => $lot_style);
        }
        if ($additional_rooms !== '') {
            $highlights[] = array('label' => 'Additional Rooms', 'value' => $additional_rooms);
        }
        $highlights[] = array('label' => 'Garage', 'value' => $garage !== '' ? $garage : 'No garage');
        $highlights = array_slice($highlights, 0, 6);

        if (empty($faqs) || !is_array($faqs)) {
            $faqs = array(
                array('question' => 'What is included in my plan package?', 'answer' => 'Each plan package typically includes floor plans, elevations, foundation information, roof plan, sections, and major construction notes.'),
                array('question' => 'Can this plan be modified for my lot?', 'answer' => 'Yes. Modifications are available for layout, dimensions, and exterior details so the home better fits your site and goals.'),
                array('question' => 'How quickly are plans delivered?', 'answer' => 'Digital plans are delivered after checkout verification. Timing is usually fast, with support available if you need help.'),
                array('question' => 'Will this work with local permitting?', 'answer' => 'Most jurisdictions require local review. Always verify specific permit requirements with your local authority.'),
                array('question' => 'Do you provide support after purchase?', 'answer' => 'Yes. We support you through modifications, permitting clarification, and build-stage plan questions.'),
            );
        }

        $floor_images = array();
        foreach (array('floor_plan_1', 'floor_plan_2', 'floor_plan_3') as $field_key) {
            $img = get_field($field_key, $post_id);
            if (is_array($img) && !empty($img['url'])) {
                $floor_images[] = array('url' => $img['url'], 'alt' => !empty($img['alt']) ? $img['alt'] : $plan_name . ' floor plan');
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
                    <a href="<?php echo esc_url($breadcrumb_url); ?>"><?php echo esc_html($breadcrumb_cat); ?></a>
                    <span>›</span>
                    <span aria-current="page"><?php echo esc_html($plan_name); ?></span>
                </nav>

                <section class="mhp-plan-hero">
                    <div class="mhp-plan-hero__grid">
                        <div class="mhp-hero-image">
                            <div class="mhp-hero-image__main">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('full', array('loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'alt' => esc_attr($plan_name))); ?>
                                <?php endif; ?>
                                <div class="mhp-hero-image__badge"><?php echo esc_html($badge_text); ?></div>
                            </div>

                            <?php if ($gallery_markup !== '') : ?>
                                <div class="mhp-gallery-grid">
                                    <?php echo do_shortcode($gallery_match[0]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside class="mhp-purchase-card">
                            <header class="mhp-purchase-card__header">
                                <h1 class="mhp-purchase-card__plan-name"><?php echo esc_html($plan_name); ?></h1>
                                <p class="mhp-purchase-card__style"><?php echo esc_html($style !== '' ? $style : $badge_text); ?></p>

                                <div class="mhp-purchase-card__specs">
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($sqft_display !== '' ? $sqft_display : '—'); ?></div><div class="mhp-spec-cell__label">Sq Ft</div></div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($bedrooms !== '' ? $bedrooms : '—'); ?></div><div class="mhp-spec-cell__label">Bedrooms</div></div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($bathrooms !== '' ? $bathrooms : '—'); ?></div><div class="mhp-spec-cell__label">Bathrooms</div></div>
                                    <div class="mhp-spec-cell"><div class="mhp-spec-cell__value"><?php echo esc_html($stories !== '' ? $stories : '—'); ?></div><div class="mhp-spec-cell__label">Stories</div></div>
                                </div>
                            </header>

                            <div class="mhp-purchase-card__body">
                                <label class="mhp-price-option mhp-selected" data-option="pdf">
                                    <input type="radio" name="mhp-plan-format" value="pdf" checked>
                                    <span class="mhp-price-option__info"><strong class="mhp-price-option__format">PDF Plan Set</strong><small class="mhp-price-option__desc">Selected by default</small></span>
                                    <span class="mhp-price-option__price"><?php echo $pdf_price > 0 ? esc_html(mhp_plan_money($pdf_price)) : 'Call'; ?></span>
                                </label>
                                <label class="mhp-price-option" data-option="cad">
                                    <input type="radio" name="mhp-plan-format" value="cad">
                                    <span class="mhp-price-option__info"><strong class="mhp-price-option__format">CAD + PDF Set</strong><small class="mhp-price-option__desc">1.26× PDF Price</small></span>
                                    <span class="mhp-price-option__price"><?php echo $cad_price > 0 ? esc_html(mhp_plan_money($cad_price)) : 'Call'; ?></span>
                                </label>

                                <?php if (!empty($hosted_button_id)) : ?>
                                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                        <input type="hidden" name="cmd" value="_s-xclick">
                                        <input type="hidden" name="hosted_button_id" value="<?php echo esc_attr($hosted_button_id); ?>">
                                        <input type="hidden" name="on0" value="Plan Format">
                                        <input type="hidden" id="mhp-paypal-format" name="os0" value="PDF Plan Set">
                                        <button class="mhp-btn-buy" type="submit">Buy This Plan</button>
                                    </form>
                                <?php else : ?>
                                    <a class="mhp-btn-buy" href="<?php echo esc_url(home_url('/contact/')); ?>">Contact to Purchase</a>
                                <?php endif; ?>

                                <div class="mhp-trust-row">
                                    <span class="mhp-trust-item">Secure Checkout</span>
                                    <span class="mhp-trust-item">Instant Download</span>
                                </div>

                                <p class="mhp-modify-link"><a href="<?php echo esc_url(home_url('/contact/')); ?>">Need changes? Request a modification →</a></p>
                            </div>
                        </aside>
                    </div>
                </section>
            </div>

            <section class="mhp-authority-strip" aria-label="Max Fulbright Authority Strip">
                <div class="mhp-container">
                    <div class="mhp-authority-strip__grid">
                        <div class="mhp-authority-strip__col">
                            <h3>Max Fulbright</h3>
                            <p>Engineer · Builder · Designer | 25+ Years Experience</p>
                        </div>
                        <div class="mhp-authority-strip__col">
                            <h3>The MaxHousePlans Difference</h3>
                            <p>Every plan designed to save wasted space and cut construction costs. Family business. Real people. Real plans.</p>
                        </div>
                        <div class="mhp-authority-strip__col mhp-authority-process">
                            <span>1. Purchase</span>
                            <span>2. Modifications</span>
                            <span>3. Permitting Support</span>
                            <span>4. Build Support</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mhp-quick-specs-bar">
                <div class="mhp-container">
                    <div class="mhp-quick-specs__grid">
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__icon">◻</div><div class="mhp-quick-spec__value"><?php echo esc_html($sqft_display !== '' ? $sqft_display . ' sq ft' : '—'); ?></div><div class="mhp-quick-spec__label">Heated Sq Ft</div></div>
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__icon">⌖</div><div class="mhp-quick-spec__value"><?php echo esc_html(trim($width . ' × ' . $depth)); ?></div><div class="mhp-quick-spec__label">Footprint</div></div>
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__icon">▭</div><div class="mhp-quick-spec__value"><?php echo esc_html($main_display !== '' ? $main_display . ' sq ft' : '—'); ?></div><div class="mhp-quick-spec__label">Main Floor</div></div>
                        <?php if ($upper_display !== '' || $lower_display !== '') : ?>
                            <div class="mhp-quick-spec"><div class="mhp-quick-spec__icon">⇅</div><div class="mhp-quick-spec__value"><?php echo esc_html(trim(($upper_display !== '' ? 'Upper ' . $upper_display : '') . ($lower_display !== '' ? ' / Lower ' . $lower_display : ''))); ?></div><div class="mhp-quick-spec__label">Upper/Lower</div></div>
                        <?php endif; ?>
                        <div class="mhp-quick-spec"><div class="mhp-quick-spec__icon">$</div><div class="mhp-quick-spec__value"><?php echo esc_html($pdf_price > 0 ? mhp_plan_money($pdf_price) : 'Call'); ?></div><div class="mhp-quick-spec__label">Price</div></div>
                    </div>
                </div>
            </section>

            <div class="mhp-container">
                <section class="mhp-section">
                    <div class="mhp-description-grid">
                        <div class="mhp-description__content">
                            <?php echo wp_kses_post(wpautop($plan_description)); ?>
                            <p><em>Engineer turned builder turned designer. Every plan designed to save wasted space and cut construction costs. A family business that cares about your home from start to finish. Real people. Real plans.</em></p>
                        </div>
                        <aside class="mhp-highlights-card">
                            <h3 class="mhp-highlights-card__title">Plan Highlights</h3>
                            <?php foreach ($highlights as $highlight) : ?>
                                <div class="mhp-highlight-item">
                                    <span class="mhp-highlight-item__icon" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 12h16M12 4v16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    </span>
                                    <div class="mhp-highlight-item__text"><strong><?php echo esc_html($highlight['label']); ?>:</strong> <?php echo esc_html($highlight['value']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </aside>
                    </div>
                </section>

                <section class="mhp-section mhp-section--alt">
                    <header class="mhp-section__header"><p class="mhp-section__overline">Full Specifications</p><h2 class="mhp-section__title">Living Area · House Features · Construction</h2></header>
                    <div class="mhp-specs-grid">
                        <div class="mhp-specs-group">
                            <h3 class="mhp-specs-group__title">Living Area</h3>
                            <?php if ($sqft_display !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Total Living Area</span><span class="mhp-spec-row__value"><?php echo esc_html($sqft_display); ?> sq ft</span></div><?php endif; ?>
                            <?php if ($main_display !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Main Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($main_display); ?> sq ft</span></div><?php endif; ?>
                            <?php if ($upper_display !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Upper Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($upper_display); ?> sq ft</span></div><?php endif; ?>
                            <?php if ($lower_display !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Lower Floor</span><span class="mhp-spec-row__value"><?php echo esc_html($lower_display); ?> sq ft</span></div><?php endif; ?>
                            <?php if ($stories !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Stories</span><span class="mhp-spec-row__value"><?php echo esc_html($stories); ?></span></div><?php endif; ?>
                            <?php if ($bedrooms !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Bedrooms</span><span class="mhp-spec-row__value"><?php echo esc_html($bedrooms); ?></span></div><?php endif; ?>
                            <?php if ($bathrooms !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Bathrooms</span><span class="mhp-spec-row__value"><?php echo esc_html($bathrooms); ?></span></div><?php endif; ?>
                        </div>
                        <div class="mhp-specs-group">
                            <h3 class="mhp-specs-group__title">House Features</h3>
                            <?php if ($style !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Style</span><span class="mhp-spec-row__value"><?php echo esc_html($style); ?></span></div><?php endif; ?>
                            <?php if ($garage !== '' || $garage === '0') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Garage</span><span class="mhp-spec-row__value"><?php echo esc_html($garage); ?></span></div><?php endif; ?>
                            <?php if ($outdoor !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Outdoor</span><span class="mhp-spec-row__value"><?php echo esc_html($outdoor); ?></span></div><?php endif; ?>
                            <?php if ($lot_style !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Lot Style</span><span class="mhp-spec-row__value"><?php echo esc_html($lot_style); ?></span></div><?php endif; ?>
                            <?php if ($additional_rooms !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Additional Rooms</span><span class="mhp-spec-row__value"><?php echo esc_html($additional_rooms); ?></span></div><?php endif; ?>
                            <?php if ($other_features !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Other Features</span><span class="mhp-spec-row__value"><?php echo esc_html($other_features); ?></span></div><?php endif; ?>
                        </div>
                        <div class="mhp-specs-group">
                            <h3 class="mhp-specs-group__title">Construction</h3>
                            <?php if ($width !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Width</span><span class="mhp-spec-row__value"><?php echo esc_html($width); ?></span></div><?php endif; ?>
                            <?php if ($depth !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Depth</span><span class="mhp-spec-row__value"><?php echo esc_html($depth); ?></span></div><?php endif; ?>
                            <?php if ($roof !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Roof</span><span class="mhp-spec-row__value"><?php echo esc_html($roof); ?></span></div><?php endif; ?>
                            <?php if ($ceiling !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Ceiling</span><span class="mhp-spec-row__value"><?php echo esc_html($ceiling); ?></span></div><?php endif; ?>
                            <?php if ($exterior !== '') : ?><div class="mhp-spec-row"><span class="mhp-spec-row__label">Exterior</span><span class="mhp-spec-row__value"><?php echo esc_html($exterior); ?></span></div><?php endif; ?>
                            <div class="mhp-spec-row"><span class="mhp-spec-row__label">Designer</span><span class="mhp-spec-row__value">Max Fulbright</span></div>
                        </div>
                    </div>
                </section>

                <section class="mhp-section">
                    <header class="mhp-section__header"><p class="mhp-section__overline">Floor Plans</p><h2 class="mhp-section__title">Floor Plan Drawings & Layouts</h2></header>
                    <div class="mhp-floor-plans__wysiwyg">
                        <?php echo $floor_plans !== '' ? wp_kses_post($floor_plans) : '<p>Detailed floor plan sheets are included with your plan purchase.</p>'; ?>
                    </div>
                    <?php if (!empty($floor_images)) : ?>
                        <div class="mhp-related-grid">
                            <?php foreach ($floor_images as $fp) : ?>
                                <article class="mhp-related-card"><img src="<?php echo esc_url($fp['url']); ?>" alt="<?php echo esc_attr($fp['alt']); ?>" loading="lazy"></article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="mhp-section mhp-section--alt">
                    <header class="mhp-section__header"><p class="mhp-section__overline">Cost-To-Build Estimator</p><h2 class="mhp-section__title">Estimate by Region and Finish Level</h2></header>
                    <div class="mhp-estimator">
                        <div class="mhp-estimator__inner">
                            <div class="mhp-estimator__info"><p class="mhp-estimator__desc">Use this planning tool for a directional range before bidding.</p></div>
                            <div class="mhp-estimator__calc">
                                <label for="mhpRegion">Region</label>
                                <select id="mhpRegion">
                                    <option value="south">South</option><option value="midwest">Midwest</option><option value="northeast">Northeast</option><option value="west">West</option>
                                </select>
                                <label for="mhpFinishLevel">Finish Level: <span id="mhpFinishLabel">Standard</span></label>
                                <input id="mhpFinishLevel" type="range" min="1" max="3" step="1" value="1">
                                <div class="mhp-estimator__result">
                                    <div><strong>Low:</strong> <span id="mhpCostLow">$0</span></div>
                                    <div><strong>High:</strong> <span id="mhpCostHigh">$0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mhp-section">
                    <header class="mhp-section__header"><h2 class="mhp-section__title">What's Included</h2></header>
                    <div class="mhp-included-grid">
                        <div class="mhp-included-card">Elevations<br><small>Exterior views with dimensional guidance.</small></div>
                        <div class="mhp-included-card">Floor Plans<br><small>Room layout, walls, openings, and flow.</small></div>
                        <div class="mhp-included-card">Foundation Plan<br><small>Structural foundation framework details.</small></div>
                        <div class="mhp-included-card">Roof Plan<br><small>Roof geometry, slopes, and framing intent.</small></div>
                    </div>
                </section>

                <?php if (!empty($related_plans) && is_array($related_plans)) : ?>
                    <section class="mhp-section mhp-section--alt">
                        <header class="mhp-section__header"><h2 class="mhp-section__title">Related Plans</h2></header>
                        <div class="mhp-related-grid">
                            <?php foreach ($related_plans as $related) : ?>
                                <?php if (!($related instanceof WP_Post)) { continue; } ?>
                                <?php $r_id = $related->ID; ?>
                                <article class="mhp-related-card">
                                    <a href="<?php echo esc_url(get_permalink($r_id)); ?>">
                                        <?php echo get_the_post_thumbnail($r_id, 'medium', array('loading' => 'lazy', 'decoding' => 'async')); ?>
                                        <h3><?php echo esc_html(get_field('plan_name', $r_id) ?: get_the_title($r_id)); ?></h3>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="mhp-section">
                    <header class="mhp-section__header"><h2 class="mhp-section__title">Frequently Asked Questions</h2></header>
                    <div class="mhp-faq-list">
                        <?php foreach ($faqs as $i => $faq) : ?>
                            <?php $q = isset($faq['question']) ? trim((string) $faq['question']) : ''; $a = isset($faq['answer']) ? trim((string) $faq['answer']) : ''; if ($q === '' || $a === '') { continue; } ?>
                            <article class="mhp-faq-item">
                                <button class="mhp-faq-question" aria-expanded="false" aria-controls="mhp-faq-answer-<?php echo esc_attr((string) $i); ?>"><span><?php echo esc_html($q); ?></span><span class="mhp-faq-question__icon">+</span></button>
                                <div id="mhp-faq-answer-<?php echo esc_attr((string) $i); ?>" class="mhp-faq-answer" hidden><div class="mhp-faq-answer__inner"><?php echo wp_kses_post(wpautop($a)); ?></div></div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="mhp-cta-section" id="mhp-plan-contact">
                    <h2 class="mhp-cta__title">Ready to Build Your Dream Home?</h2>
                    <p class="mhp-cta__text">Engineer turned builder turned designer. Every plan designed to save wasted space and cut construction costs. A family business that cares about your home from start to finish. Real people. Real plans.</p>
                    <div class="mhp-cta__buttons">
                        <a class="mhp-btn-cta mhp-btn-cta--primary" href="<?php echo esc_url($buy_url); ?>">Purchase Plan</a>
                        <a class="mhp-btn-cta mhp-btn-cta--outline" href="<?php echo esc_url(home_url('/contact/')); ?>">Modify Plan</a>
                        <a class="mhp-btn-cta mhp-btn-cta--outline" href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
                    </div>
                </section>
            </div>

            <div class="mhp-mobile-buy-bar">
                <div class="mhp-mobile-buy-bar__inner">
                    <div><div class="mhp-mobile-buy-bar__name"><?php echo esc_html($plan_name); ?></div><div class="mhp-mobile-buy-bar__price"><?php echo esc_html($pdf_price > 0 ? mhp_plan_money($pdf_price) : 'Call for pricing'); ?></div></div>
                    <a class="mhp-btn-buy" href="<?php echo esc_url($buy_url); ?>">Buy</a>
                </div>
            </div>
        </article>

        <script>
            (function () {
                var options = document.querySelectorAll('.mhp-price-option');
                var paypalFormat = document.getElementById('mhp-paypal-format');
                options.forEach(function (opt) {
                    var radio = opt.querySelector('input[type="radio"]');
                    if (!radio) return;
                    radio.addEventListener('change', function () {
                        options.forEach(function (o) { o.classList.remove('mhp-selected'); });
                        opt.classList.add('mhp-selected');
                        if (paypalFormat) paypalFormat.value = radio.value === 'cad' ? 'CAD + PDF Set' : 'PDF Plan Set';
                    });
                });

                var faqButtons = document.querySelectorAll('.mhp-faq-question');
                faqButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        var expanded = button.getAttribute('aria-expanded') === 'true';
                        var id = button.getAttribute('aria-controls');
                        var panel = id ? document.getElementById(id) : null;
                        var icon = button.querySelector('.mhp-faq-question__icon');
                        button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                        if (!panel) return;

                        if (expanded) {
                            panel.style.maxHeight = panel.scrollHeight + 'px';
                            requestAnimationFrame(function () {
                                panel.style.maxHeight = '0px';
                            });
                            setTimeout(function () { panel.hidden = true; panel.classList.remove('mhp-open'); }, 250);
                            if (icon) icon.textContent = '+';
                        } else {
                            panel.hidden = false;
                            panel.classList.add('mhp-open');
                            panel.style.maxHeight = '0px';
                            requestAnimationFrame(function () { panel.style.maxHeight = panel.scrollHeight + 'px'; });
                            if (icon) icon.textContent = '−';
                        }
                    });
                });

                var sqFt = parseFloat('<?php echo esc_js($total_living !== '' ? $total_living : '0'); ?>') || 0;
                var regionEl = document.getElementById('mhpRegion');
                var finishEl = document.getElementById('mhpFinishLevel');
                var finishLabel = document.getElementById('mhpFinishLabel');
                var lowEl = document.getElementById('mhpCostLow');
                var highEl = document.getElementById('mhpCostHigh');

                var regionBase = { south: 165, midwest: 180, northeast: 220, west: 235 };
                var finishMult = { 1: {label: 'Standard', low: 0.95, high: 1.08}, 2: {label: 'Premium', low: 1.1, high: 1.25}, 3: {label: 'Luxury', low: 1.28, high: 1.5} };

                function money(v) { return '$' + Math.round(v).toLocaleString(); }
                function calcEstimate() {
                    if (!regionEl || !finishEl || !lowEl || !highEl || sqFt <= 0) return;
                    var rb = regionBase[regionEl.value] || 180;
                    var f = finishMult[finishEl.value] || finishMult[1];
                    if (finishLabel) finishLabel.textContent = f.label;
                    lowEl.textContent = money(sqFt * rb * f.low);
                    highEl.textContent = money(sqFt * rb * f.high);
                }

                if (regionEl && finishEl) {
                    regionEl.addEventListener('change', calcEstimate);
                    finishEl.addEventListener('input', calcEstimate);
                    calcEstimate();
                }
            })();
        </script>
        <?php
    }
}
add_action('genesis_loop', 'mhp_render_single_plan_v2');

genesis();
