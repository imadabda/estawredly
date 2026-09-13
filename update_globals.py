import os, re

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Extract the global elements
match = re.search(r'<!-- ═══════════════ CART DRAWER ═══════════════ -->.*?<button class="btt-btn".*?>↑</button>', content, re.DOTALL)
if not match:
    print("Could not find global elements in index.html")
    exit(1)

globals_html = match.group(0)
print(f"Extracted {len(globals_html)} bytes of globals")

targets = ['shop.html', 'product.html', 'checkout.html']
for t in targets:
    if not os.path.exists(t):
        continue
    with open(t, 'r', encoding='utf-8') as f:
        html = f.read()
    
    # Remove old cart/login modals if any exist before script tags
    html = re.sub(r'<!-- Cart Sidebar -->.*?(?=<script)', '', html, flags=re.DOTALL)
    html = re.sub(r'<!-- Login Modal -->.*?(?=<script)', '', html, flags=re.DOTALL)
    html = re.sub(r'<!-- ═══════════════ CART DRAWER ═══════════════ -->.*?<button class="btt-btn".*?>↑</button>\s*', '', html, flags=re.DOTALL)
    
    # Inject before the first <script src="
    # Actually, before <script src="products_db.js
    new_html = re.sub(r'(<script src="products_db\.js|<script src="store\.js|<script src="main\.js)', globals_html + r'\n\n\1', html, count=1)
    
    with open(t, 'w', encoding='utf-8') as f:
        f.write(new_html)
    print(f"Updated {t}")

