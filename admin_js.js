">




function toggleAdminSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('admin-overlay').classList.toggle('open');
}


let adminProducts = (typeof Store !== 'undefined') ? Store.getProducts() : [];
window.LIVE_ORDERS = [];
function getAdminOrders() { return window.LIVE_ORDERS; }

async function fetchLiveOrders() {
  try {
    const res = await fetch('api/get_orders.php');
    const data = await res.json();
    if (data.success && data.orders) {
      window.LIVE_ORDERS = data.orders.map(o => ({
        id: o.id,
        userName: o.customer_name,
        customer: o.customer_name,
        phone: o.customer_phone,
        address: o.customer_address,
        zone: o.shipping_zone,
        items: typeof o.items_json === 'string' ? JSON.parse(o.items_json) : o.items_json,
        subtotal: parseFloat(o.subtotal),
        shipping: parseFloat(o.shipping_cost),
        total: parseFloat(o.total_price),
        status: o.status,
        date: o.created_at,
        notes: o.notes
      }));
    } else {
        window.LIVE_ORDERS = (typeof Store !== 'undefined') ? Store.getOrders() : [];
    }
  } catch(e) {
    console.error("Error fetching orders:", e);
    window.LIVE_ORDERS = (typeof Store !== 'undefined') ? Store.getOrders() : [];
  }
  
  // Update sidebar count
  const badge = document.querySelector('.sb-item[onclick*="orders"] .sb-badge');
  if (badge) badge.textContent = window.LIVE_ORDERS.length;
  
  updateStats();
  if (document.getElementById('page-orders').classList.contains('active')) {
    renderOrders(getAdminOrders());
  }
}

function getAdminUsers() { return (typeof Store !== 'undefined') ? Store.getUsers() : []; }

const NOTIFS = [
  {icon:'🛒',color:'rgba(59,130,246,.15)', title:'مرحباً في لوحة الإدارة', sub:'البيانات متزامنة مع الموقع مباشرةً'},
  {icon:'📦',color:'rgba(16,185,129,.15)', title:'206 منتج في المتجر', sub:'جميع المنتجات محملة من قاعدة البيانات'},
];


const CAT_DATA = [
  {name:'مماسح مسطحة', icon:'🧹', count:0, sales:0},
  {name:'مماسح قطنية', icon:'🪣', count:0, sales:0},
  {name:'مماسح مايكروفايبر', icon:'✨', count:0, sales:0},
  {name:'قطع غيار', icon:'🔧', count:0, sales:0},
];

let editingId = null;

async function saveAdminProducts() {
  if (typeof Store !== 'undefined') Store.saveProducts(adminProducts);
  try {
      const res = await fetch('api/save_products.php?_t=' + Date.now(), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(adminProducts)
      });
      const data = await res.json();
      if (!data.success) {
          console.error("Failed to save to server:", data.message);
          alert("خطأ في الحفظ على السيرفر: " + data.message);
      } else {
          console.log("Saved to server successfully.");
      }
  } catch (err) {
      console.error("API Error saving products:", err);
      alert("لم يتم الحفظ! يرجى التأكد من رفع ملف sw.js الجديد أو تحديث الصفحة الإجباري. التفاصيل: " + err);
  }
}

function showPage(id, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.sb-item').forEach(b => b.classList.remove('active'));
  const pg = document.getElementById('page-'+id);
  if (pg) pg.classList.add('active');
  if (el) el.classList.add('active');
  else {
    document.querySelectorAll('.sb-item').forEach(b => {
      if (b.getAttribute('onclick') && b.getAttribute('onclick').includes(`'${id}'`)) b.classList.add('active');
    });
  }
  // Lazy render
  if (id === 'products') renderProducts();
  if (id === 'inventory') renderInventory();
  if (id === 'orders')   renderOrders(getAdminOrders());
  if (id === 'customers') renderCustomers();
  if (id === 'media')    renderMedia();
  
  if (id === 'hero')  adminSliders.load();
  if (id === 'banners')  adminBanners.load();

  if (id === 'categories') renderCategories();
  if (id === 'notifications') renderNotifs();
  if (id === 'analytics') renderBigChart();
}

function updateStats() {
  // Reload from Store
  if (typeof Store !== 'undefined') adminProducts = Store.getProducts();
  const storeOrders = getAdminOrders();
  
  let rev = 0;
  let totalCost = 0;
  
  storeOrders.filter(o=>o.status!=='cancelled').forEach(o => {
    rev += (o.total || 0);
    if(o.items) {
      o.items.forEach(item => {
        let cost = item.costPrice || 0;
        if (!cost) {
          const product = adminProducts.find(p => String(p.id) === String(item.id));
          if (product && product.costPrice) {
            cost = Number(product.costPrice);
          }
        }
        totalCost += cost * (item.quantity || 1);
      });
    }
  });

  const profit = rev - totalCost;
  
  document.getElementById('stat-revenue').textContent = '₪' + rev.toLocaleString('ar-SA');
  const statProfitEl = document.getElementById('stat-profit');
  if(statProfitEl) statProfitEl.textContent = '₪' + Math.max(0, profit).toLocaleString('ar-SA');

  document.getElementById('stat-orders').textContent = storeOrders.length;
  document.getElementById('stat-products').textContent = adminProducts.length;
  document.getElementById('stat-pending') && (document.getElementById('stat-pending').textContent = storeOrders.filter(o=>o.status==='pending').length);
  const pcEl = document.getElementById('products-count');
  if(pcEl) pcEl.textContent = adminProducts.length;
    const psEl = document.getElementById('products-subtitle');
  if(psEl) psEl.textContent = `${adminProducts.length} منتج إجمالاً، ${adminProducts.filter(p=>p.active!==false).length} منشور`;
  const usersCount = (typeof Store !== 'undefined') ? Store.getUsers().length : 0;
  const suEl = document.getElementById('stat-users');
  if(suEl) suEl.textContent = usersCount;
}

function renderChart() {
  const vals = [4200,5800,3900,7200,6100,8500,9200];
  const days = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
  const max = Math.max(...vals);
  const el = document.getElementById('revenue-chart');
  if (!el) return;
  el.innerHTML = vals.map((v,i) => `
    <div class="bar" style="height:${(v/max)*100}%" data-val="₪${v.toLocaleString()}" title="${days[i]}: ₪${v.toLocaleString()}"></div>
  `).join('');
}
function renderBigChart() {
  const el = document.getElementById('big-chart');
  if (!el) return;
  const orders = getAdminOrders();
  
  // Calculate analytics data
  const now = new Date();
  const thirtyDaysAgo = new Date(now.getTime() - 30*24*60*60*1000);
  
  let salesThisMonth = 0;
  let salesLastMonth = 0;
  let totalOrderValue = 0;
  let returnedOrders = 0;
  let completedOrdersCount = 0;
  
  const dailySales = new Array(30).fill(0);
  const customersMap = new Map();
  
  orders.forEach(o => {
    const oDate = new Date(o.date);
    if (o.status === 'cancelled') returnedOrders++;
    if (o.status !== 'cancelled') {
        completedOrdersCount++;
        totalOrderValue += (o.total || 0);
        
        // Month sales
        if (oDate.getMonth() === now.getMonth() && oDate.getFullYear() === now.getFullYear()) {
            salesThisMonth += (o.total || 0);
        } else if (oDate.getMonth() === (now.getMonth()===0?11:now.getMonth()-1) && oDate.getFullYear() === (now.getMonth()===0?now.getFullYear()-1:now.getFullYear())) {
            salesLastMonth += (o.total || 0);
        }
        
        // 30 days chart
        if (oDate >= thirtyDaysAgo) {
            const dayIndex = Math.floor((now.getTime() - oDate.getTime()) / (24*60*60*1000));
            if (dayIndex >= 0 && dayIndex < 30) {
                dailySales[29 - dayIndex] += (o.total || 0);
            }
        }
        
        // Repeat customers
        const phone = o.phone || 'بدون';
        const key = phone !== 'بدون' ? phone : (o.userName || o.customer || 'غير معروف');
        customersMap.set(key, (customersMap.get(key) || 0) + 1);
    }
  });
  
  // Update UI Stats
  let repeatCustomers = 0;
  customersMap.forEach(count => { if (count > 1) repeatCustomers++; });
  const repeatRate = customersMap.size ? Math.round((repeatCustomers / customersMap.size) * 100) : 0;
  const avgOrder = completedOrdersCount ? Math.round(totalOrderValue / completedOrdersCount) : 0;
  const returnRate = orders.length ? (returnedOrders / orders.length * 100).toFixed(1) : 0;
  
  const salesChange = salesLastMonth ? Math.round(((salesThisMonth - salesLastMonth) / salesLastMonth) * 100) : (salesThisMonth > 0 ? 100 : 0);
  
  const monthSalesEl = document.getElementById('analytics-month-sales');
  if (monthSalesEl) {
      monthSalesEl.textContent = '₪' + salesThisMonth.toLocaleString();
      document.getElementById('analytics-repeat-rate').textContent = repeatRate + '%';
      document.getElementById('analytics-avg-order').textContent = '₪' + avgOrder.toLocaleString();
      document.getElementById('analytics-return-rate').textContent = returnRate + '%';
      
      document.getElementById('analytics-month-sales-change').textContent = salesChange >= 0 ? '↑ ' + salesChange + '% عن الشهر السابق' : '↓ ' + Math.abs(salesChange) + '% عن الشهر السابق';
      document.getElementById('analytics-month-sales-change').className = 'stat-change ' + (salesChange >= 0 ? 'up' : 'down');
  }

  // Draw chart
  const max = Math.max(...dailySales, 1);
  el.innerHTML = dailySales.map((v,i)=>`<div class="bar" style="height:${(v/max)*100}%;background:${i===dailySales.length-1?'linear-gradient(to top,#10b981,#34d399)':'linear-gradient(to top,rgba(59,130,246,.4),rgba(59,130,246,.8))'}" data-val="₪${v.toLocaleString()}"></div>`).join('');
  el.dataset.rendered = '1';
}

function renderRecentOrders() {
  const statusMap = {pending:'قيد الانتظار',processing:'جاري المعالجة',shipped:'تم الشحن',delivered:'تم التسليم',cancelled:'ملغي'};
  const body = document.getElementById('recent-orders-body');
  if (!body) return;
  const orders = getAdminOrders();
  if (!orders.length) { body.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text3);padding:32px">لا توجد طلبيات بعد</td></tr>`; return; }
  body.innerHTML = orders.slice(0,5).map(o => {
    const name = o.userName || o.customer || '-';
    const itemsCount = Array.isArray(o.items) ? o.items.length : (o.items || 0);
    
    // شارة جديد للطلبات في آخر 48 ساعة
    const isNew = o.status === 'pending' && (Date.now() - new Date(o.date).getTime() < 48*60*60*1000);
    const newBadge = isNew ? '<span class="badge-new">جديد 🌟</span>' : '';

    return `
    <tr style="${isNew ? 'background:rgba(16, 185, 129, 0.05)' : ''}">
      <td><span class="order-id">${o.id}</span> ${newBadge}</td>
      <td><div class="order-customer"><div class="oc-av">${name[0]||'?'}</div>${name}</div></td>
      <td>${itemsCount} منتج</td>
      <td style="font-weight:800;color:#fff">₪${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge status-${o.status}">${statusMap[o.status]||o.status}</span></td>
      <td style="color:var(--text3)">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span>${new Date(o.date).toLocaleDateString('ar-SA')}</span>
          <button class="view-btn" onclick="viewOrder('${o.id}')" title="معاينة الطلبية">👁️</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function renderOrders(list) {
  const statusMap = {pending:'قيد الانتظار',processing:'جاري المعالجة',shipped:'تم الشحن',delivered:'تم التسليم',cancelled:'ملغي'};
  const body = document.getElementById('all-orders-body');
  if (!body) return;
  if (!list || !list.length) { body.innerHTML = `<tr><td colspan="7" style="text-align:center;color:var(--text3);padding:40px">لا توجد طلبيات</td></tr>`; return; }
  body.innerHTML = list.map(o => {
    const name = o.userName || o.customer || '-';
    const itemsCount = Array.isArray(o.items) ? o.items.length : (o.items || 0);
    let itemsHtml = '';
    if (Array.isArray(o.items)) {
        itemsHtml = o.items.map(i => `<div style="font-size:11px;color:var(--text3);margin-bottom:2px;">• ${i.name} (x${i.quantity||1})</div>`).join('');
    } else {
        itemsHtml = `${itemsCount} منتج`;
    }
    
    // شارة جديد للطلبات في آخر 48 ساعة
    const isNew = o.status === 'pending' && (Date.now() - new Date(o.date).getTime() < 48*60*60*1000);
    const newBadge = isNew ? '<span class="badge-new">جديد 🌟</span>' : '';

    return `
    <tr style="${isNew ? 'background:rgba(16, 185, 129, 0.05)' : ''}">
      <td><span class="order-id">${o.id}</span><br>${newBadge}</td>
      <td>
        <div class="order-customer">
          <div class="oc-av">${name[0]||'?'}</div>
          <div>
            <div style="font-weight:700">${name}</div>
            <div style="font-size:11px;color:var(--text3);margin-top:2px">${o.phone || '-'}</div>
          </div>
        </div>
      </td>
      <td>
        <div style="font-size:12px;max-width:180px;white-space:normal;">
          <strong style="color:var(--p)">${o.zone || 'لم يحدد'}</strong><br/>
          <span style="color:var(--text2)">${o.address || '-'}</span>
        </div>
      </td>
      <td>
        <div style="max-height:70px;overflow-y:auto;padding-right:4px;">
            ${itemsHtml}
        </div>
      </td>
      <td style="font-weight:800;color:#fff">₪${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge status-${o.status}">${statusMap[o.status]||o.status}</span></td>
      <td style="color:var(--text3);font-size:12px">${new Date(o.date).toLocaleDateString('ar-SA')}</td>
      <td>
        <div style="display:flex;align-items:center;gap:4px;">
          <select onchange="changeOrderStatus('${o.id}',this.value)" style="background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:8px;padding:4px 8px;font-family:inherit;font-size:12px">
            ${['pending','processing','shipped','delivered','cancelled'].map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${statusMap[s]||s}</option>`).join('')}
          </select>
          <button class="view-btn" onclick="viewOrder('${o.id}')" title="معاينة الطلبية">👁️</button>
          <button class="print-btn" onclick="printOrder('${o.id}')" title="طباعة الفاتورة">🖨️</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function filterOrders(status) {
  const orders = getAdminOrders();
  renderOrders(status ? orders.filter(o=>o.status===status) : orders);
}

async function changeOrderStatus(id, newStatus) {
  try {
    const formData = new FormData();
    formData.append('order_id', id);
    formData.append('status', newStatus);
    const res = await fetch('api/update_order_status.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('✅ تم تحديث حالة الطلبية');
      await fetchLiveOrders(); // refresh UI
    } else {
      showToast('❌ خطأ: ' + data.message);
    }
  } catch (e) {
    showToast('❌ فشل الاتصال بالخادم');
  }
}


let allRegisteredUsers = [];

function switchCustomerTab(tab) {
    document.getElementById('tab-requests').style.background = tab === 'requests' ? 'var(--p)' : 'transparent';
    document.getElementById('tab-requests').style.border = tab === 'requests' ? 'none' : '1px solid var(--border)';
    
    document.getElementById('tab-active').style.background = tab === 'active' ? 'var(--p)' : 'transparent';
    document.getElementById('tab-active').style.border = tab === 'active' ? 'none' : '1px solid var(--border)';
    
    document.getElementById('view-requests').style.display = tab === 'requests' ? 'block' : 'none';
    document.getElementById('view-active').style.display = tab === 'active' ? 'block' : 'none';
}

async function fetchMembershipRequests() {
    try {
        const res = await fetch('api/get_users.php');
        const data = await res.json();
        if(data.success) {
            allRegisteredUsers = data.users;
            renderMembershipRequests();
        }
    } catch(err) {
        console.error('Error fetching users', err);
    }
}

function renderMembershipRequests() {
    const body = document.getElementById('customers-requests-body');
    if (!body) return;
    
    const pendingUsers = allRegisteredUsers.filter(u => u.status === 'pending' && u.role === 'customer');
    document.getElementById('tab-requests').textContent = `طلبات العضوية (${pendingUsers.length})`;
    
    const badge = document.getElementById('pending-users-badge');
    if (badge) {
        if (pendingUsers.length > 0) {
            badge.style.display = 'block';
            badge.textContent = pendingUsers.length;
        } else {
            badge.style.display = 'none';
        }
    }
    
    if (!pendingUsers.length) {
        body.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--text3);padding:40px">لا توجد طلبات عضوية معلقة</td></tr>`;
        return;
    }
    
    body.innerHTML = pendingUsers.map(u => `
    <tr>
      <td>
        <div class="order-customer">
            <div class="oc-av" style="background:linear-gradient(135deg,#f59e0b,#d97706)">${u.name[0]||'?'}</div>
            <div>
                <div style="font-weight:700">${u.name}</div>
            </div>
        </div>
      </td>
      <td style="color:var(--text3)">${u.email}</td>
      <td style="color:var(--text3)">${u.phone || '-'}</td>
      <td style="color:var(--text3)">${new Date(u.created_at).toLocaleDateString('ar-SA')}</td>
      <td>
        <button class="print-btn" style="background:#10b981; color:#fff; border:none;" onclick="approveUser(${u.id})">✅ قبول وتفعيل</button>
      </td>
    </tr>
    `).join('');
}

async function approveUser(id) {
    if(!confirm('هل أنت متأكد من تفعيل هذا الحساب؟ سيتمكن من رؤية الأسعار والشراء.')) return;
    try {
        const res = await fetch('api/approve_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({user_id: id, status: 'active'})
        });
        const data = await res.json();
        if(data.success) {
            showToast('تم تفعيل الحساب بنجاح', 'success');
            fetchMembershipRequests();
        } else {
            showToast(data.message, 'error');
        }
    } catch(err) {
        showToast('حدث خطأ', 'error');
    }
}

function renderCustomers() {
  fetchMembershipRequests(); // Trigger fetch for requests when rendering customers
  
  const body = document.getElementById('customers-body');
  if (!body) return;
  const orders = getAdminOrders();
  
  // Extract unique customers based on phone number or name
  const customersMap = new Map();
  orders.forEach(o => {
      const name = o.userName || o.customer || 'غير معروف';
      const phone = o.phone || 'بدون رقم';
      const key = phone !== 'بدون رقم' ? phone : name;
      
      if (!customersMap.has(key)) {
          customersMap.set(key, {
              name: name,
              phone: phone,
              email: o.userEmail || '-',
              zone: o.zone || '-',
              orders: [],
              spent: 0,
              firstOrderDate: o.date
          });
      }
      const c = customersMap.get(key);
      c.orders.push(o);
      c.spent += (o.total || 0);
      if (new Date(o.date) < new Date(c.firstOrderDate)) {
          c.firstOrderDate = o.date;
      }
  });
  
  const users = Array.from(customersMap.values());
  
  if (!users.length) { body.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text3);padding:40px">لا يوجد عملاء حتى الآن</td></tr>`; return; }
  
  body.innerHTML = users.sort((a,b) => b.spent - a.spent).map(c => {
    return `
    <tr>
      <td>
        <div class="order-customer">
            <div class="oc-av" style="background:linear-gradient(135deg,#7c3aed,#3b82f6)">${c.name[0]||'?'}</div>
            <div>
                <div style="font-weight:700">${c.name}</div>
                <div style="font-size:11px;color:var(--text3);margin-top:2px">${c.phone}</div>
            </div>
        </div>
      </td>
      <td style="color:var(--text3)">${c.email}</td>
      <td><span style="background:var(--bg3);padding:4px 8px;border-radius:12px;font-size:12px;">${c.zone}</span></td>
      <td style="font-weight:700;color:var(--p)">${c.orders.length} طلبية</td>
      <td style="font-weight:800;color:#fff">₪${c.spent.toLocaleString()}</td>
      <td style="color:var(--text3)">${new Date(c.firstOrderDate).toLocaleDateString('ar-SA')}</td>
    </tr>`;
  }).join('');
  
  // Update customers count
  const el = document.querySelector('#customers-page-sub');
  if (el) el.textContent = `${users.length} عميل قاموا بالشراء`;
}


function renderInventory() {
  const body = document.getElementById('inventory-body');
  if (!body) return;
  
  if (!adminProducts.length) {
      body.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--text3);padding:40px">لا توجد منتجات</td></tr>`;
      return;
  }
  
  body.innerHTML = adminProducts.map(p => `
    <tr>
      <td>
        <img src="${p.img}" alt="" style="width:40px;height:40px;border-radius:6px;object-fit:cover;border:1px solid var(--border)">
      </td>
      <td style="font-weight:600;">${p.name}</td>
      <td style="color:var(--text2);font-size:12px;">${p.cat}</td>
      <td>
        <input type="number" id="inv-stock-${p.id}" value="${p.stock !== undefined && p.stock !== null ? p.stock : ''}" placeholder="غير محدود" style="width:100px;padding:8px;border:1px solid var(--border);border-radius:6px;background:var(--bg);color:var(--text);">
      </td>
    </tr>
  `).join('');
}

async function saveInventory() {
  adminProducts.forEach(p => {
      const input = document.getElementById(`inv-stock-${p.id}`);
      if (input) {
          const val = input.value;
          p.stock = val === '' ? null : parseInt(val);
      }
  });
  
  await saveAdminProducts();
  showToast('✅ تم حفظ المخزون بنجاح');
  if (document.getElementById('page-products').classList.contains('active')) {
      renderProducts();
  }
}

function renderProducts(list) {
  list = list || adminProducts;
  const grid = document.getElementById('products-grid');
  if (!grid) return;
  if (!list.length) { grid.innerHTML='<div class="empty-state"><div class="es-icon">📦</div><p>لا توجد منتجات</p></div>'; return; }
  grid.innerHTML = list.map(p => {
    const disc = p.oldPrice ? Math.round((1-p.price/p.oldPrice)*100) : 0;
    const badgeNames = {sale:`-${disc}%`,new:'جديد',hot:'رائج',best:'مميز'};
    return `
      <div class="prod-admin-card">
        <div class="pac-img">
          <img src="${p.img}" alt="${p.name}" loading="lazy" onerror="this.src='https://via.placeholder.com/300x200?text=📦'"/>
          ${p.badge?`<div class="pac-badge badge-${p.badge}" style="padding:3px 10px;border-radius:8px;font-size:10px;font-weight:800;background:rgba(0,0,0,.5);color:#fff">${badgeNames[p.badge]||p.badge}</div>`:''}
          <div class="pac-overlay">
            <button class="pac-action" onclick="editProduct(${p.id})" title="تعديل">✏️</button>
            <button class="pac-action" onclick="window.open('product.html?id=${p.id}','_blank')" title="معاينة">👁️</button>
            <button class="pac-action del" onclick="deleteProduct(${p.id})" title="حذف">🗑️</button>
          </div>
        </div>
        <div class="pac-info">
          <div class="pac-cat">${p.cat}</div>
          <div class="pac-name">${p.name}</div>
          <div class="pac-price">
            <span class="pac-price-main">₪${p.price}</span>
            ${p.oldPrice?`<span class="pac-price-old">₪${p.oldPrice}</span>`:''}
          </div>
          <div style="font-size:11px;color:var(--text3);margin-bottom:8px">
            المربح: <strong style="color:var(--green)">₪${Math.max(0, p.price - (p.costPrice || 0))}</strong>
          </div>
          <div class="pac-stats">
            <span class="pac-stat">⭐ <strong>${p.stars}</strong></span>
            <span class="pac-stat">💬 <strong>${p.reviews}</strong></span>
            <span class="pac-stat" style="margin-right:auto">
              <label class="toggle" title="${p.active?'منشور':'مخفي'}">
                <input type="checkbox" ${p.active?'checked':''} onchange="toggleProduct(${p.id},this.checked)"/>
                <div class="toggle-slider"></div>
              </label>
            </span>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function filterProducts() {
  const q = document.getElementById('prod-search').value.toLowerCase();
  const cat = document.getElementById('cat-filter').value;
  const badge = document.getElementById('badge-filter').value;
  const filtered = adminProducts.filter(p =>
    (!q || p.name.toLowerCase().includes(q) || p.cat.toLowerCase().includes(q)) &&
    (!cat || p.cat === cat) &&
    (!badge || p.badge === badge)
  );
  renderProducts(filtered);
}

function globalSearch(q) {
  if (!q) return;
  const hits = adminProducts.filter(p => p.name.toLowerCase().includes(q.toLowerCase())).length;
  if (hits) showToast(`🔍 وجدت ${hits} نتيجة`);
}


let currentProductVariants = [];
let currentProductImages = [];

function addAdditionalImage(url) {
    if(!url) return;
    currentProductImages.push(url);
    renderAdditionalImages();
}
function addAdditionalImageBase64(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        currentProductImages.push(e.target.result);
        renderAdditionalImages();
    };
    reader.readAsDataURL(input.files[0]);
    input.value = '';
}
function removeAdditionalImage(index) {
    currentProductImages.splice(index, 1);
    renderAdditionalImages();
}
function renderAdditionalImages() {
    const c = document.getElementById('additional-images-container');
    c.innerHTML = currentProductImages.map((img, i) => `
        <div style="position:relative; width:60px; height:60px; border-radius:8px; overflow:hidden; border:1px solid var(--border)">
            <img src="${img}" style="width:100%; height:100%; object-fit:cover;">
            <button type="button" onclick="removeAdditionalImage(${i})" style="position:absolute; top:2px; right:2px; background:red; color:white; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center;">✕</button>
        </div>
    `).join('');
}

function addVariantField() {
    currentProductVariants.push({ name: '', options: [] });
    renderVariants();
}
function removeVariantField(index) {
    currentProductVariants.splice(index, 1);
    renderVariants();
}
function addVariantOption(index, inputEl) {
    const val = inputEl.value.trim();
    if(val) {
        currentProductVariants[index].options.push(val);
        inputEl.value = '';
        renderVariants();
    }
}
function removeVariantOption(vIndex, optIndex) {
    currentProductVariants[vIndex].options.splice(optIndex, 1);
    renderVariants();
}
function updateVariantName(index, val) {
    currentProductVariants[index].name = val;
}
function renderVariants() {
    const c = document.getElementById('variants-container');
    c.innerHTML = currentProductVariants.map((v, i) => `
        <div style="background:var(--bg2); padding:10px; border-radius:8px; border:1px solid var(--border); position:relative;">
            <button type="button" onclick="removeVariantField(${i})" style="position:absolute; top:10px; left:10px; color:red; border:none; background:transparent; cursor:pointer;">✕ حذف الخاصية</button>
            <div style="margin-bottom:10px;">
                <label style="font-size:12px; color:var(--text3)">اسم الخاصية (مثال: اللون)</label>
                <input type="text" value="${v.name}" onchange="updateVariantName(${i}, this.value)" placeholder="مثال: اللون" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; background:var(--bg); color:var(--text1); margin-top:5px;">
            </div>
            <div>
                <label style="font-size:12px; color:var(--text3)">خيارات الخاصية</label>
                <div style="display:flex; gap:5px; flex-wrap:wrap; margin:5px 0;">
                    ${v.options.map((opt, oi) => `
                        <span style="background:var(--primary); color:white; padding:4px 8px; border-radius:4px; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                            ${opt} <span style="cursor:pointer; font-weight:bold" onclick="removeVariantOption(${i}, ${oi})">×</span>
                        </span>
                    `).join('')}
                </div>
                <input type="text" placeholder="اكتب خيار واضغط Enter..." onkeypress="if(event.key==='Enter') { event.preventDefault(); addVariantOption(${i}, this); }" style="width:100%; padding:8px; border:1px dashed var(--border); border-radius:6px; background:var(--bg); color:var(--text1); font-size:13px;">
            </div>
        </div>
    `).join('');
}

function openModal(p) {
  editingId = p ? p.id : null;
  document.getElementById('modal-title').textContent = p ? '✏️ تعديل المنتج' : '➕ إضافة منتج جديد';
  document.getElementById('f-name').value = p?.name || '';
  document.getElementById('f-cat').value = p?.cat || '';
  
  // Find if product belongs to any icon
  let mappedIcon = '';
  if (p && adminIcons && adminIcons.mapping) {
      for (const [iconKey, prodIds] of Object.entries(adminIcons.mapping)) {
          if (prodIds.includes(String(p.id))) {
              mappedIcon = iconKey;
              break;
          }
      }
  }
  document.getElementById('f-icon-cat').value = mappedIcon;

  document.getElementById('f-badge').value = p?.badge || '';
  document.getElementById('f-price').value = p?.price || '';
  document.getElementById('f-cost-price').value = p?.costPrice || '';
  document.getElementById('f-old-price').value = p?.oldPrice || '';
  document.getElementById('f-stars').value = p?.stars || '';
  document.getElementById('f-reviews').value = p?.reviews || '';
  document.getElementById('f-stock').value = (p && p.stock !== undefined) ? p.stock : '';
  document.getElementById('f-tab').value = p?.tab || 'all';
  document.getElementById('f-img-url').value = p?.img || '';
  const prev = document.getElementById('img-preview-el');
  const ph = document.getElementById('upload-placeholder');
  if (p?.img) { prev.src=p.img; prev.style.display='block'; ph.style.display='none'; }
  else { prev.style.display='none'; ph.style.display='block'; }
  document.getElementById('product-modal').classList.add('open');
}
function closeModal() { document.getElementById('product-modal').classList.remove('open'); }

function editProduct(id) {
  const p = adminProducts.find(x=>x.id===id);
  if (p) { showPage('products',null); openModal(p); }
}

function saveProduct() {
  const name = document.getElementById('f-name').value.trim();
  const price = parseFloat(document.getElementById('f-price').value);
  const costPrice = parseFloat(document.getElementById('f-cost-price').value) || 0;
  const stockStr = document.getElementById('f-stock').value;
  const stock = stockStr === '' ? null : parseInt(stockStr);
  
  if (!name || isNaN(price)) { showToast('⚠️ الاسم والسعر مطلوبان!','warn'); return; }

  const pToEdit = editingId ? adminProducts.find(x=>x.id===editingId) : null;
  const pToEdit = editingId ? adminProducts.find(x=>x.id===editingId) : null;
  const product = {
    id: editingId || (Date.now()),
    name,
    cat: document.getElementById('f-cat').value,
    badge: document.getElementById('f-badge').value,
    price,
    costPrice,
    oldPrice: parseFloat(document.getElementById('f-old-price').value) || null,
    stars: parseFloat(document.getElementById('f-stars').value) || 4.5,
    reviews: parseInt(document.getElementById('f-reviews').value) || 0,
    stock: stock,
    tab: document.getElementById('f-tab').value || 'all',
    img: document.getElementById('f-img-url').value || document.getElementById('img-preview-el').src || 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80',
    active: pToEdit ? pToEdit.active : true,

    images: currentProductImages.length > 0 ? [...currentProductImages] : null,
    variants: currentProductVariants.length > 0 ? JSON.parse(JSON.stringify(currentProductVariants)) : null,

  };

  if (editingId) {
    const idx = adminProducts.findIndex(p=>p.id===editingId);
    if (idx !== -1) adminProducts[idx] = product;
    showToast('✅ تم تحديث المنتج بنجاح!');
  } else {
    adminProducts.unshift(product);
    showToast('✅ تم إضافة المنتج للمتجر!');
  }
  
  const iconCat = document.getElementById('f-icon-cat').value;
  if (iconCat && adminIcons.mapping) {
      if(!adminIcons.mapping[iconCat]) adminIcons.mapping[iconCat] = [];
      if(!adminIcons.mapping[iconCat].includes(String(product.id))) {
          adminIcons.mapping[iconCat].push(String(product.id));
          adminIcons.saveIconsToServer(); // Auto-save mapping
      }
  }

  saveAdminProducts();
  closeModal();
  renderProducts();
  updateStats();
}

function deleteProduct(id) {
  if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) return;
  adminProducts = adminProducts.filter(p=>p.id!==id);
  saveAdminProducts();
  renderProducts();
  updateStats();
  showToast('🗑️ تم حذف المنتج');
}

function toggleProduct(id, active) {
  const p = adminProducts.find(x=>x.id===id);
  if (p) { p.active=active; saveAdminProducts(); showToast(active?'✅ المنتج منشور':'⛔ المنتج مخفي'); }
}

function exportProducts() {
  const header = 'الاسم,التصنيف,السعر,السعر القديم,التقييم,التقييمات\n';
  const rows = adminProducts.map(p=>`"${p.name}","${p.cat}",${p.price},${p.oldPrice||''},${p.stars},${p.reviews}`).join('\n');
  const blob = new Blob(['\uFEFF'+header+rows], {type:'text/csv;charset=utf-8'});
  const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='products.csv'; a.click();
  showToast('📥 تم تصدير CSV');
}

function renderTopProducts() {
  const grid = document.getElementById('top-products-grid');
  if (!grid) return;
  const top = [...adminProducts].sort((a,b)=>b.reviews-a.reviews).slice(0,4);
  grid.innerHTML = top.map(p=>`
    <div class="prod-admin-card" onclick="editProduct(${p.id})" style="cursor:pointer">
      <div class="pac-img" style="height:120px"><img src="${p.img}" alt="${p.name}" loading="lazy"/></div>
      <div class="pac-info">
        <div class="pac-cat">${p.cat}</div>
        <div class="pac-name" style="font-size:12px">${p.name}</div>
        <div class="pac-price"><span class="pac-price-main" style="font-size:14px">₪${p.price}</span></div>
        <div class="pac-stats">
          <span class="pac-stat">⭐<strong>${p.stars}</strong></span>
          <span class="pac-stat">💬<strong>${p.reviews}</strong></span>
          <span class="chip chip-green" style="margin-right:auto">رائج</span>
        </div>
      </div>
    </div>
  `).join('');
}

function renderMedia() {
  const grid = document.getElementById('media-grid');
  if (!grid || grid.dataset.rendered) return;
  const imgs = adminProducts.slice(0,8).map(p=>p.img);
  grid.innerHTML = `
    <div class="upload-slot" onclick="document.getElementById('bulk-upload').click()">
      <div class="up-icon">+</div>
      <small>رفع صورة</small>
    </div>
    ${imgs.map(src=>`
      <div class="media-item">
        <img src="${src}" alt="" loading="lazy"/>
        <div class="mi-overlay">
          <button class="pac-action" onclick="showToast('📋 تم نسخ الرابط!')" style="font-size:14px">📋</button>
          <button class="pac-action del" onclick="this.closest('.media-item').remove();showToast('🗑️ تم الحذف')" style="font-size:14px">🗑️</button>
        </div>
      </div>
    `).join('')}
  `;
  grid.dataset.rendered='1';
}

function handleBulkUpload(input) {
  const files = [...input.files];
  files.forEach(f => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div'); div.className='media-item';
      div.innerHTML=`<img src="${e.target.result}" alt=""/><div class="mi-overlay"><button class="pac-action" style="font-size:14px">📋</button></div>`;
      document.getElementById('media-grid')?.prepend(div);
    };
    reader.readAsDataURL(f);
  });
  showToast(`✅ تم رفع ${files.length} صورة`);
}


function renderCategories() {
  const grid = document.getElementById('cat-grid');
  if (!grid || grid.dataset.rendered) return;
  grid.innerHTML = CAT_DATA.map(c=>`
    <div class="stat-card blue" onclick="showToast('📂 تعديل: ${c.name}')" style="cursor:pointer">
      <div class="stat-icon">${c.icon}</div>
      <div class="stat-val">${c.count}</div>
      <div class="stat-label">${c.name}</div>
      <div class="stat-change up">↑ ${c.sales} مبيعة هذا الشهر</div>
    </div>
  `).join('');
  grid.dataset.rendered='1';
}

function renderNotifs() {
  const el = document.getElementById('notif-list');
  if (!el) return;
  el.innerHTML = NOTIFS.map(n=>`
    <div class="notif-item">
      <div class="notif-icon" style="background:${n.color}">${n.icon}</div>
      <div>
        <strong>${n.title}</strong>
        <small>${n.sub}</small>
      </div>
      <button onclick="this.closest('.notif-item').style.opacity='.4'" style="margin-right:auto;font-size:12px;color:var(--text3);cursor:pointer">✓ قراءة</button>
    </div>
  `).join('');
}

function previewImg(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('img-preview-el').src=e.target.result;
    document.getElementById('img-preview-el').style.display='block';
    document.getElementById('upload-placeholder').style.display='none';
    document.getElementById('img-preview-area').classList.add('has-img');
    document.getElementById('f-img-url').value=e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}
function previewUrl(url) {
  if (!url) return;
  const prev = document.getElementById('img-preview-el');
  prev.src=url; prev.style.display='block';
  document.getElementById('upload-placeholder').style.display='none';
}

function pickColor(el, color) {
  document.querySelectorAll('.color-swatch').forEach(s=>s.classList.remove('active'));
  el.classList.add('active');
  showToast(`🎨 اللون المختار: ${color}`);
}

function showToast(msg, type) {
  const t = document.getElementById('admin-toast');
  t.textContent = msg;
  t.className = 'toast-admin show';
  clearTimeout(t._timer);
  t._timer = setTimeout(()=>t.classList.remove('show'), 3000);
}

function updateClock() {
  document.getElementById('live-time').textContent = new Date().toLocaleTimeString('ar-PS');
}

updateStats();
renderChart();
renderRecentOrders();
renderTopProducts();
updateClock();
setInterval(updateClock, 1000);

// Close modal on outside click
document.getElementById('product-modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// ══ REAL DATA BINDING ══
function loadAdminProducts() {
  if (typeof Store === 'undefined') return;
  const products = Store.getProducts();
  // Update stats
  document.getElementById('stat-products') && (document.getElementById('stat-products').textContent = products.length);
  const orders = getAdminOrders();
  document.getElementById('stat-orders') && (document.getElementById('stat-orders').textContent = orders.length);
  document.getElementById('stat-pending') && (document.getElementById('stat-pending').textContent = orders.filter(o=>o.status==='pending').length);
  const revenue = orders.filter(o=>o.status!=='cancelled').reduce((s,o)=>s+(o.total||0),0);
  document.getElementById('stat-revenue') && (document.getElementById('stat-revenue').textContent = '\u20AA' + revenue.toLocaleString('ar-SA'));
}

function loadAdminOrders() {
  if (typeof Store === 'undefined') return;
  const orders = getAdminOrders();
  const tbody = document.getElementById('all-orders-body');
  if (!tbody) return;
  const statusMap = {pending:'\u23f0 \u0642\u064a\u062f \u0627\u0644\u0627\u0646\u062a\u0638\u0627\u0631', processing:'\u2699\ufe0f \u0642\u064a\u062f \u0627\u0644\u0645\u0639\u0627\u0644\u062c\u0629', shipped:'\ud83d\ude9a \u062a\u0645 \u0627\u0644\u0634\u062d\u0646', delivered:'\u2705 \u062a\u0645 \u0627\u0644\u062a\u0633\u0644\u064a\u0645', cancelled:'\u274c \u0645\u0644\u063a\u064a'};
  tbody.innerHTML = orders.slice(0,50).map(o => `
    <tr>
      <td style="font-weight:700">${o.id}</td>
      <td>${o.userName || '-'}</td>
      <td>${o.items?.length || 0} \u0645\u0646\u062a\u062c</td>
      <td>\u20AA${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge">${statusMap[o.status]||o.status}</span></td>
      <td>${new Date(o.date).toLocaleDateString('ar-SA')}</td>
      <td>
        <select onchange="changeOrderStatus('${o.id}', this.value)" style="background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:8px;padding:4px 8px;font-family:inherit;">
          ${['pending','processing','shipped','delivered','cancelled'].map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${statusMap[s]||s}</option>`).join('')}
        </select>
      </td>
    </tr>
  `).join('');
}

window.editProductAdmin = function(id) {
  if (typeof Store === 'undefined') return;
  const p = Store.getProduct(id);
  if (!p) return;
  const name = prompt('\u0627\u0633\u0645 \u0627\u0644\u0645\u0646\u062a\u062c:', p.name);
  if (name === null) return;
  const price = prompt('\u0627\u0644\u0633\u0639\u0631 (\u20AA):', p.price);
  if (price === null) return;
  Store.updateProduct(id, { name, price: parseFloat(price) || p.price });
  loadAdminProducts();
  alert('\u2705 \u062a\u0645 \u062a\u062d\u062f\u064a\u062b \u0627\u0644\u0645\u0646\u062a\u062c');
};

window.deleteProductAdmin = function(id) {
  if (!confirm('\u0647\u0644 \u0623\u0646\u062a \u0645\u062a\u0623\u0643\u062f \u0645\u0646 \u062d\u0630\u0641 \u0647\u0630\u0627 \u0627\u0644\u0645\u0646\u062a\u062c\u061f')) return;
  Store.deleteProduct(id);
  loadAdminProducts();
};

document.addEventListener('DOMContentLoaded', () => {
  fetchLiveOrders();
  loadAdminProducts();
});



  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
      for(let registration of registrations) {
        registration.unregister();
      }
    });
  }
function renderDeliveryZonesUI() {
  const container = document.getElementById('delivery-zones-container');
  if(!container) return;
  const zones = (typeof Store !== 'undefined') ? Store.getDeliveryZones() : [];
  container.innerHTML = zones.map((z) => `
    <div style="display:flex; gap:10px" class="dz-row">
      <input type="text" class="dz-name" value="${z.name}" placeholder="اسم المنطقة" style="flex:2" />
      <input type="number" class="dz-price" value="${z.price}" placeholder="السعر" style="flex:1" />
      <button class="btn" style="background:var(--red);color:#fff;padding:0 12px" onclick="this.parentElement.remove()">✕</button>
    </div>
  `).join('');
}

function addDeliveryZone() {
  document.getElementById('delivery-zones-container').insertAdjacentHTML('beforeend', `
    <div style="display:flex; gap:10px" class="dz-row">
      <input type="text" class="dz-name" value="" placeholder="اسم المنطقة" style="flex:2" />
      <input type="number" class="dz-price" value="0" placeholder="السعر" style="flex:1" />
      <button class="btn" style="background:var(--red);color:#fff;padding:0 12px" onclick="this.parentElement.remove()">✕</button>
    </div>
  `);
}

function saveDeliveryZonesUI() {
  const container = document.getElementById('delivery-zones-container');
  const zones = [];
  container.querySelectorAll('.dz-row').forEach((div, i) => {
    const name = div.querySelector('.dz-name').value.trim();
    const price = parseFloat(div.querySelector('.dz-price').value) || 0;
    if(name) zones.push({id: i+1, name, price});
  });
  if(typeof Store !== 'undefined') Store.saveDeliveryZones(zones);
  showToast('✅ تم حفظ مناطق التوصيل بنجاح!');
}

document.addEventListener('DOMContentLoaded', () => {
  renderDeliveryZonesUI();
});

function printOrder(id) {
    const orders = getAdminOrders();
    const o = orders.find(x => String(x.id) === String(id));
    if (!o) return;
    
    let itemsHtml = '';
    if (Array.isArray(o.items)) {
        itemsHtml = o.items.map(i => `
            <tr>
                <td style="padding:10px;border-bottom:1px solid #ddd;">${i.name}</td>
                <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${i.quantity || 1}</td>
                <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">₪${i.price}</td>
                <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">₪${(i.price * (i.quantity || 1))}</td>
            </tr>
        `).join('');
    } else {
        itemsHtml = `<tr><td colspan="4" style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${o.items} منتج</td></tr>`;
    }
    
    const w = window.open('', '_blank');
    w.document.write(`
        <html dir="rtl" lang="ar">
        <head>
            <title>طباعة طلبية #${o.id}</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #000; margin: 0; }
                h1 { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 30px; font-size: 28px; }
                .info-box { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #fafafa; }
                .info-row { margin-bottom: 12px; font-size: 16px; }
                .info-row strong { display: inline-block; width: 140px; color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #f0f0f0; padding: 12px 10px; text-align: right; border-bottom: 2px solid #000; font-size: 16px; }
                .total { margin-top: 30px; text-align: left; font-size: 24px; font-weight: bold; border-top: 2px solid #000; padding-top: 15px; }
                @media print {
                    body { padding: 20px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .info-box { border: 1px solid #000; background: transparent; }
                }
            </style>
        </head>
        <body>
            <h1>فاتورة طلبية - إستوردلي</h1>
            <div style="display:flex;justify-content:space-between;font-size:16px;margin-bottom:20px;">
                <div><strong>رقم الطلبية:</strong> #${o.id}</div>
                <div><strong>تاريخ الطلب:</strong> ${new Date(o.date).toLocaleDateString('ar-SA')}</div>
            </div>
            
            <div class="info-box">
                <h3 style="margin-top:0;border-bottom:1px solid #ddd;padding-bottom:10px;">تفاصيل العميل والتوصيل</h3>
                <div class="info-row"><strong>الاسم:</strong> ${o.userName || o.customer || '-'}</div>
                <div class="info-row"><strong>رقم الهاتف:</strong> ${o.phone || '-'}</div>
                <div class="info-row"><strong>منطقة التوصيل:</strong> ${o.zone || 'لم يحدد'}</div>
                <div class="info-row"><strong>العنوان المفصل:</strong> ${o.address || '-'}</div>
                <div class="info-row"><strong>ملاحظات العميل:</strong> ${o.notes || '-'}</div>
            </div>
            
            <h3 style="margin-top:30px">المنتجات المطلوبة</h3>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th style="text-align:center;">الكمية</th>
                        <th style="text-align:center;">سعر الوحدة</th>
                        <th style="text-align:center;">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            
            <div class="total">
                المجموع الكلي: ₪${(o.total || 0).toLocaleString()}
            </div>
            
            <div style="text-align:center; margin-top:60px; font-size:14px; color:#555;">
                <p>نشكركم على تسوقكم من متجر إستوردلي!</p>
                <p dir="ltr">estawredli.com</p>
            </div>
        </body>
        </html>
    `);
    w.document.close();
    w.focus();
    setTimeout(() => { w.print(); w.close(); }, 500);
}

function viewOrder(id) {
    const o = getAdminOrders().find(x => String(x.id) === String(id));
    if (!o) return;
    
    document.getElementById('ov-id').textContent = '#' + o.id;
    
    let itemsHtml = '';
    if (Array.isArray(o.items)) {
        itemsHtml = o.items.map(i => `
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                <div>
                  <strong>${i.name}</strong> <span style="color:var(--text3)">(x${i.quantity || i.qty || 1})</span>
                  ${i.selectedVariants && Object.keys(i.selectedVariants).length > 0 ? `<div style="font-size:12px; color:var(--text3); margin-top:2px;">` + Object.entries(i.selectedVariants).map(([k,v]) => `${k}: ${v}`).join(' | ') + `</div>` : ''}
                </div>
                <div>₪${(i.price || 0) * (i.quantity || i.qty || 1)}</div>
            </div>
        `).join('');
    } else {
        itemsHtml = `<div style="padding:8px 0">${o.items} منتج</div>`;
    }
    
    document.getElementById('ov-body').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
            <div style="background:var(--bg3);padding:15px;border-radius:12px;">
                <h3 style="margin-bottom:10px;font-size:14px;color:var(--text2)">معلومات العميل</h3>
                <div style="margin-bottom:6px"><strong>الاسم:</strong> ${o.userName || o.customer || '-'}</div>
                <div style="margin-bottom:6px"><strong>رقم الهاتف:</strong> ${o.phone || '-'}</div>
                <div style="margin-bottom:6px"><strong>ملاحظات:</strong> ${o.notes || '-'}</div>
            </div>
            <div style="background:var(--bg3);padding:15px;border-radius:12px;">
                <h3 style="margin-bottom:10px;font-size:14px;color:var(--text2)">تفاصيل التوصيل</h3>
                <div style="margin-bottom:6px"><strong>المنطقة:</strong> ${o.zone || 'لم يحدد'}</div>
                <div style="margin-bottom:6px"><strong>العنوان المفصل:</strong> ${o.address || '-'}</div>
                <div style="margin-bottom:6px"><strong>التاريخ:</strong> ${new Date(o.date).toLocaleString('ar-SA')}</div>
            </div>
        </div>
        
        <h3 style="margin-bottom:10px;font-size:14px;color:var(--text2);border-bottom:1px solid var(--border);padding-bottom:8px;">المنتجات المطلوبة</h3>
        <div style="margin-bottom:20px;">
            ${itemsHtml}
        </div>
        
        <div style="display:flex;justify-content:space-between;align-items:center;background:var(--p);color:#fff;padding:15px;border-radius:12px;">
            <strong style="font-size:16px;">المجموع الكلي:</strong>
            <strong style="font-size:20px;">₪${(o.total || 0).toLocaleString()}</strong>
        </div>
    `;
    
    document.getElementById('ov-print-btn').onclick = () => { closeOrderModal(); printOrder(id); };
    document.getElementById('order-view-modal').classList.add('open');
}

function closeOrderModal() {
    document.getElementById('order-view-modal').classList.remove('open');
}



// ==========================================
// BANNERS MANAGER
// ==========================================

const adminSliders = {
    slides: [],
    
    async load() {
        try {
            const res = await fetch('api/get_sliders.php');
            this.slides = await res.json();
            this.render();
        } catch(e) {
            console.error('Failed to load sliders', e);
        }
    },
    
    render() {
        const container = document.getElementById('hero-slides-list');
        if (!container) return;
        
        if (!this.slides || this.slides.length === 0) {
            container.innerHTML = '<div style="text-align:center;color:var(--text3);">لا يوجد شرائح حالياً.</div>';
            return;
        }
        
        container.innerHTML = this.slides.map((s, i) => `
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:12px; padding:15px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:5px;">
                    <h3 style="margin-bottom:0;">شريحة رقم ${i+1}</h3>
                    <button onclick="adminSliders.removeSlide(${i})" style="background:red; color:white; border:none; border-radius:4px; cursor:pointer; padding:4px 8px; font-size:12px;">حذف</button>
                </div>
                
                <label style="font-size:12px; color:var(--text2);">صورة الخلفية (رابط)</label>
                <input type="text" value="${s.img || ''}" onchange="adminSliders.update(${i}, 'img', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">الشارة (Tag)</label>
                <input type="text" value="${s.tag || ''}" onchange="adminSliders.update(${i}, 'tag', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">العنوان الرئيسي (استخدم &lt;br/&gt; للسطر الجديد و &lt;em&gt; للكلمات المميزة)</label>
                <input type="text" value="${(s.title || '').replace(/"/g, '&quot;')}" onchange="adminSliders.update(${i}, 'title', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">الوصف والتفاصيل</label>
                <input type="text" value="${s.desc || ''}" onchange="adminSliders.update(${i}, 'desc', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 1: النص</label>
                        <input type="text" value="${s.btn1_text || ''}" onchange="adminSliders.update(${i}, 'btn1_text', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 1: الرابط</label>
                        <input type="text" value="${s.btn1_link || ''}" onchange="adminSliders.update(${i}, 'btn1_link', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 2: النص</label>
                        <input type="text" value="${s.btn2_text || ''}" onchange="adminSliders.update(${i}, 'btn2_text', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 2: الرابط</label>
                        <input type="text" value="${s.btn2_link || ''}" onchange="adminSliders.update(${i}, 'btn2_link', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                </div>
            </div>
        `).join('');
    },
    
    update(index, field, value) {
        this.slides[index][field] = value;
    },
    
    addSlide() {
        this.slides.push({
            img: '', tag: '', title: '', desc: '', 
            btn1_text: 'تسوق الآن', btn1_link: 'shop.html', btn1_class: 'btn btn-primary btn-lg',
            btn2_text: '', btn2_link: '', btn2_class: ''
        });
        this.render();
    },
    
    removeSlide(index) {
        if(confirm('هل أنت متأكد من حذف هذه الشريحة؟')) {
            this.slides.splice(index, 1);
            this.render();
        }
    },
    
    async save() {
        try {
            const res = await fetch('api/save_sliders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.slides)
            });
            const data = await res.json();
            if(data.success) {
                alert('✅ تم حفظ السلايدر بنجاح!');
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم!');
        }
    }
};

const adminBanners = {

    banners: [],
    
    async load() {
        try {
            const res = await fetch('api/get_banners.php?t=' + Date.now());
            this.banners = await res.json();
            this.render();
        } catch (e) {
            console.error("Error loading banners:", e);
        }
    },
    
    render() {
        const container = document.getElementById('admin-banners-builder');
        if (!container) return;
        
        if (!this.banners || this.banners.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text3);">لا يوجد بنرات حالياً.</div>';
            return;
        }
        
        container.innerHTML = this.banners.map((b, i) => `
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:12px; padding:15px; display:flex; flex-direction:column; gap:10px;">
                <h3 style="margin-bottom:10px; border-bottom:1px solid var(--border); padding-bottom:5px;">بنر رقم ${i+1} ${b.is_big ? '(كبير)' : ''}</h3>
                
                <label style="font-size:12px; color:var(--text2);">صورة الخلفية (رابط)</label>
                <input type="text" value="${b.image || ''}" onchange="adminBanners.update(${i}, 'image', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">الشارة (Tag)</label>
                <input type="text" value="${b.tag || ''}" onchange="adminBanners.update(${i}, 'tag', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">العنوان الرئيسي</label>
                <input type="text" value="${b.title || ''}" onchange="adminBanners.update(${i}, 'title', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">النص الفرعي (الوصف)</label>
                <input type="text" value="${b.desc || ''}" onchange="adminBanners.update(${i}, 'desc', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">نص الزر</label>
                <input type="text" value="${b.btn_text || ''}" onchange="adminBanners.update(${i}, 'btn_text', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">رابط الزر</label>
                <input type="text" value="${b.link || ''}" onchange="adminBanners.update(${i}, 'link', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">لون النص (Hex Color)</label>
                <input type="color" value="${b.text_color || '#1f2937'}" onchange="adminBanners.update(${i}, 'text_color', this.value)" style="width:100%; height:40px; border-radius:6px; border:1px solid var(--border); background:var(--bg); cursor:pointer; margin-bottom:5px;">
            </div>
        `).join('');
    },
    
    update(index, field, value) {
        this.banners[index][field] = value;
    },
    
    async save() {
        try {
            const res = await fetch('api/save_banners.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.banners)
            });
            const data = await res.json();
            if(data.success) {
                alert('✅ تم حفظ البنرات بنجاح!');
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم!');
        }
    }
};

const adminNav = {
    data: [],
    async load() {
        try {
            const res = await fetch('api/get_nav.php?t=' + Date.now());
            this.data = await res.json();
            
            // Migrate old "columns" structure to simple "subLinks" if needed
            this.data.forEach(item => {
                if (item.type === 'dropdown' && item.columns) {
                    if (!item.subLinks) item.subLinks = [];
                    item.columns.forEach(col => {
                        if (col.links) {
                            col.links.forEach(l => item.subLinks.push(l));
                        }
                    });
                    delete item.columns;
                }
            });
            this.render();

    // Add dynamic nav items to datalist
    const datalist = document.getElementById('cats-list');
    if (datalist && this.data) {
        this.data.forEach(item => {
            if (item.title && !datalist.querySelector(`option[value="${item.title}"]`)) {
                datalist.insertAdjacentHTML('beforeend', `<option value="${item.title}">`);
            }
            if (item.subLinks) {
                item.subLinks.forEach(sub => {
                    if (sub.title && !datalist.querySelector(`option[value="${sub.title}"]`)) {
                        datalist.insertAdjacentHTML('beforeend', `<option value="${sub.title}">`);
                    }
                });
            }
        });
    }

        } catch(e) {
            showToast('خطأ في تحميل القائمة', 'error');
        }
    },
    
    render() {
        const container = document.getElementById('admin-nav-builder');
        if (!container) return;
        
        if (this.data.length === 0) {
            container.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text3)">القائمة فارغة. اضغط على "+ رابط رئيسي" للبدء.</div>`;
            return;
        }
        
        let html = '';
        this.data.forEach((item, index) => {
            html += `
            <div style="background:var(--bg1); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:20px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                    <div style="display:flex; gap:15px; align-items:center; flex-grow:1;">
                        <span style="cursor:move; font-size:24px; color:var(--text3)">☰</span>
                        
                        <div>
                            <label style="display:block; font-size:12px; color:var(--text3); margin-bottom:4px;">اسم القسم</label>
                            <input type="text" value="${item.title}" onchange="adminNav.updateItem(${index}, 'title', this.value)" style="padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1); width:200px; font-weight:bold; font-size:16px;" placeholder="مثال: مماسح">
                        </div>
                        
                        <div>
                            <label style="display:block; font-size:12px; color:var(--text3); margin-bottom:4px;">النوع</label>
                            <select onchange="adminNav.updateItem(${index}, 'type', this.value)" style="padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1); font-size:14px;">
                                <option value="link" ${item.type === 'link' ? 'selected' : ''}>رابط مباشر</option>
                                <option value="dropdown" ${item.type === 'dropdown' ? 'selected' : ''}>قائمة منسدلة (تحتوي تفرعات)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style="display:block; font-size:12px; color:var(--text3); margin-bottom:4px;">أيقونة (اختياري)</label>
                            <input type="text" value="${item.badge || ''}" onchange="adminNav.updateItem(${index}, 'badge', this.value)" style="padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1); width:80px; font-size:14px;" placeholder="🔥">
                        </div>
                    </div>
                    
                    <div style="display:flex; gap:8px;">
                        <button onclick="adminNav.moveItem(${index}, -1)" title="أعلى" style="background:var(--bg2); border:1px solid var(--border); color:var(--text1); padding:10px; border-radius:8px; cursor:pointer;">⬆</button>
                        <button onclick="adminNav.moveItem(${index}, 1)" title="أسفل" style="background:var(--bg2); border:1px solid var(--border); color:var(--text1); padding:10px; border-radius:8px; cursor:pointer;">⬇</button>
                        <button onclick="adminNav.deleteItem(${index})" title="حذف" style="background:rgba(239,68,68,0.1); border:1px solid var(--red); color:var(--red); padding:10px 15px; border-radius:8px; cursor:pointer; font-weight:bold;">حذف</button>
                    </div>
                </div>
                
                ${item.type === 'dropdown' ? this.renderSimpleDropdown(item, index) : ''}
            </div>
            `;
        });
        
        container.innerHTML = html;
    },
    
    renderSimpleDropdown(item, itemIndex) {
        let html = `<div style="margin-top:20px; padding:20px; background:var(--bg2); border-radius:8px; border:1px solid var(--border);">`;
        html += `<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h4 style="margin:0; color:var(--text2); font-size:15px;">التصنيفات الفرعية</h4>
                    <button onclick="adminNav.addSubLink(${itemIndex})" style="background:var(--blue); color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:bold;">+ إضافة تصنيف فرعي</button>
                 </div>`;
                 
        if (!item.subLinks || item.subLinks.length === 0) {
            html += `<div style="color:var(--text3); font-size:13px; text-align:center; padding:10px;">لا يوجد تصنيفات فرعية بعد.</div>`;
        } else {
            html += `<div style="display:flex; flex-direction:column; gap:10px;">`;
            item.subLinks.forEach((link, linkIndex) => {
                html += `<div style="display:flex; gap:10px; align-items:center; background:var(--bg1); padding:10px; border-radius:6px; border:1px solid var(--border);">
                            <span style="color:var(--text3); font-size:12px;">${linkIndex+1}.</span>
                            <input type="text" value="${link.title}" onchange="adminNav.updateSubLink(${itemIndex}, ${linkIndex}, this.value)" style="padding:8px; width:250px; border:1px solid var(--border); border-radius:6px; background:var(--bg2); color:var(--text1); font-size:14px;" placeholder="اسم التصنيف (مثال: ممسحة دوارة)">
                            <button onclick="adminNav.deleteSubLink(${itemIndex}, ${linkIndex})" style="background:transparent; border:none; color:var(--red); cursor:pointer; font-size:20px; padding:0 10px;" title="حذف">×</button>
                        </div>`;
            });
            html += `</div>`;
        }
        
        html += `</div>`;
        return html;
    },
    
    updateItem(index, key, val) {
        this.data[index][key] = val;
        if (key === 'title' && this.data[index].type === 'link') {
            this.data[index].url = 'shop.html?cat=' + encodeURIComponent(val);
        }
        if (key === 'type' && val === 'dropdown' && !this.data[index].subLinks) {
            this.data[index].subLinks = [];
        }
        this.render();
    },
    moveItem(index, dir) {
        if (index + dir < 0 || index + dir >= this.data.length) return;
        const temp = this.data[index];
        this.data[index] = this.data[index + dir];
        this.data[index + dir] = temp;
        this.render();
    },
    deleteItem(index) {
        if(confirm('هل أنت متأكد من حذف هذا القسم؟')) {
            this.data.splice(index, 1);
            this.render();
        }
    },
    addMainItem() {
        this.data.push({
            id: 'nav_' + Date.now(),
            title: 'قسم جديد',
            url: 'shop.html?cat=' + encodeURIComponent('قسم جديد'),
            type: 'link'
        });
        this.render();
    },
    
    // Simple SubLinks
    addSubLink(itemIndex) {
        if(!this.data[itemIndex].subLinks) this.data[itemIndex].subLinks = [];
        this.data[itemIndex].subLinks.push({title: 'تصنيف فرعي', url: 'shop.html?cat=' + encodeURIComponent('تصنيف فرعي')});
        this.render();
    },
    updateSubLink(itemIndex, linkIndex, val) {
        this.data[itemIndex].subLinks[linkIndex].title = val;
        this.data[itemIndex].subLinks[linkIndex].url = 'shop.html?cat=' + encodeURIComponent(val);
    },
    deleteSubLink(itemIndex, linkIndex) {
        this.data[itemIndex].subLinks.splice(linkIndex, 1);
        this.render();
    },
    
    async saveNavToServer() {
        try {
            const res = await fetch('api/save_nav.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(this.data)
            });
            const result = await res.json();
            if (result.success) {
                showToast('✅ تم حفظ القائمة بنجاح! ستظهر للزوار فوراً.');
            } else {
                showToast('❌ فشل الحفظ: ' + result.message, 'error');
            }
        } catch(e) {
            showToast('❌ حدث خطأ أثناء الحفظ', 'error');
        }
    }
};

window.addEventListener('DOMContentLoaded', () => {
    adminNav.load();
});


const adminIcons = {
    mapping: {}, // { 'squeegees': [prod1_id, prod2_id] }
    iconNames: {
        'squeegees': 'قشاطات',
        'brooms': 'مكانس',
        'sponges': 'ليفة جلي',
        'loofahs': 'ليف حمام',
        'scissors': 'مقصات',
        'personal_care': 'عناية شخصية',
        'dusters': 'منفضة غبار',
        'cosmetics': 'كورمتكس',
        'scales': 'موازين',
        'party': 'حفلات',
        'foil': 'قصدير',
        'plastic': 'بلاستيك',
        'nylon_bags': 'أكياس نايلون',
        'batteries': 'بطاريات',
        'microfiber': 'مايكروفايبر'
    },
    
    async load() {
        try {
            const res = await fetch('api/get_icons.php?t=' + Date.now());
            this.mapping = await res.json();
            // initialize empty ones if not present
            Object.keys(this.iconNames).forEach(key => {
                if(!this.mapping[key]) this.mapping[key] = [];
            });
            this.render();
        } catch(e) {
            console.error(e);
        }
    },
    
    render() {
        const container = document.getElementById('admin-icons-builder');
        if (!container) return;
        
        let html = '';
        Object.keys(this.iconNames).forEach(key => {
            const name = this.iconNames[key];
            const prodIds = this.mapping[key] || [];
            
            // Get product names for these IDs
            const prodsHtml = prodIds.map(id => {
                const p = adminProducts.find(x => String(x.id) === String(id));
                const pName = p ? p.name : 'منتج غير معروف';
                return `<div style="display:inline-flex; align-items:center; background:var(--bg1); padding:4px 8px; border-radius:4px; margin:4px; font-size:12px; border:1px solid var(--border);">
                          ${pName}
                          <span onclick="adminIcons.removeProduct('${key}', '${id}')" style="margin-right:8px; color:var(--red); cursor:pointer; font-weight:bold;">×</span>
                        </div>`;
            }).join('');
            
            html += `
            <div style="background:var(--bg1); border:1px solid var(--border); border-radius:8px; padding:15px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:15px; width:40%;">
                        <div style="background:var(--bg2); padding:10px; border-radius:8px; text-align:center; min-width:120px;">
                            <strong style="color:var(--text1)">${name}</strong>
                        </div>
                        <div style="font-size:12px; color:var(--text3)">${prodIds.length} منتج مرتبط</div>
                    </div>
                    
                    <div style="flex-grow:1; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; gap:10px;">
                            <select id="sel_${key}" style="flex-grow:1; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1);">
                                <option value="">اختر منتجاً لإضافته لهذه الأيقونة...</option>
                                ${adminProducts.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                            </select>
                            <button onclick="adminIcons.addProduct('${key}')" class="btn-outline" style="padding:8px 15px;">إضافة</button>
                        </div>
                        
                        <div style="min-height:30px; padding:8px; border:1px dashed var(--border); border-radius:6px;">
                            ${prodsHtml || '<span style="color:var(--text3); font-size:12px;">لا يوجد منتجات محددة. سيتم عرض المنتجات تلقائياً إذا كان تصنيفها يطابق الأيقونة.</span>'}
                        </div>
                    </div>
                </div>
            </div>`;
        });
        
        container.innerHTML = html;
    },
    
    addProduct(key) {
        const select = document.getElementById(`sel_${key}`);
        const prodId = select.value;
        if(!prodId) return;
        
        if(!this.mapping[key].includes(prodId)) {
            this.mapping[key].push(prodId);
            this.render();
        }
    },
    
    removeProduct(key, prodId) {
        this.mapping[key] = this.mapping[key].filter(id => String(id) !== String(prodId));
        this.render();
    },
    
    async saveIconsToServer() {
        try {
            const res = await fetch('api/save_icons.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(this.mapping)
            });
            const result = await res.json();
            if (result.success) {
                showToast('✅ تم حفظ منتجات الأيقونات بنجاح!');
            } else {
                showToast('❌ فشل الحفظ: ' + result.message, 'error');
            }
        } catch(e) {
            showToast('❌ حدث خطأ أثناء الحفظ', 'error');
        }
    }
};

const originalLoadIcons = adminNav.load;
adminNav.load = async function() {
    await originalLoadIcons.call(adminNav);
    adminIcons.load();
};

