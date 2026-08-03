import re, subprocess, sys

def check_html(path):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Find all <script>...</script> blocks
    scripts = re.findall(r'<script.*?>\s*(.*?)\s*</script>', content, re.DOTALL | re.IGNORECASE)
    
    for i, script in enumerate(scripts):
        if not script.strip(): continue
        # if it contains PHP tags, we must skip or mock them
        script = re.sub(r'<\?php.*?\?>', '/*php*/', script, flags=re.DOTALL)
        script = re.sub(r'<\?=.*?\?>', '/*php*/', script, flags=re.DOTALL)
        
        with open('temp.js', 'w', encoding='utf-8') as f:
            f.write(script)
        
        res = subprocess.run(['node', '-c', 'temp.js'], capture_output=True, text=True)
        if res.returncode != 0:
            print(f"ERROR in {path} Script {i}:")
            print(res.stderr)

check_html('admin.php')
check_html('product.html')
check_html('shop.html')
check_html('index.html')
check_html('checkout.html')
