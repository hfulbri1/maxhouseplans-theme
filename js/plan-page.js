/**
 * plan-page.js — MaxHousePlans single plan page interactions
 * Version 2.0
 */
(function () {
  'use strict';

  /* ── Sticky bar ──────────────────────────────────────────── */
  var stickyBar = document.getElementById('mhpStickyBar');
  if (stickyBar) {
    window.addEventListener('scroll', function () {
      stickyBar.classList.toggle('visible', window.scrollY > 500);
    }, { passive: true });
  }

  /* ── Scroll fade-in (IntersectionObserver) ───────────────── */
  var fadeEls = document.querySelectorAll('.mhp-fade-in');
  if ('IntersectionObserver' in window) {
    var fadeObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          fadeObs.unobserve(e.target);
        }
      });
    }, { threshold: 0.07 });
    fadeEls.forEach(function (el) { fadeObs.observe(el); });
  } else {
    fadeEls.forEach(function (el) { el.classList.add('visible'); });
  }

  /* ── Gallery: thumbnails + prev/next arrows + touch swipe ─── */
  var heroImg    = document.getElementById('mhpHeroMainImg');
  var thumbs     = document.querySelectorAll('.mhp-hero-thumb');
  var prevBtn    = document.getElementById('mhpGalleryPrev');
  var nextBtn    = document.getElementById('mhpGalleryNext');
  var counterCur = document.getElementById('mhpGalleryCurrent');
  var counterTot = document.getElementById('mhpGalleryTotal');
  var currentIdx = 0;
  var thumbArr   = Array.prototype.slice.call(thumbs);

  if (counterTot) counterTot.textContent = thumbArr.length || 1;

  function setActiveThumb(idx) {
    if (!thumbArr.length) return;
    idx = (idx + thumbArr.length) % thumbArr.length;
    currentIdx = idx;
    var fullSrc = thumbArr[idx].getAttribute('data-full');
    if (heroImg && fullSrc) {
      heroImg.style.opacity = '0.3';
      setTimeout(function () {
        heroImg.src = fullSrc;
        heroImg.style.opacity = '1';
      }, 180);
    }
    thumbArr.forEach(function (t) { t.classList.remove('active'); });
    thumbArr[idx].classList.add('active');
    if (counterCur) counterCur.textContent = idx + 1;
    thumbArr[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  }

  thumbArr.forEach(function (thumb, i) {
    thumb.addEventListener('click', function () {
      if (thumb.classList.contains('mhp-thumb-more')) return;
      setActiveThumb(i);
    });
  });

  if (prevBtn) prevBtn.addEventListener('click', function () { setActiveThumb(currentIdx - 1); });
  if (nextBtn) nextBtn.addEventListener('click', function () { setActiveThumb(currentIdx + 1); });

  document.addEventListener('keydown', function (e) {
    if (!heroImg) return;
    if (e.key === 'ArrowLeft')  setActiveThumb(currentIdx - 1);
    if (e.key === 'ArrowRight') setActiveThumb(currentIdx + 1);
  });

  var touchStartX = 0;
  var heroWrap = heroImg ? heroImg.closest('.mhp-hero-video-wrap') : null;
  if (heroWrap) {
    heroWrap.addEventListener('touchstart', function (e) { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
    heroWrap.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].screenX - touchStartX;
      if (Math.abs(dx) > 40) dx < 0 ? setActiveThumb(currentIdx + 1) : setActiveThumb(currentIdx - 1);
    }, { passive: true });
  }

  /* ── Floor plan tab switcher ─────────────────────────────── */
  window.mhpShowFloor = function (floor, btn) {
    document.querySelectorAll('.mhp-floor-content').forEach(function (f) {
      f.classList.remove('active');
    });
    document.querySelectorAll('.mhp-floor-btn').forEach(function (b) {
      b.classList.remove('active');
    });
    var target = document.getElementById('mhp-floor-' + floor);
    if (target) target.classList.add('active');
    if (btn) btn.classList.add('active');
  };

  /* ── FAQ accordion ───────────────────────────────────────── */
  document.querySelectorAll('.mhp-faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item   = btn.closest('.mhp-faq-item');
      var isOpen = item.classList.contains('open');

      // Close all
      document.querySelectorAll('.mhp-faq-item.open').forEach(function (i) {
        i.classList.remove('open');
        var q = i.querySelector('.mhp-faq-q');
        if (q) q.setAttribute('aria-expanded', 'false');
      });

      // Open clicked (if it was closed)
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ── Build cost calculator ───────────────────────────────── */
  // Core logic is inline in the PHP (using PHP-injected sqft values).
  // This file can override or extend if needed.
  // mhpUpdateCost() is called on DOMContentLoaded from inline script.

})();

  /* ── Gallery: thumbnails + prev/next arrows + touch swipe ─── */
  var heroImg    = document.getElementById('mhpHeroMainImg');
  var thumbs     = document.querySelectorAll('.mhp-hero-thumb');
  var prevBtn    = document.getElementById('mhpGalleryPrev');
  var nextBtn    = document.getElementById('mhpGalleryNext');
  var counterCur = document.getElementById('mhpGalleryCurrent');
  var counterTot = document.getElementById('mhpGalleryTotal');
  var currentIdx = 0;
  var thumbArr   = Array.prototype.slice.call(thumbs);

  if (counterTot) counterTot.textContent = thumbArr.length || 1;

  function setActiveThumb(idx) {
    if (!thumbArr.length) return;
    idx = (idx + thumbArr.length) % thumbArr.length;
    currentIdx = idx;
    var fullSrc = thumbArr[idx].getAttribute('data-full');
    if (heroImg && fullSrc) {
      heroImg.style.opacity = '0.3';
      setTimeout(function () {
        heroImg.src = fullSrc;
        heroImg.style.opacity = '1';
      }, 180);
    }
    thumbArr.forEach(function (t) { t.classList.remove('active'); });
    thumbArr[idx].classList.add('active');
    if (counterCur) counterCur.textContent = idx + 1;
    // Scroll thumb into view
    thumbArr[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  }

  thumbArr.forEach(function (thumb, i) {
    thumb.addEventListener('click', function () {
      // Skip "more" overlay clicks — let FooBox handle those
      if (thumb.classList.contains('mhp-thumb-more')) return;
      setActiveThumb(i);
    });
  });

  if (prevBtn) prevBtn.addEventListener('click', function () { setActiveThumb(currentIdx - 1); });
  if (nextBtn) nextBtn.addEventListener('click', function () { setActiveThumb(currentIdx + 1); });

  // Keyboard arrows
  document.addEventListener('keydown', function (e) {
    if (!heroImg) return;
    if (e.key === 'ArrowLeft')  setActiveThumb(currentIdx - 1);
    if (e.key === 'ArrowRight') setActiveThumb(currentIdx + 1);
  });

  // Touch swipe
  var touchStartX = 0;
  var heroWrap = heroImg ? heroImg.closest('.mhp-hero-video-wrap') : null;
  if (heroWrap) {
    heroWrap.addEventListener('touchstart', function (e) {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    heroWrap.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].screenX - touchStartX;
      if (Math.abs(dx) > 40) {
        dx < 0 ? setActiveThumb(currentIdx + 1) : setActiveThumb(currentIdx - 1);
      }
    }, { passive: true });
  }