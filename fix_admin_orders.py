import os

filepath = "/Users/imadabda/Documents/Projects/BBQ TOOLS/استوردلي/admin.php"
with open(filepath, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the old loadAdminOrders function
old_func = """function loadAdminOrders() {
  if (typeof Store === 'undefined') return;
  const orders = Store.getOrders();
  const tbody = document.getElementById('all-orders-body');
  if (!tbody) return;
  const statusMap = {pending:'⏳ قيد الانتظار', processing:'⚙️ قيد المعالجة', shipped:'🚛 تم الشحن', delivered:'✅ تم التسليم', cancelled:'❌ ملغي'};
  tbody.innerHTML = orders.slice(0,50).map(o => `
    <tr>
      <td style="font-weight:700">${o.id}</td>
      <td>${o.userName || '-'}</td>
      <td>${o.items?.length || 0} منتج</td>
      <td>₪${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge">${statusMap[o.status]||o.status}</span></td>
      <td>${new Date(o.date).toLocaleDateString('ar-SA')}</td>
      <td>
        <select onchange="Store.updateOrderStatus('${o.id}', this.value); loadAdminOrders(); loadAdminProducts();" style="background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:8px;padding:4px 8px;font-family:inherit;">
          ${['pending','processing','shipped','delivered','cancelled'].map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${statusMap[s]||s}</option>`).join('')}
        </select>
      </td>
    </tr>
  `).join('');
}"""

new_func = """function loadAdminOrders() {
  const tbody = document.getElementById('all-orders-body');
  if (!tbody) return;
  
  tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">جاري التحميل...</td></tr>';

  fetch('api/get_orders.php')
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:red">${data.message}</td></tr>`;
        return;
      }
      
      const orders = data.orders;
      
      // Update stats
      document.getElementById('stat-orders') && (document.getElementById('stat-orders').textContent = orders.length);
      document.getElementById('stat-pending') && (document.getElementById('stat-pending').textContent = orders.filter(o=>o.status==='pending').length);
      const revenue = orders.filter(o=>o.status!=='cancelled').reduce((s,o)=>s+parseFloat(o.total_price||0),0);
      document.getElementById('stat-revenue') && (document.getElementById('stat-revenue').textContent = '₪' + revenue.toLocaleString('ar-SA'));

      const statusMap = {pending:'⏳ قيد الانتظار', processing:'⚙️ قيد المعالجة', completed:'✅ مكتمل', cancelled:'❌ ملغي'};
      
      tbody.innerHTML = orders.map(o => {
        let itemsHtml = '';
        try {
          const items = JSON.parse(o.items_json || '[]');
          itemsHtml = items.map(i => `<div>- ${i.name} <b>(x${i.qty})</b></div>`).join('');
        } catch(e) {}

        return `
        <tr>
          <td style="font-weight:700">#${o.id}</td>
          <td>
            <strong>${o.customer_name}</strong><br/>
            <a href="tel:${o.customer_phone}" style="color:var(--p);font-size:12px">${o.customer_phone}</a>
          </td>
          <td>
            <div style="font-weight:700;color:var(--p)">${o.shipping_zone}</div>
            <div style="font-size:11px;color:var(--text3);max-width:150px;white-space:normal">${o.customer_address}</div>
          </td>
          <td style="font-size:12px;white-space:normal;min-width:150px">${itemsHtml}</td>
          <td>
            <div style="font-weight:900;color:#fff">₪${parseFloat(o.total_price).toLocaleString()}</div>
            <div style="font-size:11px;color:var(--text3)">شحن: ₪${parseFloat(o.shipping_cost).toLocaleString()}</div>
          </td>
          <td><span class="status-badge status-${o.status}">${statusMap[o.status]||o.status}</span></td>
          <td>${new Date(o.created_at).toLocaleDateString('ar-SA')}<br/><small>${new Date(o.created_at).toLocaleTimeString('ar-SA')}</small></td>
          <td>
            <select onchange="updateDbOrderStatus(${o.id}, this.value)" style="background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:8px;padding:4px 8px;font-family:inherit;">
              ${Object.keys(statusMap).map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${statusMap[s]}</option>`).join('')}
            </select>
          </td>
        </tr>
      `}).join('');
    })
    .catch(err => {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:red">خطأ في الاتصال بالخادم</td></tr>`;
    });
}

function updateDbOrderStatus(id, status) {
  fetch('api/update_order_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id, status: status })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert('✅ ' + data.message);
      loadAdminOrders();
    } else {
      alert('❌ ' + data.message);
    }
  })
  .catch(err => alert('خطأ في الاتصال.'));
}
"""

import re
# I will just use regex to replace from "function loadAdminOrders()" to "}" before "window.editProductAdmin"
content = re.sub(r'function loadAdminOrders\(\) \{.*?\n\}\n(?=window\.editProductAdmin)', new_func + '\n', content, flags=re.DOTALL)

with open(filepath, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated loadAdminOrders in admin.php")
