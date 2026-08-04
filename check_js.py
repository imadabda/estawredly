import re

with open('admin.php', 'r', encoding='utf-8') as f:
    admin = f.read()

scripts = re.findall(r'<script.*?>(.*?)</script>', admin, re.DOTALL)
js_content = "\n".join(scripts)

with open('admin_js.js', 'w', encoding='utf-8') as f:
    f.write(js_content)
