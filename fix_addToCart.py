import re

with open('main.js', 'r', encoding='utf-8') as f:
    js = f.read()

# 1. Update addToCart definition
js = js.replace('function addToCart(product) {', 'function addToCart(product, qty=1, variants={}) {')

# 2. Update cart.find logic to match variants
cart_logic = """
  // Stringify variants for comparison
  const varsStr = JSON.stringify(variants);
  const existing = Store.cart.find(i => i.id === product.id && JSON.stringify(i.selectedVariants || {}) === varsStr);
  if(existing) {
      existing.qty += qty;
  } else {
      Store.cart.push({...product, qty, selectedVariants: variants});
  }
"""

# Replace the old logic
js = re.sub(r'const existing = Store.cart.find\(i=>i.id===product.id\);\s*if\(existing\) existing.qty \+= 1;\s*else Store.cart.push\(\{\.\.\.product, qty:1\}\);', cart_logic, js)

with open('main.js', 'w', encoding='utf-8') as f:
    f.write(js)

print("Fixed addToCart logic")
