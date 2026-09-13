const fs = require('fs');

let adminPhp = fs.readFileSync('admin.php', 'utf-8');

// Add syncVariantsFromDOM before addVariantField
const syncFunc = `
function syncVariantsFromDOM() {
    const container = document.getElementById('variants-container');
    if (!container) return;
    const variantBlocks = container.querySelectorAll('.variant-block');
    const newVariants = [];
    variantBlocks.forEach(block => {
        const nameInput = block.querySelector('.v-name-input');
        const name = nameInput ? nameInput.value.trim() : '';
        
        const optSpans = block.querySelectorAll('.v-opt-text');
        const options = Array.from(optSpans).map(span => span.innerText.trim());
        
        const newOptInput = block.querySelector('.v-new-opt-input');
        if (newOptInput && newOptInput.value.trim() !== '') {
            options.push(newOptInput.value.trim());
            newOptInput.value = '';
        }
        
        newVariants.push({ name, options });
    });
    currentProductVariants = newVariants;
}

function addVariantField() {
    syncVariantsFromDOM();
    currentProductVariants.push({ name: '', options: [] });
    renderVariants();
}
function removeVariantField(index) {
    syncVariantsFromDOM();
    currentProductVariants.splice(index, 1);
    renderVariants();
}
function addVariantOption(index, inputEl) {
    syncVariantsFromDOM();
    const val = inputEl.value.trim();
    if(val) {
        currentProductVariants[index].options.push(val);
        inputEl.value = '';
        renderVariants();
    }
}
function removeVariantOption(vIndex, optIndex) {
    syncVariantsFromDOM();
    currentProductVariants[vIndex].options.splice(optIndex, 1);
    renderVariants();
}
function renderVariants() {
    const c = document.getElementById('variants-container');
    c.innerHTML = currentProductVariants.map((v, i) => \`
        <div class="variant-block" style="background:var(--bg2); padding:15px; border-radius:8px; border:1px solid var(--border); position:relative;">
            <button type="button" onclick="removeVariantOptionField(\${i})" style="position:absolute; top:12px; left:12px; color:var(--red); border:none; background:transparent; cursor:pointer; font-size:12px; font-weight:bold;">✕ حذف الخاصية</button>
            <div style="margin-bottom:12px; max-width:80%;">
                <label style="font-size:13px; color:var(--text2); font-weight:bold; display:block; margin-bottom:5px;">اسم الخاصية (مثال: اللون، المقاس)</label>
                <input type="text" class="v-name-input" value="\${v.name}" placeholder="مثال: اللون" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:6px; background:var(--bg); color:var(--text1);">
            </div>
            <div>
                <label style="font-size:13px; color:var(--text2); font-weight:bold; display:block; margin-bottom:5px;">الخيارات المتاحة</label>
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
                    \${v.options.map((opt, oi) => \`
                        <span style="background:var(--p); color:white; padding:6px 12px; border-radius:6px; font-size:13px; display:inline-flex; align-items:center; gap:8px; font-weight:bold; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                            <span class="v-opt-text">\${opt}</span> 
                            <span style="cursor:pointer; font-weight:bold; color:rgba(255,255,255,0.7);" onclick="removeVariantOptionItem(\${i}, \${oi})">×</span>
                        </span>
                    \`).join('')}
                </div>
                <input type="text" class="v-new-opt-input" placeholder="اكتب خيار واضغط Enter للإضافة..." onkeypress="if(event.key==='Enter') { event.preventDefault(); addVariantOptionItem(\${i}, this); }" style="width:100%; padding:10px; border:1px dashed var(--border); border-radius:6px; background:var(--bg); color:var(--text1); font-size:13px;">
            </div>
        </div>
    \`).join('');
}
// Aliases for the inline onclick handlers since I renamed them slightly to avoid conflicts during testing
window.removeVariantOptionField = function(i) { removeVariantField(i); };
window.removeVariantOptionItem = function(i, oi) { removeVariantOption(i, oi); };
window.addVariantOptionItem = function(i, el) { addVariantOption(i, el); };
`;

// Replace old functions in adminPhp
adminPhp = adminPhp.replace(/function addVariantField\(\) {[\s\S]*?function renderVariants\(\) {[\s\S]*?<\/div>\\n\s*`\)\.join\(''\);\n}/, syncFunc);


// Modal HTML replacement
const newModalHtml = `
<div class="modal-bg" id="product-modal">
  <div class="modal" style="max-width: 800px; width: 95%;">
    <div class="modal-head">
      <h2 class="modal-title" id="modal-title">إضافة منتج جديد</h2>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px;">
      
      <!-- القسم 1: الصور -->
      <div class="form-section">
        <h3 class="section-title">🖼️ صور المنتج</h3>
        
        <div class="field full">
          <label>الصورة الرئيسية <span style="color:var(--red)">*</span></label>
          <div class="img-preview-area" id="img-preview-area" onclick="document.getElementById('img-file').click()" style="max-width: 200px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px dashed var(--border); cursor: pointer; aspect-ratio: 1; display:flex; align-items:center; justify-content:center; background:var(--bg2);">
            <img class="img-preview" id="img-preview-el" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;"/>
            <div id="upload-placeholder" style="text-align:center;">
              <div style="font-size:32px;margin-bottom:8px">📷</div>
              <strong style="color:var(--text2);font-size:13px">اضغط لرفع صورة</strong>
              <small style="color:var(--text3);font-size:11px;display:block;margin-top:4px">أو استخدم الرابط بالأسفل</small>
            </div>
            <input type="file" id="img-file" accept="image/*" style="display:none" onchange="previewImg(this)"/>
          </div>
          <div class="field" style="margin-top:12px">
            <input type="url" id="f-img-url" placeholder="أو أدخل رابط للصورة الرئيسية هنا..." oninput="previewUrl(this.value)" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); background:var(--bg2); color:var(--text1);"/>
          </div>
        </div>

        <div class="field full" style="margin-top: 15px; border-top: 1px solid var(--border); padding-top: 15px;">
          <label style="margin-bottom:10px; display:block;">صور إضافية للمنتج (معرض الصور)</label>
          <div id="additional-images-container" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;"></div>
          <div style="display:flex; gap:10px;">
            <input type="url" id="f-new-img-url" placeholder="أدخل رابط صورة إضافية واضغط Enter..." onkeypress="if(event.key==='Enter'){event.preventDefault();addAdditionalImage(this.value);this.value='';}" style="flex:1; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
            <button type="button" onclick="document.getElementById('img-file-multi').click()" style="padding:10px 15px; border:1px solid var(--p); border-radius:8px; background:var(--p); color:white; cursor:pointer; font-weight:bold; white-space:nowrap;">+ رفع صورة</button>
            <input type="file" id="img-file-multi" accept="image/*" style="display:none" onchange="addAdditionalImageBase64(this)"/>
          </div>
        </div>
      </div>

      <!-- القسم 2: المعلومات الأساسية -->
      <div class="form-section">
        <h3 class="section-title">📝 المعلومات الأساسية</h3>
        <div class="form-grid">
          <div class="field full">
            <label>اسم المنتج <span style="color:var(--red)">*</span></label>
            <input type="text" id="f-name" placeholder="مثال: سماعات Sony WH-1000XM5"/>
          </div>
          <div class="field">
            <label>تصنيف الشريط العلوي <span style="color:var(--red)">*</span></label>
            <input type="text" id="f-cat" list="cats-list" placeholder="اكتب أو اختر..." style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
          </div>
          <div class="field">
            <label>تصنيف التبويب <span style="color:var(--text3);font-size:10px">(للفلترة)</span></label>
            <select id="f-tab">
              <option value="all">الكل</option>
              <option value="men">رجال</option>
              <option value="women">نساء</option>
              <option value="electronics">عروض خاصة</option>
              <option value="home">منزل</option>
            </select>
          </div>
          <div class="field">
            <label>الشارة الإعلانية</label>
            <select id="f-badge">
              <option value="">بدون شارة</option>
              <option value="new">🆕 جديد</option>
              <option value="sale">🔥 تخفيض</option>
              <option value="hot">⚡ رائج</option>
              <option value="best">⭐ مميز</option>
            </select>
          </div>
          <div class="field">
            <label>أيقونة الرئيسية (اختياري)</label>
            <select id="f-icon-cat" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
              <option value="">لا يتبع لأيقونة محددة</option>
              <option value="squeegees">قشاطات</option>
              <option value="brooms">مكانس</option>
              <option value="sponges">ليفة جلي</option>
              <option value="loofahs">ليف حمام</option>
              <option value="scissors">مقصات</option>
              <option value="personal_care">عناية شخصية</option>
              <option value="dusters">منفضة غبار</option>
              <option value="cosmetics">كورمتكس</option>
              <option value="scales">موازين</option>
              <option value="party">حفلات</option>
              <option value="foil">قصدير</option>
              <option value="plastic">بلاستيك</option>
              <option value="nylon_bags">أكياس نايلون</option>
              <option value="batteries">بطاريات</option>
              <option value="microfiber">مايكروفايبر</option>
            </select>
          </div>
          <div class="field full">
            <label>وصف المنتج الترويجي</label>
            <textarea id="f-desc" placeholder="وصف تفصيلي للمنتج، مميزاته واستخداماته..." style="min-height: 80px;"></textarea>
          </div>
        </div>
      </div>

      <!-- القسم 3: الأسعار والمخزون -->
      <div class="form-section">
        <h3 class="section-title">💰 الأسعار والمخزون</h3>
        <div class="form-grid">
          <div class="field">
            <label>السعر الحالي (₪) <span style="color:var(--red)">*</span></label>
            <input type="number" id="f-price" placeholder="0.00" min="0" step="0.01" style="font-weight:bold; color:var(--p);"/>
          </div>
          <div class="field">
            <label>السعر القديم (₪) <span style="color:var(--text3);font-size:10px">يظهر مشطوباً</span></label>
            <input type="number" id="f-old-price" placeholder="0.00" min="0" step="0.01"/>
          </div>
          <div class="field">
            <label>التكلفة / الجملة (₪) <span style="color:var(--text3);font-size:10px">لحساب الأرباح</span></label>
            <input type="number" id="f-cost-price" placeholder="0.00" min="0" step="0.01"/>
          </div>
          <div class="field">
            <label>المخزون المتوفر</label>
            <input type="number" id="f-stock" placeholder="غير محدود" min="0"/>
          </div>
        </div>
      </div>

      <!-- القسم 4: الخصائص والمتغيرات -->
      <div class="form-section">
        <h3 class="section-title">🎨 خصائص المنتج (ألوان، مقاسات، أنواع...)</h3>
        <p style="font-size:12px; color:var(--text3); margin-bottom:12px;">أضف الخصائص ليتمكن العميل من اختيارها عند الشراء.</p>
        <div id="variants-container" style="display:flex; flex-direction:column; gap:12px; margin-bottom:12px;"></div>
        <button type="button" onclick="addVariantField()" style="width:100%; padding:12px; border:2px dashed var(--p); border-radius:8px; background:rgba(67, 97, 238, 0.05); color:var(--p); cursor:pointer; font-weight:bold; transition:all 0.2s;" onmouseover="this.style.background='rgba(67, 97, 238, 0.1)'" onmouseout="this.style.background='rgba(67, 97, 238, 0.05)'">+ إضافة خاصية جديدة</button>
      </div>

      <!-- القسم 5: التقييمات الوهمية -->
      <div class="form-section">
        <h3 class="section-title">⭐ التقييمات (اختياري)</h3>
        <div class="form-grid">
          <div class="field">
            <label>متوسط التقييم (1–5)</label>
            <input type="number" id="f-stars" placeholder="4.8" min="1" max="5" step="0.1"/>
          </div>
          <div class="field">
            <label>عدد المقيّمين</label>
            <input type="number" id="f-reviews" placeholder="0" min="0"/>
          </div>
        </div>
      </div>

    </div>
    <div class="modal-footer" style="padding: 15px 20px; border-top: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <button class="btn-cancel" onclick="closeModal()" style="padding:10px 20px; border-radius:8px;">إلغاء</button>
      <button class="btn-save" onclick="saveProduct()" style="padding:10px 30px; border-radius:8px; font-weight:bold; font-size:16px;">💾 حفظ المنتج بالنظام</button>
    </div>
  </div>
</div>
`;

adminPhp = adminPhp.replace(/<div class="modal-bg" id="product-modal">[\s\S]*?<\/div>\s*<\/div>\s*<\/div>/, newModalHtml);

// Make sure syncVariantsFromDOM is called inside saveProduct
if (adminPhp.includes('function saveProduct() {') && !adminPhp.includes('syncVariantsFromDOM();\n  const name = document.getElementById(\'f-name\')')) {
  adminPhp = adminPhp.replace('function saveProduct() {', 'function saveProduct() {\n  if(typeof syncVariantsFromDOM === "function") syncVariantsFromDOM();');
}

fs.writeFileSync('admin.php', adminPhp, 'utf-8');
console.log('Successfully patched admin.php');
