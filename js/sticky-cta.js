/**
 * Sticky Mobile CTA Bar
 * Shows the fixed bottom bar on plan pages after user scrolls past the plan header.
 * Vanilla JS, no dependencies.
 */
(function () {
    'use strict';

    var bar    = document.getElementById('mobile-cta-bar');
    var header = document.querySelector('.plan-header');

    if (!bar || !header) return;

    var showThreshold = header.offsetTop + header.offsetHeight;

    function updateBar() {
        if (window.scrollY > showThreshold) {
            bar.classList.add('is-visible');
            bar.setAttribute('aria-hidden', 'false');
        } else {
            bar.classList.remove('is-visible');
            bar.setAttribute('aria-hidden', 'true');
        }
    }

    // Replace PayPal GIF image-button with a styled <button>
    var paypalImgs = document.querySelectorAll('.plan-header__buy-button input[type="image"]');
    paypalImgs.forEach(function (img) {
        var btn = document.createElement('button');
        btn.type = 'submit';
        btn.className = 'btn btn--accent btn--lg btn--full';
        btn.textContent = 'Buy This Plan';
        img.parentNode.replaceChild(btn, img);
    });

    // Throttled scroll handler
    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                updateBar();
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    // Recalculate threshold on resize
    window.addEventListener('resize', function () {
        showThreshold = header.offsetTop + header.offsetHeight;
        updateBar();
    }, { passive: true });

    // Initial check
    updateBar();

    // Mobile CTA bar buy button — submit the main PayPal form
    var mobileBuyBtn = document.querySelector('.mobile-cta-bar__buy');
    if (mobileBuyBtn) {
        mobileBuyBtn.addEventListener('click', function () {
            var planForm = document.querySelector('.plan-header__buy-button form');
            if (planForm) {
                planForm.submit();
            }
        });
    }
}());
