<?php
session_start();
// التحقق من صلاحية الإدارة
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <title>استوردلي | لوحة الإدارة</title>
  <link rel="icon" type="image/png" href="favicon.png" />
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"/>
<script src="products_db.js?v=1785796859234<?= time() ?>">
</script>
<script src="store.js?v=1785796859234">
</script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f1117; --bg2:#1a1d27; --bg3:#22263a;
  --border:rgba(255,255,255,.07); --border2:rgba(255,255,255,.12);
  --text:#e2e8f0; --text2:#94a3b8; --text3:#64748b;
  --p:#3b82f6; --p2:#2563eb; --p-glow:rgba(59,130,246,.2);
  --green:#10b981; --red:#ef4444; --yellow:#f59e0b; --purple:#8b5cf6;
  --r:12px; --r2:18px; --t:.2s cubic-bezier(.4,0,.2,1);
  --sh:0 4px 24px rgba(0,0,0,.3);
}
html{font-size:15px;scroll-behavior:smooth;overflow-x:hidden;width:100%;max-width:100vw}
body{font-family:'Tajawal',sans-serif;direction:rtl;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;width:100%;max-width:100vw;margin:0;padding:0}
a{text-decoration:none;color:inherit}
button{font-family:inherit;cursor:pointer;border:none;background:none}
input,select,textarea{font-family:inherit}
img{max-width:100%;display:block}
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:var(--bg2)}
::-webkit-scrollbar-thumb{background:var(--bg3);border-radius:99px}

.admin-layout{display:grid;grid-template-columns:260px 1fr;min-height:100vh}
@media(max-width:900px){.admin-layout{grid-template-columns:1fr}}

/* ══ SIDEBAR ══ */
.sidebar{
  background:var(--bg2);border-left:1px solid var(--border);
  position:sticky;top:0;height:100vh;overflow-y:auto;
  display:flex;flex-direction:column;transition:transform .3s;
}

.admin-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(3px);z-index:199;opacity:0;visibility:hidden;transition:var(--t)}
.admin-overlay.open{opacity:1;visibility:visible}
.sb-close-btn{display:none;position:absolute;left:15px;top:25px;font-size:18px;color:var(--text);background:var(--bg3);border:none;border-radius:8px;width:32px;height:32px;cursor:pointer}
@media(max-width:900px){
  .sidebar{position:fixed;inset:0 auto 0 auto;right:-280px;width:280px;z-index:200;transition:right .3s ease;transform:none}
  .sidebar.open{right:0;transform:none}
  .sb-close-btn{display:flex;align-items:center;justify-content:center}
}

.sb-logo{padding:24px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px}
.sb-logo-icon{width:36px;height:36px;background:linear-gradient(135deg,var(--p),var(--purple));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.sb-logo-text{font-size:17px;font-weight:900;color:#fff}
.sb-logo-sub{font-size:11px;color:var(--text3);letter-spacing:1px}
.sb-nav{flex:1;padding:12px 12px 24px}
.sb-section{margin-bottom:4px}
.sb-section-title{font-size:10px;font-weight:800;color:var(--text3);text-transform:uppercase;letter-spacing:1.5px;padding:14px 12px 6px}
.sb-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 14px;border-radius:12px;
  font-size:14px;font-weight:600;color:var(--text2);
  cursor:pointer;transition:var(--t);margin-bottom:2px;
  border:none;background:none;width:100%;text-align:right;
}
.sb-item:hover{background:var(--bg3);color:var(--text)}
.sb-item.active{background:linear-gradient(135deg,rgba(59,130,246,.15),rgba(139,92,246,.1));color:var(--p);border:1px solid rgba(59,130,246,.2)}
.sb-item .sb-icon{width:32px;height:32px;border-radius:10px;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;transition:var(--t)}
.sb-item.active .sb-icon{background:linear-gradient(135deg,var(--p),var(--purple));box-shadow:0 4px 12px rgba(59,130,246,.3)}
.sb-item .sb-badge{margin-right:auto;min-width:20px;height:20px;background:var(--red);color:#fff;border-radius:50%;font-size:10px;font-weight:900;display:flex;align-items:center;justify-content:center;padding:0 5px}
.sb-footer{padding:16px 20px;border-top:1px solid var(--border)}
.sb-user{display:flex;align-items:center;gap:10px}
.sb-av{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--p),var(--purple));display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;color:#fff;flex-shrink:0}
.sb-user-name{font-size:13px;font-weight:800;color:#fff}
.sb-user-role{font-size:11px;color:var(--text3)}

/* ══ MAIN ══ */
.main-area{display:flex;flex-direction:column;min-height:100vh}

/* Top Bar */
.topbar{
  background:var(--bg2);border-bottom:1px solid var(--border);
  padding:14px 28px;display:flex;align-items:center;gap:16px;
  position:sticky;top:0;z-index:100;
}
.topbar-menu-btn{display:none;width:38px;height:38px;border-radius:10px;background:var(--bg3);align-items:center;justify-content:center;font-size:18px}
@media(max-width:900px){.topbar-menu-btn{display:flex}}
.topbar-search{flex:1;max-width:360px;position:relative}
.topbar-search input{
  width:100%;padding:9px 14px 9px 36px;border-radius:12px;
  border:1px solid var(--border2);background:var(--bg3);color:var(--text);font-size:13px;font-family:inherit;
  transition:border-color .2s;
}
.topbar-search input::placeholder{color:var(--text3)}
.topbar-search input:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px var(--p-glow)}
.topbar-search .s-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:14px}
.topbar-right{display:flex;align-items:center;gap:10px;margin-right:auto}
.topbar-btn{width:38px;height:38px;border-radius:10px;background:var(--bg3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer;transition:var(--t);position:relative}
.topbar-btn:hover{background:var(--bg);border-color:var(--p)}
.notif-dot{position:absolute;top:7px;right:7px;width:8px;height:8px;background:var(--red);border-radius:50%;border:2px solid var(--bg2)}
.topbar-divider{width:1px;height:28px;background:var(--border)}
.topbar-profile{display:flex;align-items:center;gap:8px;cursor:pointer;padding:6px 10px;border-radius:12px;transition:var(--t)}
.topbar-profile:hover{background:var(--bg3)}
.tp-av{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--p),var(--purple));display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;color:#fff}
.tp-name{font-size:13px;font-weight:700;color:var(--text)}

/* Content */
.content{padding:28px;flex:1}
.page{display:none}
.page.active{display:block}

.page-title{font-size:22px;font-weight:900;color:#fff;margin-bottom:4px}
.page-sub{font-size:13px;color:var(--text2);margin-bottom:28px}

/* Stats Grid */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px;margin-bottom:28px}
.stat-card{
  background:var(--bg2);border:1px solid var(--border);border-radius:var(--r2);padding:22px;
  transition:var(--t);position:relative;overflow:hidden;
}
.stat-card:hover{border-color:var(--border2);transform:translateY(-3px);box-shadow:var(--sh)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--r2) var(--r2) 0 0}
.stat-card.blue::before{background:linear-gradient(90deg,var(--p),var(--purple))}
.stat-card.green::before{background:linear-gradient(90deg,var(--green),#34d399)}
.stat-card.red::before{background:linear-gradient(90deg,var(--red),#f87171)}
.stat-card.yellow::before{background:linear-gradient(90deg,var(--yellow),#fcd34d)}
.stat-icon{width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:14px}
.stat-card.blue .stat-icon{background:linear-gradient(135deg,rgba(59,130,246,.2),rgba(139,92,246,.2))}
.stat-card.green .stat-icon{background:rgba(16,185,129,.15)}
.stat-card.red .stat-icon{background:rgba(239,68,68,.15)}
.stat-card.yellow .stat-icon{background:rgba(245,158,11,.15)}
.stat-val{font-size:28px;font-weight:900;color:#fff;margin-bottom:4px}
.stat-label{font-size:12px;color:var(--text3);font-weight:600}
.stat-change{font-size:12px;font-weight:700;margin-top:10px;display:flex;align-items:center;gap:4px}
.stat-change.up{color:var(--green)}
.stat-change.down{color:var(--red)}

/* Charts Row */
.charts-row{display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:28px}
@media(max-width:1100px){.charts-row{grid-template-columns:1fr}}
.chart-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r2);padding:24px}
.card-title{font-size:15px;font-weight:800;color:#fff;margin-bottom:4px;display:flex;align-items:center;gap:8px}
.card-sub{font-size:12px;color:var(--text3);margin-bottom:20px}
.mini-chart{height:120px;display:flex;align-items:flex-end;gap:8px}
.bar{
  flex:1;border-radius:8px 8px 0 0;min-width:20px;
  background:linear-gradient(to top,rgba(59,130,246,.4),rgba(59,130,246,.8));
  transition:all .4s ease;cursor:pointer;position:relative;
}
.bar:hover{background:linear-gradient(to top,var(--p),var(--purple));transform:scaleY(1.03)}
.bar::after{
  content:attr(data-val);position:absolute;top:-22px;left:50%;transform:translateX(-50%);
  font-size:10px;font-weight:700;color:var(--text2);white-space:nowrap;opacity:0;transition:opacity .2s;
}
.bar:hover::after{opacity:1}
.donut-wrap{display:flex;flex-direction:column;align-items:center;gap:16px}
.donut{width:120px;height:120px;border-radius:50%;position:relative}
.donut-labels{width:100%}
.donut-label{display:flex;align-items:center;justify-content:space-between;font-size:12px;padding:5px 0;border-bottom:1px solid var(--border)}
.donut-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.donut-label span{color:var(--text2)}
.donut-label strong{color:#fff}

/* Recent Orders Table */
.table-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r2);overflow:hidden;margin-bottom:28px}
.table-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
table{width:100%;border-collapse:collapse}
th{font-size:11px;font-weight:800;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;padding:12px 20px;text-align:right;background:rgba(255,255,255,.02);border-bottom:1px solid var(--border)}
td{padding:14px 20px;font-size:13px;color:var(--text2);border-bottom:1px solid rgba(255,255,255,.03)}
tr:hover td{background:rgba(255,255,255,.02);color:var(--text)}
tr:last-child td{border-bottom:none}
.order-id{font-weight:800;color:var(--p);font-size:13px}
.order-customer{display:flex;align-items:center;gap:10px}
.oc-av{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,var(--p),var(--purple));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:#fff;flex-shrink:0}
.status-badge{padding:4px 12px;border-radius:8px;font-size:11px;font-weight:800}
.status-pending{background:rgba(245,158,11,.15);color:var(--yellow)}
.status-shipped{background:rgba(59,130,246,.15);color:var(--p)}
.status-delivered{background:rgba(16,185,129,.15);color:var(--green)}
.status-cancelled{background:rgba(239,68,68,.15);color:var(--red)}

.toolbar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px}
.search-field{
  flex:1;min-width:200px;padding:10px 14px;border-radius:12px;
  border:1px solid var(--border2);background:var(--bg3);color:var(--text);font-size:13px;font-family:inherit;
}
.search-field:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px var(--p-glow)}
.select-field{
  padding:10px 14px;border-radius:12px;border:1px solid var(--border2);
  background:var(--bg3);color:var(--text);font-size:13px;font-family:inherit;cursor:pointer;
}
.select-field:focus{outline:none;border-color:var(--p)}
.btn-add{
  display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:12px;
  background:linear-gradient(135deg,var(--p),var(--purple));color:#fff;
  font-size:13px;font-weight:800;cursor:pointer;transition:var(--t);border:none;font-family:inherit;
  box-shadow:0 4px 16px rgba(59,130,246,.25);
}
.btn-add:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(59,130,246,.35)}
.btn-outline{
  padding:10px 16px;border-radius:12px;border:1px solid var(--border2);
  color:var(--text2);font-size:13px;font-weight:700;cursor:pointer;transition:var(--t);font-family:inherit;background:transparent;
}
.btn-outline:hover{border-color:var(--p);color:var(--p);background:var(--p-glow)}

/* Products grid */
.prod-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
.prod-admin-card{
  background:var(--bg2);border:1px solid var(--border);border-radius:var(--r2);overflow:hidden;
  transition:var(--t);position:relative;
}
.prod-admin-card:hover{border-color:var(--border2);transform:translateY(-4px);box-shadow:var(--sh)}
.pac-img{height:160px;overflow:hidden;position:relative;background:var(--bg3)}
.pac-img img{width:100%;height:100%;object-fit:contain;transition:transform .4s}
.prod-admin-card:hover .pac-img img{transform:scale(1.06)}
.pac-overlay{
  position:absolute;inset:0;background:rgba(0,0,0,.6);
  display:flex;align-items:center;justify-content:center;gap:8px;
  opacity:0;transition:opacity .3s;
}
.prod-admin-card:hover .pac-overlay{opacity:1}
.pac-action{
  width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);
  border:1px solid rgba(255,255,255,.2);color:#fff;font-size:16px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--t);
}
.pac-action:hover{background:rgba(255,255,255,.3);transform:scale(1.1)}
.pac-action.del:hover{background:rgba(239,68,68,.4)}
.pac-badge{position:absolute;top:10px;right:10px;padding:3px 10px;border-radius:8px;font-size:10px;font-weight:800}
.pac-info{padding:14px}
.pac-cat{font-size:10px;font-weight:800;color:var(--p);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.pac-name{font-size:13px;font-weight:800;color:var(--text);margin-bottom:8px;line-height:1.4}
.pac-price{display:flex;align-items:center;justify-content:space-between}
.pac-price-main{font-size:16px;font-weight:900;color:#fff}
.pac-price-old{font-size:12px;color:var(--text3);text-decoration:line-through}
.pac-stats{display:flex;gap:12px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)}
.pac-stat{font-size:11px;color:var(--text3);display:flex;align-items:center;gap:4px}
.pac-stat strong{color:var(--text2)}
.pac-toggle{position:absolute;top:10px;left:10px}

/* Toggle switch */
.toggle{position:relative;width:36px;height:20px;display:inline-block}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.toggle-slider{
  position:absolute;inset:0;background:var(--bg3);border-radius:99px;cursor:pointer;transition:var(--t);
  border:1px solid var(--border2);
}
.toggle-slider::before{
  content:'';position:absolute;width:14px;height:14px;background:#fff;border-radius:50%;
  top:2px;right:2px;transition:var(--t);
}
.toggle input:checked+.toggle-slider{background:var(--green);border-color:var(--green)}
.toggle input:checked+.toggle-slider::before{transform:translateX(-16px)}

.modal-bg{
  position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(8px);z-index:1000;
  display:flex;align-items:center;justify-content:center;padding:20px;
  opacity:0;visibility:hidden;transition:var(--t);
}
.modal-bg.open{opacity:1;visibility:visible}
.modal{
  background:var(--bg2);border:1px solid var(--border2);border-radius:24px;
  width:100%;max-width:640px;max-height:90vh;overflow-y:auto;
  transform:scale(.9);transition:transform .3s;
}
.modal-bg.open .modal{transform:scale(1)}
.modal-head{padding:24px 28px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--bg2);z-index:2}
.modal-title{font-size:18px;font-weight:900;color:#fff}
.modal-close{width:36px;height:36px;border-radius:10px;background:var(--bg3);color:var(--text2);font-size:18px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--t)}
.modal-close:hover{background:rgba(239,68,68,.2);color:var(--red)}
.modal-body{font-family:'Tajawal',sans-serif;direction:rtl;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;width:100%;max-width:100vw;margin:0;padding:0}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:600px){.form-grid{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:6px}
.field.full{grid-column:1/-1}
.field label{font-size:12px;font-weight:800;color:var(--text2)}
.field input,.field select,.field textarea{
  padding:11px 14px;border-radius:12px;border:1px solid var(--border2);
  background:var(--bg3);color:var(--text);font-size:14px;font-family:inherit;
  transition:border-color .2s;
}
.field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px var(--p-glow)}
.field textarea{resize:vertical;min-height:90px}
.field input[type="file"]{padding:8px;cursor:pointer}
.img-preview-area{
  border:2px dashed var(--border2);border-radius:16px;padding:24px;text-align:center;
  cursor:pointer;transition:var(--t);background:var(--bg3);
}
.img-preview-area:hover{border-color:var(--p);background:var(--p-glow)}
.img-preview-area.has-img{border-style:solid;border-color:var(--p)}
.img-preview{width:100%;max-height:180px;object-fit:contain;border-radius:10px;margin-bottom:10px;display:none}
.modal-footer{padding:20px 28px;border-top:1px solid var(--border);display:flex;gap:12px;justify-content:flex-end;position:sticky;bottom:0;background:var(--bg2)}
.btn-save{padding:12px 28px;border-radius:12px;background:linear-gradient(135deg,var(--p),var(--purple));color:#fff;font-size:14px;font-weight:800;border:none;cursor:pointer;font-family:inherit;transition:var(--t)}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(59,130,246,.3)}
.btn-cancel{padding:12px 20px;border-radius:12px;border:1px solid var(--border2);color:var(--text2);font-size:14px;font-weight:700;background:transparent;cursor:pointer;font-family:inherit;transition:var(--t)}
.btn-cancel:hover{border-color:var(--red);color:var(--red)}

.settings-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:768px){.settings-grid{grid-template-columns:1fr}}
.setting-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r2);padding:24px}
.setting-card h3{font-size:15px;font-weight:800;color:#fff;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.setting-card.full{grid-column:1/-1}
.setting-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border)}
.setting-row:last-child{border-bottom:none}
.setting-row-info strong{font-size:13px;color:var(--text);display:block;margin-bottom:2px}
.setting-row-info small{font-size:11px;color:var(--text3)}
.color-picker-row{display:flex;align-items:center;gap:8px}
.color-swatch{width:32px;height:32px;border-radius:8px;border:2px solid var(--border2);cursor:pointer;transition:transform .2s}
.color-swatch:hover{transform:scale(1.1)}
.color-swatch.active{border-color:var(--p);transform:scale(1.15)}

.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px}
.media-item{border-radius:14px;overflow:hidden;position:relative;aspect-ratio:1;background:var(--bg3);border:2px solid var(--border);cursor:pointer;transition:var(--t)}
.media-item:hover{border-color:var(--p);transform:scale(1.03)}
.media-item img{width:100%;height:100%;object-fit:contain}
.media-item .mi-overlay{position:absolute;inset:0;background:rgba(0,0,0,.6);opacity:0;transition:opacity .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.media-item:hover .mi-overlay{opacity:1}
.upload-slot{border:2px dashed var(--border2);border-radius:14px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;cursor:pointer;transition:var(--t);color:var(--text3)}
.upload-slot:hover{border-color:var(--p);color:var(--p);background:var(--p-glow)}
.upload-slot .up-icon{font-size:28px}
.upload-slot small{font-size:11px;font-weight:600}

.section-divider{height:1px;background:var(--border);margin:28px 0}
.empty-state{text-align:center;padding:60px 20px;color:var(--text3)}
.empty-state .es-icon{font-size:48px;margin-bottom:12px;opacity:.5}
.empty-state p{font-size:14px;font-weight:600}
.tooltip{position:relative}
.tooltip::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 6px);right:50%;transform:translateX(50%);background:#0f1117;color:#fff;font-size:11px;padding:5px 10px;border-radius:8px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .2s;border:1px solid var(--border2)}
.tooltip:hover::after{opacity:1}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.breadcrumb-admin{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text3);margin-bottom:6px}
.breadcrumb-admin span{color:var(--text2)}
.chip{padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700}
.chip-blue{background:rgba(59,130,246,.15);color:var(--p)}
.chip-green{background:rgba(16,185,129,.15);color:var(--green)}
.chip-red{background:rgba(239,68,68,.15);color:var(--red)}
.chip-yellow{background:rgba(245,158,11,.15);color:var(--yellow)}
.notification-list{display:flex;flex-direction:column;gap:12px}
.notif-item{display:flex;align-items:flex-start;gap:12px;padding:14px;background:var(--bg3);border-radius:14px;border:1px solid var(--border)}
.notif-icon{width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.notif-item strong{display:block;font-size:13px;color:var(--text);margin-bottom:2px}
.notif-item small{font-size:11px;color:var(--text3)}
.toast-admin{position:fixed;top:24px;left:24px;z-index:9999;background:#1a1d27;border:1px solid var(--border2);border-radius:14px;padding:14px 18px;color:var(--text);font-size:13px;font-weight:700;display:flex;align-items:center;gap:10px;box-shadow:0 16px 40px rgba(0,0,0,.4);transform:translateY(-80px);transition:transform .3s;min-width:240px}
.toast-admin.show{transform:translateY(0)}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
.spin{animation:spin 2s linear infinite}


@media(max-width:900px){
  .topbar { padding: 12px; gap: 8px; flex-wrap: wrap; }
  .topbar-search { order: 3; flex: 1 1 100%; max-width: 100%; margin-top: 5px; min-width: 0; }
  .tp-name, .topbar-divider { display: none; }
  .topbar-right { margin-right: auto; gap: 4px; }
  .stats-grid { grid-template-columns: 1fr !important; gap: 12px; }
  .content { padding: 16px; }
}



@media(max-width:900px){
  input, select, textarea { font-size: 16px !important; }
}


/* Fix CSS grid min-width: auto bug */
.main-area, .content, .charts-row, .table-card { min-width: 0; }

@keyframes pulseNew {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
  70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.badge-new {
  background: #10b981;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 12px;
  margin-right: 6px;
  animation: pulseNew 2s infinite;
  display: inline-block;
  vertical-align: middle;
}
.print-btn {
  background: var(--bg3);
  color: var(--text);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 4px 8px;
  font-family: inherit;
  font-size: 12px;
  cursor: pointer;
  margin-right: 4px;
}
.print-btn:hover { background: var(--border); }
.view-btn {
  background: rgba(59, 130, 246, 0.1);
  color: var(--p);
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 8px;
  padding: 4px 8px;
  font-family: inherit;
  font-size: 12px;
  cursor: pointer;
  margin-right: 4px;
}
.view-btn:hover { background: rgba(59, 130, 246, 0.2); }

</style>
  <link rel="manifest" href="manifest.json" />
  <meta name="theme-color" content="#3b82f6" />
  <link rel="apple-touch-icon" href="logo.jpg" />
</head>
<body>

<!-- Toast -->
<div class="toast-admin" id="admin-toast">✅ تم الحفظ بنجاح</div>

<!-- View Order Modal -->
<div class="modal-bg" id="order-view-modal">
  <div class="modal" style="max-width: 600px;">
    <div class="modal-head">
      <h2 class="modal-title">تفاصيل الطلبية <span id="ov-id" style="color:var(--p)"></span></h2>
      <button class="modal-close" onclick="closeOrderModal()">✕</button>
    </div>
    <div class="modal-body" id="ov-body" style="padding: 20px; color: var(--text);">
      <!-- Content populated by JS -->
    </div>
    <div class="modal-footer" style="padding: 15px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px;">
      <button class="btn" style="background:var(--bg3);color:var(--text)" onclick="closeOrderModal()">إغلاق</button>
      <button class="btn btn-primary" id="ov-print-btn">🖨️ طباعة الفاتورة</button>
    </div>
  </div>
</div>

<!-- Add/Edit Product Modal -->
<div class="modal-bg" id="product-modal">
  <div class="modal">
    <div class="modal-head">
      <h2 class="modal-title" id="modal-title">إضافة منتج جديد</h2>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="field full">
          <label>صورة المنتج</label>
          <div class="img-preview-area" id="img-preview-area" onclick="document.getElementById('img-file').click()">
            <img class="img-preview" id="img-preview-el" src="" alt=""/>
            <div id="upload-placeholder">
              <div style="font-size:32px;margin-bottom:8px">📷</div>
              <strong style="color:var(--text2);font-size:13px">اضغط لرفع صورة</strong>
              <small style="color:var(--text3);font-size:11px;display:block;margin-top:4px">JPG, PNG, WEBP – حتى 5MB</small>
            </div>
            <input type="file" id="img-file" accept="image/*" style="display:none" onchange="previewImg(this)"/>
          </div>
          <div class="field" style="margin-top:8px">
            <label>أو أدخل رابط صورة</label>
            <input type="url" id="f-img-url" placeholder="https://..." oninput="previewUrl(this.value)"/>
          </div>
        </div>
        <div class="field full">
          <label>اسم المنتج <span style="color:var(--red)">*</span></label>
          <input type="text" id="f-name" placeholder="مثال: سماعات Sony WH-1000XM5"/>
        </div>
        <div style="display:flex; gap:10px;">
        <div class="field" style="flex:1;">
          <label>تصنيف الشريط العلوي <span style="color:var(--red)">*</span></label>
          <input type="text" id="f-cat" list="cats-list" placeholder="اكتب أو اختر..." style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
          <datalist id="cats-list">
            <!-- Dynamic navbar cats loaded here -->
          </datalist>
        </div>
        
        <div class="field" style="flex:1;">
          <label>أيقونة الرئيسية (اختياري)</label>
          <select id="f-icon-cat" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
            <option value="">لا يتبع لأيقونة محددة</option>
            <option value="squeegees">قشاطات</option>
            <option value="brooms">مكانس</option>
            <option value="sponges">ليفة جلي</option>
            <option value="loofahs">ليف حمام</option>
            <option value="scissors">مقصات</option>
            <option value="personal_care">عناية شخصية</option>
            <option value="dusters">منفضة غبار</option>
            <option value="cosmetics">كورمتكس</option>
            <option value="scales">موازين</option>
            <option value="party">حفلات</option>
            <option value="foil">قصدير</option>
            <option value="plastic">بلاستيك</option>
            <option value="nylon_bags">أكياس نايلون</option>
            <option value="batteries">بطاريات</option>
            <option value="microfiber">مايكروفايبر</option>
          </select>
        </div>
        </div>
        <div class="field">
          <label>الشارة</label>
          <select id="f-badge">
            <option value="">بدون شارة</option>
            <option value="new">🆕 جديد</option>
            <option value="sale">🔥 تخفيض</option>
            <option value="hot">⚡ رائج</option>
            <option value="best">⭐ مميز</option>
          </select>
        </div>
        <div class="field">
          <label>السعر الحالي (₪) <span style="color:var(--red)">*</span></label>
          <input type="number" id="f-price" placeholder="0.00" min="0" step="0.01"/>
        </div>
        <div class="field">
          <label>التكلفة / سعر الجملة (₪) <span style="color:var(--text3);font-size:10px">لحساب الأرباح</span></label>
          <input type="number" id="f-cost-price" placeholder="0.00" min="0" step="0.01"/>
        </div>
        <div class="field">
          <label>السعر القديم (₪) <span style="color:var(--text3);font-size:10px">اختياري</span></label>
          <input type="number" id="f-old-price" placeholder="0.00" min="0" step="0.01"/>
        </div>
        <div class="field">
          <label>التقييم (1–5)</label>
          <input type="number" id="f-stars" placeholder="4.8" min="1" max="5" step="0.1"/>
        </div>
        <div class="field">
          <label>عدد التقييمات</label>
          <input type="number" id="f-reviews" placeholder="0" min="0"/>
        </div>
        <div class="field">
          <label>الكمية المتوفرة (المخزون)</label>
          <input type="number" id="f-stock" placeholder="غير محدود" min="0"/>
        </div>
        <div class="field full">
          <label>وصف المنتج</label>
          <textarea id="f-desc" placeholder="وصف تفصيلي للمنتج، مميزاته واستخداماته..."></textarea>
        </div>
        <div class="field full">
          <label>تصنيف التبويب <span style="color:var(--text3);font-size:10px">(للفلترة)</span></label>
          <select id="f-tab">
            <option value="all">الكل</option>
            <option value="men">رجال</option>
            <option value="women">نساء</option>
            <option value="electronics">عروض خاصة</option>
            <option value="home">منزل</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">إلغاء</button>
      <button class="btn-save" onclick="saveProduct()">💾 حفظ المنتج</button>
    </div>
  </div>
</div>

<!-- LAYOUT -->
<div class="admin-overlay" id="admin-overlay" onclick="toggleAdminSidebar()"></div>
<div class="admin-layout">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar" id="sidebar">
    <div class="sb-logo">
      <div class="sb-logo-icon">🛍️</div>
            <div>
        <div class="sb-logo-text">إستوردلي</div>
        <div class="sb-logo-sub">ADMIN PANEL</div>
      </div>
      <button class="sb-close-btn" onclick="toggleAdminSidebar()">✕</button>
    </div>

    <nav class="sb-nav">
      <div class="sb-section">
        <div class="sb-section-title">الرئيسية</div>
        <button class="sb-item active" onclick="showPage('overview',this)">
          <span class="sb-icon">📊</span> نظرة عامة
        </button>
        <button class="sb-item" onclick="showPage('analytics',this)">
          <span class="sb-icon">📈</span> التحليلات
        </button>
      </div>

      <div class="sb-section">
        <div class="sb-section-title">المتجر</div>
        <button class="sb-item" onclick="showPage('products',this)">
          <span class="sb-icon">📦</span> المنتجات
          <span class="sb-badge" id="products-count">0</span>
        </button>
        <button class="sb-item" onclick="showPage('inventory',this)">
          <span class="sb-icon">📋</span> المخزون
        </button>
        <button class="sb-item" onclick="showPage('orders',this)">
          <span class="sb-icon">🛒</span> الطلبيات
          <span class="sb-badge" style="background:var(--yellow);color:#000">12</span>
        </button>
        <button class="sb-item" onclick="showPage('customers',this)">
          <span class="sb-icon">👥</span> العملاء
          <span class="sb-badge" id="pending-users-badge" style="background:var(--red);display:none;">0</span>
        </button>
        <button class="sb-item" onclick="showPage('media',this)">
          <span class="sb-icon">🖼️</span> مكتبة الصور
        </button>
      </div>

      <div class="sb-section">
        <div class="sb-section-title">المحتوى</div>
        <button class="sb-item" onclick="showPage('banners',this)">
          <span class="sb-icon">🏷️</span> إدارة البنرات
        </button>
        <button class="sb-item" onclick="showPage('icons',this)">
          <span class="sb-icon">🖼️</span> أيقونات الرئيسية
        </button>
        <button class="sb-item" onclick="showPage('hero',this)">
          <span class="sb-icon">🎨</span> السلايدر الرئيسي
        </button>
        <button class="sb-item" onclick="showPage('categories',this)">
          <span class="sb-icon">🗂️</span> التصنيفات
        </button>
      </div>

      <div class="sb-section">
        <div class="sb-section-title">الإعدادات</div>
        <button class="sb-item" onclick="showPage('settings',this)">
          <span class="sb-icon">⚙️</span> إعدادات المتجر
        </button>
        <button class="sb-item" onclick="showPage('notifications',this)">
          <span class="sb-icon">🔔</span> الإشعارات
          <span class="sb-badge" id="notif-badge">3</span>
        </button>
        <button class="sb-item" onclick="window.open('index.html','_blank')">
          <span class="sb-icon">🌐</span> عرض الموقع
        </button>
      </div>
    </nav>

    <div class="sb-footer">
      <div class="sb-user">
        <div class="sb-av">م</div>
        <div>
          <div class="sb-user-name">مدير المتجر</div>
          <div class="sb-user-role">admin@estawredly.com</div>
        </div>
        <button onclick="showToast('⛔ تم تسجيل الخروج')" style="margin-right:auto;font-size:16px;color:var(--text3);cursor:pointer;" title="تسجيل خروج">↩</button>
      </div>
    </div>
  </aside>

  <!-- ══ MAIN AREA ══ -->
  <div class="main-area">

    <!-- Top Bar -->
    <div class="topbar">
      <button class="topbar-menu-btn" onclick="toggleAdminSidebar()">☰</button>
      <div class="topbar-search">
        <span class="s-icon">🔍</span>
        <input type="text" placeholder="بحث سريع في المنتجات..." id="global-search" oninput="globalSearch(this.value)"/>
      </div>
      <div class="topbar-right">
        <button class="topbar-btn tooltip" data-tip="الإشعارات" onclick="showPage('notifications',null)">
          🔔<div class="notif-dot"></div>
        </button>
        <button class="topbar-btn tooltip" data-tip="الدعم الفني">💬</button>
        <div class="topbar-divider"></div>
        <div class="topbar-profile">
          <div class="tp-av">م</div>
          <span class="tp-name">مدير المتجر</span>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="content">

      <!-- ══ OVERVIEW ══ -->
      <div class="page active" id="page-overview">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">لوحة التحكم <span>›</span> نظرة عامة</div>
            <h1 class="page-title">مرحباً بك 👋</h1>
            <p class="page-sub">آخر تحديث: الآن — <span id="live-time"></span></p>
          </div>
          <button class="btn-add" onclick="showPage('products',null);setTimeout(()=>openModal(),200)">
            + إضافة منتج جديد
          </button>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-icon">💰</div>
            <div class="stat-val" id="stat-revenue">₪0</div>
            <div class="stat-label">إجمالي الإيرادات</div>
            <div class="stat-change up">من الطلبات غير الملغية</div>
          </div>
          <div class="stat-card green" style="border-top:3px solid var(--green)">
            <div class="stat-icon" style="background:rgba(16,185,129,.15)">📈</div>
            <div class="stat-val" id="stat-profit">₪0</div>
            <div class="stat-label">إجمالي الأرباح</div>
            <div class="stat-change up">صافي الربح بعد التكلفة</div>
          </div>
          <div class="stat-card green">
            <div class="stat-icon">🛒</div>
            <div class="stat-val" id="stat-orders">0</div>
            <div class="stat-label">الطلبيات هذا الشهر</div>
            <div class="stat-change up">↑ 24 طلبية هذا الأسبوع</div>
          </div>
          <div class="stat-card yellow">
            <div class="stat-icon">📦</div>
            <div class="stat-val" id="stat-products">0</div>
            <div class="stat-label">إجمالي المنتجات</div>
            <div class="stat-change up">↑ أُضيف 8 منتجات اليوم</div>
          </div>
          <div class="stat-card red">
            <div class="stat-icon">👥</div>
            <div class="stat-val" id="stat-users">1,247</div>
            <div class="stat-label">العملاء المسجلين</div>
            <div class="stat-change up">↑ 43 عميل جديد اليوم</div>
          </div>
        </div>

        <!-- Charts -->
        <div class="charts-row">
          <div class="chart-card">
            <div class="card-title">📊 المبيعات – آخر 7 أيام</div>
            <div class="card-sub">الإيرادات اليومية بالشيقل</div>
            <div class="mini-chart" id="revenue-chart"></div>
          </div>
          <div class="chart-card">
            <div class="card-title">🥧 المبيعات حسب الفئة</div>
            <div class="card-sub">توزيع المبيعات</div>
            <div class="donut-wrap">
              <svg width="120" height="120" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="45" fill="none" stroke="#1a1d27" stroke-width="20"/>
                <circle cx="60" cy="60" r="45" fill="none" stroke="#3b82f6" stroke-width="20" stroke-dasharray="113 169" stroke-dashoffset="0" transform="rotate(-90 60 60)"/>
                <circle cx="60" cy="60" r="45" fill="none" stroke="#10b981" stroke-width="20" stroke-dasharray="67 215" stroke-dashoffset="-113" transform="rotate(-90 60 60)"/>
                <circle cx="60" cy="60" r="45" fill="none" stroke="#f59e0b" stroke-width="20" stroke-dasharray="50 232" stroke-dashoffset="-180" transform="rotate(-90 60 60)"/>
                <circle cx="60" cy="60" r="45" fill="none" stroke="#8b5cf6" stroke-width="20" stroke-dasharray="52 230" stroke-dashoffset="-230" transform="rotate(-90 60 60)"/>
              </svg>
              <div class="donut-labels">
                <div class="donut-label"><span><div class="donut-dot" style="background:#3b82f6;display:inline-block"></div> عروض خاصة</span><strong>40%</strong></div>
                <div class="donut-label"><span><div class="donut-dot" style="background:#10b981;display:inline-block"></div> مماسح</span><strong>24%</strong></div>
                <div class="donut-label"><span><div class="donut-dot" style="background:#f59e0b;display:inline-block"></div> مماسح بخاخ</span><strong>18%</strong></div>
                <div class="donut-label"><span><div class="donut-dot" style="background:#8b5cf6;display:inline-block"></div> أخرى</span><strong>18%</strong></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="table-card">
          <div class="table-header">
            <div class="card-title">🛒 أحدث الطلبيات</div>
            <button class="btn-outline" onclick="showPage('orders',null)">عرض الكل</button>
          </div>
          <div style="overflow-x:auto">
            <table>
              <thead><tr>
                <th>رقم الطلبية</th><th>العميل</th><th>المنتجات</th><th>الإجمالي</th><th>الحالة</th><th>التاريخ</th>
              </tr></thead>
              <tbody id="recent-orders-body"></tbody>
            </table>
          </div>
        </div>

        <!-- Top Products -->
        <div class="card-title" style="margin-bottom:14px">🔥 أكثر المنتجات مبيعاً</div>
        <div id="top-products-grid" class="prod-admin-grid"></div>
      </div>

      <!-- ══ PRODUCTS ══ -->
      <div class="page" id="page-products">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المتجر <span>›</span> المنتجات</div>
            <h1 class="page-title">إدارة المنتجات</h1>
            <p class="page-sub" id="products-subtitle">جميع منتجات المتجر</p>
          </div>
          <button class="btn-add" onclick="openModal()">+ إضافة منتج</button>
        </div>
        <div class="toolbar">
          <input type="text" class="search-field" placeholder="🔍 بحث بالاسم أو التصنيف..." id="prod-search" oninput="filterProducts()"/>
          <select class="select-field" id="cat-filter" onchange="filterProducts()">
            <option value="">كل التصنيفات</option>
            <option>عروض خاصة</option><option>مماسح مسطحة</option><option>مماسح مايكروفايبر</option>
            <option>أحذية رياضية</option><option>حقائب فاخرة</option><option>مماسح بخاخ منزلي</option>
            <option>أجهزة مطبخ</option><option>مجوهرات وساعات</option><option>عطور رجالية</option>
          </select>
          <select class="select-field" id="badge-filter" onchange="filterProducts()">
            <option value="">كل الشارات</option>
            <option value="sale">تخفيض</option><option value="new">جديد</option>
            <option value="hot">رائج</option><option value="best">مميز</option>
          </select>
          <button class="btn-outline" onclick="exportProducts()">📥 تصدير CSV</button>
        </div>
        <div class="prod-admin-grid" id="products-grid"></div>
      </div>

      <!-- ══ INVENTORY PAGE ══ -->
      <div class="page" id="page-inventory">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المتجر <span>›</span> المخزون</div>
            <h1 class="page-title">إدارة المخزون</h1>
            <p class="page-sub">مراقبة كميات المنتجات وتحديثها بسرعة</p>
          </div>
          <div class="header-actions">
            <button class="btn-add" onclick="saveInventory()">💾 حفظ المخزون</button>
          </div>
        </div>
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; margin-top:20px; overflow-x:auto;">
          <table class="admin-table">
            <thead>
              <tr>
                <th>صورة</th>
                <th>المنتج</th>
                <th>التصنيف</th>
                <th>الكمية المتوفرة</th>
              </tr>
            </thead>
            <tbody id="inventory-body">
              <!-- Inventory rows -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- ══ getAdminOrders() ══ -->
      <div class="page" id="page-orders">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المتجر <span>›</span> الطلبيات</div>
            <h1 class="page-title">إدارة الطلبيات</h1>
            <p class="page-sub">تتبع وإدارة جميع طلبيات العملاء</p>
          </div>
          <button class="btn-outline">📥 تصدير الطلبيات</button>
        </div>
        <div class="toolbar">
          <input type="text" class="search-field" placeholder="🔍 بحث برقم الطلبية أو اسم العميل..."/>
          <select class="select-field" id="order-status-filter" onchange="filterOrders(this.value)">
            <option value="">كل الحالات</option>
            <option value="pending">قيد الانتظار</option>
            <option value="shipped">تم الشحن</option>
            <option value="delivered">تم التسليم</option>
            <option value="cancelled">ملغي</option>
          </select>
        </div>
        <div class="table-card">
          <div style="overflow-x:auto">
            <table>
              <thead><tr>
                <th>رقم الطلبية</th><th>العميل (الاسم ورقم الهاتف)</th><th>العنوان والتوصيل</th><th>المنتجات</th><th>الإجمالي</th><th>الحالة</th><th>التاريخ</th><th>إجراءات</th>
              </tr></thead>
              <tbody id="all-orders-body"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ══ CUSTOMERS ══ -->
      <div class="page" id="page-customers">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المتجر <span>›</span> العملاء</div>
            <h1 class="page-title">إدارة الأعضاء والعملاء</h1>
            <p class="page-sub" id="customers-page-sub">يتم التحميل...</p>
          </div>
        </div>
        
        <div class="tabs-admin" style="display:flex; gap:12px; margin-bottom:20px;">
            <button class="btn-add" id="tab-requests" onclick="switchCustomerTab('requests')" style="background:var(--p); border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:bold; color:#fff;">طلبات العضوية (قيد الانتظار)</button>
            <button class="btn-add" id="tab-active" onclick="switchCustomerTab('active')" style="background:transparent; border:1px solid var(--border); padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:bold; color:var(--text);">العملاء (المشترين)</button>
        </div>

        <div class="table-card" id="view-requests">
          <div style="overflow-x:auto">
            <table>
              <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>الهاتف</th><th>تاريخ التسجيل</th><th>إجراء</th></tr></thead>
              <tbody id="customers-requests-body"></tbody>
            </table>
          </div>
        </div>

        <div class="table-card" id="view-active" style="display:none;">
          <div style="overflow-x:auto">
            <table>
              <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>المدينة</th><th>الطلبيات</th><th>الإنفاق الكلي</th><th>أول طلبية</th></tr></thead>
              <tbody id="customers-body"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ══ MEDIA ══ -->
      <div class="page" id="page-media">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> مكتبة الصور</div>
            <h1 class="page-title">مكتبة الصور</h1>
            <p class="page-sub">رفع وإدارة صور المنتجات والبانرات</p>
          </div>
          <button class="btn-add" onclick="document.getElementById('bulk-upload').click()">📷 رفع صور</button>
          <input type="file" id="bulk-upload" accept="image/*" multiple style="display:none" onchange="handleBulkUpload(this)"/>
        </div>
        <div class="media-grid" id="media-grid"></div>
      </div>

      <!-- ══ HERO ══ -->
      <div class="page" id="page-hero">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> السلايدر</div>
            <h1 class="page-title">السلايدر الرئيسي</h1>
            <p class="page-sub">تعديل صور وعناوين الصفحة الرئيسية</p>
          </div>
          <div><button class="btn-outline" onclick="adminSliders.addSlide()" style="background:transparent; margin-left:10px;">+ شريحة جديدة</button><button class="btn-add" onclick="adminSliders.save()">💾 حفظ السلايدر</button></div>
        </div>
        <div id="hero-slides-list" style="display:flex;flex-direction:column;gap:16px"></div>
      </div>

      <!-- ══ CATEGORIES ══ -->
      <div class="page" id="page-categories">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> القائمة العلوية والتصنيفات</div>
            <h1 class="page-title">إدارة القائمة العلوية</h1>
            <p class="page-sub">إضافة وتعديل روابط القائمة العلوية والقوائم المنسدلة للزوار</p>
          </div>
          <div>
            <button class="btn-add" onclick="adminNav.addMainItem()" style="background:var(--blue)">+ رابط رئيسي</button>
            <button class="btn-add" onclick="adminNav.saveNavToServer()">💾 حفظ القائمة بالنظام</button>
          </div>
        </div>
        
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:20px;">
            <div id="admin-nav-builder" style="display:flex; flex-direction:column; gap:15px;">
                <!-- Nav items will be rendered here -->
                <div style="text-align:center; padding:40px; color:var(--text3)">جاري تحميل القائمة...</div>
            </div>
        </div>
      </div>

      <!-- ══ BANNERS MANAGER ══ -->
      <div class="page" id="page-banners">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> إدارة البنرات</div>
            <h1 class="page-title">إدارة البنرات (الصفحة الرئيسية)</h1>
            <p class="page-sub">تعديل الصور، النصوص، الألوان والروابط للبنرات الأربعة</p>
          </div>
          <div>
            <button class="btn-add" onclick="adminBanners.save()">💾 حفظ البنرات</button>
          </div>
        </div>
        
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:20px;">
            <div id="admin-banners-builder" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <!-- Banners will be rendered here -->
                <div style="grid-column:1/-1;text-align:center; padding:40px; color:var(--text3)">جاري تحميل البنرات...</div>
            </div>
        </div>
      </div>

      <!-- ══ ICONS MANAGER ══ -->
      <div class="page" id="page-icons">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> أيقونات الرئيسية</div>
            <h1 class="page-title">إدارة تصنيفات الأيقونات</h1>
            <p class="page-sub">تحديد المنتجات التي تظهر عند الضغط على كل أيقونة في الصفحة الرئيسية</p>
          </div>
          <button class="btn-add" onclick="adminIcons.saveIconsToServer()" style="background:var(--blue)">💾 حفظ التغييرات</button>
        </div>
        
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:20px;">
            <div id="admin-icons-builder" style="display:flex; flex-direction:column; gap:15px;">
                <div style="text-align:center; padding:40px; color:var(--text3)">جاري تحميل الأيقونات...</div>
            </div>
        </div>
      </div>

      <!-- ══ ANALYTICS ══ -->
      <div class="page" id="page-analytics">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">الرئيسية <span>›</span> التحليلات</div>
            <h1 class="page-title">التحليلات المتقدمة</h1>
            <p class="page-sub">تقارير المبيعات والأداء</p>
          </div>
        </div>
        <div class="stats-grid">
          <div class="stat-card blue"><div class="stat-icon">📊</div><div class="stat-val" id="analytics-month-sales">₪0</div><div class="stat-label">مبيعات هذا الشهر</div><div class="stat-change up" id="analytics-month-sales-change"></div></div>
          <div class="stat-card green"><div class="stat-icon">🔄</div><div class="stat-val" id="analytics-repeat-rate">0%</div><div class="stat-label">معدل تكرار الشراء</div><div class="stat-change up" id="analytics-repeat-rate-change"></div></div>
          <div class="stat-card yellow"><div class="stat-icon">🛒</div><div class="stat-val" id="analytics-avg-order">₪0</div><div class="stat-label">متوسط قيمة الطلبية</div><div class="stat-change" id="analytics-avg-order-change"></div></div>
          <div class="stat-card red"><div class="stat-icon">↩️</div><div class="stat-val" id="analytics-return-rate">0%</div><div class="stat-label">معدل الإرجاع</div><div class="stat-change" id="analytics-return-rate-change"></div></div>
        </div>
        <div class="chart-card" style="margin-top:18px">
          <div class="card-title">📈 المبيعات – آخر 30 يوم</div>
          <div class="card-sub">الإيرادات اليومية التفصيلية</div>
          <div class="mini-chart" id="big-chart" style="height:180px;gap:4px"></div>
        </div>
      </div>

      <!-- ══ SETTINGS ══ -->
      <div class="page" id="page-settings">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">الإعدادات</div>
            <h1 class="page-title">إعدادات المتجر</h1>
            <p class="page-sub">تخصيص الهوية البصرية والإعدادات العامة</p>
          </div>
          <button class="btn-add" onclick="showToast('✅ تم حفظ الإعدادات!')">💾 حفظ التغييرات</button>
        </div>
        <div class="settings-grid">
          <div class="setting-card">
            <h3>⚙️ معلومات المتجر</h3>
            <div class="field" style="margin-bottom:12px"><label>اسم المتجر</label><input type="text" value="إستوردلي"/></div>
            <div class="field" style="margin-bottom:12px"><label>الوصف المختصر</label><textarea style="min-height:60px">متجر إلكتروني متنوع بأفضل المنتجات</textarea></div>
            <div class="field"><label>رقم الواتساب</label><input type="tel" value="+972 59 123 4567" dir="ltr"/></div>
          </div>
          <div class="setting-card">
            <h3>🎨 الألوان والمظهر</h3>
            <div class="setting-row">
              <div class="setting-row-info"><strong>اللون الأساسي</strong><small>زر الإضافة للسلة وروابط التنقل</small></div>
              <div class="color-picker-row">
                <div class="color-swatch active" style="background:#1d4ed8" onclick="pickColor(this,'#1d4ed8')"></div>
                <div class="color-swatch" style="background:#dc2626" onclick="pickColor(this,'#dc2626')"></div>
                <div class="color-swatch" style="background:#7c3aed" onclick="pickColor(this,'#7c3aed')"></div>
                <div class="color-swatch" style="background:#16a34a" onclick="pickColor(this,'#16a34a')"></div>
                <div class="color-swatch" style="background:#d97706" onclick="pickColor(this,'#d97706')"></div>
              </div>
            </div>
            <div class="setting-row">
              <div class="setting-row-info"><strong>نمط العرض</strong><small>طريقة عرض المنتجات</small></div>
              <select class="select-field" style="font-size:12px;padding:6px 10px"><option>شبكة (Grid)</option><option>قائمة (List)</option></select>
            </div>
            <div class="setting-row">
              <div class="setting-row-info"><strong>وضع الظلام</strong><small>استخدام الوضع الداكن افتراضياً</small></div>
              <label class="toggle"><input type="checkbox"/><div class="toggle-slider"></div></label>
            </div>
          </div>
          
          <div class="setting-card">
            <h3>🚚 مناطق وأسعار التوصيل</h3>
            <p style="font-size:12px;color:var(--text3);margin-bottom:12px">أضف المناطق التي توصل إليها مع تحديد تكلفة التوصيل لكل منطقة، ليختار منها الزبون عند الطلب.</p>
            <div id="delivery-zones-container" style="display:flex;flex-direction:column;gap:10px;"></div>
            <button class="btn" style="margin-top:10px;border:1px dashed var(--border);width:100%;background:transparent" onclick="addDeliveryZone()">➕ إضافة منطقة جديدة</button>
            <button class="btn btn-primary" style="margin-top:10px;width:100%" onclick="saveDeliveryZonesUI()">💾 حفظ مناطق التوصيل</button>
          </div>

          <div class="setting-card">
            <h3>🚚 الشحن والدفع</h3>
            <div class="setting-row">
              <div class="setting-row-info"><strong>الشحن المجاني</strong><small>للطلبات التي تتجاوز الحد الأدنى</small></div>
              <label class="toggle"><input type="checkbox" checked/><div class="toggle-slider"></div></label>
            </div>
            <div class="field" style="margin-top:12px"><label>الحد الأدنى للشحن المجاني (₪)</label><input type="number" value="200"/></div>
            <div class="field" style="margin-top:12px"><label>تكلفة الشحن الافتراضية (₪)</label><input type="number" value="15"/></div>
          </div>
          <div class="setting-card">
            <h3>🔔 الإشعارات</h3>
            <div class="setting-row"><div class="setting-row-info"><strong>إشعار طلبية جديدة</strong><small>إشعار فوري عند كل طلبية</small></div><label class="toggle"><input type="checkbox" checked/><div class="toggle-slider"></div></label></div>
            <div class="setting-row"><div class="setting-row-info"><strong>نفاد المخزون</strong><small>تنبيه عند أقل من 5 قطع</small></div><label class="toggle"><input type="checkbox" checked/><div class="toggle-slider"></div></label></div>
            <div class="setting-row"><div class="setting-row-info"><strong>تقييم جديد</strong><small>إشعار عند كل تقييم</small></div><label class="toggle"><input type="checkbox"/><div class="toggle-slider"></div></label></div>
          </div>
          <div class="setting-card full">
            <h3>💳 طرق الدفع المقبولة</h3>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text2)"><input type="checkbox" checked style="accent-color:var(--p)"> VISA / MasterCard</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text2)"><input type="checkbox" checked style="accent-color:var(--p)"> Apple Pay</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text2)"><input type="checkbox" checked style="accent-color:var(--p)"> PayPal</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text2)"><input type="checkbox" style="accent-color:var(--p)"> Crypto</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text2)"><input type="checkbox" checked style="accent-color:var(--p)"> الدفع عند الاستلام</label>
            </div>
          </div>
        </div>
      </div>

      <!-- ══ NOTIFICATIONS ══ -->
      <div class="page" id="page-notifications">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">الإعدادات <span>›</span> الإشعارات</div>
            <h1 class="page-title">الإشعارات</h1>
          </div>
          <button class="btn-outline" onclick="document.getElementById('notif-badge').textContent='0';showToast('✅ تم تحديد الكل كمقروء')">✓ تحديد الكل كمقروء</button>
        </div>
        <div class="notification-list" id="notif-list"></div>
      </div>

    </div><!-- end content -->
  </div><!-- end main-area -->
</div><!-- end admin-layout -->

<script>
function toggleAdminSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('admin-overlay').classList.toggle('open');
}


let adminProducts = (typeof Store !== 'undefined') ? Store.getProducts() : [];
window.LIVE_ORDERS = [];
function getAdminOrders() { return window.LIVE_ORDERS; }

async function fetchLiveOrders() {
  try {
    const res = await fetch('api/get_orders.php');
    const data = await res.json();
    if (data.success && data.orders) {
      window.LIVE_ORDERS = data.orders.map(o => ({
        id: o.id,
        userName: o.customer_name,
        customer: o.customer_name,
        phone: o.customer_phone,
        address: o.customer_address,
        zone: o.shipping_zone,
        items: typeof o.items_json === 'string' ? JSON.parse(o.items_json) : o.items_json,
        subtotal: parseFloat(o.subtotal),
        shipping: parseFloat(o.shipping_cost),
        total: parseFloat(o.total_price),
        status: o.status,
        date: o.created_at,
        notes: o.notes
      }));
    } else {
        window.LIVE_ORDERS = (typeof Store !== 'undefined') ? Store.getOrders() : [];
    }
  } catch(e) {
    console.error("Error fetching orders:", e);
    window.LIVE_ORDERS = (typeof Store !== 'undefined') ? Store.getOrders() : [];
  }
  
  // Update sidebar count
  const badge = document.querySelector('.sb-item[onclick*="orders"] .sb-badge');
  if (badge) badge.textContent = window.LIVE_ORDERS.length;
  
  updateStats();
  if (document.getElementById('page-orders').classList.contains('active')) {
    renderOrders(getAdminOrders());
  }
}

function getAdminUsers() { return (typeof Store !== 'undefined') ? Store.getUsers() : []; }

const NOTIFS = [
  {icon:'🛒',color:'rgba(59,130,246,.15)', title:'مرحباً في لوحة الإدارة', sub:'البيانات متزامنة مع الموقع مباشرةً'},
  {icon:'📦',color:'rgba(16,185,129,.15)', title:'206 منتج في المتجر', sub:'جميع المنتجات محملة من قاعدة البيانات'},
];


const CAT_DATA = [
  {name:'مماسح مسطحة', icon:'🧹', count:0, sales:0},
  {name:'مماسح قطنية', icon:'🪣', count:0, sales:0},
  {name:'مماسح مايكروفايبر', icon:'✨', count:0, sales:0},
  {name:'قطع غيار', icon:'🔧', count:0, sales:0},
];

let editingId = null;

async function saveAdminProducts() {
  if (typeof Store !== 'undefined') Store.saveProducts(adminProducts);
  try {
      const res = await fetch('api/save_products.php?_t=' + Date.now(), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(adminProducts)
      });
      const data = await res.json();
      if (!data.success) {
          console.error("Failed to save to server:", data.message);
          alert("خطأ في الحفظ على السيرفر: " + data.message);
      } else {
          console.log("Saved to server successfully.");
      }
  } catch (err) {
      console.error("API Error saving products:", err);
      alert("لم يتم الحفظ! يرجى التأكد من رفع ملف sw.js الجديد أو تحديث الصفحة الإجباري. التفاصيل: " + err);
  }
}

function showPage(id, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.sb-item').forEach(b => b.classList.remove('active'));
  const pg = document.getElementById('page-'+id);
  if (pg) pg.classList.add('active');
  if (el) el.classList.add('active');
  else {
    document.querySelectorAll('.sb-item').forEach(b => {
      if (b.getAttribute('onclick') && b.getAttribute('onclick').includes(`'${id}'`)) b.classList.add('active');
    });
  }
  // Lazy render
  if (id === 'products') renderProducts();
  if (id === 'inventory') renderInventory();
  if (id === 'orders')   renderOrders(getAdminOrders());
  if (id === 'customers') renderCustomers();
  if (id === 'media')    renderMedia();
  
  if (id === 'hero')  adminSliders.load();
  if (id === 'banners')  adminBanners.load();

  if (id === 'categories') renderCategories();
  if (id === 'notifications') renderNotifs();
  if (id === 'analytics') renderBigChart();
}

function updateStats() {
  // Reload from Store
  if (typeof Store !== 'undefined') adminProducts = Store.getProducts();
  const storeOrders = getAdminOrders();
  
  let rev = 0;
  let totalCost = 0;
  
  storeOrders.filter(o=>o.status!=='cancelled').forEach(o => {
    rev += (o.total || 0);
    if(o.items) {
      o.items.forEach(item => {
        let cost = item.costPrice || 0;
        if (!cost) {
          const product = adminProducts.find(p => String(p.id) === String(item.id));
          if (product && product.costPrice) {
            cost = Number(product.costPrice);
          }
        }
        totalCost += cost * (item.quantity || 1);
      });
    }
  });

  const profit = rev - totalCost;
  
  document.getElementById('stat-revenue').textContent = '₪' + rev.toLocaleString('ar-SA');
  const statProfitEl = document.getElementById('stat-profit');
  if(statProfitEl) statProfitEl.textContent = '₪' + Math.max(0, profit).toLocaleString('ar-SA');

  document.getElementById('stat-orders').textContent = storeOrders.length;
  document.getElementById('stat-products').textContent = adminProducts.length;
  document.getElementById('stat-pending') && (document.getElementById('stat-pending').textContent = storeOrders.filter(o=>o.status==='pending').length);
  const pcEl = document.getElementById('products-count');
  if(pcEl) pcEl.textContent = adminProducts.length;
    const psEl = document.getElementById('products-subtitle');
  if(psEl) psEl.textContent = `${adminProducts.length} منتج إجمالاً، ${adminProducts.filter(p=>p.active!==false).length} منشور`;
  const usersCount = (typeof Store !== 'undefined') ? Store.getUsers().length : 0;
  const suEl = document.getElementById('stat-users');
  if(suEl) suEl.textContent = usersCount;
}

function renderChart() {
  const vals = [4200,5800,3900,7200,6100,8500,9200];
  const days = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
  const max = Math.max(...vals);
  const el = document.getElementById('revenue-chart');
  if (!el) return;
  el.innerHTML = vals.map((v,i) => `
    <div class="bar" style="height:${(v/max)*100}%" data-val="₪${v.toLocaleString()}" title="${days[i]}: ₪${v.toLocaleString()}"></div>
  `).join('');
}
function renderBigChart() {
  const el = document.getElementById('big-chart');
  if (!el) return;
  const orders = getAdminOrders();
  
  // Calculate analytics data
  const now = new Date();
  const thirtyDaysAgo = new Date(now.getTime() - 30*24*60*60*1000);
  
  let salesThisMonth = 0;
  let salesLastMonth = 0;
  let totalOrderValue = 0;
  let returnedOrders = 0;
  let completedOrdersCount = 0;
  
  const dailySales = new Array(30).fill(0);
  const customersMap = new Map();
  
  orders.forEach(o => {
    const oDate = new Date(o.date);
    if (o.status === 'cancelled') returnedOrders++;
    if (o.status !== 'cancelled') {
        completedOrdersCount++;
        totalOrderValue += (o.total || 0);
        
        // Month sales
        if (oDate.getMonth() === now.getMonth() && oDate.getFullYear() === now.getFullYear()) {
            salesThisMonth += (o.total || 0);
        } else if (oDate.getMonth() === (now.getMonth()===0?11:now.getMonth()-1) && oDate.getFullYear() === (now.getMonth()===0?now.getFullYear()-1:now.getFullYear())) {
            salesLastMonth += (o.total || 0);
        }
        
        // 30 days chart
        if (oDate >= thirtyDaysAgo) {
            const dayIndex = Math.floor((now.getTime() - oDate.getTime()) / (24*60*60*1000));
            if (dayIndex >= 0 && dayIndex < 30) {
                dailySales[29 - dayIndex] += (o.total || 0);
            }
        }
        
        // Repeat customers
        const phone = o.phone || 'بدون';
        const key = phone !== 'بدون' ? phone : (o.userName || o.customer || 'غير معروف');
        customersMap.set(key, (customersMap.get(key) || 0) + 1);
    }
  });
  
  // Update UI Stats
  let repeatCustomers = 0;
  customersMap.forEach(count => { if (count > 1) repeatCustomers++; });
  const repeatRate = customersMap.size ? Math.round((repeatCustomers / customersMap.size) * 100) : 0;
  const avgOrder = completedOrdersCount ? Math.round(totalOrderValue / completedOrdersCount) : 0;
  const returnRate = orders.length ? (returnedOrders / orders.length * 100).toFixed(1) : 0;
  
  const salesChange = salesLastMonth ? Math.round(((salesThisMonth - salesLastMonth) / salesLastMonth) * 100) : (salesThisMonth > 0 ? 100 : 0);
  
  const monthSalesEl = document.getElementById('analytics-month-sales');
  if (monthSalesEl) {
      monthSalesEl.textContent = '₪' + salesThisMonth.toLocaleString();
      document.getElementById('analytics-repeat-rate').textContent = repeatRate + '%';
      document.getElementById('analytics-avg-order').textContent = '₪' + avgOrder.toLocaleString();
      document.getElementById('analytics-return-rate').textContent = returnRate + '%';
      
      document.getElementById('analytics-month-sales-change').textContent = salesChange >= 0 ? '↑ ' + salesChange + '% عن الشهر السابق' : '↓ ' + Math.abs(salesChange) + '% عن الشهر السابق';
      document.getElementById('analytics-month-sales-change').className = 'stat-change ' + (salesChange >= 0 ? 'up' : 'down');
  }

  // Draw chart
  const max = Math.max(...dailySales, 1);
  el.innerHTML = dailySales.map((v,i)=>`<div class="bar" style="height:${(v/max)*100}%;background:${i===dailySales.length-1?'linear-gradient(to top,#10b981,#34d399)':'linear-gradient(to top,rgba(59,130,246,.4),rgba(59,130,246,.8))'}" data-val="₪${v.toLocaleString()}"></div>`).join('');
  el.dataset.rendered = '1';
}

function renderRecentOrders() {
  const statusMap = {pending:'قيد الانتظار',processing:'جاري المعالجة',shipped:'تم الشحن',delivered:'تم التسليم',cancelled:'ملغي'};
  const body = document.getElementById('recent-orders-body');
  if (!body) return;
  const orders = getAdminOrders();
  if (!orders.length) { body.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text3);padding:32px">لا توجد طلبيات بعد</td></tr>`; return; }
  body.innerHTML = orders.slice(0,5).map(o => {
    const name = o.userName || o.customer || '-';
    const itemsCount = Array.isArray(o.items) ? o.items.length : (o.items || 0);
    
    // شارة جديد للطلبات في آخر 48 ساعة
    const isNew = o.status === 'pending' && (Date.now() - new Date(o.date).getTime() < 48*60*60*1000);
    const newBadge = isNew ? '<span class="badge-new">جديد 🌟</span>' : '';

    return `
    <tr style="${isNew ? 'background:rgba(16, 185, 129, 0.05)' : ''}">
      <td><span class="order-id">${o.id}</span> ${newBadge}</td>
      <td><div class="order-customer"><div class="oc-av">${name[0]||'?'}</div>${name}</div></td>
      <td>${itemsCount} منتج</td>
      <td style="font-weight:800;color:#fff">₪${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge status-${o.status}">${statusMap[o.status]||o.status}</span></td>
      <td style="color:var(--text3)">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span>${new Date(o.date).toLocaleDateString('ar-SA')}</span>
          <button class="view-btn" onclick="viewOrder('${o.id}')" title="معاينة الطلبية">👁️</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function renderOrders(list) {
  const statusMap = {pending:'قيد الانتظار',processing:'جاري المعالجة',shipped:'تم الشحن',delivered:'تم التسليم',cancelled:'ملغي'};
  const body = document.getElementById('all-orders-body');
  if (!body) return;
  if (!list || !list.length) { body.innerHTML = `<tr><td colspan="7" style="text-align:center;color:var(--text3);padding:40px">لا توجد طلبيات</td></tr>`; return; }
  body.innerHTML = list.map(o => {
    const name = o.userName || o.customer || '-';
    const itemsCount = Array.isArray(o.items) ? o.items.length : (o.items || 0);
    let itemsHtml = '';
    if (Array.isArray(o.items)) {
        itemsHtml = o.items.map(i => `<div style="font-size:11px;color:var(--text3);margin-bottom:2px;">• ${i.name} (x${i.quantity||1})</div>`).join('');
    } else {
        itemsHtml = `${itemsCount} منتج`;
    }
    
    // شارة جديد للطلبات في آخر 48 ساعة
    const isNew = o.status === 'pending' && (Date.now() - new Date(o.date).getTime() < 48*60*60*1000);
    const newBadge = isNew ? '<span class="badge-new">جديد 🌟</span>' : '';

    return `
    <tr style="${isNew ? 'background:rgba(16, 185, 129, 0.05)' : ''}">
      <td><span class="order-id">${o.id}</span><br>${newBadge}</td>
      <td>
        <div class="order-customer">
          <div class="oc-av">${name[0]||'?'}</div>
          <div>
            <div style="font-weight:700">${name}</div>
            <div style="font-size:11px;color:var(--text3);margin-top:2px">${o.phone || '-'}</div>
          </div>
        </div>
      </td>
      <td>
        <div style="font-size:12px;max-width:180px;white-space:normal;">
          <strong style="color:var(--p)">${o.zone || 'لم يحدد'}</strong><br/>
          <span style="color:var(--text2)">${o.address || '-'}</span>
        </div>
      </td>
      <td>
        <div style="max-height:70px;overflow-y:auto;padding-right:4px;">
            ${itemsHtml}
        </div>
      </td>
      <td style="font-weight:800;color:#fff">₪${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge status-${o.status}">${statusMap[o.status]||o.status}</span></td>
      <td style="color:var(--text3);font-size:12px">${new Date(o.date).toLocaleDateString('ar-SA')}</td>
      <td>
        <div style="display:flex;align-items:center;gap:4px;">
          <select onchange="changeOrderStatus('${o.id}',this.value)" style="background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:8px;padding:4px 8px;font-family:inherit;font-size:12px">
            ${['pending','processing','shipped','delivered','cancelled'].map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${statusMap[s]||s}</option>`).join('')}
          </select>
          <button class="view-btn" onclick="viewOrder('${o.id}')" title="معاينة الطلبية">👁️</button>
          <button class="print-btn" onclick="printOrder('${o.id}')" title="طباعة الفاتورة">🖨️</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function filterOrders(status) {
  const orders = getAdminOrders();
  renderOrders(status ? orders.filter(o=>o.status===status) : orders);
}

async function changeOrderStatus(id, newStatus) {
  try {
    const formData = new FormData();
    formData.append('order_id', id);
    formData.append('status', newStatus);
    const res = await fetch('api/update_order_status.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('✅ تم تحديث حالة الطلبية');
      await fetchLiveOrders(); // refresh UI
    } else {
      showToast('❌ خطأ: ' + data.message);
    }
  } catch (e) {
    showToast('❌ فشل الاتصال بالخادم');
  }
}


let allRegisteredUsers = [];

function switchCustomerTab(tab) {
    document.getElementById('tab-requests').style.background = tab === 'requests' ? 'var(--p)' : 'transparent';
    document.getElementById('tab-requests').style.border = tab === 'requests' ? 'none' : '1px solid var(--border)';
    
    document.getElementById('tab-active').style.background = tab === 'active' ? 'var(--p)' : 'transparent';
    document.getElementById('tab-active').style.border = tab === 'active' ? 'none' : '1px solid var(--border)';
    
    document.getElementById('view-requests').style.display = tab === 'requests' ? 'block' : 'none';
    document.getElementById('view-active').style.display = tab === 'active' ? 'block' : 'none';
}

async function fetchMembershipRequests() {
    try {
        const res = await fetch('api/get_users.php');
        const data = await res.json();
        if(data.success) {
            allRegisteredUsers = data.users;
            renderMembershipRequests();
        }
    } catch(err) {
        console.error('Error fetching users', err);
    }
}

function renderMembershipRequests() {
    const body = document.getElementById('customers-requests-body');
    if (!body) return;
    
    const pendingUsers = allRegisteredUsers.filter(u => u.status === 'pending' && u.role === 'customer');
    document.getElementById('tab-requests').textContent = `طلبات العضوية (${pendingUsers.length})`;
    
    const badge = document.getElementById('pending-users-badge');
    if (badge) {
        if (pendingUsers.length > 0) {
            badge.style.display = 'block';
            badge.textContent = pendingUsers.length;
        } else {
            badge.style.display = 'none';
        }
    }
    
    if (!pendingUsers.length) {
        body.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--text3);padding:40px">لا توجد طلبات عضوية معلقة</td></tr>`;
        return;
    }
    
    body.innerHTML = pendingUsers.map(u => `
    <tr>
      <td>
        <div class="order-customer">
            <div class="oc-av" style="background:linear-gradient(135deg,#f59e0b,#d97706)">${u.name[0]||'?'}</div>
            <div>
                <div style="font-weight:700">${u.name}</div>
            </div>
        </div>
      </td>
      <td style="color:var(--text3)">${u.email}</td>
      <td style="color:var(--text3)">${u.phone || '-'}</td>
      <td style="color:var(--text3)">${new Date(u.created_at).toLocaleDateString('ar-SA')}</td>
      <td>
        <button class="print-btn" style="background:#10b981; color:#fff; border:none;" onclick="approveUser(${u.id})">✅ قبول وتفعيل</button>
      </td>
    </tr>
    `).join('');
}

async function approveUser(id) {
    if(!confirm('هل أنت متأكد من تفعيل هذا الحساب؟ سيتمكن من رؤية الأسعار والشراء.')) return;
    try {
        const res = await fetch('api/approve_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({user_id: id, status: 'active'})
        });
        const data = await res.json();
        if(data.success) {
            showToast('تم تفعيل الحساب بنجاح', 'success');
            fetchMembershipRequests();
        } else {
            showToast(data.message, 'error');
        }
    } catch(err) {
        showToast('حدث خطأ', 'error');
    }
}

function renderCustomers() {
  fetchMembershipRequests(); // Trigger fetch for requests when rendering customers
  
  const body = document.getElementById('customers-body');
  if (!body) return;
  const orders = getAdminOrders();
  
  // Extract unique customers based on phone number or name
  const customersMap = new Map();
  orders.forEach(o => {
      const name = o.userName || o.customer || 'غير معروف';
      const phone = o.phone || 'بدون رقم';
      const key = phone !== 'بدون رقم' ? phone : name;
      
      if (!customersMap.has(key)) {
          customersMap.set(key, {
              name: name,
              phone: phone,
              email: o.userEmail || '-',
              zone: o.zone || '-',
              orders: [],
              spent: 0,
              firstOrderDate: o.date
          });
      }
      const c = customersMap.get(key);
      c.orders.push(o);
      c.spent += (o.total || 0);
      if (new Date(o.date) < new Date(c.firstOrderDate)) {
          c.firstOrderDate = o.date;
      }
  });
  
  const users = Array.from(customersMap.values());
  
  if (!users.length) { body.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text3);padding:40px">لا يوجد عملاء حتى الآن</td></tr>`; return; }
  
  body.innerHTML = users.sort((a,b) => b.spent - a.spent).map(c => {
    return `
    <tr>
      <td>
        <div class="order-customer">
            <div class="oc-av" style="background:linear-gradient(135deg,#7c3aed,#3b82f6)">${c.name[0]||'?'}</div>
            <div>
                <div style="font-weight:700">${c.name}</div>
                <div style="font-size:11px;color:var(--text3);margin-top:2px">${c.phone}</div>
            </div>
        </div>
      </td>
      <td style="color:var(--text3)">${c.email}</td>
      <td><span style="background:var(--bg3);padding:4px 8px;border-radius:12px;font-size:12px;">${c.zone}</span></td>
      <td style="font-weight:700;color:var(--p)">${c.orders.length} طلبية</td>
      <td style="font-weight:800;color:#fff">₪${c.spent.toLocaleString()}</td>
      <td style="color:var(--text3)">${new Date(c.firstOrderDate).toLocaleDateString('ar-SA')}</td>
    </tr>`;
  }).join('');
  
  // Update customers count
  const el = document.querySelector('#customers-page-sub');
  if (el) el.textContent = `${users.length} عميل قاموا بالشراء`;
}


function renderInventory() {
  const body = document.getElementById('inventory-body');
  if (!body) return;
  
  if (!adminProducts.length) {
      body.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--text3);padding:40px">لا توجد منتجات</td></tr>`;
      return;
  }
  
  body.innerHTML = adminProducts.map(p => `
    <tr>
      <td>
        <img src="${p.img}" alt="" style="width:40px;height:40px;border-radius:6px;object-fit:cover;border:1px solid var(--border)">
      </td>
      <td style="font-weight:600;">${p.name}</td>
      <td style="color:var(--text2);font-size:12px;">${p.cat}</td>
      <td>
        <input type="number" id="inv-stock-${p.id}" value="${p.stock !== undefined && p.stock !== null ? p.stock : ''}" placeholder="غير محدود" style="width:100px;padding:8px;border:1px solid var(--border);border-radius:6px;background:var(--bg);color:var(--text);">
      </td>
    </tr>
  `).join('');
}

async function saveInventory() {
  adminProducts.forEach(p => {
      const input = document.getElementById(`inv-stock-${p.id}`);
      if (input) {
          const val = input.value;
          p.stock = val === '' ? null : parseInt(val);
      }
  });
  
  await saveAdminProducts();
  showToast('✅ تم حفظ المخزون بنجاح');
  if (document.getElementById('page-products').classList.contains('active')) {
      renderProducts();
  }
}

function renderProducts(list) {
  list = list || adminProducts;
  const grid = document.getElementById('products-grid');
  if (!grid) return;
  if (!list.length) { grid.innerHTML='<div class="empty-state"><div class="es-icon">📦</div><p>لا توجد منتجات</p></div>'; return; }
  grid.innerHTML = list.map(p => {
    const disc = p.oldPrice ? Math.round((1-p.price/p.oldPrice)*100) : 0;
    const badgeNames = {sale:`-${disc}%`,new:'جديد',hot:'رائج',best:'مميز'};
    return `
      <div class="prod-admin-card">
        <div class="pac-img">
          <img src="${p.img}" alt="${p.name}" loading="lazy" onerror="this.src='https://via.placeholder.com/300x200?text=📦'"/>
          ${p.badge?`<div class="pac-badge badge-${p.badge}" style="padding:3px 10px;border-radius:8px;font-size:10px;font-weight:800;background:rgba(0,0,0,.5);color:#fff">${badgeNames[p.badge]||p.badge}</div>`:''}
          <div class="pac-overlay">
            <button class="pac-action" onclick="editProduct(${p.id})" title="تعديل">✏️</button>
            <button class="pac-action" onclick="window.open('product.html?id=${p.id}','_blank')" title="معاينة">👁️</button>
            <button class="pac-action del" onclick="deleteProduct(${p.id})" title="حذف">🗑️</button>
          </div>
        </div>
        <div class="pac-info">
          <div class="pac-cat">${p.cat}</div>
          <div class="pac-name">${p.name}</div>
          <div class="pac-price">
            <span class="pac-price-main">₪${p.price}</span>
            ${p.oldPrice?`<span class="pac-price-old">₪${p.oldPrice}</span>`:''}
          </div>
          <div style="font-size:11px;color:var(--text3);margin-bottom:8px">
            المربح: <strong style="color:var(--green)">₪${Math.max(0, p.price - (p.costPrice || 0))}</strong>
          </div>
          <div class="pac-stats">
            <span class="pac-stat">⭐ <strong>${p.stars}</strong></span>
            <span class="pac-stat">💬 <strong>${p.reviews}</strong></span>
            <span class="pac-stat" style="margin-right:auto">
              <label class="toggle" title="${p.active?'منشور':'مخفي'}">
                <input type="checkbox" ${p.active?'checked':''} onchange="toggleProduct(${p.id},this.checked)"/>
                <div class="toggle-slider"></div>
              </label>
            </span>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function filterProducts() {
  const q = document.getElementById('prod-search').value.toLowerCase();
  const cat = document.getElementById('cat-filter').value;
  const badge = document.getElementById('badge-filter').value;
  const filtered = adminProducts.filter(p =>
    (!q || p.name.toLowerCase().includes(q) || p.cat.toLowerCase().includes(q)) &&
    (!cat || p.cat === cat) &&
    (!badge || p.badge === badge)
  );
  renderProducts(filtered);
}

function globalSearch(q) {
  if (!q) return;
  const hits = adminProducts.filter(p => p.name.toLowerCase().includes(q.toLowerCase())).length;
  if (hits) showToast(`🔍 وجدت ${hits} نتيجة`);
}

function openModal(p) {
  editingId = p ? p.id : null;
  document.getElementById('modal-title').textContent = p ? '✏️ تعديل المنتج' : '➕ إضافة منتج جديد';
  document.getElementById('f-name').value = p?.name || '';
  document.getElementById('f-cat').value = p?.cat || '';
  
  // Find if product belongs to any icon
  let mappedIcon = '';
  if (p && adminIcons && adminIcons.mapping) {
      for (const [iconKey, prodIds] of Object.entries(adminIcons.mapping)) {
          if (prodIds.includes(String(p.id))) {
              mappedIcon = iconKey;
              break;
          }
      }
  }
  document.getElementById('f-icon-cat').value = mappedIcon;

  document.getElementById('f-badge').value = p?.badge || '';
  document.getElementById('f-price').value = p?.price || '';
  document.getElementById('f-cost-price').value = p?.costPrice || '';
  document.getElementById('f-old-price').value = p?.oldPrice || '';
  document.getElementById('f-stars').value = p?.stars || '';
  document.getElementById('f-reviews').value = p?.reviews || '';
  document.getElementById('f-stock').value = (p && p.stock !== undefined) ? p.stock : '';
  document.getElementById('f-tab').value = p?.tab || 'all';
  document.getElementById('f-img-url').value = p?.img || '';
  const prev = document.getElementById('img-preview-el');
  const ph = document.getElementById('upload-placeholder');
  if (p?.img) { prev.src=p.img; prev.style.display='block'; ph.style.display='none'; }
  else { prev.style.display='none'; ph.style.display='block'; }
  document.getElementById('product-modal').classList.add('open');
}
function closeModal() { document.getElementById('product-modal').classList.remove('open'); }

function editProduct(id) {
  const p = adminProducts.find(x=>x.id===id);
  if (p) { showPage('products',null); openModal(p); }
}

function saveProduct() {
  const name = document.getElementById('f-name').value.trim();
  const price = parseFloat(document.getElementById('f-price').value);
  const costPrice = parseFloat(document.getElementById('f-cost-price').value) || 0;
  const stockStr = document.getElementById('f-stock').value;
  const stock = stockStr === '' ? null : parseInt(stockStr);
  
  if (!name || isNaN(price)) { showToast('⚠️ الاسم والسعر مطلوبان!','warn'); return; }

  const product = {
    id: editingId || (Date.now()),
    name,
    cat: document.getElementById('f-cat').value,
    badge: document.getElementById('f-badge').value,
    price,
    costPrice,
    oldPrice: parseFloat(document.getElementById('f-old-price').value) || null,
    stars: parseFloat(document.getElementById('f-stars').value) || 4.5,
    reviews: parseInt(document.getElementById('f-reviews').value) || 0,
    stock: stock,
    tab: document.getElementById('f-tab').value || 'all',
    img: document.getElementById('f-img-url').value || document.getElementById('img-preview-el').src || 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80',
    active: true,
  };

  if (editingId) {
    const idx = adminProducts.findIndex(p=>p.id===editingId);
    if (idx !== -1) adminProducts[idx] = product;
    showToast('✅ تم تحديث المنتج بنجاح!');
  } else {
    adminProducts.unshift(product);
    showToast('✅ تم إضافة المنتج للمتجر!');
  }
  
  const iconCat = document.getElementById('f-icon-cat').value;
  if (iconCat && adminIcons.mapping) {
      if(!adminIcons.mapping[iconCat]) adminIcons.mapping[iconCat] = [];
      if(!adminIcons.mapping[iconCat].includes(String(product.id))) {
          adminIcons.mapping[iconCat].push(String(product.id));
          adminIcons.saveIconsToServer(); // Auto-save mapping
      }
  }

  saveAdminProducts();
  closeModal();
  renderProducts();
  updateStats();
}

function deleteProduct(id) {
  if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) return;
  adminProducts = adminProducts.filter(p=>p.id!==id);
  saveAdminProducts();
  renderProducts();
  updateStats();
  showToast('🗑️ تم حذف المنتج');
}

function toggleProduct(id, active) {
  const p = adminProducts.find(x=>x.id===id);
  if (p) { p.active=active; saveAdminProducts(); showToast(active?'✅ المنتج منشور':'⛔ المنتج مخفي'); }
}

function exportProducts() {
  const header = 'الاسم,التصنيف,السعر,السعر القديم,التقييم,التقييمات\n';
  const rows = adminProducts.map(p=>`"${p.name}","${p.cat}",${p.price},${p.oldPrice||''},${p.stars},${p.reviews}`).join('\n');
  const blob = new Blob(['\uFEFF'+header+rows], {type:'text/csv;charset=utf-8'});
  const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='products.csv'; a.click();
  showToast('📥 تم تصدير CSV');
}

function renderTopProducts() {
  const grid = document.getElementById('top-products-grid');
  if (!grid) return;
  const top = [...adminProducts].sort((a,b)=>b.reviews-a.reviews).slice(0,4);
  grid.innerHTML = top.map(p=>`
    <div class="prod-admin-card" onclick="editProduct(${p.id})" style="cursor:pointer">
      <div class="pac-img" style="height:120px"><img src="${p.img}" alt="${p.name}" loading="lazy"/></div>
      <div class="pac-info">
        <div class="pac-cat">${p.cat}</div>
        <div class="pac-name" style="font-size:12px">${p.name}</div>
        <div class="pac-price"><span class="pac-price-main" style="font-size:14px">₪${p.price}</span></div>
        <div class="pac-stats">
          <span class="pac-stat">⭐<strong>${p.stars}</strong></span>
          <span class="pac-stat">💬<strong>${p.reviews}</strong></span>
          <span class="chip chip-green" style="margin-right:auto">رائج</span>
        </div>
      </div>
    </div>
  `).join('');
}

function renderMedia() {
  const grid = document.getElementById('media-grid');
  if (!grid || grid.dataset.rendered) return;
  const imgs = adminProducts.slice(0,8).map(p=>p.img);
  grid.innerHTML = `
    <div class="upload-slot" onclick="document.getElementById('bulk-upload').click()">
      <div class="up-icon">+</div>
      <small>رفع صورة</small>
    </div>
    ${imgs.map(src=>`
      <div class="media-item">
        <img src="${src}" alt="" loading="lazy"/>
        <div class="mi-overlay">
          <button class="pac-action" onclick="showToast('📋 تم نسخ الرابط!')" style="font-size:14px">📋</button>
          <button class="pac-action del" onclick="this.closest('.media-item').remove();showToast('🗑️ تم الحذف')" style="font-size:14px">🗑️</button>
        </div>
      </div>
    `).join('')}
  `;
  grid.dataset.rendered='1';
}

function handleBulkUpload(input) {
  const files = [...input.files];
  files.forEach(f => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div'); div.className='media-item';
      div.innerHTML=`<img src="${e.target.result}" alt=""/><div class="mi-overlay"><button class="pac-action" style="font-size:14px">📋</button></div>`;
      document.getElementById('media-grid')?.prepend(div);
    };
    reader.readAsDataURL(f);
  });
  showToast(`✅ تم رفع ${files.length} صورة`);
}


function renderCategories() {
  const grid = document.getElementById('cat-grid');
  if (!grid || grid.dataset.rendered) return;
  grid.innerHTML = CAT_DATA.map(c=>`
    <div class="stat-card blue" onclick="showToast('📂 تعديل: ${c.name}')" style="cursor:pointer">
      <div class="stat-icon">${c.icon}</div>
      <div class="stat-val">${c.count}</div>
      <div class="stat-label">${c.name}</div>
      <div class="stat-change up">↑ ${c.sales} مبيعة هذا الشهر</div>
    </div>
  `).join('');
  grid.dataset.rendered='1';
}

function renderNotifs() {
  const el = document.getElementById('notif-list');
  if (!el) return;
  el.innerHTML = NOTIFS.map(n=>`
    <div class="notif-item">
      <div class="notif-icon" style="background:${n.color}">${n.icon}</div>
      <div>
        <strong>${n.title}</strong>
        <small>${n.sub}</small>
      </div>
      <button onclick="this.closest('.notif-item').style.opacity='.4'" style="margin-right:auto;font-size:12px;color:var(--text3);cursor:pointer">✓ قراءة</button>
    </div>
  `).join('');
}

function previewImg(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('img-preview-el').src=e.target.result;
    document.getElementById('img-preview-el').style.display='block';
    document.getElementById('upload-placeholder').style.display='none';
    document.getElementById('img-preview-area').classList.add('has-img');
    document.getElementById('f-img-url').value=e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}
function previewUrl(url) {
  if (!url) return;
  const prev = document.getElementById('img-preview-el');
  prev.src=url; prev.style.display='block';
  document.getElementById('upload-placeholder').style.display='none';
}

function pickColor(el, color) {
  document.querySelectorAll('.color-swatch').forEach(s=>s.classList.remove('active'));
  el.classList.add('active');
  showToast(`🎨 اللون المختار: ${color}`);
}

function showToast(msg, type) {
  const t = document.getElementById('admin-toast');
  t.textContent = msg;
  t.className = 'toast-admin show';
  clearTimeout(t._timer);
  t._timer = setTimeout(()=>t.classList.remove('show'), 3000);
}

function updateClock() {
  document.getElementById('live-time').textContent = new Date().toLocaleTimeString('ar-PS');
}

updateStats();
renderChart();
renderRecentOrders();
renderTopProducts();
updateClock();
setInterval(updateClock, 1000);

// Close modal on outside click
document.getElementById('product-modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// ══ REAL DATA BINDING ══
function loadAdminProducts() {
  if (typeof Store === 'undefined') return;
  const products = Store.getProducts();
  // Update stats
  document.getElementById('stat-products') && (document.getElementById('stat-products').textContent = products.length);
  const orders = getAdminOrders();
  document.getElementById('stat-orders') && (document.getElementById('stat-orders').textContent = orders.length);
  document.getElementById('stat-pending') && (document.getElementById('stat-pending').textContent = orders.filter(o=>o.status==='pending').length);
  const revenue = orders.filter(o=>o.status!=='cancelled').reduce((s,o)=>s+(o.total||0),0);
  document.getElementById('stat-revenue') && (document.getElementById('stat-revenue').textContent = '\u20AA' + revenue.toLocaleString('ar-SA'));
}

function loadAdminOrders() {
  if (typeof Store === 'undefined') return;
  const orders = getAdminOrders();
  const tbody = document.getElementById('all-orders-body');
  if (!tbody) return;
  const statusMap = {pending:'\u23f0 \u0642\u064a\u062f \u0627\u0644\u0627\u0646\u062a\u0638\u0627\u0631', processing:'\u2699\ufe0f \u0642\u064a\u062f \u0627\u0644\u0645\u0639\u0627\u0644\u062c\u0629', shipped:'\ud83d\ude9a \u062a\u0645 \u0627\u0644\u0634\u062d\u0646', delivered:'\u2705 \u062a\u0645 \u0627\u0644\u062a\u0633\u0644\u064a\u0645', cancelled:'\u274c \u0645\u0644\u063a\u064a'};
  tbody.innerHTML = orders.slice(0,50).map(o => `
    <tr>
      <td style="font-weight:700">${o.id}</td>
      <td>${o.userName || '-'}</td>
      <td>${o.items?.length || 0} \u0645\u0646\u062a\u062c</td>
      <td>\u20AA${(o.total||0).toLocaleString()}</td>
      <td><span class="status-badge">${statusMap[o.status]||o.status}</span></td>
      <td>${new Date(o.date).toLocaleDateString('ar-SA')}</td>
      <td>
        <select onchange="changeOrderStatus('${o.id}', this.value)" style="background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:8px;padding:4px 8px;font-family:inherit;">
          ${['pending','processing','shipped','delivered','cancelled'].map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${statusMap[s]||s}</option>`).join('')}
        </select>
      </td>
    </tr>
  `).join('');
}

window.editProductAdmin = function(id) {
  if (typeof Store === 'undefined') return;
  const p = Store.getProduct(id);
  if (!p) return;
  const name = prompt('\u0627\u0633\u0645 \u0627\u0644\u0645\u0646\u062a\u062c:', p.name);
  if (name === null) return;
  const price = prompt('\u0627\u0644\u0633\u0639\u0631 (\u20AA):', p.price);
  if (price === null) return;
  Store.updateProduct(id, { name, price: parseFloat(price) || p.price });
  loadAdminProducts();
  alert('\u2705 \u062a\u0645 \u062a\u062d\u062f\u064a\u062b \u0627\u0644\u0645\u0646\u062a\u062c');
};

window.deleteProductAdmin = function(id) {
  if (!confirm('\u0647\u0644 \u0623\u0646\u062a \u0645\u062a\u0623\u0643\u062f \u0645\u0646 \u062d\u0630\u0641 \u0647\u0630\u0627 \u0627\u0644\u0645\u0646\u062a\u062c\u061f')) return;
  Store.deleteProduct(id);
  loadAdminProducts();
};

document.addEventListener('DOMContentLoaded', () => {
  fetchLiveOrders();
  loadAdminProducts();
});

</script>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
      for(let registration of registrations) {
        registration.unregister();
      }
    });
  }
function renderDeliveryZonesUI() {
  const container = document.getElementById('delivery-zones-container');
  if(!container) return;
  const zones = (typeof Store !== 'undefined') ? Store.getDeliveryZones() : [];
  container.innerHTML = zones.map((z) => `
    <div style="display:flex; gap:10px" class="dz-row">
      <input type="text" class="dz-name" value="${z.name}" placeholder="اسم المنطقة" style="flex:2" />
      <input type="number" class="dz-price" value="${z.price}" placeholder="السعر" style="flex:1" />
      <button class="btn" style="background:var(--red);color:#fff;padding:0 12px" onclick="this.parentElement.remove()">✕</button>
    </div>
  `).join('');
}

function addDeliveryZone() {
  document.getElementById('delivery-zones-container').insertAdjacentHTML('beforeend', `
    <div style="display:flex; gap:10px" class="dz-row">
      <input type="text" class="dz-name" value="" placeholder="اسم المنطقة" style="flex:2" />
      <input type="number" class="dz-price" value="0" placeholder="السعر" style="flex:1" />
      <button class="btn" style="background:var(--red);color:#fff;padding:0 12px" onclick="this.parentElement.remove()">✕</button>
    </div>
  `);
}

function saveDeliveryZonesUI() {
  const container = document.getElementById('delivery-zones-container');
  const zones = [];
  container.querySelectorAll('.dz-row').forEach((div, i) => {
    const name = div.querySelector('.dz-name').value.trim();
    const price = parseFloat(div.querySelector('.dz-price').value) || 0;
    if(name) zones.push({id: i+1, name, price});
  });
  if(typeof Store !== 'undefined') Store.saveDeliveryZones(zones);
  showToast('✅ تم حفظ مناطق التوصيل بنجاح!');
}

document.addEventListener('DOMContentLoaded', () => {
  renderDeliveryZonesUI();
});

function printOrder(id) {
    const orders = getAdminOrders();
    const o = orders.find(x => String(x.id) === String(id));
    if (!o) return;
    
    let itemsHtml = '';
    if (Array.isArray(o.items)) {
        itemsHtml = o.items.map(i => `
            <tr>
                <td style="padding:10px;border-bottom:1px solid #ddd;">${i.name}</td>
                <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${i.quantity || 1}</td>
                <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">₪${i.price}</td>
                <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">₪${(i.price * (i.quantity || 1))}</td>
            </tr>
        `).join('');
    } else {
        itemsHtml = `<tr><td colspan="4" style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${o.items} منتج</td></tr>`;
    }
    
    const w = window.open('', '_blank');
    w.document.write(`
        <html dir="rtl" lang="ar">
        <head>
            <title>طباعة طلبية #${o.id}</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #000; margin: 0; }
                h1 { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 30px; font-size: 28px; }
                .info-box { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #fafafa; }
                .info-row { margin-bottom: 12px; font-size: 16px; }
                .info-row strong { display: inline-block; width: 140px; color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #f0f0f0; padding: 12px 10px; text-align: right; border-bottom: 2px solid #000; font-size: 16px; }
                .total { margin-top: 30px; text-align: left; font-size: 24px; font-weight: bold; border-top: 2px solid #000; padding-top: 15px; }
                @media print {
                    body { padding: 20px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .info-box { border: 1px solid #000; background: transparent; }
                }
            </style>
        </head>
        <body>
            <h1>فاتورة طلبية - إستوردلي</h1>
            <div style="display:flex;justify-content:space-between;font-size:16px;margin-bottom:20px;">
                <div><strong>رقم الطلبية:</strong> #${o.id}</div>
                <div><strong>تاريخ الطلب:</strong> ${new Date(o.date).toLocaleDateString('ar-SA')}</div>
            </div>
            
            <div class="info-box">
                <h3 style="margin-top:0;border-bottom:1px solid #ddd;padding-bottom:10px;">تفاصيل العميل والتوصيل</h3>
                <div class="info-row"><strong>الاسم:</strong> ${o.userName || o.customer || '-'}</div>
                <div class="info-row"><strong>رقم الهاتف:</strong> ${o.phone || '-'}</div>
                <div class="info-row"><strong>منطقة التوصيل:</strong> ${o.zone || 'لم يحدد'}</div>
                <div class="info-row"><strong>العنوان المفصل:</strong> ${o.address || '-'}</div>
                <div class="info-row"><strong>ملاحظات العميل:</strong> ${o.notes || '-'}</div>
            </div>
            
            <h3 style="margin-top:30px">المنتجات المطلوبة</h3>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th style="text-align:center;">الكمية</th>
                        <th style="text-align:center;">سعر الوحدة</th>
                        <th style="text-align:center;">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            
            <div class="total">
                المجموع الكلي: ₪${(o.total || 0).toLocaleString()}
            </div>
            
            <div style="text-align:center; margin-top:60px; font-size:14px; color:#555;">
                <p>نشكركم على تسوقكم من متجر إستوردلي!</p>
                <p dir="ltr">estawredli.com</p>
            </div>
        </body>
        </html>
    `);
    w.document.close();
    w.focus();
    setTimeout(() => { w.print(); w.close(); }, 500);
}

function viewOrder(id) {
    const o = getAdminOrders().find(x => String(x.id) === String(id));
    if (!o) return;
    
    document.getElementById('ov-id').textContent = '#' + o.id;
    
    let itemsHtml = '';
    if (Array.isArray(o.items)) {
        itemsHtml = o.items.map(i => `
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                <div><strong>${i.name}</strong> <span style="color:var(--text3)">(x${i.quantity || 1})</span></div>
                <div>₪${i.price * (i.quantity || 1)}</div>
            </div>
        `).join('');
    } else {
        itemsHtml = `<div style="padding:8px 0">${o.items} منتج</div>`;
    }
    
    document.getElementById('ov-body').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
            <div style="background:var(--bg3);padding:15px;border-radius:12px;">
                <h3 style="margin-bottom:10px;font-size:14px;color:var(--text2)">معلومات العميل</h3>
                <div style="margin-bottom:6px"><strong>الاسم:</strong> ${o.userName || o.customer || '-'}</div>
                <div style="margin-bottom:6px"><strong>رقم الهاتف:</strong> ${o.phone || '-'}</div>
                <div style="margin-bottom:6px"><strong>ملاحظات:</strong> ${o.notes || '-'}</div>
            </div>
            <div style="background:var(--bg3);padding:15px;border-radius:12px;">
                <h3 style="margin-bottom:10px;font-size:14px;color:var(--text2)">تفاصيل التوصيل</h3>
                <div style="margin-bottom:6px"><strong>المنطقة:</strong> ${o.zone || 'لم يحدد'}</div>
                <div style="margin-bottom:6px"><strong>العنوان المفصل:</strong> ${o.address || '-'}</div>
                <div style="margin-bottom:6px"><strong>التاريخ:</strong> ${new Date(o.date).toLocaleString('ar-SA')}</div>
            </div>
        </div>
        
        <h3 style="margin-bottom:10px;font-size:14px;color:var(--text2);border-bottom:1px solid var(--border);padding-bottom:8px;">المنتجات المطلوبة</h3>
        <div style="margin-bottom:20px;">
            ${itemsHtml}
        </div>
        
        <div style="display:flex;justify-content:space-between;align-items:center;background:var(--p);color:#fff;padding:15px;border-radius:12px;">
            <strong style="font-size:16px;">المجموع الكلي:</strong>
            <strong style="font-size:20px;">₪${(o.total || 0).toLocaleString()}</strong>
        </div>
    `;
    
    document.getElementById('ov-print-btn').onclick = () => { closeOrderModal(); printOrder(id); };
    document.getElementById('order-view-modal').classList.add('open');
}

function closeOrderModal() {
    document.getElementById('order-view-modal').classList.remove('open');
}



// ==========================================
// BANNERS MANAGER
// ==========================================

const adminSliders = {
    slides: [],
    
    async load() {
        try {
            const res = await fetch('api/get_sliders.php');
            this.slides = await res.json();
            this.render();
        } catch(e) {
            console.error('Failed to load sliders', e);
        }
    },
    
    render() {
        const container = document.getElementById('hero-slides-list');
        if (!container) return;
        
        if (!this.slides || this.slides.length === 0) {
            container.innerHTML = '<div style="text-align:center;color:var(--text3);">لا يوجد شرائح حالياً.</div>';
            return;
        }
        
        container.innerHTML = this.slides.map((s, i) => `
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:12px; padding:15px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:5px;">
                    <h3 style="margin-bottom:0;">شريحة رقم ${i+1}</h3>
                    <button onclick="adminSliders.removeSlide(${i})" style="background:red; color:white; border:none; border-radius:4px; cursor:pointer; padding:4px 8px; font-size:12px;">حذف</button>
                </div>
                
                <label style="font-size:12px; color:var(--text2);">صورة الخلفية (رابط)</label>
                <input type="text" value="${s.img || ''}" onchange="adminSliders.update(${i}, 'img', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">الشارة (Tag)</label>
                <input type="text" value="${s.tag || ''}" onchange="adminSliders.update(${i}, 'tag', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">العنوان الرئيسي (استخدم &lt;br/&gt; للسطر الجديد و &lt;em&gt; للكلمات المميزة)</label>
                <input type="text" value="${(s.title || '').replace(/"/g, '&quot;')}" onchange="adminSliders.update(${i}, 'title', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">الوصف والتفاصيل</label>
                <input type="text" value="${s.desc || ''}" onchange="adminSliders.update(${i}, 'desc', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 1: النص</label>
                        <input type="text" value="${s.btn1_text || ''}" onchange="adminSliders.update(${i}, 'btn1_text', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 1: الرابط</label>
                        <input type="text" value="${s.btn1_link || ''}" onchange="adminSliders.update(${i}, 'btn1_link', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 2: النص</label>
                        <input type="text" value="${s.btn2_text || ''}" onchange="adminSliders.update(${i}, 'btn2_text', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:var(--text2);">زر 2: الرابط</label>
                        <input type="text" value="${s.btn2_link || ''}" onchange="adminSliders.update(${i}, 'btn2_link', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                </div>
            </div>
        `).join('');
    },
    
    update(index, field, value) {
        this.slides[index][field] = value;
    },
    
    addSlide() {
        this.slides.push({
            img: '', tag: '', title: '', desc: '', 
            btn1_text: 'تسوق الآن', btn1_link: 'shop.html', btn1_class: 'btn btn-primary btn-lg',
            btn2_text: '', btn2_link: '', btn2_class: ''
        });
        this.render();
    },
    
    removeSlide(index) {
        if(confirm('هل أنت متأكد من حذف هذه الشريحة؟')) {
            this.slides.splice(index, 1);
            this.render();
        }
    },
    
    async save() {
        try {
            const res = await fetch('api/save_sliders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.slides)
            });
            const data = await res.json();
            if(data.success) {
                alert('✅ تم حفظ السلايدر بنجاح!');
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم!');
        }
    }
};

const adminBanners = {

    banners: [],
    
    async load() {
        try {
            const res = await fetch('api/get_banners.php?t=' + Date.now());
            this.banners = await res.json();
            this.render();
        } catch (e) {
            console.error("Error loading banners:", e);
        }
    },
    
    render() {
        const container = document.getElementById('admin-banners-builder');
        if (!container) return;
        
        if (!this.banners || this.banners.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text3);">لا يوجد بنرات حالياً.</div>';
            return;
        }
        
        container.innerHTML = this.banners.map((b, i) => `
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:12px; padding:15px; display:flex; flex-direction:column; gap:10px;">
                <h3 style="margin-bottom:10px; border-bottom:1px solid var(--border); padding-bottom:5px;">بنر رقم ${i+1} ${b.is_big ? '(كبير)' : ''}</h3>
                
                <label style="font-size:12px; color:var(--text2);">صورة الخلفية (رابط)</label>
                <input type="text" value="${b.image || ''}" onchange="adminBanners.update(${i}, 'image', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">الشارة (Tag)</label>
                <input type="text" value="${b.tag || ''}" onchange="adminBanners.update(${i}, 'tag', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">العنوان الرئيسي</label>
                <input type="text" value="${b.title || ''}" onchange="adminBanners.update(${i}, 'title', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">النص الفرعي (الوصف)</label>
                <input type="text" value="${b.desc || ''}" onchange="adminBanners.update(${i}, 'desc', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">نص الزر</label>
                <input type="text" value="${b.btn_text || ''}" onchange="adminBanners.update(${i}, 'btn_text', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">رابط الزر</label>
                <input type="text" value="${b.link || ''}" onchange="adminBanners.update(${i}, 'link', this.value)" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:5px;">
                
                <label style="font-size:12px; color:var(--text2);">لون النص (Hex Color)</label>
                <input type="color" value="${b.text_color || '#1f2937'}" onchange="adminBanners.update(${i}, 'text_color', this.value)" style="width:100%; height:40px; border-radius:6px; border:1px solid var(--border); background:var(--bg); cursor:pointer; margin-bottom:5px;">
            </div>
        `).join('');
    },
    
    update(index, field, value) {
        this.banners[index][field] = value;
    },
    
    async save() {
        try {
            const res = await fetch('api/save_banners.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.banners)
            });
            const data = await res.json();
            if(data.success) {
                alert('✅ تم حفظ البنرات بنجاح!');
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم!');
        }
    }
};

const adminNav = {
    data: [],
    async load() {
        try {
            const res = await fetch('api/get_nav.php?t=' + Date.now());
            this.data = await res.json();
            
            // Migrate old "columns" structure to simple "subLinks" if needed
            this.data.forEach(item => {
                if (item.type === 'dropdown' && item.columns) {
                    if (!item.subLinks) item.subLinks = [];
                    item.columns.forEach(col => {
                        if (col.links) {
                            col.links.forEach(l => item.subLinks.push(l));
                        }
                    });
                    delete item.columns;
                }
            });
            this.render();

    // Add dynamic nav items to datalist
    const datalist = document.getElementById('cats-list');
    if (datalist && this.data) {
        this.data.forEach(item => {
            if (item.title && !datalist.querySelector(`option[value="${item.title}"]`)) {
                datalist.insertAdjacentHTML('beforeend', `<option value="${item.title}">`);
            }
            if (item.subLinks) {
                item.subLinks.forEach(sub => {
                    if (sub.title && !datalist.querySelector(`option[value="${sub.title}"]`)) {
                        datalist.insertAdjacentHTML('beforeend', `<option value="${sub.title}">`);
                    }
                });
            }
        });
    }

        } catch(e) {
            showToast('خطأ في تحميل القائمة', 'error');
        }
    },
    
    render() {
        const container = document.getElementById('admin-nav-builder');
        if (!container) return;
        
        if (this.data.length === 0) {
            container.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text3)">القائمة فارغة. اضغط على "+ رابط رئيسي" للبدء.</div>`;
            return;
        }
        
        let html = '';
        this.data.forEach((item, index) => {
            html += `
            <div style="background:var(--bg1); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:20px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                    <div style="display:flex; gap:15px; align-items:center; flex-grow:1;">
                        <span style="cursor:move; font-size:24px; color:var(--text3)">☰</span>
                        
                        <div>
                            <label style="display:block; font-size:12px; color:var(--text3); margin-bottom:4px;">اسم القسم</label>
                            <input type="text" value="${item.title}" onchange="adminNav.updateItem(${index}, 'title', this.value)" style="padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1); width:200px; font-weight:bold; font-size:16px;" placeholder="مثال: مماسح">
                        </div>
                        
                        <div>
                            <label style="display:block; font-size:12px; color:var(--text3); margin-bottom:4px;">النوع</label>
                            <select onchange="adminNav.updateItem(${index}, 'type', this.value)" style="padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1); font-size:14px;">
                                <option value="link" ${item.type === 'link' ? 'selected' : ''}>رابط مباشر</option>
                                <option value="dropdown" ${item.type === 'dropdown' ? 'selected' : ''}>قائمة منسدلة (تحتوي تفرعات)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style="display:block; font-size:12px; color:var(--text3); margin-bottom:4px;">أيقونة (اختياري)</label>
                            <input type="text" value="${item.badge || ''}" onchange="adminNav.updateItem(${index}, 'badge', this.value)" style="padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1); width:80px; font-size:14px;" placeholder="🔥">
                        </div>
                    </div>
                    
                    <div style="display:flex; gap:8px;">
                        <button onclick="adminNav.moveItem(${index}, -1)" title="أعلى" style="background:var(--bg2); border:1px solid var(--border); color:var(--text1); padding:10px; border-radius:8px; cursor:pointer;">⬆</button>
                        <button onclick="adminNav.moveItem(${index}, 1)" title="أسفل" style="background:var(--bg2); border:1px solid var(--border); color:var(--text1); padding:10px; border-radius:8px; cursor:pointer;">⬇</button>
                        <button onclick="adminNav.deleteItem(${index})" title="حذف" style="background:rgba(239,68,68,0.1); border:1px solid var(--red); color:var(--red); padding:10px 15px; border-radius:8px; cursor:pointer; font-weight:bold;">حذف</button>
                    </div>
                </div>
                
                ${item.type === 'dropdown' ? this.renderSimpleDropdown(item, index) : ''}
            </div>
            `;
        });
        
        container.innerHTML = html;
    },
    
    renderSimpleDropdown(item, itemIndex) {
        let html = `<div style="margin-top:20px; padding:20px; background:var(--bg2); border-radius:8px; border:1px solid var(--border);">`;
        html += `<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h4 style="margin:0; color:var(--text2); font-size:15px;">التصنيفات الفرعية</h4>
                    <button onclick="adminNav.addSubLink(${itemIndex})" style="background:var(--blue); color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:bold;">+ إضافة تصنيف فرعي</button>
                 </div>`;
                 
        if (!item.subLinks || item.subLinks.length === 0) {
            html += `<div style="color:var(--text3); font-size:13px; text-align:center; padding:10px;">لا يوجد تصنيفات فرعية بعد.</div>`;
        } else {
            html += `<div style="display:flex; flex-direction:column; gap:10px;">`;
            item.subLinks.forEach((link, linkIndex) => {
                html += `<div style="display:flex; gap:10px; align-items:center; background:var(--bg1); padding:10px; border-radius:6px; border:1px solid var(--border);">
                            <span style="color:var(--text3); font-size:12px;">${linkIndex+1}.</span>
                            <input type="text" value="${link.title}" onchange="adminNav.updateSubLink(${itemIndex}, ${linkIndex}, this.value)" style="padding:8px; width:250px; border:1px solid var(--border); border-radius:6px; background:var(--bg2); color:var(--text1); font-size:14px;" placeholder="اسم التصنيف (مثال: ممسحة دوارة)">
                            <button onclick="adminNav.deleteSubLink(${itemIndex}, ${linkIndex})" style="background:transparent; border:none; color:var(--red); cursor:pointer; font-size:20px; padding:0 10px;" title="حذف">×</button>
                        </div>`;
            });
            html += `</div>`;
        }
        
        html += `</div>`;
        return html;
    },
    
    updateItem(index, key, val) {
        this.data[index][key] = val;
        if (key === 'title' && this.data[index].type === 'link') {
            this.data[index].url = 'shop.html?cat=' + encodeURIComponent(val);
        }
        if (key === 'type' && val === 'dropdown' && !this.data[index].subLinks) {
            this.data[index].subLinks = [];
        }
        this.render();
    },
    moveItem(index, dir) {
        if (index + dir < 0 || index + dir >= this.data.length) return;
        const temp = this.data[index];
        this.data[index] = this.data[index + dir];
        this.data[index + dir] = temp;
        this.render();
    },
    deleteItem(index) {
        if(confirm('هل أنت متأكد من حذف هذا القسم؟')) {
            this.data.splice(index, 1);
            this.render();
        }
    },
    addMainItem() {
        this.data.push({
            id: 'nav_' + Date.now(),
            title: 'قسم جديد',
            url: 'shop.html?cat=' + encodeURIComponent('قسم جديد'),
            type: 'link'
        });
        this.render();
    },
    
    // Simple SubLinks
    addSubLink(itemIndex) {
        if(!this.data[itemIndex].subLinks) this.data[itemIndex].subLinks = [];
        this.data[itemIndex].subLinks.push({title: 'تصنيف فرعي', url: 'shop.html?cat=' + encodeURIComponent('تصنيف فرعي')});
        this.render();
    },
    updateSubLink(itemIndex, linkIndex, val) {
        this.data[itemIndex].subLinks[linkIndex].title = val;
        this.data[itemIndex].subLinks[linkIndex].url = 'shop.html?cat=' + encodeURIComponent(val);
    },
    deleteSubLink(itemIndex, linkIndex) {
        this.data[itemIndex].subLinks.splice(linkIndex, 1);
        this.render();
    },
    
    async saveNavToServer() {
        try {
            const res = await fetch('api/save_nav.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(this.data)
            });
            const result = await res.json();
            if (result.success) {
                showToast('✅ تم حفظ القائمة بنجاح! ستظهر للزوار فوراً.');
            } else {
                showToast('❌ فشل الحفظ: ' + result.message, 'error');
            }
        } catch(e) {
            showToast('❌ حدث خطأ أثناء الحفظ', 'error');
        }
    }
};

window.addEventListener('DOMContentLoaded', () => {
    adminNav.load();
});


const adminIcons = {
    mapping: {}, // { 'squeegees': [prod1_id, prod2_id] }
    iconNames: {
        'squeegees': 'قشاطات',
        'brooms': 'مكانس',
        'sponges': 'ليفة جلي',
        'loofahs': 'ليف حمام',
        'scissors': 'مقصات',
        'personal_care': 'عناية شخصية',
        'dusters': 'منفضة غبار',
        'cosmetics': 'كورمتكس',
        'scales': 'موازين',
        'party': 'حفلات',
        'foil': 'قصدير',
        'plastic': 'بلاستيك',
        'nylon_bags': 'أكياس نايلون',
        'batteries': 'بطاريات',
        'microfiber': 'مايكروفايبر'
    },
    
    async load() {
        try {
            const res = await fetch('api/get_icons.php?t=' + Date.now());
            this.mapping = await res.json();
            // initialize empty ones if not present
            Object.keys(this.iconNames).forEach(key => {
                if(!this.mapping[key]) this.mapping[key] = [];
            });
            this.render();
        } catch(e) {
            console.error(e);
        }
    },
    
    render() {
        const container = document.getElementById('admin-icons-builder');
        if (!container) return;
        
        let html = '';
        Object.keys(this.iconNames).forEach(key => {
            const name = this.iconNames[key];
            const prodIds = this.mapping[key] || [];
            
            // Get product names for these IDs
            const prodsHtml = prodIds.map(id => {
                const p = adminProducts.find(x => String(x.id) === String(id));
                const pName = p ? p.name : 'منتج غير معروف';
                return `<div style="display:inline-flex; align-items:center; background:var(--bg1); padding:4px 8px; border-radius:4px; margin:4px; font-size:12px; border:1px solid var(--border);">
                          ${pName}
                          <span onclick="adminIcons.removeProduct('${key}', '${id}')" style="margin-right:8px; color:var(--red); cursor:pointer; font-weight:bold;">×</span>
                        </div>`;
            }).join('');
            
            html += `
            <div style="background:var(--bg1); border:1px solid var(--border); border-radius:8px; padding:15px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:15px; width:40%;">
                        <div style="background:var(--bg2); padding:10px; border-radius:8px; text-align:center; min-width:120px;">
                            <strong style="color:var(--text1)">${name}</strong>
                        </div>
                        <div style="font-size:12px; color:var(--text3)">${prodIds.length} منتج مرتبط</div>
                    </div>
                    
                    <div style="flex-grow:1; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; gap:10px;">
                            <select id="sel_${key}" style="flex-grow:1; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1);">
                                <option value="">اختر منتجاً لإضافته لهذه الأيقونة...</option>
                                ${adminProducts.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                            </select>
                            <button onclick="adminIcons.addProduct('${key}')" class="btn-outline" style="padding:8px 15px;">إضافة</button>
                        </div>
                        
                        <div style="min-height:30px; padding:8px; border:1px dashed var(--border); border-radius:6px;">
                            ${prodsHtml || '<span style="color:var(--text3); font-size:12px;">لا يوجد منتجات محددة. سيتم عرض المنتجات تلقائياً إذا كان تصنيفها يطابق الأيقونة.</span>'}
                        </div>
                    </div>
                </div>
            </div>`;
        });
        
        container.innerHTML = html;
    },
    
    addProduct(key) {
        const select = document.getElementById(`sel_${key}`);
        const prodId = select.value;
        if(!prodId) return;
        
        if(!this.mapping[key].includes(prodId)) {
            this.mapping[key].push(prodId);
            this.render();
        }
    },
    
    removeProduct(key, prodId) {
        this.mapping[key] = this.mapping[key].filter(id => String(id) !== String(prodId));
        this.render();
    },
    
    async saveIconsToServer() {
        try {
            const res = await fetch('api/save_icons.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(this.mapping)
            });
            const result = await res.json();
            if (result.success) {
                showToast('✅ تم حفظ منتجات الأيقونات بنجاح!');
            } else {
                showToast('❌ فشل الحفظ: ' + result.message, 'error');
            }
        } catch(e) {
            showToast('❌ حدث خطأ أثناء الحفظ', 'error');
        }
    }
};

const originalLoadIcons = adminNav.load;
adminNav.load = async function() {
    await originalLoadIcons.call(adminNav);
    adminIcons.load();
};

</script>
</body>
</html>
