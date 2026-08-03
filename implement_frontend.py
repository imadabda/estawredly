import re

with open('product.html', 'r', encoding='utf-8') as f:
    html = f.read()

# 1. Image Gallery
gallery_html = """
          <div class="gallery-container">
            <img src="" alt="" class="product-img" id="p-img">
            <div id="p-gallery" style="display:flex; gap:10px; margin-top:15px; overflow-x:auto; padding-bottom:5px;"></div>
          </div>
"""
html = html.replace('<img src="" alt="" class="product-img" id="p-img">', gallery_html)

# 2. Variants Container
variants_html = """
          <div id="product-variants-container" style="margin:20px 0;"></div>
"""
html = html.replace('<div class="product-actions">', variants_html + '\n          <div class="product-actions">')

with open('product.html', 'w', encoding='utf-8') as f:
    f.write(html)

with open('main.js', 'r', encoding='utf-8') as f:
    js = f.read()

# 3. Handle Variants and Gallery in JS
js_addition = """
let selectedVariants = {};

function changeMainImage(src) {
    document.getElementById('p-img').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.style.borderColor = 'var(--border)');
    event.currentTarget.style.borderColor = 'var(--primary)';
}

function selectVariant(variantName, optionVal, btnEl) {
    selectedVariants[variantName] = optionVal;
    const parent = btnEl.parentElement;
    parent.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('selected'));
    btnEl.classList.add('selected');
}
"""

js = js.replace("function loadProductDetails() {", js_addition + "\nfunction loadProductDetails() {")

# Modify loadProductDetails
load_p_addition = """
    // Setup Gallery
    const galleryDiv = document.getElementById('p-gallery');
    if (galleryDiv) {
        let galleryHtml = '';
        const allImages = [p.img];
        if (p.images && p.images.length > 0) {
            allImages.push(...p.images);
        }
        
        if (allImages.length > 1) {
            galleryHtml = allImages.map((src, i) => `
                <img src="${src}" class="gallery-thumb" onclick="changeMainImage('${src}')" style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:2px solid ${i===0?'var(--primary)':'var(--border)'}; cursor:pointer; flex-shrink:0;">
            `).join('');
        }
        galleryDiv.innerHTML = galleryHtml;
    }

    // Setup Variants
    const variantsDiv = document.getElementById('product-variants-container');
    selectedVariants = {}; // reset
    if (variantsDiv) {
        if (p.variants && p.variants.length > 0) {
            variantsDiv.innerHTML = p.variants.map(v => {
                // Pre-select first option by default
                if (v.options.length > 0) selectedVariants[v.name] = v.options[0];
                return `
                <div style="margin-bottom:15px;">
                    <strong style="display:block; margin-bottom:8px;">${v.name}:</strong>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        ${v.options.map((opt, i) => `
                            <button onclick="selectVariant('${v.name}', '${opt}', this)" class="variant-btn ${i===0?'selected':''}" style="padding:8px 16px; border:1px solid var(--border); background:var(--bg); color:var(--text); border-radius:6px; cursor:pointer;">${opt}</button>
                        `).join('')}
                    </div>
                </div>
                `;
            }).join('');
        } else {
            variantsDiv.innerHTML = '';
        }
    }
"""

js = js.replace("document.getElementById('p-img').src = p.img;", "document.getElementById('p-img').src = p.img;\n" + load_p_addition)

# Modify addToCart calls inside product page
js = js.replace("addToCart(currentProductId, qty);", "addToCart(currentProductId, qty, selectedVariants);")

with open('main.js', 'w', encoding='utf-8') as f:
    f.write(js)

print("Updated product.html and main.js")
