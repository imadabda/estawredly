import re

with open('store.js', 'r', encoding='utf-8') as f:
    store = f.read()

# Fix the array reference bug
store = store.replace(
"""    if (typeof PRODUCTS_DB !== 'undefined') {
        // تحديث النسخة في الذاكرة لتجنب الحاجة لعمل ريفريش فوري
        PRODUCTS_DB.length = 0;
        PRODUCTS_DB.push(...products);
    }""",
"""    if (typeof PRODUCTS_DB !== 'undefined' && PRODUCTS_DB !== products) {
        // تحديث النسخة في الذاكرة لتجنب الحاجة لعمل ريفريش فوري
        PRODUCTS_DB.length = 0;
        PRODUCTS_DB.push(...products);
    }"""
)

with open('store.js', 'w', encoding='utf-8') as f:
    f.write(store)

print("Fixed store.js")
