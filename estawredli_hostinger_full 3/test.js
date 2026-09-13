const fs = require('fs');
const PRODUCTS_DB = eval(fs.readFileSync('products_db.js', 'utf8') + '; PRODUCTS_DB;');

const state = {
  cart: [],
  wishlist: new Set(),
};

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

try {
    const card = makeCard(PRODUCTS_DB[0]);
    console.log("SUCCESS");
} catch(e) {
    console.log("ERROR", e);
}
