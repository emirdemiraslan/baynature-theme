/**
 * Overlay menu with accessibility (focus trap, ESC close, reduced motion).
 */

(function () {
  const hamburgers = document.querySelectorAll('.bn-hamburger');
  const overlay = document.getElementById('bn-overlay-menu');
  const closeBtn = overlay ? overlay.querySelector('.bn-overlay-close') : null;

  if (hamburgers.length === 0 || !overlay) {
    return;
  }

  let lastFocusedElement = null;
  const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

  function open() {
    lastFocusedElement = document.activeElement;
    overlay.setAttribute('aria-hidden', 'false');
    // Update all hamburger buttons
    hamburgers.forEach(h => h.setAttribute('aria-expanded', 'true'));
    document.body.style.overflow = 'hidden';

    // Focus first focusable element inside overlay
    const firstFocusable = overlay.querySelector(focusableSelector);
    if (firstFocusable) {
      firstFocusable.focus();
    }
  }

  function close() {
    overlay.setAttribute('aria-hidden', 'true');
    // Update all hamburger buttons
    hamburgers.forEach(h => h.setAttribute('aria-expanded', 'false'));
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

  // Attach click handler to all hamburger buttons
  hamburgers.forEach(hamburger => {
    hamburger.addEventListener('click', () => {
      if (overlay.getAttribute('aria-hidden') === 'true') {
        open();
      } else {
        close();
      }
    });
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

// Dual header scroll behavior
(function(){
  var preScrollHeader = document.querySelector('.bn-header-bar-pre-scroll');
  var afterScrollHeader = document.querySelector('.bn-header-bar-after-scroll');
  
  if (!preScrollHeader || !afterScrollHeader) return;
  
  var ticking = false;
  var scrollThreshold = 100;
  
  function onScroll(){
    if (ticking) return; 
    ticking = true;
    requestAnimationFrame(function(){
      var scrolled = window.scrollY > scrollThreshold;
      
      // Toggle body class
      document.body.classList.toggle('header-scrolled', scrolled);
      
      // Toggle pre-scroll header visibility
      preScrollHeader.classList.toggle('is-hidden', scrolled);
      
      // Toggle after-scroll header visibility
      afterScrollHeader.classList.toggle('is-visible', scrolled);
      
      ticking = false;
    });
  }
  
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // Initial check
})();

