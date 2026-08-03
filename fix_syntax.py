import os, re

def clean_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    new_lines = []
    for line in lines:
        stripped = line.strip()
        # if the line is exactly uppercase words and spaces (at least 5 characters) and nothing else
        if re.match(r'^[A-Z0-9 \-–]+$', stripped) and len(stripped) > 3 and not stripped.startswith('<') and not stripped.startswith('//'):
            # It's an orphaned comment line, skip it
            continue
        # Also remove lines with Arabic text that might have been left over from comments
        if "ADMIN DASHBOARD" in stripped or "PRODUCT PAGE LOGIC" in stripped or "PAGE LOADER" in stripped or "SCROLL REVEAL" in stripped or "HEADER SCROLL SHADOW" in stripped or "SMOOTH COUNTER ANIMATION" in stripped or "SMOOTH ANCHOR SCROLL" in stripped:
            continue
            
        new_lines.append(line)
        
    with open(path, 'w', encoding='utf-8') as f:
        f.writelines(new_lines)

for root, dirs, files in os.walk('.'):
    for f in files:
        if f.endswith('.js') or f.endswith('.php') or f.endswith('.html'):
            if 'vendor' not in root and 'node_modules' not in root:
                clean_file(os.path.join(root, f))
print("Fixed.")
