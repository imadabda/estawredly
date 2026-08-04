const fs = require('fs');
let adminPhp = fs.readFileSync('admin.php', 'utf-8');
const customCss = `
.form-section { background: var(--bg2); padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.section-title { font-size: 16px; font-weight: 900; color: #fff; margin-bottom: 16px; display:flex; align-items:center; gap:8px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
`;

if(!adminPhp.includes('.form-section {')) {
  adminPhp = adminPhp.replace('.modal-footer{', customCss + '\\n.modal-footer{');
  fs.writeFileSync('admin.php', adminPhp, 'utf-8');
  console.log('Added CSS.');
}
