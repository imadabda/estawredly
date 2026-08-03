with open('product.html', 'r', encoding='utf-8') as f:
    html = f.read()

html = html.replace('addToCart(product);', 'addToCart(product, currentQty, selectedVariants || {});')

with open('product.html', 'w', encoding='utf-8') as f:
    f.write(html)
