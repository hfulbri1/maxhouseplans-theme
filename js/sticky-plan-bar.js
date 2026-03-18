/**
 * MaxHousePlans — Sticky Buy Bar JS
 * - Shows sticky bar after scrolling 400px past hero
 * - Hides when back near top
 * - Throttled via requestAnimationFrame
 * - Extracts price from page DOM if not hardcoded
 */
(function () {
  'use strict';

  var bar = document.getElementById('mhp-sticky-bar');
  if (!bar) return;

  // Try to extract price from hero price element
  var priceEl = document.querySelector('.mhp-price-amount');
  var stickyPriceEl = document.getElementById('mhp-sticky-price');

  if (priceEl && stickyPriceEl && !stickyPriceEl.textContent.trim()) {
    stickyPriceEl.textContent = priceEl.textContent.trim();
  }

  var ticking = false;
  var threshold = 400;

  function update() {
    if (window.scrollY > threshold) {
      bar.classList.add('visible');
    } else {
      bar.classList.remove('visible');
    }
    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      window.requestAnimationFrame(update);
      ticking = true;
    }
  }, { passive: true });

})();
