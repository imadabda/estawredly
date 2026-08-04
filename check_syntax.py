import re
import subprocess
import os

with open('admin.php', 'r', encoding='utf-8') as f:
    html = f.read()

scripts = re.findall(r'<script>(.*?)</script>', html, re.DOTALL)
for i, s in enumerate(scripts):
    with open(f'temp_script_{i}.js', 'w', encoding='utf-8') as sf:
        sf.write(s)
    res = subprocess.run(['node', '-c', f'temp_script_{i}.js'], capture_output=True, text=True)
    if res.returncode != 0:
        print(f"Error in script {i}:\n{res.stderr}")
    else:
        print(f"Script {i} OK")
    os.remove(f'temp_script_{i}.js')
