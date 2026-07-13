'use strict';
/* ======================================
   إستوردلي – Store JavaScript
   ====================================== */

// ══════════════════════════════════════
// PRODUCT DATA
// ══════════════════════════════════════
const PRODUCTS = {
  all: [
    {"id": 1, "name": "ممسحة مسطحة شينيل", "desc": "ممسحة مسطحة 40 سم، رأس ممسحة شينيل قابل للاستبدال، مقبض معدني قابل للتطويل من 73 إلى 130 سم بقطر 19-22 مم.", "cat": "قطع غيار", "code": "GSA001", "img": "prod_flat.jpg", "price": 20, "oldPrice": 26, "badge": "", "stars": 4.6, "reviews": 135},
    {"id": 2, "name": "رأس ممسحة شينيل بديل", "desc": "الوزن 125 جم، مناسب للموديل GSA001، المقاس: 43.5×13 سم.", "cat": "قطع غيار", "code": "GSR-A001", "img": "prod_flat.jpg", "price": 9, "oldPrice": 12, "badge": "", "stars": 4.7, "reviews": 150},
    {"id": 3, "name": "ممسحة مسطحة", "desc": "ممسحة مسطحة مقاس 40×10 سم، رأس ممسحة شينيل بديل مقاس 43×13 سم، مقبض معدني قابل للتطويل من 70 إلى 120 سم.", "cat": "مماسح مسطحة", "code": "KA2210", "img": "prod_flat.jpg", "price": 18, "oldPrice": 24, "badge": "new", "stars": 4.8, "reviews": 165},
    {"id": 4, "name": "رأس ممسحة بديل من الألياف الدقيقة", "desc": "رأس بديل للموديل KA2210، المقاس: 43×13 سم، التغليف: بطاقة ورقية + كيس شفاف (OPP).", "cat": "قطع غيار", "code": "KR2204", "img": "prod_flat.jpg", "price": 7, "oldPrice": 10, "badge": "", "stars": 4.9, "reviews": 180},
    {"id": 5, "name": "ممسحة من الألياف الدقيقة", "desc": "ممسحة مسطحة 40 سم، رأس ممسحة من الألياف الدقيقة مقاس 44×14 سم، مقبض معدني قابل للتطويل من 73 إلى 125 سم.", "cat": "مماسح مسطحة", "code": "KA2525", "img": "prod_flat.jpg", "price": 20, "oldPrice": 26, "badge": "", "stars": 4.5, "reviews": 195},
    {"id": 6, "name": "رأس ممسحة بديل", "desc": "المقاس: 44×14 سم؛ رأس ممسحة بديل من الألياف الدقيقة.", "cat": "قطع غيار", "code": "R-KA2525", "img": "prod_flat.jpg", "price": 9, "oldPrice": 12, "badge": "new", "stars": 4.6, "reviews": 210},
    {"id": 7, "name": "ممسحة من الألياف الدقيقة", "desc": "ممسحة مسطحة 52 سم، رأس ممسحة من الألياف الدقيقة مقاس 54.5×15 سم، مقبض معدني قابل للتطويل من 75 إلى 130 سم.", "cat": "مماسح مسطحة", "code": "KA2519", "img": "prod_flat.jpg", "price": 33, "oldPrice": 43, "badge": "", "stars": 4.7, "reviews": 225},
    {"id": 8, "name": "ممسحة مسطحة", "desc": "ممسحة مسطحة 38 سم، رأس ممسحة من الألياف الدقيقة مقاس 41.5×12.5 سم، مقبض معدني قابل للتطويل بقطر 19-22 مم من 70 إلى 120 سم، تغليف ببطاقة ورقية.", "cat": "مماسح مسطحة", "code": "KA2208", "img": "prod_flat.jpg", "price": 17, "oldPrice": 23, "badge": "", "stars": 4.8, "reviews": 240},
    {"id": 9, "name": "رأس ممسحة بديل", "desc": "رأس بديل للموديل KA2208، المقاس: 41.5×12.5 سم، التغليف: بطاقة ورقية + كيس شفاف.", "cat": "قطع غيار", "code": "KR2203", "img": "prod_flat.jpg", "price": 6, "oldPrice": 8, "badge": "new", "stars": 4.9, "reviews": 255},
    {"id": 10, "name": "ممسحة مسطحة من الألياف الدقيقة", "desc": "طقم ممسحة مسطحة مقاس 41×10.5 سم، رأس بديل من الألياف الدقيقة، مقبض معدني قابل للتطويل من 75 إلى 130 سم بقطر 22/25 مم.", "cat": "مماسح مسطحة", "code": "K19008", "img": "prod_flat.jpg", "price": 26, "oldPrice": 34, "badge": "", "stars": 4.5, "reviews": 270},
    {"id": 11, "name": "رأس ممسحة بديل من الألياف الدقيقة", "desc": "المقاس: 45×14 سم، رأس ممسحة بديل من الألياف الدقيقة موديل K190008، بتصميم الجيب.", "cat": "قطع غيار", "code": "K19008-R", "img": "prod_flat.jpg", "price": 9, "oldPrice": 12, "badge": "", "stars": 4.6, "reviews": 285},
    {"id": 12, "name": "ممسحة مسطحة من الألياف الدقيقة", "desc": "ممسحة مسطحة من الألياف الدقيقة، مقاس الرأس: 37×11 سم، رأس بديل واحد بنظام الفيلكرو، مقبض معدني قابل للتطويل من 75 إلى 130 سم بقطر 21.5/25 مم، تغليف ببطاقة ورقية.", "cat": "مماسح مسطحة", "code": "KA2312", "img": "prod_flat.jpg", "price": 23, "oldPrice": 30, "badge": "new", "stars": 4.7, "reviews": 300},
    {"id": 13, "name": "رأس ممسحة بديل", "desc": "رأس بديل من الألياف الدقيقة للممسحة المسطحة KA2312، بنظام الفيلكرو من الخلف، المقاس: 38×13 سم، التغليف: بطاقة ورقية + كيس بلاستيكي.", "cat": "قطع غيار", "code": "R-KA2312", "img": "prod_flat.jpg", "price": 6, "oldPrice": 8, "badge": "", "stars": 4.8, "reviews": 315},
    {"id": 14, "name": "ممسحة مسطحة من الألياف الدقيقة", "desc": "ممسحة مسطحة من الألياف الدقيقة، مقاس الرأس: 47×13.2 سم، رأس بديل واحد من الألياف الدقيقة، مقبض معدني قابل للتطويل من 75 إلى 130 سم بقطر 21.5/25 مم، تغليف ببطاقة ورقية.", "cat": "مماسح مسطحة", "code": "KA2315", "img": "prod_flat.jpg", "price": 29, "oldPrice": 38, "badge": "", "stars": 4.9, "reviews": 330},
    {"id": 15, "name": "رأس ممسحة بديل من الألياف الدقيقة 48 سم", "desc": "رأس ممسحة بديل من الألياف الدقيقة، المقاس: 48×15.5 سم، طول الخيوط الحلقية 3 سم، بتصميم الجيب، التغليف: بطاقة ورقية + كيس شفاف، 24 قطعة/كرتون.", "cat": "قطع غيار", "code": "R-KA2315", "img": "prod_flat.jpg", "price": 11, "oldPrice": 15, "badge": "new", "stars": 4.5, "reviews": 345},
    {"id": 16, "name": "ممسحة قطنية", "desc": "ممسحة مسطحة 40 سم، رأس ممسحة قطنية بديلة مقاس 43×13 سم، مقبض معدني قابل للتطويل من 73 إلى 125 سم، رأس دوّار بزاوية 360 درجة.", "cat": "مماسح قطنية", "code": "KA2526", "img": "prod_cotton.jpg", "price": 22, "oldPrice": 29, "badge": "", "stars": 4.6, "reviews": 360},
    {"id": 17, "name": "رأس ممسحة بديل من القطن المخلوط", "desc": "المقاس: 43×13 سم؛ طول الخيوط 3.5 سم، رأس ممسحة بديل من القطن المخلوط.", "cat": "مماسح قطنية", "code": "R-KA2526", "img": "prod_flat.jpg", "price": 10, "oldPrice": 13, "badge": "", "stars": 4.7, "reviews": 375},
    {"id": 18, "name": "ممسحة قطنية 52 سم", "desc": "ممسحة مسطحة 52 سم، رأس ممسحة قطنية بديلة مقاس 54×15 سم، مقبض معدني قابل للتطويل من 74 إلى 130 سم، رأس دوّار بزاوية 360 درجة.", "cat": "مماسح قطنية", "code": "KA2523", "img": "prod_cotton.jpg", "price": 32, "oldPrice": 42, "badge": "new", "stars": 4.8, "reviews": 390},
    {"id": 19, "name": "ممسحة قطنية احترافية", "desc": "ممسحة مسطحة 60 سم، رأس ممسحة بديل من القطن المخلوط، مقبض معدني طول 135 سم (يشمل المشبك ومقبض اليد).", "cat": "مماسح قطنية", "code": "GSA011", "img": "prod_cotton.jpg", "price": 33, "oldPrice": 43, "badge": "", "stars": 4.9, "reviews": 405},
    {"id": 20, "name": "رأس ممسحة قطنية بديل", "desc": "رأس ممسحة بديل من القطن المخلوط للموديل GSA011؛ المقاس: 60×16.5 سم.", "cat": "مماسح قطنية", "code": "GSR-A011", "img": "prod_cotton.jpg", "price": 14, "oldPrice": 19, "badge": "", "stars": 4.5, "reviews": 420},
    {"id": 21, "name": "ممسحة مسطحة من الألياف الدقيقة", "desc": "ممسحة مسطحة من الألياف الدقيقة، مقاس الرأس: 54×12 سم، رأس بديل واحد بنظام الفيلكرو، مقبض معدني قابل للتطويل من 74 إلى 130 سم بقطر 21.5/25 مم، تغليف ببطاقة ورقية.", "cat": "مماسح مسطحة", "code": "KA2524", "img": "prod_flat.jpg", "price": 30, "oldPrice": 39, "badge": "new", "stars": 4.6, "reviews": 435},
    {"id": 22, "name": "رأس ممسحة بديل", "desc": "رأس بديل من الألياف الدقيقة للممسحة المسطحة KA2524، بنظام الفيلكرو من الخلف، المقاس: 57×14.5 سم، التغليف: بطاقة ورقية + كيس بلاستيكي.", "cat": "قطع غيار", "code": "R-KA2524", "img": "prod_flat.jpg", "price": 10, "oldPrice": 13, "badge": "", "stars": 4.7, "reviews": 450},
    {"id": 23, "name": "ممسحة ملتوية (تويست)", "desc": "رأس ممسحة من الألياف الدقيقة، مقبض معدني طول 120 سم، بخاصية القفل الذاتي.", "cat": "مماسح مسطحة", "code": "GSA010", "img": "prod_flat.jpg", "price": 16, "oldPrice": 21, "badge": "", "stars": 4.8, "reviews": 465},
    {"id": 24, "name": "ممسحة مائية قطنية", "desc": "رأس قطن مخلوط وزن 200 جم، مقبض معدني طول 130 سم، قطر 21.5 مم مع رأس بربطة حلزونية إيطالية.", "cat": "مماسح قطنية", "code": "K19022", "img": "prod_cotton.jpg", "price": 11, "oldPrice": 15, "badge": "new", "stars": 4.9, "reviews": 480},
    {"id": 25, "name": "ممسحة مائية من الألياف الدقيقة", "desc": "رأس من الألياف الدقيقة وزن 160 جم، مقبض معدني طول 130 سم، قطر 21.5 مم مع رأس بربطة حلزونية إيطالية.", "cat": "مماسح مسطحة", "code": "K19023", "img": "prod_flat.jpg", "price": 12, "oldPrice": 16, "badge": "", "stars": 4.5, "reviews": 495},
    {"id": 26, "name": "ممسحة ملتوية (تويست)", "desc": "رأس قطن مخلوط وزن 300 جم، مقبض معدني طول 130 سم، بخاصية القفل الذاتي.", "cat": "مماسح مسطحة", "code": "K19024", "img": "prod_flat.jpg", "price": 20, "oldPrice": 26, "badge": "", "stars": 4.6, "reviews": 510},
    {"id": 27, "name": "ممسحة ملتوية (تويست)", "desc": "رأس ممسحة ملتوية من الألياف الدقيقة، مقبض معدني طول 130 سم، بخاصية القفل الذاتي.", "cat": "مماسح مسطحة", "code": "K19025", "img": "prod_flat.jpg", "price": 20, "oldPrice": 26, "badge": "new", "stars": 4.7, "reviews": 525},
    {"id": 28, "name": "ممسحة قطنية", "desc": "رأس ممسحة من القطن المخلوط وزن 450 جم، طول الخيوط 36 سم، مقبض معدني طول 130 سم بقطر 22 مم، حامل ممسحة بلاستيكي، التغليف: كيس شفاف + بطاقة ورقية.", "cat": "مماسح قطنية", "code": "GSA031", "img": "prod_cotton.jpg", "price": 26, "oldPrice": 34, "badge": "", "stars": 4.8, "reviews": 540},
    {"id": 29, "name": "ممسحة PVA السحرية", "desc": "رأس إسفنج PVA مقاس 27 سم قابل للاستبدال، مقبض من الفولاذ المقاوم للصدأ قابل للتطويل من 76 إلى 107 سم.", "cat": "مماسح مسطحة", "code": "GSA004", "img": "prod_flat.jpg", "price": 23, "oldPrice": 30, "badge": "", "stars": 4.9, "reviews": 555},

    {"id": 11, "name": "Microfiber Mop refill", "desc": "K190008配布头", "cat": "قطع غيار", "code": "K19008-R", "img": "prod_micro_refill.jpg", "price": 11, "oldPrice": 14, "badge": "hot", "stars": 4.8, "reviews": 249},
    {"id": 12, "name": "Microfiber Flat Mop", "desc": "粘扣式超细纤维拖把", "cat": "مماسح مايكروفايبر", "code": "KA2312", "img": "prod_micro_flat.jpg", "price": 30, "oldPrice": 39, "badge": "hot", "stars": 4.6, "reviews": 54},
    {"id": 13, "name": "MOP REFILL", "desc": "拖把配布", "cat": "قطع غيار", "code": "R-KA2312", "img": "prod_cotton_refill.jpg", "price": 7, "oldPrice": 9, "badge": "new", "stars": 4.9, "reviews": 83},
    {"id": 14, "name": "Microfiber Flat Mop", "desc": "超细纤维拖把", "cat": "مماسح مايكروفايبر", "code": "KA2315", "img": "prod_micro_flat.jpg", "price": 37, "oldPrice": 48, "badge": "new", "stars": 4.8, "reviews": 98},
    {"id": 15, "name": "48CM Microfiber mop refill", "desc": "48CM圆头拖把布", "cat": "قطع غيار", "code": "R-KA2315", "img": "prod_micro_refill.jpg", "price": 13, "oldPrice": 16, "badge": "", "stars": 4.6, "reviews": 162},
    {"id": 16, "name": "Cotton Mop", "desc": "棉纱平拖", "cat": "مماسح قطنية", "code": "KA2526", "img": "prod_cotton_mop.jpg", "price": 29, "oldPrice": 37, "badge": "new", "stars": 4.5, "reviews": 78},
    {"id": 17, "name": "mix cotton Mop refill", "desc": "涤棉纱替换头", "cat": "قطع غيار", "code": "R-KA2526", "img": "prod_cotton_refill.jpg", "price": 13, "oldPrice": 16, "badge": "", "stars": 4.9, "reviews": 95},
    {"id": 18, "name": "Cotton Mop 52cm", "desc": "52cm棉纱平拖", "cat": "مماسح قطنية", "code": "KA2523", "img": "prod_cotton_mop.jpg", "price": 41, "oldPrice": 53, "badge": "new", "stars": 4.8, "reviews": 204},
    {"id": 19, "name": "Professional Cotton Mop", "desc": "60CM商务平拖 GSA011", "cat": "مماسح قطنية", "code": "GSA011", "img": "prod_cotton_mop.jpg", "price": 42, "oldPrice": 54, "badge": "hot", "stars": 4.5, "reviews": 211},
    {"id": 20, "name": "Cotton Mop refill", "desc": "60CM商务型棉纱平拖", "cat": "قطع غيار", "code": "GSR-A011", "img": "prod_cotton_refill.jpg", "price": 17, "oldPrice": 22, "badge": "hot", "stars": 4.7, "reviews": 69},
  ],
  flash: [
    {"id": 1, "name": "ممسحة مسطحة شينيل", "desc": "ممسحة مسطحة 40 سم، رأس ممسحة شينيل قابل للاستبدال، مقبض معدني قابل للتطويل من 73 إلى 130 سم بقطر 19-22 مم.", "cat": "قطع غيار", "code": "GSA001", "img": "prod_flat.jpg", "price": 20, "oldPrice": 26, "badge": "sale", "stars": 4.6, "reviews": 135},
    {"id": 2, "name": "رأس ممسحة شينيل بديل", "desc": "الوزن 125 جم، مناسب للموديل GSA001، المقاس: 43.5×13 سم.", "cat": "قطع غيار", "code": "GSR-A001", "img": "prod_flat.jpg", "price": 9, "oldPrice": 12, "badge": "sale", "stars": 4.7, "reviews": 150},
    {"id": 3, "name": "ممسحة مسطحة", "desc": "ممسحة مسطحة مقاس 40×10 سم، رأس ممسحة شينيل بديل مقاس 43×13 سم، مقبض معدني قابل للتطويل من 70 إلى 120 سم.", "cat": "مماسح مسطحة", "code": "KA2210", "img": "prod_flat.jpg", "price": 18, "oldPrice": 24, "badge": "sale", "stars": 4.8, "reviews": 165},
    {"id": 4, "name": "رأس ممسحة بديل من الألياف الدقيقة", "desc": "رأس بديل للموديل KA2210، المقاس: 43×13 سم، التغليف: بطاقة ورقية + كيس شفاف (OPP).", "cat": "قطع غيار", "code": "KR2204", "img": "prod_flat.jpg", "price": 7, "oldPrice": 10, "badge": "sale", "stars": 4.9, "reviews": 180},
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

  btn.addEventListener('click',(e)=>{
    e.stopPropagation();
    nav.classList.toggle('open');
  });

  // Close menu when clicking outside
  document.addEventListener('click',(e)=>{
    if(!nav.contains(e.target) && !btn.contains(e.target)){
      nav.classList.remove('open');
    }
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
