with open('main.js', 'r', encoding='utf-8') as f:
    main_js = f.read()

# Fix changeSlide
main_js = main_js.replace('function changeSlide(dir) { goSlide(state.slideIndex+dir); loadHeroSliders(); }', 'function changeSlide(dir) { goSlide(state.slideIndex+dir); resetSlideTimer(); }')

# Fix other loadHeroSliders
main_js = main_js.replace('loadHeroSliders();\n  state.slideTimer = setInterval(()=>goSlide(state.slideIndex+1), 3000);', 'resetSlideTimer();\n  state.slideTimer = setInterval(()=>goSlide(state.slideIndex+1), 3000);')
main_js = main_js.replace('function resetSlideTimer() {\n  clearInterval(state.slideTimer);\n  loadHeroSliders();\n}', 'function resetSlideTimer() {\n  clearInterval(state.slideTimer);\n  state.slideTimer = setInterval(()=>goSlide(state.slideIndex+1), 3000);\n}')

# Ensure loadHeroSliders() is called at the end of the file or in init
main_js = main_js.replace('  renderCategories();\n  renderShopItems();', '  renderCategories();\n  renderShopItems();\n  loadHeroSliders();')

with open('main.js', 'w', encoding='utf-8') as f:
    f.write(main_js)
