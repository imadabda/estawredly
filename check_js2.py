import re
import subprocess

def check_js_in_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    scripts = re.findall(r'<script>(.*?)</script>', content, re.DOTALL)
    for i, script in enumerate(scripts):
        if not script.strip() or 'document.write' in script: continue
        with open('temp.js', 'w', encoding='utf-8') as temp:
            temp.write(script)
        res = subprocess.run(['node', '-c', 'temp.js'], capture_output=True, text=True)
        if res.returncode != 0:
            print(f"Error in {path} script {i}:")
            print(res.stderr)
check_js_in_file('product.html')
check_js_in_file('admin.php')
check_js_in_file('shop.html')
check_js_in_file('index.html')
