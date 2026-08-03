import re

with open('admin.php', 'r', encoding='utf-8') as f:
    admin = f.read()

# 1. Add UI for Additional Images and Variants
ui_addition = """
        <div class="field full" style="background:var(--bg); border:1px solid var(--border); padding:15px; border-radius:12px; margin-top:10px;">
          <label style="font-weight:bold; color:var(--primary)">🖼️ صور إضافية للمنتج (معرض الصور)</label>
          <div id="additional-images-container" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px; margin-top:10px;"></div>
          <input type="url" id="f-new-img-url" placeholder="أدخل رابط صورة واضغط Enter..." onkeypress="if(event.key==='Enter') { event.preventDefault(); addAdditionalImage(this.value); this.value=''; }" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
          <button class="btn btn-outline" onclick="document.getElementById('img-file-multi').click()" type="button" style="margin-top:10px; width:100%">+ رفع صورة من الجهاز</button>
          <input type="file" id="img-file-multi" accept="image/*" style="display:none" onchange="addAdditionalImageBase64(this)"/>
        </div>

        <div class="field full" style="background:var(--bg); border:1px solid var(--border); padding:15px; border-radius:12px; margin-top:10px;">
          <label style="font-weight:bold; color:var(--primary)">🎨 خصائص المنتج (ألوان، مقاسات...)</label>
          <div id="variants-container" style="display:flex; flex-direction:column; gap:15px; margin-bottom:10px; margin-top:10px;"></div>
          <button class="btn btn-outline" onclick="addVariantField()" type="button" style="width:100%">+ إضافة خاصية جديدة (مثل: اللون، المقاس)</button>
        </div>
"""

# Inject before the submit button
admin = admin.replace('<button class="btn btn-primary" onclick="saveProduct()" style="width:100%">حفظ المنتج</button>', ui_addition + '\n        <button class="btn btn-primary" onclick="saveProduct()" style="width:100%">حفظ المنتج</button>')

# 2. Add JavaScript logic for Variants and Images
js_addition = """
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
            <button onclick="removeAdditionalImage(${i})" style="position:absolute; top:2px; right:2px; background:red; color:white; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center;">✕</button>
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
            <button onclick="removeVariantField(${i})" style="position:absolute; top:10px; left:10px; color:red; border:none; background:transparent; cursor:pointer;">✕ حذف الخاصية</button>
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
"""

admin = admin.replace('function openModal(p=null) {', js_addition + '\nfunction openModal(p=null) {')

# 3. Update openModal to load these
open_modal_addition = """
  if (p) {
      currentProductImages = p.images ? [...p.images] : [];
      currentProductVariants = p.variants ? JSON.parse(JSON.stringify(p.variants)) : [];
  } else {
      currentProductImages = [];
      currentProductVariants = [];
  }
  renderAdditionalImages();
  renderVariants();
"""
admin = admin.replace("document.getElementById('f-img-url').value = p ? (p.img || '') : '';", "document.getElementById('f-img-url').value = p ? (p.img || '') : '';\n" + open_modal_addition)

# 4. Update saveProduct to save these
save_addition = """
    images: currentProductImages.length > 0 ? [...currentProductImages] : null,
    variants: currentProductVariants.length > 0 ? JSON.parse(JSON.stringify(currentProductVariants)) : null,
"""
admin = admin.replace("active: true,", "active: pToEdit ? pToEdit.active : true,\n" + save_addition)
admin = admin.replace("const product = {", "const pToEdit = editingId ? adminProducts.find(x=>x.id===editingId) : null;\n  const product = {")

with open('admin.php', 'w', encoding='utf-8') as f:
    f.write(admin)

print("Updated admin.php successfully.")
