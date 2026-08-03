with open('main.js', 'r', encoding='utf-8') as f:
    main = f.read()

main = main.replace('    loadHeroSliders();\n  } catch (e) {', '    resetSlideTimer();\n  } catch (e) {')

with open('main.js', 'w', encoding='utf-8') as f:
    f.write(main)
