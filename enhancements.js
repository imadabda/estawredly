'use strict';
/* ======================================
   إستوردلي – Modern Enhancements JS
   تحسينات تفاعلية احترافية
   ====================================== */

/* ══════════════════════════════════════
   PAGE LOADER
   ══════════════════════════════════════ */
(function initLoader() {
  // Create loader
  const loader = document.createElement('div');
  loader.className = 'page-loader';
  loader.id = 'page-loader';
  loader.innerHTML = `
    <div class="loader-brand">إستوردلي</div>
    <div class="loader-dots">
      <div class="loader-dot"></div>
      <div class="loader-dot"></div>
      <div class="loader-dot"></div>
    </div>
  `;
  document.body.appendChild(loader);

  window.addEventListener('load', () => {
    setTimeout(() => loader.classList.add('hidden'), 300);
    setTimeout(() => loader.remove(), 900);
  });
  // Fallback: remove after 2.5s regardless
  setTimeout(() => { loader.classList.add('hidden'); setTimeout(() => loader.remove(), 500); }, 2500);
})();

/* ══════════════════════════════════════
   CURSOR (desktop only)
   ══════════════════════════════════════ */
(function initCursor() {
  return; // Disabled - causes floating ring element on some devices

  const dot  = document.createElement('div'); dot.className  = 'cursor-dot';
  const ring = document.createElement('div'); ring.className = 'cursor-ring';
  document.body.append(dot, ring);

  let rx = 0, ry = 0;
  window.addEventListener('mousemove', e => {
    dot.style.left = e.clientX + 'px'; dot.style.top = e.clientY + 'px';
    rx += (e.clientX - rx) * .12;
    ry += (e.clientY - ry) * .12;
    ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
  });

  // Request animation frame for smoother ring follow
  (function loop() {
    ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
    requestAnimationFrame(loop);
  })();

  document.querySelectorAll('a, button, .p-card, .cat-pill, .banner-card').forEach(el => {
    el.addEventListener('mouseenter', () => ring.classList.add('expand'));
    el.addEventListener('mouseleave', () => ring.classList.remove('expand'));
  });

  // Hide on leave
  document.addEventListener('mouseleave', () => { dot.style.opacity='0'; ring.style.opacity='0'; });
  document.addEventListener('mouseenter', () => { dot.style.opacity='1'; ring.style.opacity='1'; });
})();

/* ══════════════════════════════════════
   SCROLL REVEAL
   ══════════════════════════════════════ */
(function initReveal() {
  const targets = [
    '.p-card', '.source-card', '.ihb-card', '.review-card', '.banner-card',
    '.trust-item', '.split-feat', '.brand-item', '.flash-header',
    '.sec-hdr', '.promo-content', '.split-left', '.split-right',
    '.ihb-text', '.ihb-cards', '.sec-title'
  ];

  const elements = document.querySelectorAll(targets.join(', '));
  elements.forEach((el, i) => {
    el.classList.add('reveal');
    // Stagger cards in a grid
    const parent = el.parentElement;
    const siblings = [...parent.children];
    const idx = siblings.indexOf(el) % 4;
    if (idx > 0) el.classList.add(`reveal-delay-${idx}`);
  });

  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

  elements.forEach(el => io.observe(el));
})();

/* ══════════════════════════════════════
   HEADER SCROLL SHADOW
   ══════════════════════════════════════ */
(function initHeaderScroll() {
  const header = document.getElementById('header');
  if (!header) return;
  let ticking = false;
  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        header.classList.toggle('scrolled', window.scrollY > 40);
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
})();

/* ══════════════════════════════════════
   HERO SLIDER PROGRESS BAR
   ══════════════════════════════════════ */
(function initSliderProgress() {
  const hero = document.querySelector('.hero-slider');
  if (!hero) return;
  const progressWrap = document.createElement('div'); progressWrap.className = 'slider-progress';
  const progressBar  = document.createElement('div'); progressBar.className  = 'slider-progress-bar';
  progressWrap.appendChild(progressBar);
  hero.appendChild(progressWrap);

  const SLIDE_DURATION = 5000;

  function animateProgress() {
    progressBar.style.transition = 'none';
    progressBar.style.width = '0%';
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        progressBar.style.transition = `width ${SLIDE_DURATION}ms linear`;
        progressBar.style.width = '100%';
      });
    });
  }

  // Hook into existing slide changes
  const origChange = window.changeSlide;
  const origGo     = window.goSlide;

  if (typeof origChange === 'function') {
    window.changeSlide = function(...args) { origChange(...args); animateProgress(); };
  }
  if (typeof origGo === 'function') {
    window.goSlide = function(...args) { origGo(...args); animateProgress(); };
  }

  animateProgress();
  setInterval(animateProgress, SLIDE_DURATION);
})();

/* ══════════════════════════════════════
   SMOOTH COUNTER ANIMATION
   ══════════════════════════════════════ */
(function initCounters() {
  function animateCount(el) {
    const target = parseFloat(el.dataset.count || el.textContent.replace(/[^0-9.]/g,''));
    const suffix = el.textContent.replace(/[0-9.]/g, '');
    const duration = 1800;
    const start = performance.now();
    const isFloat = String(target).includes('.');

    function update(now) {
      const progress = Math.min((now - start) / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3); // cubic ease out
      const val = target * ease;
      el.textContent = (isFloat ? val.toFixed(1) : Math.floor(val)) + suffix;
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  }

  const counters = document.querySelectorAll('.ihb-num');
  if (!counters.length) return;

  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) { animateCount(e.target); io.unobserve(e.target); }
    });
  }, { threshold: .5 });
  counters.forEach(c => { c.dataset.count = parseFloat(c.textContent); io.observe(c); });
})();

/* ══════════════════════════════════════
   TILT EFFECT on product cards
   ══════════════════════════════════════ */
(function initTilt() {
  if (window.matchMedia('(hover: none)').matches) return;
  document.querySelectorAll('.p-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect  = card.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width  - .5;
      const y = (e.clientY - rect.top)  / rect.height - .5;
      card.style.transform = `translateY(-10px) scale(1.01) perspective(600px) rotateX(${-y*6}deg) rotateY(${x*6}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });
})();

/* ══════════════════════════════════════
   SMOOTH ANCHOR SCROLL
   ══════════════════════════════════════ */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

/* ══════════════════════════════════════
   IMAGE LAZY LOAD with fade
   ══════════════════════════════════════ */
(function initLazyImages() {
  const imgs = document.querySelectorAll('img[src]');
  imgs.forEach(img => {
    img.style.opacity = '0';
    if (img.complete) { img.style.opacity = '1'; return; }
    img.addEventListener('load', () => { img.style.opacity = '1'; });
    img.addEventListener('error', () => { img.style.opacity = '0.3'; });
  });
})();

/* ══════════════════════════════════════
   PROMO FLOATING SHAPES
   ══════════════════════════════════════ */
(function initPromoShapes() {
  const promo = document.querySelector('.promo-banner');
  if (!promo) return;
  [1,2,3].forEach(i => {
    const s = document.createElement('div'); s.className = 'promo-shape'; promo.appendChild(s);
  });
})();

/* ══════════════════════════════════════
   SECTION TITLE LEFT BAR (RTL)
   ══════════════════════════════════════ */
(function fixSectionTitles() {
  document.querySelectorAll('.sec-hdr .sec-title').forEach(title => {
    title.style.paddingRight = '16px';
  });
})();

/* ══════════════════════════════════════
   PRODUCT IMAGE FALLBACK
   ══════════════════════════════════════ */
document.querySelectorAll('.p-card-img img, .ci-img img').forEach(img => {
  img.addEventListener('error', function() {
    this.parentElement.style.background = 'linear-gradient(135deg,#f1f5f9,#e2e8f0)';
    const emoji = document.createElement('span');
    emoji.textContent = '📦';
    emoji.style.cssText = 'font-size:48px;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)';
    this.parentElement.appendChild(emoji);
    this.remove();
  });
});

/* ══════════════════════════════════════
   KEYBOARD NAVIGATION – ESC closes modals
   ══════════════════════════════════════ */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  document.querySelectorAll('.drawer-mask.open').forEach(m => m.click());
  document.querySelectorAll('.modal-mask.open').forEach(m => m.click());
});

/* ══════════════════════════════════════
   SEARCH SHORTCUT (/ key)
   ══════════════════════════════════════ */
document.addEventListener('keydown', e => {
  if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
    e.preventDefault();
    const searchInput = document.getElementById('search-input');
    if (searchInput) { searchInput.focus(); searchInput.select(); }
  }
});

/* ══════════════════════════════════════
   TOUCH SWIPE on hero slider
   ══════════════════════════════════════ */
(function initSwipe() {
  const hero = document.querySelector('.hero-slider');
  if (!hero) return;
  let startX = 0;
  hero.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
  hero.addEventListener('touchend', e => {
    const diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) {
      if (typeof changeSlide === 'function') changeSlide(diff > 0 ? 1 : -1);
    }
  }, { passive: true });
})();

/* ══════════════════════════════════════
   RIPPLE on buttons
   ══════════════════════════════════════ */
(function initRipple() {
  document.querySelectorAll('.p-add-btn, .btn-primary, .checkout-btn, .load-more-btn').forEach(btn => {
    btn.style.position = 'relative';
    btn.style.overflow = 'hidden';
    btn.addEventListener('click', function(e) {
      const rect = this.getBoundingClientRect();
      const ripple = document.createElement('span');
      const size = Math.max(rect.width, rect.height);
      ripple.style.cssText = `
        position:absolute; border-radius:50%; background:rgba(255,255,255,.35);
        width:${size}px; height:${size}px;
        top:${e.clientY - rect.top - size/2}px;
        left:${e.clientX - rect.left - size/2}px;
        transform:scale(0); animation:rippleAnim .5s linear;
        pointer-events:none;
      `;
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 550);
    });
  });

  if (!document.querySelector('#ripple-style')) {
    const style = document.createElement('style');
    style.id = 'ripple-style';
    style.textContent = '@keyframes rippleAnim{to{transform:scale(2.5);opacity:0}}';
    document.head.appendChild(style);
  }
})();

/* ══════════════════════════════════════
   RE-APPLY cursor expand on dynamically
   rendered product cards
   ══════════════════════════════════════ */
(function watchDynamicCards() {
  if (window.matchMedia('(hover: none)').matches) return;
  const ring = document.querySelector('.cursor-ring');
  if (!ring) return;
  const mo = new MutationObserver(() => {
    document.querySelectorAll('.p-card:not([data-cursor])').forEach(el => {
      el.dataset.cursor = '1';
      el.addEventListener('mouseenter', () => ring.classList.add('expand'));
      el.addEventListener('mouseleave', () => ring.classList.remove('expand'));
    });
  });
  mo.observe(document.body, { childList: true, subtree: true });
})();

console.log('%c✨ إستوردلي – Enhanced Mode Active', 'color:#2563eb;font-weight:900;font-size:14px');
