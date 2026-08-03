import re

with open('admin.php', 'r', encoding='utf-8') as f:
    admin = f.read()

# Replace in viewOrder
replacement_html = """
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                <div>
                  <strong>${i.name}</strong> <span style="color:var(--text3)">(x${i.quantity || i.qty || 1})</span>
                  ${i.selectedVariants && Object.keys(i.selectedVariants).length > 0 ? `<div style="font-size:12px; color:var(--text3); margin-top:2px;">` + Object.entries(i.selectedVariants).map(([k,v]) => `${k}: ${v}`).join(' | ') + `</div>` : ''}
                </div>
                <div>₪${(i.price || 0) * (i.quantity || i.qty || 1)}</div>
            </div>
"""

old_html = """
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                <div><strong>${i.name}</strong> <span style="color:var(--text3)">(x${i.quantity || 1})</span></div>
                <div>₪${i.price * (i.quantity || 1)}</div>
            </div>
"""

admin = admin.replace(old_html.strip(), replacement_html.strip())

# Replace in printOrder
print_replacement = """
    let itemsHtml = Array.isArray(o.items) ? o.items.map(i => `
        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #ddd;">
            <div>
              <strong>${i.name}</strong> (x${i.quantity || i.qty || 1})
              ${i.selectedVariants && Object.keys(i.selectedVariants).length > 0 ? `<div style="font-size:12px; color:#666; margin-top:2px;">` + Object.entries(i.selectedVariants).map(([k,v]) => `${k}: ${v}`).join(' | ') + `</div>` : ''}
            </div>
            <div>₪${(i.price || 0) * (i.quantity || i.qty || 1)}</div>
        </div>
    `).join('') : `<p>${o.items} منتج</p>`;
"""

old_print_html = """
    let itemsHtml = Array.isArray(o.items) ? o.items.map(i => `
        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #ddd;">
            <div><strong>${i.name}</strong> (x${i.quantity || 1})</div>
            <div>₪${i.price * (i.quantity || 1)}</div>
        </div>
    `).join('') : `<p>${o.items} منتج</p>`;
"""
admin = admin.replace(old_print_html.strip(), print_replacement.strip())

with open('admin.php', 'w', encoding='utf-8') as f:
    f.write(admin)

print("Updated viewOrder and printOrder")
