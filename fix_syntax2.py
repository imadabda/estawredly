import os, re

def clean_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    new_lines = []
    for line in lines:
        if '════' in line or '───' in line:
            continue
        # Also remove dangling */ if it's alone on a line and we removed the opening (though removing ════ might fix it)
        # Actually just removing '════' is enough because the closing line is `   ══════ */`
        new_lines.append(line)
        
    with open(path, 'w', encoding='utf-8') as f:
        f.writelines(new_lines)

for root, dirs, files in os.walk('.'):
    for f in files:
        if f.endswith('.js') or f.endswith('.php') or f.endswith('.html'):
            if 'vendor' not in root and 'node_modules' not in root:
                clean_file(os.path.join(root, f))
print("Fixed.")
