import pandas as pd
import json
import math

file = "Kleane translated AR.xlsx"
df = pd.read_excel(file, skiprows=2)

# Filter rows where 'Unnamed: 20' (اسم المنتج بالعربية) is not null
df = df.dropna(subset=['Unnamed: 20', 'Unnamed: 8'])

products = []
id_counter = 1

for idx, row in df.head(30).iterrows():
    name = str(row['Unnamed: 20']).strip()
    desc = str(row['Unnamed: 19']).strip()
    price_rmb = float(row['Unnamed: 8'])
    code = str(row['Unnamed: 1']).strip()
    
    # Pricing formula: RMB * 1.5 for a quick retail Shekel price (1 RMB ~ 0.5 ILS, so * 1.5 is a 3x markup roughly)
    price_ils = math.ceil(price_rmb * 1.5)
    old_price = math.ceil(price_ils * 1.3) # 30% fake discount
    
    # Categorization logic based on name
    cat = 'مماسح مسطحة'
    if 'مايكروفايبر' in name:
        cat = 'مماسح مايكروفايبر'
    elif 'قطنية' in name or 'قطن' in name:
        cat = 'مماسح قطنية'
    elif 'غيار' in name or 'شينيل' in name or 'refill' in str(row['Unnamed: 2']).lower():
        cat = 'قطع غيار'
        
    img = 'https://images.unsplash.com/photo-1584820927508-ea24dfc04f98?auto=format&fit=crop&q=80&w=400' # Generic cleaning placeholder
    
    if 'مايكروفايبر' in name:
        img = 'https://images.unsplash.com/photo-1585058177583-05b1c5521b4a?auto=format&fit=crop&q=80&w=400'
    elif 'قطنية' in name:
        img = 'https://images.unsplash.com/photo-1616401784845-180882ba9ba8?auto=format&fit=crop&q=80&w=400'

    products.append({
        "id": id_counter,
        "name": name,
        "desc": desc,
        "cat": cat,
        "code": code,
        "img": img,
        "price": price_ils,
        "oldPrice": old_price,
        "badge": "new" if id_counter % 3 == 0 else "",
        "stars": 4.5 + (id_counter % 5)*0.1,
        "reviews": 120 + id_counter*15
    })
    id_counter += 1

# Output as JS formatted string
js_out = "const PRODUCTS = {\n  all: [\n"
for p in products:
    js_out += f"    {json.dumps(p, ensure_ascii=False)},\n"
js_out += "  ],\n  flash: [\n"
for p in products[:4]:
    p["badge"] = "sale"
    js_out += f"    {json.dumps(p, ensure_ascii=False)},\n"
js_out += "  ]\n};"

print(js_out)
