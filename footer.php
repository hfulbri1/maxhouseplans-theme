<?php
/**
 * MaxHousePlans — Custom Genesis Footer
 * 3-column layout: Brand | Quick Links | Contact
 * Warm gray / charcoal background
 */
?>
  </div><!-- /#content .site-content -->
</div><!-- /#page .site -->

<!-- ===================== SITE FOOTER ===================== -->
<footer class="mhp-footer" role="contentinfo">

  <div class="mhp-footer-inner">

    <!-- Column 1: Brand -->
    <div class="mhp-footer-col mhp-footer-brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mhp-footer-logo" aria-label="MaxHousePlans Home">
        Max<span>House</span>Plans
      </a>
      <p class="mhp-footer-tagline">Designed by a builder. Built for real life.</p>
      <p class="mhp-footer-bio">
        25+ years of residential design experience in West Georgia and East Alabama.
        Every plan is drawn by Max — not a template factory.
      </p>
    </div>

    <!-- Column 2: Quick Links -->
    <div class="mhp-footer-col">
      <h4 class="mhp-footer-heading">Quick Links</h4>
      <nav aria-label="Footer navigation">
        <ul class="mhp-footer-links" role="list">
          <li><a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">Browse House Plans</a></li>
          <li><a href="<?php echo esc_url( home_url( '/home-plans/' ) ); ?>">Plan Categories</a></li>
          <li><a href="<?php echo esc_url( home_url( '/home-plan-modifications/' ) ); ?>">Custom Modifications</a></li>
          <li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About Max</a></li>
          <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Contact</a></li>
          <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
        </ul>
      </nav>
    </div>

    <!-- Column 3: Contact -->
    <div class="mhp-footer-col">
      <h4 class="mhp-footer-heading">Contact</h4>
      <ul class="mhp-footer-contact" role="list">
        <li>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.79a19.79 19.79 0 01-3.07-8.7 2 2 0 011.99-2.18h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 6.91A16 16 0 0015.91 14.7l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
          <a href="tel:+17065550100">(706) 555-0100</a>
        </li>
        <li>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Send a Message</a>
        </li>
        <li>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          West Georgia &amp; East Alabama
        </li>
      </ul>

      <?php if ( is_active_sidebar( 'mhp_footer' ) ) : ?>
        <div class="mhp-footer-widgets">
          <?php dynamic_sidebar( 'mhp_footer' ); ?>
        </div>
      <?php endif; ?>
    </div>

  </div><!-- /.mhp-footer-inner -->

  <!-- Footer bottom bar -->
  <div class="mhp-footer-bottom">
    <div class="mhp-footer-bottom-inner">
      <p class="mhp-footer-copy">
        &copy; <?php echo esc_html( date( 'Y' ) ); ?> Max Fulbright Designs &mdash; MaxHousePlans.com. All rights reserved.
      </p>
      <p class="mhp-footer-tagline-bottom">
        25+ Years Building in West Georgia &amp; East Alabama
      </p>
    </div>
  </div>

</footer><!-- /.mhp-footer -->

<?php wp_footer(); ?>
</body>
</html>
