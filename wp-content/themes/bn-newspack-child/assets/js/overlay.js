/**
 * Overlay menu with accessibility (focus trap, ESC close, reduced motion).
 */

(function () {
  const hamburger = document.querySelector('.bn-hamburger');
  const overlay = document.getElementById('bn-overlay-menu');
  const closeBtn = overlay ? overlay.querySelector('.bn-overlay-close') : null;

  if (!hamburger || !overlay) {
    return;
  }

  let lastFocusedElement = null;
  const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

  function open() {
    lastFocusedElement = document.activeElement;
    overlay.setAttribute('aria-hidden', 'false');
    hamburger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';

    // Focus first focusable element inside overlay
    const firstFocusable = overlay.querySelector(focusableSelector);
    if (firstFocusable) {
      firstFocusable.focus();
    }
  }

  function close() {
    overlay.setAttribute('aria-hidden', 'true');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';

    // Restore focus
    if (lastFocusedElement) {
      lastFocusedElement.focus();
      lastFocusedElement = null;
    }
  }

  function trapFocus(e) {
    if (overlay.getAttribute('aria-hidden') === 'true') {
      return;
    }

    const focusableElements = Array.from(overlay.querySelectorAll(focusableSelector));
    if (focusableElements.length === 0) {
      return;
    }

    const firstFocusable = focusableElements[0];
    const lastFocusable = focusableElements[focusableElements.length - 1];

    if (e.key === 'Tab') {
      if (e.shiftKey) {
        // Shift+Tab
        if (document.activeElement === firstFocusable) {
          e.preventDefault();
          lastFocusable.focus();
        }
      } else {
        // Tab
        if (document.activeElement === lastFocusable) {
          e.preventDefault();
          firstFocusable.focus();
        }
      }
    }
  }

  hamburger.addEventListener('click', () => {
    if (overlay.getAttribute('aria-hidden') === 'true') {
      open();
    } else {
      close();
    }
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', close);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.getAttribute('aria-hidden') === 'false') {
      close();
    }
    trapFocus(e);
  });

  // Close on click outside overlay-inner
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      close();
    }
  });
})();

// Sticky header compact state toggle
(function(){
  var header = document.querySelector('.bn-header-bar');
  if (!header) return;
  // Ensure spacer exists to avoid layout jump when fixed
  var spacer = document.querySelector('.bn-header-spacer');
  if (!spacer) {
    spacer = document.createElement('div');
    spacer.className = 'bn-header-spacer';
    var masthead = header.parentElement; // header is inside .bn-header-wrapper group
    if (masthead && masthead.parentNode) {
      masthead.parentNode.insertBefore(spacer, masthead.nextSibling);
    }
  }
  var ticking = false;
  function onScroll(){
    if (ticking) return; ticking = true;
    requestAnimationFrame(function(){
      var stuck = window.scrollY > 1;
      header.classList.toggle('is-stuck', stuck);
      header.classList.toggle('is-fixed', stuck);
      if (spacer) {
        spacer.style.height = stuck ? header.offsetHeight + 'px' : '0px';
      }
      ticking = false;
    });
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

