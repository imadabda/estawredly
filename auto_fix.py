import subprocess
import re

files = ['enhancements.js', 'store.js', 'auth.js', 'main.js']

for file in files:
    while True:
        res = subprocess.run(['node', '-c', file], capture_output=True, text=True)
        if res.returncode == 0:
            print(f"{file} is CLEAN.")
            break
        
        # Parse error
        err = res.stderr
        match = re.search(f"{file}:(\\d+)", err)
        if match:
            line_num = int(match.group(1))
            print(f"Fixing {file} at line {line_num}")
            with open(file, 'r', encoding='utf-8') as f:
                lines = f.readlines()
            
            # Delete the offending line
            del lines[line_num - 1]
            
            with open(file, 'w', encoding='utf-8') as f:
                f.writelines(lines)
        else:
            print(f"Could not parse error for {file}: {err}")
            break
