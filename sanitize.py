import os
import re

def clean_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Pattern to match line comments starting with // ════ or // ── or /* ──
    # And we can also just remove lines that look like AI decoration
    lines = content.split('\n')
    new_lines = []
    skip_next = False
    for line in lines:
        stripped = line.strip()
        if stripped.startswith('// ════') or stripped.startswith('/* ════') or stripped.startswith('// ──') or stripped.startswith('/* ──'):
            continue
        if stripped.startswith('// Run on load') or 'Local Fallback' in line or 'Clear cart' in line or 'Handle Icon Mapping' in line:
            continue
        # also if line is just a normal comment that I added, maybe keep it unless it looks very AI
        new_lines.append(line)
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write('\n'.join(new_lines))

for root, dirs, files in os.walk('.'):
    for f in files:
        if f.endswith('.js') or f.endswith('.php') or f.endswith('.html'):
            if 'vendor' not in root and 'node_modules' not in root:
                clean_file(os.path.join(root, f))

print("Sanitized.")
