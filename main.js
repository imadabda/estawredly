'use strict';
/* ======================================
   إستوردلي – Store JavaScript
   ====================================== */

// ══════════════════════════════════════
// PRODUCT DATA
// ══════════════════════════════════════
const PRODUCTS = {
  all: [
    {id:1,  name:'طقم كنب زاوية إيطالي فخم', cat:'أثاث منزلي', img:'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&q=80', price:850,  oldPrice:1200, badge:'hot', stars:4.9, reviews:142, tab:'home'},
    {id:2,  name:'شاشة Samsung OLED 65"',      cat:'إلكترونيات',  img:'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=400&q=80', price:1299, oldPrice:1599,badge:'best', stars:4.9, reviews:528, tab:'electronics'},
    {id:3,  name:'حذاء Nike Air Max 270',        cat:'أحذية رجالية',       img:'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80', price:110,  oldPrice:145, badge:'sale', stars:4.8, reviews:310, tab:'men'},
    {id:4,  name:'حقيبة كتف شانيل جلد طبيعي',     cat:'حقائب نسائية',       img:'https://images.unsplash.com/photo-1584916201218-f4242ceb4809?w=400&q=80', price:240, oldPrice:320, badge:'hot',  stars:4.8, reviews:167, tab:'women'},
    {id:5,  name:'طاولة طعام خشب زان 6 كراسي',   cat:'أثاث منزلي',        img:'https://images.unsplash.com/photo-1577140917170-285929fb55b7?w=400&q=80',price:450, oldPrice:600,badge:'sale',  stars:4.7, reviews:84,  tab:'home'},
    {id:6,  name:'ساعة Apple Watch Ultra 2',   cat:'إلكترونيات ذكية',  img:'https://images.unsplash.com/photo-1434493789847-2f02bca0f008?w=400&q=80', price:799, oldPrice:null, badge:'new', stars:4.9, reviews:445, tab:'electronics'},
    {id:7,  name:'فستان سهرة تركي مطرز',       cat:'ملابس نسائية',img:'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&q=80', price:120,  oldPrice:180,  badge:'sale', stars:4.6, reviews:290, tab:'women'},
    {id:8,  name:'عجانة كينوود 1200 واط',         cat:'أجهزة مطبخ',        img:'https://images.unsplash.com/photo-1594385208974-2e75f8d7bb48?w=400&q=80',price:280,oldPrice:350,badge:'hot',  stars:4.8, reviews:112,  tab:'home'},
    {id:9,  name:'عطر ديور سوفاج 100مل',        cat:'عطور رجالية', img:'https://images.unsplash.com/photo-1523293115678-d290274f83b2?w=400&q=80', price:135,  oldPrice:160, badge:'best',  stars:4.9, reviews:678, tab:'men'},
    {id:10, name:'سماعات Sony WH-1000XM5',         cat:'إلكترونيات',  img:'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&q=80', price:348, oldPrice:399, badge:'sale', stars:4.8, reviews:812, tab:'electronics'},
    {id:11, name:'طقم ذهب عيار 21 إماراتي',         cat:'مجوهرات',     img:'https://images.unsplash.com/photo-1605100804763-247f679f220a?w=400&q=80', price:1250, oldPrice:null,badge:'new',  stars:5.0, reviews:43,  tab:'women'},
    {id:12, name:'جاكيت جلد طبيعي Massimo',          cat:'ملابس رجالية',img:'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&q=80', price:195,  oldPrice:250,  badge:'sale', stars:4.7, reviews:188, tab:'men'},
  ],
  new: [
    {id:13, name:'لابتوب MacBook Pro M3',   cat:'إلكترونيات',  img:'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&q=80', price:1999,oldPrice:null,badge:'new',  stars:4.9, reviews:87,  tab:'electronics'},
    {id:14, name:'طقم عباية فاخرة بقصة فرنسية',          cat:'ملابس نسائية',img:'https://images.unsplash.com/photo-1618932260643-eee4a2f652a6?w=400&q=80', price:95,  oldPrice:null,badge:'new',  stars:4.8, reviews:14,  tab:'women'},
    {id:15, name:'حذاء رياضي Adidas Ultraboost',             cat:'أحذية رجالية',img:'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=400&q=80', price:160,  oldPrice:null,badge:'new',  stars:4.8, reviews:41,  tab:'men'},
    {id:16, name:'كاميرا Sony Alpha a7 IV',       cat:'إلكترونيات',  img:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80', price:2499, oldPrice:null,badge:'new',  stars:5.0, reviews:28,  tab:'electronics'},
    {id:17, name:'ماكينة قهوة ديلونجي ديديكا',          cat:'أجهزة مطبخ',        img:'https://images.unsplash.com/photo-1585515320310-259814833e62?w=400&q=80', price:220, oldPrice:280,badge:'sale',  stars:4.7, reviews:319,   tab:'home'},
    {id:18, name:'حقيبة سفر ذكية Samsonite',             cat:'حقائب وسفر',       img:'https://images.unsplash.com/photo-1581553675865-06a1476db8ed?w=400&q=80', price:185,  oldPrice:null,badge:'new',  stars:4.7, reviews:52,  tab:'all'},
    {id:19, name:'ساعة رولكس صبمارينر (مستورد)',       cat:'ساعات فاخرة',       img:'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=400&q=80',price:12500, oldPrice:null,badge:'hot',  stars:5.0, reviews:11,  tab:'all'},
    {id:20, name:'ثريا كريستال مودرن LED',            cat:'ديكور وإضاءة',       img:'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=400&q=80', price:340,  oldPrice:450,badge:'sale',  stars:4.6, reviews:66,  tab:'home'},
  ],
  flash: [
    {id:21, name:'آيفون 15 برو 256GB',           cat:'إلكترونيات',  img:'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=400&q=80', price:999, oldPrice:1199, badge:'sale', stars:4.9, reviews:1892},
    {id:22, name:'مقلاية هوائية فيليبس 7لتر',          cat:'أجهزة مطبخ',       img:'https://images.unsplash.com/photo-1586208558913-9ecf79a9bd28?w=400&q=80', price:145,  oldPrice:220, badge:'hot',  stars:4.8, reviews:943},
    {id:23, name:'طقم حقائب لويس فيتون',               cat:'حقائب فاخرة',       img:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&q=80', price:450, oldPrice:750, badge:'sale', stars:4.9, reviews:317},
    {id:24, name:'بلايستيشن 5 سليم 1TB',         cat:'إلكترونيات',  img:'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=400&q=80', price:480, oldPrice:550,badge:'sale', stars:4.9, reviews:1431},
  ]
};

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
  const wishProducts = [...PRODUCTS.all, ...PRODUCTS.new, ...PRODUCTS.flash].filter(p=>state.wishlist.has(p.id));
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
  const all = [...PRODUCTS.all, ...PRODUCTS.new, ...PRODUCTS.flash];
  const p = all.find(x=>x.id===id);
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
  state.slideTimer = setInterval(()=>goSlide(state.slideIndex+1), 5000);
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
    const q=inp.value.trim();
    if(q) { toast(`🔍 جاري البحث عن: "${q}"`, 'info'); }
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
      const filtered = t==='all' ? PRODUCTS.all : PRODUCTS.all.filter(p=>p.tab===t);
      renderGrid('main-grid', filtered.length ? filtered : PRODUCTS.all);
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
  document.getElementById('login-form')?.addEventListener('submit',e=>{
    e.preventDefault(); toast('🎉 تم تسجيل الدخول بنجاح!'); closeModal('auth');
  });
  document.getElementById('signup-form')?.addEventListener('submit',e=>{
    e.preventDefault(); toast('🎉 تم إنشاء حساب جديد! مرحبًا بك 🎊'); closeModal('auth');
  });
}

// ══════════════════════════════════════
// MOBILE MENU
// ══════════════════════════════════════
function initMenu() {
  const btn = document.getElementById('menu-btn');
  const nav = document.getElementById('main-nav');
  if(!btn||!nav) return;
  btn.addEventListener('click',()=>{
    nav.classList.toggle('open');
    document.body.style.overflow = nav.classList.contains('open')?'hidden':'';
  });
}

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
  // Render products
  renderGrid('flash-grid', PRODUCTS.flash);
  renderGrid('main-grid',  PRODUCTS.all);
  renderGrid('new-grid',   PRODUCTS.new);

  // Update UI from stored state
  updateCartUI();
  updateWishUI();

  // Init everything
  initSearch();
  initProductTabs();
  initAuth();
  initMenu();
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
});
