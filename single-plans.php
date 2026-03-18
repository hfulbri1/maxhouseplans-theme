<?php
/**
 * Template Name: Plan Page
 *
 * MaxHousePlans — Single Plan Template
 * Full redesign: carolina-farmhouse layout + appalachia typography
 * Design system: Charcoal #1C1C1C · Clay #C4714A · Cream #F7F4EF
 */

// No sidebar on plan pages
add_filter( 'genesis_pre_get_option_site_layout', '__return_empty_string' );
add_filter( 'genesis_site_layout', '__mhp_plan_full_width' );
function __mhp_plan_full_width() { return 'full-width-content'; }

// Remove default Genesis loop — we output everything ourselves
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mhp_plan_page_loop' );

// Enqueue plan-specific scripts
add_action( 'wp_enqueue_scripts', 'mhp_plan_page_scripts' );
function mhp_plan_page_scripts() {
    wp_enqueue_script(
        'mhp-sticky-plan-bar',
        get_stylesheet_directory_uri() . '/js/sticky-plan-bar.js',
        array(),
        '1.0.0',
        true
    );
}

/**
 * Main plan page output
 */
function mhp_plan_page_loop() {
    if ( ! have_posts() ) { return; }
    while ( have_posts() ) {
        the_post();
        $pid = get_the_ID();

        // ---- ACF field retrieval ----
        $acf = function_exists( 'get_field' );

        $plan_name        = $acf ? get_field( 'plan_name',        $pid ) : '';
        $plan_name        = $plan_name ?: get_the_title( $pid );
        $sqft             = $acf ? get_field( 'total_living_area', $pid ) : '';
        $bedrooms         = $acf ? get_field( 'bedrooms',          $pid ) : '';
        $bathrooms        = $acf ? get_field( 'bathrooms',         $pid ) : '';
        $stories          = $acf ? get_field( 'stories',           $pid ) : '';
        $garage           = $acf ? get_field( 'garage',            $pid ) : '';
        $width            = $acf ? get_field( 'width',             $pid ) : '';
        $depth            = $acf ? get_field( 'depth',             $pid ) : '';
        $outdoor          = $acf ? get_field( 'outdoor',           $pid ) : '';
        $roof             = $acf ? get_field( 'roof',              $pid ) : '';
        $ceiling          = $acf ? get_field( 'ceiling',           $pid ) : '';
        $exterior         = $acf ? get_field( 'exterior',          $pid ) : '';
        $lot_style        = $acf ? get_field( 'lot_style',         $pid ) : '';
        $additional_rooms = $acf ? get_field( 'additional_rooms',  $pid ) : '';
        $other_features   = $acf ? get_field( 'other_features',    $pid ) : '';
        $plan_description = $acf ? get_field( 'plan_description',  $pid ) : '';
        $floor_plans      = $acf ? get_field( 'floor_plans',       $pid ) : '';
        $plan_image       = $acf ? get_field( 'plan_image',        $pid ) : '';
        $paypal           = $acf ? get_field( 'paypal',            $pid ) : '';
        $related_plans    = $acf ? get_field( 'related_plans',     $pid ) : array();
        $faqs             = $acf ? get_field( 'faqs',              $pid ) : array();

        // ---- Price resolution ----
        $price_raw = $acf ? get_field( 'price', $pid ) : '';
        if ( empty( $price_raw ) && $paypal ) {
            // Try "$X,XXX.XX USD" or "$X,XXX" patterns from PayPal HTML
            if ( preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?)\s*USD/i', $paypal, $m ) ) {
                $price_raw = $m[1];
            } elseif ( preg_match( '/amount["\s]+value["\s]*=\s*["\']([0-9.]+)["\']/i', $paypal, $m ) ) {
                $price_raw = $m[1];
            }
        }
        $price_clean  = $price_raw ? preg_replace( '/[^0-9.]/', '', $price_raw ) : '';
        $price_display = $price_clean ? '$' . number_format( (float) $price_clean, 0 ) : '';

        // ---- Hero image(s) ----
        $hero_img_url  = '';
        $hero_img_alt  = esc_attr( $plan_name );
        $thumb_id      = get_post_thumbnail_id( $pid );
        if ( $thumb_id ) {
            $hero_src = wp_get_attachment_image_src( $thumb_id, 'plan-hero' );
            if ( $hero_src ) { $hero_img_url = $hero_src[0]; }
        }
        // Fallback: plan_image ACF field
        if ( empty( $hero_img_url ) && $plan_image ) {
            $hero_img_url = is_array( $plan_image ) ? $plan_image['url'] : $plan_image;
        }

        // ---- Gallery thumbnails from post gallery shortcode or attached images ----
        $gallery_images = array();
        if ( $thumb_id ) {
            $gallery_images[] = array(
                'id'  => $thumb_id,
                'url' => $hero_img_url,
                'alt' => $hero_img_alt,
            );
        }
        // Get other attached images (up to 5 more)
        $attached = get_posts( array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_parent'    => $pid,
            'post_status'    => 'inherit',
            'posts_per_page' => 6,
            'exclude'        => $thumb_id ? array( $thumb_id ) : array(),
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );
        foreach ( $attached as $att ) {
            $att_src = wp_get_attachment_image_src( $att->ID, 'plan-hero' );
            if ( $att_src ) {
                $gallery_images[] = array(
                    'id'  => $att->ID,
                    'url' => $att_src[0],
                    'alt' => esc_attr( $att->post_excerpt ?: $plan_name ),
                );
            }
        }

        // ---- Plan style tag ----
        $plan_terms = get_the_terms( $pid, 'home-plans_categories' );
        $plan_style = '';
        if ( $plan_terms && ! is_wp_error( $plan_terms ) ) {
            $plan_style = implode( ' &middot; ', wp_list_pluck( $plan_terms, 'name' ) );
        }

        ?>

        <!-- ==================== HERO ==================== -->
        <section class="mhp-plan-hero" aria-label="<?php echo esc_attr( $plan_name ); ?> Overview">
          <div class="mhp-plan-hero-inner">

            <!-- Left: Gallery -->
            <div class="mhp-plan-gallery">

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
                  <div class="mhp-gallery-placeholder">
                    <span>Photo coming soon</span>
                  </div>
                <?php endif; ?>

                <?php if ( $plan_style ) : ?>
                  <div class="mhp-gallery-badge"><?php echo esc_html( $plan_style ); ?></div>
                <?php endif; ?>
              </div><!-- /.mhp-gallery-main -->

              <!-- Thumbnail strip -->
              <?php if ( count( $gallery_images ) > 1 ) : ?>
                <div class="mhp-gallery-thumbs" role="list" aria-label="Plan photo gallery">
                  <?php foreach ( $gallery_images as $i => $gimg ) : ?>
                    <div
                      class="mhp-gallery-thumb <?php echo $i === 0 ? 'active' : ''; ?>"
                      role="listitem"
                      tabindex="0"
                      data-full="<?php echo esc_url( $gimg['url'] ); ?>"
                      data-alt="<?php echo esc_attr( $gimg['alt'] ); ?>"
                      aria-label="View photo <?php echo $i + 1; ?>"
                    >
                      <?php echo wp_get_attachment_image( $gimg['id'], 'plan-thumb', false, array(
                        'alt'     => $gimg['alt'],
                        'loading' => 'lazy',
                      ) ); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

            </div><!-- /.mhp-plan-gallery -->

            <!-- Right: Plan info -->
            <div class="mhp-plan-info" id="mhp-buy">

              <?php if ( $plan_style ) : ?>
                <div class="mhp-plan-badge">
                  <span class="mhp-plan-badge-dot"></span>
                  <?php echo esc_html( $plan_style ); ?>
                </div>
              <?php endif; ?>

              <?php
              // Breadcrumb
              $terms_for_breadcrumb = get_the_terms( $pid, 'home-plans_categories' );
              if ( $terms_for_breadcrumb && ! is_wp_error( $terms_for_breadcrumb ) ) :
                $term = reset( $terms_for_breadcrumb );
              ?>
                <p class="mhp-plan-breadcrumb">
                  <a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">House Plans</a>
                  <span aria-hidden="true"> / </span>
                  <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
                </p>
              <?php endif; ?>

              <h1 class="mhp-plan-title"><?php echo esc_html( $plan_name ); ?></h1>

              <?php if ( $sqft || $bedrooms ) : ?>
                <p class="mhp-plan-subtitle">
                  <?php
                  $parts = array();
                  if ( $bedrooms )  { $parts[] = $bedrooms . '-Bedroom'; }
                  if ( $sqft )      { $parts[] = number_format( (int) $sqft ) . ' sq ft'; }
                  if ( $lot_style ) { $parts[] = esc_html( $lot_style ); }
                  echo implode( ' &middot; ', $parts );
                  ?>
                </p>
              <?php endif; ?>

              <!-- Specs grid -->
              <div class="mhp-specs-grid" aria-label="Plan specifications">
                <?php if ( $sqft ) : ?>
                  <div class="mhp-spec">
                    <span class="mhp-spec-value"><?php echo esc_html( number_format( (int) $sqft ) ); ?></span>
                    <span class="mhp-spec-label">Sq Ft</span>
                  </div>
                <?php endif; ?>
                <?php if ( $bedrooms ) : ?>
                  <div class="mhp-spec">
                    <span class="mhp-spec-value"><?php echo esc_html( $bedrooms ); ?></span>
                    <span class="mhp-spec-label">Bedrooms</span>
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
                <?php if ( $garage ) : ?>
                  <div class="mhp-spec">
                    <span class="mhp-spec-value"><?php echo esc_html( $garage ); ?></span>
                    <span class="mhp-spec-label">Garage</span>
                  </div>
                <?php endif; ?>
                <?php if ( $width ) : ?>
                  <div class="mhp-spec">
                    <span class="mhp-spec-value"><?php echo esc_html( $width ); ?></span>
                    <span class="mhp-spec-label">Width</span>
                  </div>
                <?php endif; ?>
                <?php if ( $depth ) : ?>
                  <div class="mhp-spec">
                    <span class="mhp-spec-value"><?php echo esc_html( $depth ); ?></span>
                    <span class="mhp-spec-label">Depth</span>
                  </div>
                <?php endif; ?>
              </div><!-- /.mhp-specs-grid -->

              <!-- Price + Buy -->
              <div class="mhp-price-block">
                <?php if ( $price_display ) : ?>
                  <div class="mhp-price-row">
                    <div>
                      <span class="mhp-price-label">PDF Plans</span>
                      <span class="mhp-price-amount"><?php echo esc_html( $price_display ); ?></span>
                    </div>
                    <div class="mhp-price-divider"></div>
                    <div class="mhp-price-secondary">
                      Instant download<br>Permit-ready
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ( $paypal ) : ?>
                  <div class="mhp-paypal-wrap">
                    <?php echo $paypal; // PayPal form HTML — already sanitized upstream ?>
                  </div>
                <?php else : ?>
                  <a
                    href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"
                    class="mhp-btn-buy"
                    aria-label="Buy <?php echo esc_attr( $plan_name ); ?>"
                  >
                    <?php echo $price_display ? 'Buy Plan — ' . esc_html( $price_display ) : 'Get This Plan'; ?>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                  </a>
                <?php endif; ?>
              </div><!-- /.mhp-price-block -->

              <!-- What's Included checklist -->
              <div class="mhp-whats-included">
                <p class="mhp-included-heading">What's Included</p>
                <ul class="mhp-included-list" role="list">
                  <li>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    PDF plan set (instant delivery)
                  </li>
                  <li>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    All floor plans — dimensioned
                  </li>
                  <li>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Front, side &amp; rear elevations
                  </li>
                  <li>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Foundation &amp; roof plan
                  </li>
                  <li>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    CAD file available — ask Max
                  </li>
                </ul>
              </div><!-- /.mhp-whats-included -->

            </div><!-- /.mhp-plan-info -->
          </div><!-- /.mhp-plan-hero-inner -->
        </section><!-- /.mhp-plan-hero -->

        <!-- ==================== STICKY BUY BAR ==================== -->
        <div id="mhp-sticky-bar" class="mhp-sticky-bar" role="complementary" aria-label="Quick purchase bar">
          <div class="mhp-sticky-bar-inner">
            <div class="mhp-sticky-info">
              <span class="mhp-sticky-name"><?php echo esc_html( $plan_name ); ?></span>
              <span class="mhp-sticky-stats">
                <?php
                $sticky_parts = array();
                if ( $sqft )     { $sticky_parts[] = number_format( (int) $sqft ) . ' sq ft'; }
                if ( $bedrooms ) { $sticky_parts[] = $bedrooms . ' bed'; }
                if ( $bathrooms ){ $sticky_parts[] = $bathrooms . ' bath'; }
                echo implode( ' &nbsp;&middot;&nbsp; ', $sticky_parts );
                ?>
              </span>
            </div>
            <div class="mhp-sticky-right">
              <?php if ( $price_display ) : ?>
                <div class="mhp-sticky-price-block">
                  <span class="mhp-sticky-price" id="mhp-sticky-price"><?php echo esc_html( $price_display ); ?></span>
                  <span class="mhp-sticky-price-label">PDF Plans</span>
                </div>
              <?php endif; ?>
              <?php if ( $paypal ) : ?>
                <div class="mhp-sticky-paypal"><?php echo $paypal; ?></div>
              <?php else : ?>
                <a href="#mhp-buy" class="mhp-btn-sticky">
                  Buy Plan
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div><!-- /.mhp-sticky-bar -->

        <!-- ==================== DESCRIPTION ==================== -->
        <?php if ( $plan_description ) : ?>
          <section class="mhp-section mhp-section--cream mhp-plan-description" aria-label="Plan description">
            <div class="mhp-section-inner">
              <div class="mhp-section-eyebrow">
                <span class="mhp-eyebrow-line"></span>
                <span class="mhp-eyebrow-text">About This Plan</span>
              </div>
              <h2 class="mhp-section-title">Plan <em>Overview</em></h2>
              <div class="mhp-plan-description-content">
                <?php echo wp_kses_post( $plan_description ); ?>
              </div>
            </div>
          </section>
        <?php endif; ?>

        <!-- ==================== FLOOR PLANS ==================== -->
        <?php if ( $floor_plans ) : ?>
          <section class="mhp-section mhp-section--white mhp-floor-plans" aria-label="Floor plans">
            <div class="mhp-section-inner">
              <div class="mhp-section-eyebrow">
                <span class="mhp-eyebrow-line"></span>
                <span class="mhp-eyebrow-text">Every Room, Every Detail</span>
              </div>
              <h2 class="mhp-section-title">Explore the <em>Floor Plans</em></h2>
              <div class="mhp-floor-plans-content">
                <?php echo wp_kses_post( $floor_plans ); ?>
              </div>
            </div>
          </section>
        <?php endif; ?>

        <!-- ==================== FULL SPECS TABLE ==================== -->
        <?php
        $spec_rows = array();
        if ( $sqft )             { $spec_rows['Total Living Area']  = number_format( (int) $sqft ) . ' sq ft'; }
        if ( $bedrooms )         { $spec_rows['Bedrooms']           = $bedrooms; }
        if ( $bathrooms )        { $spec_rows['Bathrooms']          = $bathrooms; }
        if ( $stories )          { $spec_rows['Stories']            = $stories; }
        if ( $garage )           { $spec_rows['Garage']             = $garage; }
        if ( $width )            { $spec_rows['Width']              = $width; }
        if ( $depth )            { $spec_rows['Depth']              = $depth; }
        if ( $outdoor )          { $spec_rows['Outdoor Spaces']     = $outdoor; }
        if ( $roof )             { $spec_rows['Roof Style']         = $roof; }
        if ( $ceiling )          { $spec_rows['Ceiling Height']     = $ceiling; }
        if ( $exterior )         { $spec_rows['Exterior Material']  = $exterior; }
        if ( $lot_style )        { $spec_rows['Lot Style']          = $lot_style; }
        if ( $additional_rooms ) { $spec_rows['Additional Rooms']   = $additional_rooms; }
        if ( $other_features )   { $spec_rows['Other Features']     = $other_features; }

        if ( ! empty( $spec_rows ) ) :
        ?>
          <section class="mhp-section mhp-section--cream mhp-specs-table-section" aria-label="Full specifications">
            <div class="mhp-section-inner">
              <div class="mhp-section-eyebrow">
                <span class="mhp-eyebrow-line"></span>
                <span class="mhp-eyebrow-text">Full Details</span>
              </div>
              <h2 class="mhp-section-title">Plan <em>Specifications</em></h2>
              <table class="mhp-specs-table" aria-label="Plan specifications table">
                <tbody>
                  <?php foreach ( $spec_rows as $label => $value ) : ?>
                    <tr>
                      <th scope="row"><?php echo esc_html( $label ); ?></th>
                      <td><?php echo wp_kses_post( $value ); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </section>
        <?php endif; ?>

        <!-- ==================== FAQ ==================== -->
        <?php if ( ! empty( $faqs ) && is_array( $faqs ) ) : ?>
          <section class="mhp-section mhp-section--white mhp-faq-section" id="mhp-faq" aria-label="Frequently asked questions">
            <div class="mhp-section-inner">
              <div class="mhp-section-eyebrow">
                <span class="mhp-eyebrow-line"></span>
                <span class="mhp-eyebrow-text">Common Questions</span>
              </div>
              <h2 class="mhp-section-title">Before You <em>Buy</em></h2>
              <div class="mhp-faq-list" role="list">
                <?php foreach ( $faqs as $faq ) :
                  $q = isset( $faq['question'] ) ? $faq['question'] : '';
                  $a = isset( $faq['answer'] )   ? $faq['answer']   : '';
                  if ( ! $q ) { continue; }
                  $uid = 'mhp-faq-' . sanitize_title( $q );
                ?>
                  <div class="mhp-faq-item" role="listitem">
                    <button
                      class="mhp-faq-question"
                      aria-expanded="false"
                      aria-controls="<?php echo esc_attr( $uid ); ?>"
                      onclick="mhpToggleFaq(this)"
                    >
                      <span><?php echo esc_html( $q ); ?></span>
                      <svg class="mhp-faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
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
          <section class="mhp-section mhp-section--cream mhp-related-plans" aria-label="Related house plans">
            <div class="mhp-section-inner">
              <div class="mhp-section-eyebrow">
                <span class="mhp-eyebrow-line"></span>
                <span class="mhp-eyebrow-text">You Might Also Like</span>
              </div>
              <h2 class="mhp-section-title">More <em>House Plans</em></h2>
              <div class="mhp-related-grid">
                <?php
                $shown = 0;
                foreach ( $related_plans as $rel_id ) :
                  if ( $shown >= 3 ) { break; }
                  if ( ! $rel_id )   { continue; }
                  $rel_post  = get_post( $rel_id );
                  if ( ! $rel_post ) { continue; }
                  $rel_name  = function_exists( 'get_field' ) ? get_field( 'plan_name', $rel_id ) : '';
                  $rel_name  = $rel_name ?: get_the_title( $rel_id );
                  $rel_sqft  = function_exists( 'get_field' ) ? get_field( 'total_living_area', $rel_id ) : '';
                  $rel_bed   = function_exists( 'get_field' ) ? get_field( 'bedrooms',          $rel_id ) : '';
                  $rel_bath  = function_exists( 'get_field' ) ? get_field( 'bathrooms',         $rel_id ) : '';
                  $rel_price_raw = function_exists( 'get_field' ) ? get_field( 'price', $rel_id ) : '';
                  if ( ! $rel_price_raw && function_exists( 'get_field' ) ) {
                      $rel_paypal = get_field( 'paypal', $rel_id );
                      if ( $rel_paypal && preg_match( '/\$([0-9,]+(?:\.[0-9]{2})?)\s*USD/i', $rel_paypal, $rm ) ) {
                          $rel_price_raw = $rm[1];
                      }
                  }
                  $rel_price_display = $rel_price_raw ? '$' . number_format( (float) preg_replace( '/[^0-9.]/', '', $rel_price_raw ), 0 ) : '';
                  $rel_thumb = get_the_post_thumbnail_url( $rel_id, 'plan-card' );
                  $rel_url   = get_permalink( $rel_id );
                  $rel_terms = get_the_terms( $rel_id, 'home-plans_categories' );
                  $rel_style = ( $rel_terms && ! is_wp_error( $rel_terms ) )
                      ? esc_html( reset( $rel_terms )->name )
                      : '';
                  $shown++;
                ?>
                  <article class="mhp-related-card">
                    <a href="<?php echo esc_url( $rel_url ); ?>" class="mhp-related-card-link" tabindex="-1" aria-hidden="true">
                      <?php if ( $rel_thumb ) : ?>
                        <img
                          class="mhp-related-img"
                          src="<?php echo esc_url( $rel_thumb ); ?>"
                          alt="<?php echo esc_attr( $rel_name ); ?>"
                          loading="lazy"
                        />
                      <?php endif; ?>
                    </a>
                    <div class="mhp-related-info">
                      <?php if ( $rel_style ) : ?>
                        <p class="mhp-related-style"><?php echo $rel_style; ?></p>
                      <?php endif; ?>
                      <h3 class="mhp-related-name">
                        <a href="<?php echo esc_url( $rel_url ); ?>"><?php echo esc_html( $rel_name ); ?></a>
                      </h3>
                      <?php if ( $rel_sqft || $rel_bed ) : ?>
                        <p class="mhp-related-specs">
                          <?php
                          $rp = array();
                          if ( $rel_bed )  { $rp[] = $rel_bed . ' bed'; }
                          if ( $rel_bath ) { $rp[] = $rel_bath . ' bath'; }
                          if ( $rel_sqft ) { $rp[] = number_format( (int) $rel_sqft ) . ' sq ft'; }
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

        <!-- ==================== GALLERY SWITCHER JS ==================== -->
        <script>
        (function(){
          var mainImg = document.getElementById('mhpHeroMainImg');
          var thumbs  = document.querySelectorAll('.mhp-gallery-thumb');
          if (!mainImg || !thumbs.length) return;
          thumbs.forEach(function(thumb){
            function activate(){
              var fullUrl = thumb.getAttribute('data-full');
              var altText  = thumb.getAttribute('data-alt');
              if (fullUrl) {
                mainImg.src = fullUrl;
                if (altText) mainImg.alt = altText;
              }
              thumbs.forEach(function(t){ t.classList.remove('active'); });
              thumb.classList.add('active');
            }
            thumb.addEventListener('click', activate);
            thumb.addEventListener('keydown', function(e){
              if(e.key==='Enter'||e.key===' '){e.preventDefault();activate();}
            });
          });
        })();

        // FAQ accordion
        function mhpToggleFaq(btn){
          var item = btn.closest('.mhp-faq-item');
          var isOpen = item.classList.toggle('open');
          btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          var answer = item.querySelector('.mhp-faq-answer');
          if(answer){
            answer.style.maxHeight = isOpen ? answer.scrollHeight + 'px' : '0';
          }
        }
        </script>

    <?php } // end while
}

genesis();
