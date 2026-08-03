from bs4 import BeautifulSoup
import subprocess
import os

with open("admin.php", "r", encoding="utf-8") as f:
    soup = BeautifulSoup(f.read(), 'html.parser')

scripts = soup.find_all("script")
for i, script in enumerate(scripts):
    if script.string:
        with open("temp.js", "w", encoding="utf-8") as f:
            f.write(script.string)
        res = subprocess.run(["node", "-c", "temp.js"], capture_output=True, text=True)
        if res.returncode != 0:
            print(f"admin.php Script {i} has syntax error:")
            print(res.stderr)
