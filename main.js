'use strict';
/* ======================================
   إستوردلي – Store JavaScript
   ====================================== */

// PREVENT ZOOM ON IOS
document.addEventListener('gesturestart', function (e) {
  e.preventDefault();
});

// PRODUCT DATA
// Products loaded from Store (localStorage + products_db.js)
let PRODUCTS_LIVE = [];

// Clean legacy bloated cart items to prevent QuotaExceededError
let initialCart = [];
try {
  let cartData = JSON.parse(localStorage.getItem('store_cart') || '[]');
  if (Array.isArray(cartData)) {
    initialCart = cartData.map(i => ({
      id: i.id,
      name: i.name,
      price: i.price,
      img: i.img,
      qty: i.qty,
      pieces_per_carton: i.pieces_per_carton || 1,
      product_code: i.product_code || '',
      factory_code: i.factory_code || '',
      selectedVariants: i.selectedVariants
    }));
    localStorage.setItem('store_cart', JSON.stringify(initialCart));
  }
} catch (e) {
  console.error(e);
}

// STATE
const state = {
  cart: initialCart,
  wishlist: new Set(JSON.parse(localStorage.getItem('store_wish')||'[]')),
  currentTab: 'all',
  slideIndex: 0,
  slideTimer: null,
  saveCart() { localStorage.setItem('store_cart', JSON.stringify(this.cart)); },
  saveWish() { localStorage.setItem('store_wish', JSON.stringify([...this.wishlist])); },
  cartTotal() { return this.cart.reduce((s,i)=>s+i.price*i.qty*(i.pieces_per_carton||1), 0); },
  cartCount() { return this.cart.reduce((s,i)=>s+i.qty, 0); },
};

// CART
function addToCart(product, qty=1, variants={}) {
  if (!window.authUser || window.authUser.status !== 'active') {
      openModal('auth');
      return;
  }

  const varsStr = JSON.stringify(variants);
  const ex = state.cart.find(i => i.id === product.id && JSON.stringify(i.selectedVariants || {}) === varsStr);
  if (ex) {
    ex.qty += qty;
  } else {
    state.cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      img: product.img,
      qty: qty,
      pieces_per_carton: product.pieces_per_carton || 1,
      product_code: product.product_code || '',
      factory_code: product.factory_code || '',
      selectedVariants: Object.keys(variants).length > 0 ? variants : undefined
    });
  }
  try {
    state.saveCart();
  } catch (e) {
    console.error('Failed to save cart to localStorage:', e);
  }
  updateCartUI();
  toast(`🛒 تمت الإضافة: ${product.name}`);
  animateBadge('cart-count');
}

function removeFromCart(index) {
  state.cart.splice(index, 1);
  state.saveCart();
  updateCartUI();
}

function updateQty(index, delta) {
  const item = state.cart[index];
  if (!item) return;
  item.qty = Math.max(1, item.qty+delta);
  state.saveCart();
  updateCartUI();
}

function updateCartUI() {
  // Badge
  const count = state.cartCount();
  document.getElementById('cart-count').textContent = count;
  // Drawer
  const list  = document.getElementById('cart-items-list');
  const empty = document.getElementById('cart-empty');
  const footer= document.getElementById('cart-footer');
  if (!list) return;
  if (state.cart.length===0) {
    empty.style.display='flex'; list.style.display='none'; footer.style.display='none';
  } else {
    empty.style.display='none'; list.style.display='block'; footer.style.display='block';
    list.innerHTML = state.cart.map((i, index)=>{
      const pcs = i.pieces_per_carton || 1;
      return `
        <div class="ci">
          <div class="ci-img"><img src="${i.img}" alt="${i.name}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;"></div>
          <div class="ci-info">
            <div class="ci-name">${i.name}</div>
            ${pcs > 1 ? `<div style="font-size:11px;color:var(--text3);margin-top:2px;">الكرتونة: ${pcs} قطع (إجمالي: ${i.qty * pcs} قطعة)</div>` : ''}
            ${i.selectedVariants && Object.keys(i.selectedVariants).length > 0
              ? `<div style="font-size:11px;color:var(--text3);margin:2px 0 4px;">${Object.entries(i.selectedVariants).map(([k,v])=>`${k}: <strong>${v}</strong>`).join(' | ')}</div>`
              : ''}
            <div class="ci-price">₪${(i.price * i.qty * pcs).toFixed(2)}</div>
            <div class="ci-qty">
              <button class="qty-ctrl" onclick="updateQty(${index},-1)">−</button>
              <span class="qty-num">${i.qty}</span>
              <button class="qty-ctrl" onclick="updateQty(${index},1)">+</button>
            </div>
          </div>
          <button class="ci-del" onclick="removeFromCart(${index})" title="حذف">🗑</button>
        </div>
      `;
    }).join('');
    const total = state.cartTotal();
    document.getElementById('cart-subtotal').textContent = '₪'+total.toFixed(2);
    document.getElementById('cart-shipping').textContent = 'يُحسب عند الدفع';
    document.getElementById('cart-total-price').textContent = '₪'+total.toFixed(2);
  }
}

// WISHLIST
function toggleWish(product) {
  if (state.wishlist.has(product.id)) {
    state.wishlist.delete(product.id);
    toast('💔 تمت الإزالة من المفضلة', 'info');
  } else {
    state.wishlist.add(product.id);
    toast('❤️ تمت الإضافة للمفضلة');
  }
  state.saveWish();
  updateWishUI();
  // Update heart buttons
  document.querySelectorAll(`[data-wish="${product.id}"]`).forEach(b=>{
    b.classList.toggle('wishlisted', state.wishlist.has(product.id));
  });
}

function updateWishUI() {
  const count = state.wishlist.size;
  document.getElementById('wishlist-count').textContent = count;
  const list  = document.getElementById('wish-items-list');
  const empty = document.getElementById('wish-empty');
  if (!list) return;
  const wishProducts = PRODUCTS_LIVE.filter(p=>state.wishlist.has(p.id));
  if (wishProducts.length===0) {
    empty.style.display='flex'; list.style.display='none';
  } else {
    empty.style.display='none'; list.style.display='block';
    list.innerHTML = wishProducts.map(p=>`
      <div class="ci">
        <div class="ci-img"><img src="${p.img}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;"></div>
        <div class="ci-info">
          <div class="ci-name">${p.name}</div>
          <div class="ci-price">₪${p.price}</div>
          <button class="p-add-btn btn-sm" style="margin-top:8px" onclick='addToCart(${JSON.stringify(p)})'>🛒 أضف للسلة</button>
        </div>
        <button class="ci-del" onclick='toggleWish(${JSON.stringify(p)})'>✕</button>
      </div>
    `).join('');
  }
}

// PRODUCT CARD
function makeCard(p) {
  const disc = p.oldPrice ? Math.round((1-p.price/p.oldPrice)*100) : 0;
  const stars = '★'.repeat(Math.round(p.stars))+'☆'.repeat(5-Math.round(p.stars));
  const wished = state.wishlist.has(p.id);
  return `
    <div class="p-card" data-id="${p.id}" onclick="window.location='product.html?id=${p.id}'" style="cursor:pointer;">
      <div class="p-card-img">
        <img src="${p.img}" class="p-emoji" style="width:100%;height:100%;object-fit:cover;">
        ${p.badge?`<span class="p-badge badge-${p.badge}">${
          p.badge==='sale'?`-${disc}%`:p.badge==='new'?'جديد':p.badge==='hot'?'رائج':'مميز'
        }</span>`:''}
        <div class="p-actions">
          <button class="p-action-btn ${wished?'wishlisted':''}" data-wish="${p.id}"
            onclick='event.stopPropagation();toggleWish(${JSON.stringify(p).replace(/'/g,"&#39;")})'>
            ${wished?'❤️':'🤍'}
          </button>
          <button class="p-action-btn" onclick="event.stopPropagation();quickView(${p.id})" title="معاينة سريعة">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <div class="p-info">
        <div class="p-cat">${p.cat}</div>
        <h3 class="p-name">${p.name}</h3>
        <div class="p-stars">${stars} <span>(${p.reviews})</span></div>
        ${(window.authUser && window.authUser.status === 'active') ? `
          <div class="p-price" style="display:flex; align-items:center; flex-wrap:wrap; gap:4px;">
            <span class="p-price-main">₪${p.price}</span>
            ${p.oldPrice?`<span class="p-price-old">₪${p.oldPrice}</span><span class="p-disc">-${disc}%</span>`:''}
            ${p.pieces_per_carton > 1 ? `<span style="font-size:10px; color:#166534; background:#f0fdf4; border:1px solid #bbf7d0; padding:2px 6px; border-radius:4px; margin-right:5px; font-weight:bold;">الكرتونة: ${p.pieces_per_carton} قطع</span>` : ''}
          </div>
          <button class="p-add-btn" onclick='event.stopPropagation();addToCart(${JSON.stringify(p).replace(/'/g,"&#39;")})'>
            أضف للسلة
          </button>
        ` : `
          <div style="background:#f3f4f6;color:var(--text3);padding:10px;border-radius:8px;text-align:center;font-size:12px;font-weight:bold;margin-top:10px;">
             🔒 الأسعار للأعضاء فقط <br>
             <a href="#" onclick="event.stopPropagation();openModal('auth')" style="color:var(--p);text-decoration:underline;">سجل دخول لرؤية السعر</a>
          </div>
        `}
      </div>
    </div>
  `;
}

function renderGrid(containerId, products) {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = products.map(makeCard).join('');
  // Animate in
  el.querySelectorAll('.p-card').forEach((c,i)=>{
    c.style.opacity='0'; c.style.transform='translateY(16px)';
    setTimeout(()=>{c.style.transition='.3s ease'; c.style.opacity='1'; c.style.transform='translateY(0)'},i*60+50);
  });
}

// QUICK VIEW
function quickView(id) {
  const p = PRODUCTS_LIVE.find(x=>x.id===id);
  if (!p) return;
  const disc = p.oldPrice ? Math.round((1-p.price/p.oldPrice)*100) : 0;
  document.getElementById('qv-inner').innerHTML = `
    <div class="qv-inner-wrap">
      <div class="qv-grid">
        <div class="qv-img" style="background:none;padding:0;overflow:hidden;"><img src="${p.img}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;"></div>
        <div>
          <div class="p-cat" style="margin-bottom:8px">${p.cat}</div>
          <div class="qv-name">${p.name}</div>
          <div class="p-stars" style="font-size:16px;margin-bottom:12px">
            ${'★'.repeat(Math.round(p.stars))+'☆'.repeat(5-Math.round(p.stars))}
            <span style="color:var(--gray5);font-size:13px">(${p.reviews} تقييم)</span>
          </div>
          ${(window.authUser && window.authUser.status === 'active') ? `
          <div class="qv-price">
            ₪${p.price}
            ${p.oldPrice?`<span style="font-size:16px;font-weight:400;color:var(--gray4);text-decoration:line-through;margin-right:10px">₪${p.oldPrice}</span>`:''}
            ${disc?`<span class="p-disc">-${disc}%</span>`:''}
          </div>
          <div class="qv-desc">منتج عالي الجودة مختار بعناية. يأتي مع ضمان كامل وإمكانية الإرجاع خلال 30 يومًا من تاريخ الاستلام.</div>
          <div class="qv-actions">
            <button class="btn btn-primary btn-lg w-full" onclick='addToCart(${JSON.stringify(p).replace(/'/g,"&#39;")});closeQV()'>
              أضف للسلة
            </button>
            <button class="btn w-full" style="border:2px solid var(--gray2)" onclick='toggleWish(${JSON.stringify(p).replace(/'/g,"&#39;")})'>
              ${state.wishlist.has(p.id)?'❤️ في المفضلة':'🤍 أضف للمفضلة'}
            </button>
          </div>
          ` : `
          <div class="qv-desc">منتج عالي الجودة مختار بعناية. يأتي مع ضمان كامل وإمكانية الإرجاع خلال 30 يومًا من تاريخ الاستلام.</div>
          <div style="background:#f3f4f6;color:var(--text3);padding:16px;border-radius:8px;text-align:center;font-size:14px;font-weight:bold;margin-bottom:16px;">
             🔒 الأسعار مخفية للأعضاء فقط <br><br>
             <button class="btn btn-primary w-full" onclick="closeQV();openModal('auth')">سجل دخول لرؤية السعر والطلب</button>
          </div>
          <div class="qv-actions">
            <button class="btn w-full" style="border:2px solid var(--gray2)" onclick='toggleWish(${JSON.stringify(p).replace(/'/g,"&#39;")})'>
              ${state.wishlist.has(p.id)?'❤️ في المفضلة':'🤍 أضف للمفضلة'}
            </button>
          </div>
          `}
          <div style="margin-top:16px;display:flex;gap:16px;font-size:12px;color:var(--gray5)">
            <span>شحن سريع</span>
            <span>إرجاع 30 يوم</span>
            <span>دفع آمن</span>
          </div>
        </div>
      </div>
    </div>
  `;
  openModal('qv');
}

function closeQV() { closeModal('qv'); }

// MODALS & DRAWERS
function openDrawer(name) {
  document.getElementById(name+'-mask').classList.add('open');
  document.getElementById(name+'-drawer').classList.add('open');
  document.body.style.overflow='hidden';
}
function closeDrawer(name) {
  document.getElementById(name+'-mask').classList.remove('open');
  document.getElementById(name+'-drawer').classList.remove('open');
  document.body.style.overflow='';
}
function openModal(name) {
  document.getElementById(name+'-mask').classList.add('open');
  const m=document.getElementById(name+'-modal')||document.getElementById(name+'-modal');
  if(m) m.classList.add('open');
  document.body.style.overflow='hidden';
}
function closeModal(name) {
  document.getElementById(name+'-mask').classList.remove('open');
  const m=document.getElementById(name+'-modal');
  if(m) m.classList.remove('open');
  document.body.style.overflow='';
}


// HERO SLIDER
async function loadHeroSliders() {
  const container = document.getElementById('dynamic-hero-slides');
  const dotsContainer = document.getElementById('slider-dots');
  if (!container) return;
  
  try {
    const res = await fetch('api/get_sliders.php');
    let slides = await res.json();
    if (!slides || slides.length === 0) {
      slides = [{
        img: 'hero_cleaning1.jpg', tag: 'أدوات تنظيف احترافية', 
        title: 'كل ما تحتاجه<br/><em>في مكان واحد</em>', desc: 'أكثر من 500 منتج بأفضل الأسعار',
        btn1_text: 'تسوق الآن', btn1_link: 'shop.html', btn1_class: 'btn btn-white btn-lg'
      }];
    }
    
    container.innerHTML = slides.map((s, i) => `
      <div class="slide ${i===0?'active':''}" style="background-image:url('${s.img}')">
        <div class="slide-overlay"></div>
        <div class="container slide-content">
          ${s.tag ? `<div class="slide-tag">${s.tag}</div>` : ''}
          ${s.title ? `<h1 class="slide-title">${s.title}</h1>` : ''}
          ${s.desc ? `<p class="slide-desc">${s.desc}</p>` : ''}
          <div class="slide-btns">
            ${s.btn1_text ? `<a href="${s.btn1_link}" class="${s.btn1_class || 'btn btn-primary'}">${s.btn1_text}</a>` : ''}
            ${s.btn2_text ? `<a href="${s.btn2_link}" class="${s.btn2_class || 'btn btn-outline-white'}">${s.btn2_text}</a>` : ''}
          </div>
        </div>
      </div>
    `).join('');
    
    if (dotsContainer) {
      dotsContainer.innerHTML = slides.map((s, i) => `
        <span class="dot ${i===0?'active':''}" onclick="goSlide(${i})"></span>
      `).join('');
    }
    
    resetSlideTimer();
  } catch (e) {
    console.error('Failed to load hero sliders', e);
  }
}

function goSlide(i) {

  const slides = document.querySelectorAll('.slide');
  const dots   = document.querySelectorAll('.dot');
  slides.forEach(s=>s.classList.remove('active'));
  dots.forEach(d=>d.classList.remove('active'));
  state.slideIndex = ((i%slides.length)+slides.length)%slides.length;
  slides[state.slideIndex].classList.add('active');
  if(dots[state.slideIndex]) dots[state.slideIndex].classList.add('active');
}
function changeSlide(dir) { goSlide(state.slideIndex+dir); resetSlideTimer(); }
function resetSlideTimer() {
  clearInterval(state.slideTimer);
  state.slideTimer = setInterval(()=>goSlide(state.slideIndex+1), 3000);
}
window.goSlide   = goSlide;
window.changeSlide = changeSlide;

// COUNTDOWN TIMER
function startTimer() {
  let total = 5*3600+30*60; // 5h30m
  const h=document.getElementById('t-h');
  const m=document.getElementById('t-m');
  const s=document.getElementById('t-s');
  if(!h) return;
  function tick(){
    if(total<=0){total=5*3600+30*60;}
    const hh=Math.floor(total/3600), mm=Math.floor((total%3600)/60), ss=total%60;
    h.textContent=String(hh).padStart(2,'0');
    m.textContent=String(mm).padStart(2,'0');
    s.textContent=String(ss).padStart(2,'0');
    total--;
  }
  tick();
  setInterval(tick,1000);
}

// TOAST
function toast(msg, type='success') {
  const wrap = document.getElementById('toast-wrap');
  const t = document.createElement('div');
  t.className = `toast ${type}`;
  t.textContent = msg;
  wrap.appendChild(t);
  requestAnimationFrame(()=>{ requestAnimationFrame(()=>t.classList.add('show')); });
  setTimeout(()=>{ t.classList.add('hide'); setTimeout(()=>t.remove(),300); }, 3000);
}
window.toast = toast;

// BADGE ANIMATION
function animateBadge(id) {
  const el = document.getElementById(id);
  if(!el) return;
  el.style.transform='scale(1.5)';
  setTimeout(()=>{el.style.transform='scale(1)';},200);
}

// SEARCH
window.fillSearch = function(el) {
  document.getElementById('search-input').value = el.textContent;
};
function initSearch() {
  const btn = document.getElementById('search-btn');
  const inp = document.getElementById('search-input');
  if(!btn||!inp) return;
  function doSearch(){
    const q = inp.value.trim();
    if (q) { window.location = `shop.html?q=${encodeURIComponent(q)}`; }
  }
  btn.addEventListener('click', doSearch);
  inp.addEventListener('keypress', e=>{if(e.key==='Enter')doSearch();});
}

// PRODUCT TABS (MAIN TOP-LEVEL CATEGORIES ONLY)
async function initProductTabs() {
  const container = document.querySelector('.sec-tabs') || document.getElementById('featured-tabs');
  if (!container) return;

  let navItems = [];
  try {
    const res = await fetch('api/get_nav.php?t=' + Date.now());
    const data = await res.json();
    if (Array.isArray(data) && data.length > 0) {
      navItems = data;
    }
  } catch (e) {
    console.error('Error fetching nav for product tabs:', e);
  }

  // Filter out 'الرئيسية' or 'home' to keep only main categories
  const mainCategories = navItems.filter(item => {
    if (!item || !item.title) return false;
    const t = item.title.trim();
    return t !== 'الرئيسية' && item.id !== 'nav_home' && !item.url?.endsWith('index.html');
  });

  if (mainCategories.length > 0) {
    container.innerHTML = `
      <button class="stab active" data-cat="all">الكل</button>
      ${mainCategories.map(c => `
        <button class="stab" data-cat="${c.title}" data-id="${c.id}">${c.title}</button>
      `).join('')}
    `;
  }

  const tabs = container.querySelectorAll('.stab');
  if (!tabs.length) return;

  // Category matching helper
  function matchProductToCategory(p, catTitle, navItem) {
    if (!p) return false;
    const catLow = (catTitle || '').toLowerCase().trim();
    const pCat = (p.cat || '').toLowerCase().trim();
    const pName = (p.name || '').toLowerCase().trim();
    const pDesc = (p.desc || '').toLowerCase().trim();

    // 1. Direct match on p.cat
    if (pCat === catLow || pCat.includes(catLow) || catLow.includes(pCat)) return true;

    // 2. Match with navItem subLinks (if any)
    if (navItem && Array.isArray(navItem.subLinks)) {
      for (const sub of navItem.subLinks) {
        if (!sub || !sub.title) continue;
        const sTitle = sub.title.toLowerCase().trim();
        if (pCat === sTitle || pCat.includes(sTitle) || sTitle.includes(pCat)) return true;
        if (pName.includes(sTitle)) return true;

        if (sub.url && sub.url.includes('cat=')) {
          try {
            const urlCat = decodeURIComponent(sub.url.split('cat=')[1].split('&')[0]).toLowerCase().trim();
            if (pCat === urlCat || pCat.includes(urlCat) || urlCat.includes(pCat)) return true;
          } catch(e){}
        }
      }
    }

    // 3. Keyword / Synonym mapping for known categories
    const synMap = {
      'ادوات تنظيف': ['مسطحة', 'flat', 'ممسحة', 'مماسح', 'تنظيف', 'سلة', 'حمام', 'جلي', 'قشاط'],
      'مماسح مايكروفايبر': ['مايكروفايبر', 'مايكرو', 'microfiber', 'دوارة', 'خيوط', 'spin', 'ممسحة'],
      'مماسح قطنية': ['قطنية', 'قطن', 'cotton', 'خيوط قطن'],
      'قطع غيار': ['قطع غيار', 'غيار', 'refill', 'refills', 'بديل', 'رأس ممسحة'],
      'عروض الجملة': ['جملة', 'عرض', 'كرتونة', 'عروض', 'wholesale'],
      'منافض الغبار': ['منفضة', 'منفضة غبار', 'غبار', 'duster', 'منافض'],
      'سيمو': ['سيمو', 'semo', 'simo'],
      'فراشي تنظيف': ['فرشاة', 'فراشي', 'مرحاض', 'فرشا'],
      'منظف نوافذ': ['نافذة', 'نوافذ', 'زجاج', 'قشاطة زجاج', 'منظف نوافذ'],
      'مقابض': ['مقبض', 'مقابض', 'عصا', 'يد'],
      'جاروف': ['جاروف', 'مكنسة', 'مجرفة'],
      'برداي': ['برداي', 'ستارة', 'ستائر']
    };

    if (synMap[catTitle]) {
      for (const syn of synMap[catTitle]) {
        if (pCat.includes(syn) || pName.includes(syn) || pDesc.includes(syn)) return true;
      }
    }

    // 4. Check name or desc
    if (pName.includes(catLow) || pDesc.includes(catLow)) return true;

    return false;
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const selectedCat = tab.dataset.cat;
      if (selectedCat === 'all') {
        renderGrid('main-grid', PRODUCTS_LIVE.slice(0, 24));
        return;
      }

      const matchedNav = mainCategories.find(c => c.title === selectedCat);
      const filtered = PRODUCTS_LIVE.filter(p => matchProductToCategory(p, selectedCat, matchedNav));

      if (filtered.length > 0) {
        renderGrid('main-grid', filtered);
      } else {
        const grid = document.getElementById('main-grid');
        if (grid) {
          grid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align:center; padding: 60px 20px; color: var(--text3);">
              <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
              <p style="font-size: 16px; font-weight: 700; margin-bottom: 6px; color:var(--text);">لا توجد منتجات متوفرة حالياً في قسم "${selectedCat}"</p>
              <p style="font-size: 13px; color: var(--text3);">سيتم إضافة منتجات جديدة لهذا القسم قريباً</p>
            </div>
          `;
        }
      }
    });
  });
}

// SCROLL EFFECTS
function initScroll() {
  const btt = document.getElementById('btt-btn');
  const hdr = document.getElementById('header');
  window.addEventListener('scroll',()=>{
    const y = window.scrollY;
    if(btt) btt.classList.toggle('visible', y>400);
    if(hdr) hdr.style.boxShadow = y>10 ? '0 2px 20px rgba(0,0,0,.1)' : '0 1px 0 var(--gray2)';
  },{passive:true});
}


// AUTH MODAL
function initAuth() {
  const btnLogin   = document.getElementById('btn-login');
  const btnReg     = document.getElementById('btn-register');
  const authClose  = document.getElementById('auth-close');
  const authMask   = document.getElementById('auth-mask');
  const tabIn      = document.getElementById('tab-in');
  const tabUp      = document.getElementById('tab-up');
  const authLogin  = document.getElementById('auth-login');
  const authSignup = document.getElementById('auth-signup');

  function openAuth(mode){
    openModal('auth');
    if(mode==='signup'){
      tabIn.classList.remove('active'); tabUp.classList.add('active');
      authLogin.style.display='none'; authSignup.style.display='block';
    } else {
      tabIn.classList.add('active'); tabUp.classList.remove('active');
      authLogin.style.display='block'; authSignup.style.display='none';
    }
  }
  
  // Expose to window so we can use inline onclick in HTML
  window.openAuthModal = openAuth;

  if(btnLogin)  btnLogin.addEventListener('click', e=>{e.preventDefault();openAuth('login');});
  if(btnReg)    btnReg.addEventListener('click',   e=>{e.preventDefault();openAuth('signup');});
  if(authClose) authClose.addEventListener('click', ()=>closeModal('auth'));
  if(authMask)  authMask.addEventListener('click',  ()=>closeModal('auth'));
  if(tabIn) tabIn.addEventListener('click',()=>{
    tabIn.classList.add('active'); tabUp.classList.remove('active');
    authLogin.style.display='block'; authSignup.style.display='none';
  });
  if(tabUp) tabUp.addEventListener('click',()=>{
    tabIn.classList.remove('active'); tabUp.classList.add('active');
    authLogin.style.display='none'; authSignup.style.display='block';
  });
  document.getElementById('login-form')?.addEventListener('submit', e => {
    e.preventDefault();
    const email    = document.getElementById('login-email')?.value?.trim();
    const password = document.getElementById('login-password')?.value;
    if (!email || !password) { toast('يرجى ملء جميع الحقول', 'error'); return; }
    if (typeof Store === 'undefined') { toast('خطأ في النظام', 'error'); return; }
    const result = Store.loginUser(email, password);
    if (result.error) { toast('❌ ' + result.error, 'error'); return; }
    toast('🎉 أهلاً ' + result.user.name + '!');
    closeModal('auth');
    });
  document.getElementById('signup-form')?.addEventListener('submit', e => {
    e.preventDefault();
    const name     = document.getElementById('signup-name')?.value?.trim();
    const email    = document.getElementById('signup-email')?.value?.trim();
    const password = document.getElementById('signup-password')?.value;
    const phone    = document.getElementById('signup-phone')?.value?.trim();
    if (!name || !email || !password) { toast('يرجى ملء جميع الحقول', 'error'); return; }
    if (password.length < 6) { toast('كلمة المرور يجب أن تكون 6 أحرف على الأقل', 'error'); return; }
    if (typeof Store === 'undefined') { toast('خطأ في النظام', 'error'); return; }
    const result = Store.registerUser({ name, email, password, phone });
    if (result.error) { toast('❌ ' + result.error, 'error'); return; }
    Store.loginUser(email, password);
    toast('🎉 تم إنشاء حسابك بنجاح! مرحبًا ' + name + ' 🎊');
    closeModal('auth');
    });
}

// MOBILE MENU IS HANDLED VIA INLINE ONCLICK

// PROMO FORM
function initPromo() {
  document.getElementById('promo-form')?.addEventListener('submit',e=>{
    e.preventDefault();
    toast('✅ تم الاشتراك! كود الخصم: WELCOME15 🎉');
    e.target.reset();
  });
}

// CATEGORY PILLS
function initCatPills() {
  document.querySelectorAll('.cat-pill').forEach(p=>{
    p.addEventListener('click',()=>{
      document.querySelectorAll('.cat-pill').forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
    });
  });
}

// QV MODAL
window.quickView = quickView;
window.closeQV   = closeQV;
window.addToCart = addToCart;
window.toggleWish = toggleWish;
window.updateQty = updateQty;
window.removeFromCart = removeFromCart;
window.makeCard = makeCard;

// KEYBOARD
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    closeDrawer('cart'); closeDrawer('wish');
    closeModal('auth'); closeModal('qv');
    document.getElementById('main-nav')?.classList.remove('open');
    document.body.style.overflow='';
  }
});

window.addEventListener('authLoaded', async ()=>{
  // Load products from Store after currency settings are ready
  if (typeof Store !== 'undefined') {
    await Store.ensureReady();
    PRODUCTS_LIVE = Store.getProducts();

    // Sync cart prices with dynamic exchange-rate adjusted prices
    if (state.cart && state.cart.length > 0) {
      let cartUpdated = false;
      state.cart = state.cart.map(item => {
        const lp = PRODUCTS_LIVE.find(p => String(p.id) === String(item.id));
        if (lp && lp.price !== item.price) {
          item.price = lp.price;
          cartUpdated = true;
        }
        return item;
      });
      if (cartUpdated) {
        state.saveCart();
        updateCartUI();
      }
    }
  }
  const flashProds = PRODUCTS_LIVE.filter(p => p.badge === 'sale' || p.badge === 'hot').slice(0, 16);
  const newProds   = PRODUCTS_LIVE.filter(p => p.badge === 'new').slice(0, 16);
  renderGrid('flash-grid', flashProds.length > 0 ? flashProds : PRODUCTS_LIVE.slice(0, 16));
  renderGrid('main-grid',  PRODUCTS_LIVE.slice(0, 24));
  renderGrid('new-grid',   newProds.length > 0 ? newProds : PRODUCTS_LIVE.slice(16, 32));
});

document.addEventListener('DOMContentLoaded',()=>{
  // Update UI from stored state
  updateCartUI();
  updateWishUI();

  // Init everything
  initSearch();
  initProductTabs();
  initAuth();
  initPromo();
  initCatPills();
  initScroll();
  startTimer();

  // Slider
  loadHeroSliders();

  // Cart Drawer
  document.getElementById('cart-btn')?.addEventListener('click', ()=>openDrawer('cart'));
  document.getElementById('cart-close')?.addEventListener('click',()=>closeDrawer('cart'));
  document.getElementById('cart-mask')?.addEventListener('click', ()=>closeDrawer('cart'));
  document.getElementById('continue-shopping')?.addEventListener('click',()=>closeDrawer('cart'));

  // Wishlist Drawer
  document.getElementById('wishlist-btn')?.addEventListener('click', ()=>openDrawer('wish'));
  document.getElementById('wish-close')?.addEventListener('click',  ()=>closeDrawer('wish'));
  document.getElementById('wish-mask')?.addEventListener('click',   ()=>closeDrawer('wish'));

  // QV
  document.getElementById('qv-close')?.addEventListener('click',()=>closeModal('qv'));
  document.getElementById('qv-mask')?.addEventListener('click', ()=>closeModal('qv'));

  // Checkout handlers (intercepting links)
  document.querySelectorAll('.checkout-btn, .btn-buy-now').forEach(btn => {
    btn.addEventListener('click', (e) => {
      // If the link is actually supposed to go to checkout.html
      if (btn.getAttribute('href') === 'checkout.html') {
        const cart = state.cart;
        if (!cart || !cart.length) { 
          e.preventDefault();
          toast('السلة فارغة', 'error'); 
          return; 
        }
        
        // let it navigate
      }
    });
  });

  // Mobile Menu Toggle Fix
  document.getElementById('menu-btn')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('main-nav')?.classList.toggle('open');
  });

  });

// Google login removed

async function loadNavigation() {
    try {
        const res = await fetch('api/get_nav.php?t=' + Date.now());
        const navData = await res.json();
        
        const navLists = document.querySelectorAll('.nav-list');
        if (!navLists.length || !navData.length) return;
        
        let html = '';
        navData.forEach(item => {
            const isActive = item.active || window.location.pathname.includes(item.url) ? 'active' : '';
            const badge = item.badge ? ` ${item.badge} ` : '';
            const cssClass = item.cssClass ? ` ${item.cssClass}` : '';
            
            if (item.type === 'link') {
                html += `<li><a href="${item.url}" class="nav-a ${isActive}${cssClass}">${badge}${item.title}</a></li>`;
            } else if (item.type === 'dropdown') {
                let dropdownHtml = '<div class="dd-col">'; // Unified single column for simplicity
                if (item.subLinks) {
                    item.subLinks.forEach(link => {
                        dropdownHtml += `<a href="${link.url}">${link.title}</a>`;
                    });
                } else if (item.columns) { // Fallback for old data
                    item.columns.forEach(col => {
                        if (col.links) {
                            col.links.forEach(link => {
                                dropdownHtml += `<a href="${link.url}">${link.title}</a>`;
                            });
                        }
                    });
                }
                dropdownHtml += '</div>';
                
                html += `
                <li class="nav-dd">
                  <a href="${item.url || 'javascript:void(0)'}" class="nav-a ${isActive}${cssClass}">${badge}${item.title} ▾</a>
                  <div class="dd-panel" style="min-width:200px;">
                    ${dropdownHtml}
                  </div>
                </li>`;
            }
        });
        
        html += '<li class="mobile-only-link"><hr></li>';
        
        navLists.forEach(list => {
            list.innerHTML = html;
        });
    } catch (err) {
        console.error('Failed to load navigation', err);
    }
}

document.addEventListener('DOMContentLoaded', loadNavigation);

/* ══════════════════════════════════════════════
   POPUP NOTICE / BANNER MODAL INITIALIZATION
══════════════════════════════════════════════ */
async function initPopupBanner() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const isTest = urlParams.get('test_popup') === '1';
        
        // Clean any old obsolete storage locks
        try {
            localStorage.removeItem('estawredly_pb_dismissed_v1');
        } catch(e){}
        
        // If user already explicitly closed the banner in this tab session, don't reopen
        if (!isTest && sessionStorage.getItem('pb_dismissed_now') === '1') {
            return;
        }
        
        const res = await fetch('api/get_popup_banner.php?t=' + Date.now());
        if (!res.ok) return;
        const config = await res.json();
        
        if (!config) return;
        const isEnabled = config.enabled === true || config.enabled === "1" || config.enabled === 1 || config.enabled === "true";
        if (!isEnabled && !isTest) return;
        if (!config.title && !config.message) return;
        
        // Inject foolproof popup modal styles directly if not already present
        if (!document.getElementById('pb-dynamic-styles')) {
            const styleTag = document.createElement('style');
            styleTag.id = 'pb-dynamic-styles';
            styleTag.textContent = `
                #popup-banner-modal {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    width: 100vw !important;
                    height: 100vh !important;
                    z-index: 999999 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 16px !important;
                    box-sizing: border-box !important;
                    opacity: 0;
                    visibility: hidden;
                    pointer-events: none;
                    transition: opacity 0.3s ease, visibility 0.3s ease;
                }
                #popup-banner-modal.active {
                    opacity: 1 !important;
                    visibility: visible !important;
                    pointer-events: auto !important;
                }
                .pb-backdrop {
                    position: absolute !important;
                    inset: 0 !important;
                    background: rgba(3, 7, 18, 0.75) !important;
                    backdrop-filter: blur(8px) !important;
                    -webkit-backdrop-filter: blur(8px) !important;
                    cursor: pointer !important;
                }
                .pb-dialog {
                    position: relative !important;
                    width: 100% !important;
                    max-width: 480px !important;
                    background: #0f172a !important;
                    background: rgba(15, 23, 42, 0.96) !important;
                    border: 1px solid rgba(59, 130, 246, 0.4) !important;
                    border-radius: 20px !important;
                    padding: 26px 22px 20px !important;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.85) !important;
                    text-align: center !important;
                    z-index: 10 !important;
                    box-sizing: border-box !important;
                    color: #fff !important;
                    font-family: inherit !important;
                    transform: scale(0.92);
                    transition: transform 0.3s ease;
                }
                #popup-banner-modal.active .pb-dialog {
                    transform: scale(1) !important;
                }
                .pb-close-btn {
                    position: absolute !important;
                    top: 12px !important;
                    left: 12px !important;
                    width: 32px !important;
                    height: 32px !important;
                    border-radius: 50% !important;
                    background: rgba(255, 255, 255, 0.1) !important;
                    border: 1px solid rgba(255, 255, 255, 0.15) !important;
                    color: #94a3b8 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    font-size: 16px !important;
                    cursor: pointer !important;
                    z-index: 20 !important;
                }
                .pb-close-btn:hover {
                    background: rgba(239, 68, 68, 0.25) !important;
                    color: #ef4444 !important;
                }
                .pb-tag-badge {
                    display: inline-block !important;
                    padding: 4px 14px !important;
                    border-radius: 99px !important;
                    background: rgba(59, 130, 246, 0.2) !important;
                    border: 1px solid rgba(59, 130, 246, 0.4) !important;
                    color: #60a5fa !important;
                    font-size: 12px !important;
                    font-weight: 800 !important;
                    margin-bottom: 12px !important;
                }
                .pb-img-wrapper {
                    width: 100% !important;
                    max-height: 180px !important;
                    border-radius: 12px !important;
                    overflow: hidden !important;
                    margin-bottom: 14px !important;
                }
                .pb-img-wrapper img {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                    display: block !important;
                }
                .pb-title {
                    font-size: 18px !important;
                    font-weight: 800 !important;
                    color: #f8fafc !important;
                    margin: 0 0 10px 0 !important;
                }
                .pb-body-text {
                    font-size: 13.5px !important;
                    color: #cbd5e1 !important;
                    line-height: 1.6 !important;
                    margin: 0 0 18px 0 !important;
                    text-align: right !important;
                }
                .pb-actions {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 8px !important;
                }
                .pb-btn-cta {
                    display: block !important;
                    width: 100% !important;
                    padding: 11px 16px !important;
                    border-radius: 10px !important;
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
                    color: #ffffff !important;
                    font-size: 14px !important;
                    font-weight: 800 !important;
                    text-decoration: none !important;
                    box-sizing: border-box !important;
                }
                .pb-btn-dismiss {
                    background: transparent !important;
                    border: none !important;
                    color: #94a3b8 !important;
                    font-size: 12.5px !important;
                    cursor: pointer !important;
                    padding: 6px !important;
                }
            `;
            document.head.appendChild(styleTag);
        }
        
        // Build Modal Element with indestructible inline styling
        const modal = document.createElement('div');
        modal.id = 'popup-banner-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.style.cssText = 'position:fixed !important;top:0 !important;left:0 !important;right:0 !important;bottom:0 !important;width:100vw !important;height:100vh !important;z-index:99999999 !important;display:flex !important;align-items:center !important;justify-content:center !important;padding:16px !important;box-sizing:border-box !important;opacity:1 !important;visibility:visible !important;pointer-events:auto !important;';
        
        let tagHtml = config.tag ? `<div class="pb-tag-badge" style="display:inline-block !important;padding:4px 14px !important;border-radius:99px !important;background:rgba(59,130,246,0.15) !important;border:1px solid rgba(59,130,246,0.35) !important;color:#60a5fa !important;font-size:12px !important;font-weight:800 !important;margin-bottom:14px !important;letter-spacing:0.2px !important;">${escapeHtml(config.tag)}</div>` : '';
        let imgHtml = config.image ? `<div class="pb-img-wrapper" style="width:100% !important;max-height:220px !important;border-radius:14px !important;overflow:hidden !important;margin-bottom:16px !important;background:rgba(0,0,0,0.2) !important;"><img src="${escapeHtml(config.image)}" alt="${escapeHtml(config.title || 'Popup')}" style="width:100% !important;height:100% !important;object-fit:cover !important;display:block !important;"></div>` : '';
        let titleHtml = config.title ? `<h3 class="pb-title" style="margin:0 0 10px !important;font-size:20px !important;font-weight:800 !important;color:#ffffff !important;line-height:1.4 !important;">${escapeHtml(config.title)}</h3>` : '';
        let msgHtml = config.message ? `<div class="pb-body-text" style="font-size:14px !important;line-height:1.7 !important;color:#cbd5e1 !important;margin-bottom:20px !important;">${escapeHtml(config.message).replace(/\n/g, '<br>')}</div>` : '';
        
        let ctaHtml = '';
        if (config.btn_text) {
            ctaHtml = `<a href="${escapeHtml(config.btn_link || 'shop.html')}" class="pb-btn-cta" style="display:flex !important;align-items:center !important;justify-content:center !important;width:100% !important;padding:13px 20px !important;border-radius:12px !important;background:linear-gradient(135deg,#3b82f6,#1d4ed8) !important;color:#ffffff !important;font-size:14px !important;font-weight:800 !important;text-decoration:none !important;box-sizing:border-box !important;box-shadow:0 4px 14px rgba(37,99,235,0.4) !important;">${escapeHtml(config.btn_text)}</a>`;
        }
        
        modal.innerHTML = `
            <div class="pb-backdrop" id="pb-backdrop-el" style="position:absolute !important;top:0 !important;left:0 !important;right:0 !important;bottom:0 !important;width:100% !important;height:100% !important;background:rgba(3,7,18,0.75) !important;backdrop-filter:blur(8px) !important;-webkit-backdrop-filter:blur(8px) !important;cursor:pointer !important;"></div>
            <div class="pb-dialog" style="position:relative !important;width:100% !important;max-width:480px !important;background:rgba(15,23,42,0.96) !important;border:1px solid rgba(59,130,246,0.4) !important;border-radius:22px !important;padding:28px 24px 22px !important;box-shadow:0 25px 60px rgba(0,0,0,0.85), 0 0 35px rgba(59,130,246,0.2) !important;text-align:center !important;z-index:10 !important;box-sizing:border-box !important;color:#fff !important;font-family:'Tajawal',sans-serif !important;display:flex !important;flex-direction:column !important;align-items:center !important;">
                <button class="pb-close-btn" id="pb-close-btn-el" aria-label="إغلاق" type="button" style="position:absolute !important;top:14px !important;left:14px !important;width:34px !important;height:34px !important;border-radius:50% !important;background:rgba(255,255,255,0.1) !important;border:1px solid rgba(255,255,255,0.15) !important;color:#94a3b8 !important;display:flex !important;align-items:center !important;justify-content:center !important;font-size:16px !important;cursor:pointer !important;z-index:20 !important;outline:none !important;">✕</button>
                ${tagHtml}
                ${imgHtml}
                ${titleHtml}
                ${msgHtml}
                <div class="pb-actions" style="display:flex !important;flex-direction:column !important;gap:10px !important;width:100% !important;align-items:center !important;">
                    ${ctaHtml}
                    <button type="button" class="pb-btn-dismiss" id="pb-dismiss-link-el" style="background:transparent !important;border:none !important;color:#94a3b8 !important;font-size:12.5px !important;cursor:pointer !important;padding:6px !important;font-family:inherit !important;">إغلاق الملاحظة</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Dismiss function - stores in sessionStorage ONLY when user explicitly closes it
        function dismissPopup() {
            modal.classList.remove('active');
            try {
                sessionStorage.setItem('pb_dismissed_now', '1');
            } catch(e){}
            setTimeout(() => {
                if (modal.parentNode) modal.parentNode.removeChild(modal);
            }, 400);
        }
        
        // Attach click listeners to dismiss elements
        const closeBtn = document.getElementById('pb-close-btn-el');
        if (closeBtn) closeBtn.addEventListener('click', dismissPopup);
        
        const backdrop = document.getElementById('pb-backdrop-el');
        if (backdrop) backdrop.addEventListener('click', dismissPopup);
        
        const dismissLink = document.getElementById('pb-dismiss-link-el');
        if (dismissLink) dismissLink.addEventListener('click', dismissPopup);
        
        // Dismiss on ESC key
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                dismissPopup();
                document.removeEventListener('keydown', escHandler);
            }
        });
        
        // Show with subtle delay
        setTimeout(() => {
            modal.classList.add('active');
        }, 300);
        
    } catch (err) {
        console.error("Popup banner init error:", err);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', initPopupBanner);
