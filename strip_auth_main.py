import re

filepath = "/Users/imadabda/Documents/Projects/BBQ TOOLS/استوردلي/main.js"
with open(filepath, "r", encoding="utf-8") as f:
    content = f.read()

# Remove the check in the checkout button
content = re.sub(r'if \(typeof Store === \'undefined\' \|\| !Store\.isLoggedIn\(\)\) \{.*?return;\s*\}', '', content, flags=re.DOTALL)

# Remove updateAuthUI function
content = re.sub(r'function updateAuthUI\(\) \{.*?\}\s*', '', content, flags=re.DOTALL)
content = re.sub(r'window\.updateAuthUI = updateAuthUI;\s*', '', content)
content = re.sub(r'updateAuthUI\(\);\s*', '', content)

# Remove openAuthModal function
content = re.sub(r'function openAuthModal\(tab\).*?\}\s*', '', content, flags=re.DOTALL)
content = re.sub(r'window\.openAuthModal = openAuthModal;\s*', '', content)

with open(filepath, "w", encoding="utf-8") as f:
    f.write(content)

print("Auth removed from main.js")
