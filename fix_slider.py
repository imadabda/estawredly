import re
with open('admin.php', 'r', encoding='utf-8') as f:
    admin = f.read()

admin = re.sub(r'\s*if \(id === \'hero\'\)\s*renderHeroEditor\(\);', '', admin)

with open('admin.php', 'w', encoding='utf-8') as f:
    f.write(admin)
