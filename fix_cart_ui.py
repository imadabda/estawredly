import re

with open('main.js', 'r', encoding='utf-8') as f:
    js = f.read()

variant_html = """
          <div class="ci-name">${i.name}</div>
          ${i.selectedVariants && Object.keys(i.selectedVariants).length > 0 ? `<div style="font-size:12px; color:var(--text3); margin:2px 0;">` + Object.entries(i.selectedVariants).map(([k,v]) => `${k}: ${v}`).join(' | ') + `</div>` : ''}
          <div class="ci-price">
"""

js = js.replace('<div class="ci-name">${i.name}</div>\\n          <div class="ci-price">', variant_html)

# Also update updateCartCounter if necessary
# Wait, let me make sure the replacement was exact
with open('main.js', 'w', encoding='utf-8') as f:
    f.write(js)

print("Updated cart UI")
