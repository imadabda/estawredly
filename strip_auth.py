import os
import re

dir_path = "/Users/imadabda/Documents/Projects/BBQ TOOLS/استوردلي"
html_files = [f for f in os.listdir(dir_path) if f.endswith(".html")]

for filename in html_files:
    filepath = os.path.join(dir_path, filename)
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()

    # Remove top nav user block (hdr-user) in various forms
    # We might just remove the entire <div class="hdr-user"> block up to its closing div.
    content = re.sub(r'<div class="hdr-user">.*?</div>\s*', '', content, flags=re.DOTALL)
    
    # Remove mobile auth links
    content = re.sub(r'<li class="mobile-only-link"><a href="#" onclick="window\.openAuthModal[^>]+>👤 تسجيل دخول</a></li>\s*', '', content)
    content = re.sub(r'<li class="mobile-only-link"><a href="#" onclick="window\.openAuthModal[^>]+>✨ حساب جديد</a></li>\s*', '', content)
    content = re.sub(r'<li class="mobile-only-link"><a href="#" class="nav-a">👤 تسجيل دخول</a></li>\s*', '', content)
    content = re.sub(r'<li class="mobile-only-link"><a href="#" class="nav-a">✨ حساب جديد</a></li>\s*', '', content)
    
    # Remove modal auth block
    # It starts with <!-- ═══════════════ LOGIN MODAL ═══════════════ -->
    # and ends after <div id="auth-signup" ...> ... </div> </div>
    # A regex to catch the modal mask and the auth modal itself
    content = re.sub(r'<!-- ═══════════════ LOGIN MODAL ═══════════════ -->.*?<div class="modal-mask" id="auth-mask"></div>\s*<div class="auth-modal" id="auth-modal">.*?</form>\s*</div>\s*</div>', '', content, flags=re.DOTALL)
    content = re.sub(r'<div class="modal-mask" id="auth-mask"></div>\s*<div class="auth-modal" id="auth-modal">.*?</form>\s*</div>\s*</div>', '', content, flags=re.DOTALL)
    
    # Remove auth.js inclusion
    content = re.sub(r'<script src="auth\.js[^>]*></script>\s*', '', content)
    
    # Remove Google accounts script
    content = re.sub(r'<script src="https://accounts.google.com/gsi/client" async defer></script>\s*', '', content)

    with open(filepath, "w", encoding="utf-8") as f:
        f.write(content)
        
print("Auth UI stripped from HTML files.")
