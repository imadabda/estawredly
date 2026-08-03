import re

with open('main.js', 'r', encoding='utf-8') as f:
    js = f.read()

# 1. Update removeFromCart and updateQty to use index
js = js.replace('function removeFromCart(id) {', 'function removeFromCart(index) {')
js = js.replace('state.cart = state.cart.filter(i=>i.id!==id);', 'state.cart.splice(index, 1);')

js = js.replace('function updateQty(id, delta) {', 'function updateQty(index, delta) {')
js = js.replace('const item = state.cart.find(i=>i.id===id);', 'const item = state.cart[index];')

# 2. Update updateCartUI to pass index instead of id
js = js.replace('<button onclick="updateQty(${i.id}, -1)">-</button>', '<button onclick="updateQty(${index}, -1)">-</button>')
js = js.replace('<button onclick="updateQty(${i.id}, 1)">+</button>', '<button onclick="updateQty(${index}, 1)">+</button>')
js = js.replace('<button class="ci-del" onclick="removeFromCart(${i.id})">✕</button>', '<button class="ci-del" onclick="removeFromCart(${index})">✕</button>')

# 3. We need to make sure map provides `index`
# Originally it was state.cart.map(i=>...)
js = js.replace('list.innerHTML = state.cart.map(i=>`', 'list.innerHTML = state.cart.map((i, index)=>`')

with open('main.js', 'w', encoding='utf-8') as f:
    f.write(js)

print("Fixed cart operations to support variants")
