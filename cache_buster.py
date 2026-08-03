import os, glob, re

directory = "/Users/imadabda/Documents/Projects/BBQ TOOLS/استوردلي"
extensions = ["*.html", "*.php"]
files_to_check = []
for ext in extensions:
    files_to_check.extend(glob.glob(os.path.join(directory, ext)))

for f in files_to_check:
    with open(f, "r", encoding="utf-8") as file:
        content = file.read()
    
    # Replace v=13 with v=14
    new_content = re.sub(r'v=13', 'v=14', content)
    
    if new_content != content:
        with open(f, "w", encoding="utf-8") as file:
            file.write(new_content)
        print(f"Updated {f}")
print("Cache busting versions updated to v=14")
