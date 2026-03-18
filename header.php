<?php
/**
 * MaxHousePlans — Custom Genesis Header
 * Frosted glass fixed nav with DM Serif Display logo.
 * Replaces the Genesis default header output.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ===================== SITE NAV ===================== -->
<nav id="mhp-nav" class="mhp-nav" role="navigation" aria-label="Main navigation">

  <!-- Logo -->
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mhp-nav-logo" aria-label="MaxHousePlans Home">
    Max<span>House</span>Plans
  </a>

  <!-- Desktop nav links -->
  <ul class="mhp-nav-links" role="list">
    <li><a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">House Plans</a></li>
    <li><a href="<?php echo esc_url( home_url( '/home-plans/' ) ); ?>">Categories</a></li>
    <li><a href="<?php echo esc_url( home_url( '/home-plan-modifications/' ) ); ?>">Custom Design</a></li>
    <li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About</a></li>
  </ul>

  <!-- Desktop right: phone + CTA -->
  <div class="mhp-nav-right">
    <a href="tel:+17065550100" class="mhp-nav-phone" aria-label="Call us">(706) 555-0100</a>
    <a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>" class="mhp-nav-cta">Get Plans</a>
  </div>

  <!-- Mobile hamburger -->
  <button
    id="mhp-hamburger"
    class="mhp-hamburger"
    aria-controls="mhp-mobile-menu"
    aria-expanded="false"
    aria-label="Open menu"
  >
    <span class="mhp-hamburger-bar"></span>
    <span class="mhp-hamburger-bar"></span>
    <span class="mhp-hamburger-bar"></span>
  </button>

</nav><!-- /.mhp-nav -->

<!-- ===================== MOBILE MENU OVERLAY ===================== -->
<div id="mhp-mobile-menu" class="mhp-mobile-menu" role="dialog" aria-modal="true" aria-label="Mobile navigation">
  <div class="mhp-mobile-menu-inner">

    <div class="mhp-mobile-menu-header">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mhp-mobile-logo">
        Max<span>House</span>Plans
      </a>
      <button
        id="mhp-mobile-close"
        class="mhp-mobile-close"
        aria-label="Close menu"
        onclick="document.getElementById('mhp-mobile-menu').classList.remove('open');document.getElementById('mhp-hamburger').classList.remove('open');document.getElementById('mhp-hamburger').setAttribute('aria-expanded','false');document.body.style.overflow='';"
      >&times;</button>
    </div>

    <nav class="mhp-mobile-nav" aria-label="Mobile navigation links">
      <a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>">House Plans</a>
      <a href="<?php echo esc_url( home_url( '/home-plans/' ) ); ?>">Categories</a>
      <a href="<?php echo esc_url( home_url( '/home-plan-modifications/' ) ); ?>">Custom Design</a>
      <a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About</a>
      <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Contact</a>
    </nav>

    <div class="mhp-mobile-menu-footer">
      <a href="<?php echo esc_url( home_url( '/house-plans/' ) ); ?>" class="mhp-nav-cta mhp-mobile-cta">Browse All Plans</a>
      <a href="tel:+17065550100" class="mhp-mobile-phone">(706) 555-0100</a>
    </div>

  </div>
</div><!-- /.mhp-mobile-menu -->

<div id="page" class="site">
  <div id="content" class="site-content">
