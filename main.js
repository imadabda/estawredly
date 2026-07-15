'use strict';
/* ======================================
   إستوردلي – Store JavaScript
   ====================================== */

// ══════════════════════════════════════
// PREVENT ZOOM ON IOS
// ══════════════════════════════════════
document.addEventListener('gesturestart', function (e) {
  e.preventDefault();
});

// ══════════════════════════════════════
// PRODUCT DATA
// ══════════════════════════════════════
// Products loaded from Store (localStorage + products_db.js)
let PRODUCTS_LIVE = [];

// ══════════════════════════════════════
// STATE
// ══════════════════════════════════════
const state = {
  cart: JSON.parse(localStorage.getItem('store_cart')||'[]'),
  wishlist: new Set(JSON.parse(localStorage.getItem('store_wish')||'[]')),
  currentTab: 'all',
  slideIndex: 0,
  slideTimer: null,
  saveCart() { localStorage.setItem('store_cart', JSON.stringify(this.cart)); },
  saveWish() { localStorage.setItem('store_wish', JSON.stringify([...this.wishlist])); },
  cartTotal() { return this.cart.reduce((s,i)=>s+i.price*i.qty, 0); },
  cartCount() { return this.cart.reduce((s,i)=>s+i.qty, 0); },
};

// ══════════════════════════════════════
// CART
// ══════════════════════════════════════
function addToCart(product) {
  const ex = state.cart.find(i=>i.id===product.id);
  if (ex) { ex.qty++; }
  else { state.cart.push({...product, qty:1}); }
  state.saveCart();
  updateCartUI();
  toast(`🛒 تمت الإضافة: ${product.name}`);
  animateBadge('cart-count');
}

function removeFromCart(id) {
  state.cart = state.cart.filter(i=>i.id!==id);
  state.saveCart();
  updateCartUI();
}

function updateQty(id, delta) {
  const item = state.cart.find(i=>i.id===id);
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
    list.innerHTML = state.cart.map(i=>`
      <div class="ci">
        <div class="ci-img"><img src="${i.img}" alt="${i.name}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;"></div>
        <div class="ci-info">
          <div class="ci-name">${i.name}</div>
          <div class="ci-price">₪${(i.price*i.qty).toFixed(2)}</div>
          <div class="ci-qty">
            <button class="qty-ctrl" onclick="updateQty(${i.id},-1)">−</button>
            <span class="qty-num">${i.qty}</span>
            <button class="qty-ctrl" onclick="updateQty(${i.id},1)">+</button>
          </div>
        </div>
        <button class="ci-del" onclick="removeFromCart(${i.id})" title="حذف">🗑</button>
      </div>
    `).join('');
    const total = state.cartTotal();
    document.getElementById('cart-subtotal').textContent = '₪'+total.toFixed(2);
    document.getElementById('cart-shipping').textContent = total>=200 ? 'مجاني 🎉' : '₪15.00';
    document.getElementById('cart-total-price').textContent = '₪'+(total+(total<200?15:0)).toFixed(2);
  }
}

// ══════════════════════════════════════
// WISHLIST
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// PRODUCT CARD
// ══════════════════════════════════════
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
        <div class="p-price">
          <span class="p-price-main">₪${p.price}</span>
          ${p.oldPrice?`<span class="p-price-old">₪${p.oldPrice}</span><span class="p-disc">-${disc}%</span>`:''}
        </div>
        <button class="p-add-btn" onclick='event.stopPropagation();addToCart(${JSON.stringify(p).replace(/'/g,"&#39;")})'>
          أضف للسلة
        </button>
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

// ══════════════════════════════════════
// QUICK VIEW
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// MODALS & DRAWERS
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// HERO SLIDER
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// COUNTDOWN TIMER
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// TOAST
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// BADGE ANIMATION
// ══════════════════════════════════════
function animateBadge(id) {
  const el = document.getElementById(id);
  if(!el) return;
  el.style.transform='scale(1.5)';
  setTimeout(()=>{el.style.transform='scale(1)';},200);
}

// ══════════════════════════════════════
// SEARCH
// ══════════════════════════════════════
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

// ══════════════════════════════════════
// PRODUCT TABS
// ══════════════════════════════════════
function initProductTabs() {
  const tabs = document.querySelectorAll('.stab');
  if(!tabs.length) return;
  tabs.forEach(tab=>{
    tab.addEventListener('click',()=>{
      tabs.forEach(t=>t.classList.remove('active'));
      tab.classList.add('active');
      const t = tab.dataset.tab;
      const filtered = t==='all' ? PRODUCTS_LIVE : PRODUCTS_LIVE.filter(p=>p.tab===t);
      renderGrid('main-grid', filtered.length ? filtered : PRODUCTS_LIVE);
    });
  });
}

// ══════════════════════════════════════
// SCROLL EFFECTS
// ══════════════════════════════════════
function initScroll() {
  const btt = document.getElementById('btt-btn');
  const hdr = document.getElementById('header');
  window.addEventListener('scroll',()=>{
    const y = window.scrollY;
    if(btt) btt.classList.toggle('visible', y>400);
    if(hdr) hdr.style.boxShadow = y>10 ? '0 2px 20px rgba(0,0,0,.1)' : '0 1px 0 var(--gray2)';
  },{passive:true});
}

// ══════════════════════════════════════
// AUTH UI UPDATE
// ══════════════════════════════════════
function updateAuthUI() {
  if (typeof Store === 'undefined') return;
  const user = Store.getCurrentUser();
  const btnLogin = document.getElementById('btn-login');
  const btnReg   = document.getElementById('btn-register');
  const btnUser  = document.getElementById('btn-user-name');
  if (user) {
    if (btnLogin) btnLogin.textContent = user.name.split(' ')[0];
    if (btnReg)   { btnReg.textContent = 'خروج'; btnReg.onclick = (e) => { e.preventDefault(); Store.logout(); updateAuthUI(); toast('تم تسجيل الخروج'); }; }
    if (btnUser)  btnUser.textContent = user.name;
  } else {
    if (btnLogin) { btnLogin.textContent = 'تسجيل دخول'; btnLogin.onclick = null; }
    if (btnReg)   { btnReg.textContent = 'حساب جديد'; btnReg.onclick = null; }
  }
}
window.updateAuthUI = updateAuthUI;

// ══════════════════════════════════════
// AUTH MODAL
// ══════════════════════════════════════
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
    updateAuthUI();
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
    updateAuthUI();
  });
}

// ══════════════════════════════════════
// MOBILE MENU IS HANDLED VIA INLINE ONCLICK
// ══════════════════════════════════════

// ══════════════════════════════════════
// PROMO FORM
// ══════════════════════════════════════
function initPromo() {
  document.getElementById('promo-form')?.addEventListener('submit',e=>{
    e.preventDefault();
    toast('✅ تم الاشتراك! كود الخصم: WELCOME15 🎉');
    e.target.reset();
  });
}

// ══════════════════════════════════════
// CATEGORY PILLS
// ══════════════════════════════════════
function initCatPills() {
  document.querySelectorAll('.cat-pill').forEach(p=>{
    p.addEventListener('click',()=>{
      document.querySelectorAll('.cat-pill').forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
    });
  });
}

// ══════════════════════════════════════
// QV MODAL
// ══════════════════════════════════════
window.quickView = quickView;
window.closeQV   = closeQV;
window.addToCart = addToCart;
window.toggleWish = toggleWish;
window.updateQty = updateQty;
window.removeFromCart = removeFromCart;

// ══════════════════════════════════════
// KEYBOARD
// ══════════════════════════════════════
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    closeDrawer('cart'); closeDrawer('wish');
    closeModal('auth'); closeModal('qv');
    document.getElementById('main-nav')?.classList.remove('open');
    document.body.style.overflow='';
  }
});

// ══════════════════════════════════════
// INIT
// ══════════════════════════════════════
document.addEventListener('DOMContentLoaded',()=>{
  // Load products from Store
  if (typeof Store !== 'undefined') {
    PRODUCTS_LIVE = Store.getProducts();
  }
  const flashProds = PRODUCTS_LIVE.filter(p => p.badge === 'sale' || p.badge === 'hot').slice(0, 16);
  const newProds   = PRODUCTS_LIVE.filter(p => p.badge === 'new').slice(0, 16);
  renderGrid('flash-grid', flashProds.length > 0 ? flashProds : PRODUCTS_LIVE.slice(0, 16));
  renderGrid('main-grid',  PRODUCTS_LIVE.slice(0, 24));
  renderGrid('new-grid',   newProds.length > 0 ? newProds : PRODUCTS_LIVE.slice(16, 32));

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
  resetSlideTimer();

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

  // Checkout handler
  document.getElementById('checkout-btn')?.addEventListener('click', () => {
    if (typeof Store === 'undefined' || !Store.isLoggedIn()) {
      toast('يرجى تسجيل الدخول لإتمام الطلب', 'error');
      window.openAuthModal && window.openAuthModal('login');
      return;
    }
    const cart = state.cart;
    if (!cart.length) { toast('السلة فارغة', 'error'); return; }
    const user = Store.getCurrentUser();
    const order = Store.addOrder({
      userId: user.id,
      userName: user.name,
      userEmail: user.email,
      userPhone: user.phone || '',
      items: cart.map(i => ({ id: i.id, name: i.name, price: i.price, qty: i.qty, img: i.img })),
      total: state.cartTotal(),
      address: '',
      notes: '',
    });
    state.cart = [];
    state.saveCart();
    updateCartUI();
    closeDrawer('cart');
    toast('✅ تم استلام طلبك رقم ' + order.id + '! سنتواصل معك قريباً');
  });

  // Mobile Menu Toggle Fix
  document.getElementById('menu-btn')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('main-nav')?.classList.toggle('open');
  });

  updateAuthUI();
});

// Google Login handler
window.handleGoogleLogin = function() {
  if (typeof google === 'undefined' || !google.accounts) {
    const demoUser = {
      name: 'مستخدم جوجل',
      email: 'google-user@gmail.com',
      picture: null,
    };
    if (typeof Store !== 'undefined') Store.loginWithGoogle(demoUser);
    toast('🎉 تم تسجيل الدخول بـ Google!');
    closeModal('auth');
    updateAuthUI();
    return;
  }
  google.accounts.id.initialize({
    client_id: 'YOUR_GOOGLE_CLIENT_ID',
    callback: (response) => {
      try {
        const payload = JSON.parse(atob(response.credential.split('.')[1]));
        if (typeof Store !== 'undefined') Store.loginWithGoogle({
          name: payload.name,
          email: payload.email,
          picture: payload.picture,
        });
        toast('🎉 أهلاً ' + payload.name + '!');
        closeModal('auth');
        updateAuthUI();
      } catch (err) {
        toast('❌ فشل تسجيل الدخول بجوجل', 'error');
      }
    },
  });
  google.accounts.id.prompt();
};
