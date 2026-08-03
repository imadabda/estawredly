import re

# Fix index.html dots
with open('index.html', 'r', encoding='utf-8') as f:
    index = f.read()

dots_regex = r'<div class="slider-dots" id="slider-dots">.*?</div>'
dots_replacement = '<div class="slider-dots" id="slider-dots"></div>'
index = re.sub(dots_regex, dots_replacement, index, flags=re.DOTALL)

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(index)

# Fix main.js to load sliders
with open('main.js', 'r', encoding='utf-8') as f:
    main_js = f.read()

load_sliders_js = """
// HERO SLIDER
async function loadHeroSliders() {
  const container = document.getElementById('dynamic-hero-slides');
  const dotsContainer = document.getElementById('slider-dots');
  if (!container) return;
  
  try {
    const res = await fetch('api/get_sliders.php');
    let slides = await res.json();
    if (!slides || slides.length === 0) {
      slides = [{
        img: 'hero_cleaning1.jpg', tag: 'أدوات تنظيف احترافية', 
        title: 'كل ما تحتاجه<br/><em>في مكان واحد</em>', desc: 'أكثر من 500 منتج بأفضل الأسعار',
        btn1_text: 'تسوق الآن', btn1_link: 'shop.html', btn1_class: 'btn btn-white btn-lg'
      }];
    }
    
    container.innerHTML = slides.map((s, i) => `
      <div class="slide ${i===0?'active':''}" style="background-image:url('${s.img}')">
        <div class="slide-overlay"></div>
        <div class="container slide-content">
          ${s.tag ? `<div class="slide-tag">${s.tag}</div>` : ''}
          ${s.title ? `<h1 class="slide-title">${s.title}</h1>` : ''}
          ${s.desc ? `<p class="slide-desc">${s.desc}</p>` : ''}
          <div class="slide-btns">
            ${s.btn1_text ? `<a href="${s.btn1_link}" class="${s.btn1_class || 'btn btn-primary'}">${s.btn1_text}</a>` : ''}
            ${s.btn2_text ? `<a href="${s.btn2_link}" class="${s.btn2_class || 'btn btn-outline-white'}">${s.btn2_text}</a>` : ''}
          </div>
        </div>
      </div>
    `).join('');
    
    if (dotsContainer) {
      dotsContainer.innerHTML = slides.map((s, i) => `
        <span class="dot ${i===0?'active':''}" onclick="goSlide(${i})"></span>
      `).join('');
    }
    
    resetSlideTimer();
  } catch (e) {
    console.error('Failed to load hero sliders', e);
  }
}

function goSlide(i) {
"""

main_js = main_js.replace('// HERO SLIDER\nfunction goSlide(i) {', load_sliders_js)

# Call loadHeroSliders() inside initializeApp()
main_js = main_js.replace('resetSlideTimer();', 'loadHeroSliders();')

with open('main.js', 'w', encoding='utf-8') as f:
    f.write(main_js)

print("Done")
