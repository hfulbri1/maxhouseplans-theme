/**
 * MaxHousePlans — Nav / Header JS
 * - Adds .scrolled class to nav after 80px scroll (subtle shadow)
 * - Mobile hamburger toggle (full-screen overlay)
 * - Smooth close on nav link click (mobile)
 */
(function () {
  'use strict';

  var nav       = document.getElementById('mhp-nav');
  var hamburger = document.getElementById('mhp-hamburger');
  var mobileMenu = document.getElementById('mhp-mobile-menu');
  var mobileLinks = document.querySelectorAll('.mhp-mobile-nav a');
  var body      = document.body;

  if (!nav) return;

  // ---- Scroll: add .scrolled after 80px ----
  var ticking = false;
  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(function () {
        if (window.scrollY > 80) {
          nav.classList.add('scrolled');
        } else {
          nav.classList.remove('scrolled');
        }
        ticking = false;
      });
      ticking = true;
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });

  // ---- Hamburger toggle ----
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', function () {
      var isOpen = mobileMenu.classList.toggle('open');
      hamburger.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on link click
    mobileLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        body.style.overflow = '';
      });
    });

    // Close on overlay background click
    mobileMenu.addEventListener('click', function (e) {
      if (e.target === mobileMenu) {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        body.style.overflow = '';
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        body.style.overflow = '';
      }
    });
  }
})();
