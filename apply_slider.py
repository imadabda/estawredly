import re

with open('admin.php', 'r', encoding='utf-8') as f:
    admin = f.read()

# Replace the hero container buttons
admin = re.sub(
    r'<button class="btn-add" onclick="showToast\(\'💡 قريباً: إضافة شريحة جديدة\'\)">\+ شريحة جديدة</button>',
    r'<div><button class="btn-outline" onclick="adminSliders.addSlide()" style="background:transparent; margin-left:10px;">+ شريحة جديدة</button><button class="btn-add" onclick="adminSliders.save()">💾 حفظ السلايدر</button></div>',
    admin
)

# Remove HERO_SLIDES definition
admin = re.sub(r'const HERO_SLIDES = \[\n.*?\];\n', '', admin, flags=re.DOTALL)

# Remove old renderHeroEditor function
admin = re.sub(r'function renderHeroEditor\(\) \{.*?\n\}\n', '', admin, flags=re.DOTALL)

# Add adminSliders logic next to adminBanners
sliders_js = """
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
"""

admin = admin.replace('const adminBanners = {', sliders_js)

# Also need to make sure adminSliders.load() is called when showing page-hero!
load_call = """
  if (id === 'hero')  adminSliders.load();
  if (id === 'banners')  adminBanners.load();
"""
admin = re.sub(r'if \(id === \'banners\'\)\s*adminBanners\.load\(\);', load_call, admin)

with open('admin.php', 'w', encoding='utf-8') as f:
    f.write(admin)

# Now update index.html
with open('index.html', 'r', encoding='utf-8') as f:
    index = f.read()

# Replace everything from <div class="slide active"... to <!-- Slider Controls -->
hero_regex = r'<div class="slide active" style="background-image:url\(\'hero_cleaning1\.jpg\'\)">.*?<!-- Slider Controls -->'
hero_replacement = r'''<div id="dynamic-hero-slides">
    <!-- Slides injected via JS -->
  </div>
  
  <!-- Slider Controls -->'''
index = re.sub(hero_regex, hero_replacement, index, flags=re.DOTALL)

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(index)

print("Done python script")
