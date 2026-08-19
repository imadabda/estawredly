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
<script src="products_db.js?v=1785803845471<?= time() ?>">
</script>
<script src="store.js?v=1786117170000">
</script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  /* === Palette === */
  --bg:#070B14;
  --bg2:#0D1117;
  --bg3:#111827;
  --bg4:#1a2133;
  --glass:rgba(17,24,39,0.7);
  --glass2:rgba(99,102,241,0.06);
  --border:rgba(255,255,255,.055);
  --border2:rgba(255,255,255,.1);
  --border-accent:rgba(99,102,241,.35);
  --text:#E2E8F0;
  --text2:#94A3B8;
  --text3:#475569;

  /* === Accent === */
  --p:#6366F1;
  --p2:#4F46E5;
  --pv:#8B5CF6;
  --p-glow:rgba(99,102,241,.22);
  --p-glow2:rgba(99,102,241,.12);
  --grad:linear-gradient(135deg,#6366F1,#8B5CF6);
  --grad-h:linear-gradient(135deg,#818CF8,#A78BFA);

  /* === Status === */
  --green:#10B981; --green-bg:rgba(16,185,129,.1);
  --red:#EF4444;   --red-bg:rgba(239,68,68,.1);
  --yellow:#F59E0B;--yellow-bg:rgba(245,158,11,.1);
  --blue:#3B82F6;  --blue-bg:rgba(59,130,246,.1);

  /* === Misc === */
  --r:10px;--r2:16px;--r3:22px;
  --t:.2s cubic-bezier(.4,0,.2,1);
  --sh:0 8px 32px rgba(0,0,0,.45);
  --sh2:0 20px 60px rgba(0,0,0,.6);
  --sb-w:265px;
}
html{font-size:15px;scroll-behavior:smooth;overflow-x:hidden;width:100%;max-width:100vw}
body{
  font-family:'Tajawal',sans-serif;direction:rtl;
  background:var(--bg);color:var(--text);
  min-height:100vh;overflow-x:hidden;
  background-image:
    radial-gradient(ellipse 80% 50% at 20% -5%,rgba(99,102,241,.08) 0%,transparent 60%),
    radial-gradient(ellipse 60% 40% at 80% 110%,rgba(139,92,246,.06) 0%,transparent 50%);
  background-attachment:fixed;
}
a{text-decoration:none;color:inherit}
button{font-family:inherit;cursor:pointer;border:none;background:none}
input,select,textarea{font-family:inherit}
img{max-width:100%;display:block}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:99px}
::-webkit-scrollbar-thumb:hover{background:rgba(99,102,241,.5)}

/* ══════════════════════════════════
   LAYOUT
══════════════════════════════════ */
.admin-layout{display:grid;grid-template-columns:var(--sb-w) 1fr;min-height:100vh}
@media(max-width:900px){.admin-layout{grid-template-columns:1fr}}

/* ══════════════════════════════════
   SIDEBAR
══════════════════════════════════ */
.sidebar{
  background:var(--bg2);
  border-left:1px solid var(--border);
  position:sticky;top:0;height:100vh;overflow-y:auto;
  display:flex;flex-direction:column;
  transition:transform .3s cubic-bezier(.4,0,.2,1);
  box-shadow:inset -1px 0 0 var(--border);
}
.admin-overlay{
  position:fixed;inset:0;background:rgba(0,0,0,.75);
  backdrop-filter:blur(4px);z-index:199;
  opacity:0;visibility:hidden;transition:var(--t);
}
.admin-overlay.open{opacity:1;visibility:visible}
.sb-close-btn{
  display:none;position:absolute;left:15px;top:25px;
  font-size:16px;color:var(--text2);background:var(--bg3);
  border:1px solid var(--border2);border-radius:8px;
  width:30px;height:30px;cursor:pointer;transition:var(--t);
}
.sb-close-btn:hover{color:var(--red);border-color:var(--red)}
@media(max-width:900px){
  .sidebar{position:fixed;inset:0 auto 0 auto;right:-290px;width:280px;z-index:200;transition:right .3s ease;transform:none}
  .sidebar.open{right:0;transform:none}
  .sb-close-btn{display:flex;align-items:center;justify-content:center}
}

/* Sidebar Logo */
.sb-logo{
  padding:22px 18px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:12px;
  position:relative;overflow:hidden;
}
.sb-logo::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(99,102,241,.06),transparent);
  pointer-events:none;
}
.sb-logo-icon{
  width:38px;height:38px;
  background:var(--grad);
  border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;flex-shrink:0;
  box-shadow:0 4px 16px rgba(99,102,241,.4);
  position:relative;z-index:1;
}
.sb-logo-text{
  font-size:16px;font-weight:900;
  background:var(--grad);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;
  position:relative;z-index:1;
}
.sb-logo-sub{font-size:10px;color:var(--text3);letter-spacing:2px;font-weight:700;position:relative;z-index:1;}

/* Sidebar Nav */
.sb-nav{flex:1;padding:10px 10px 20px;overflow-y:auto}
.sb-section{margin-bottom:2px}
.sb-section-title{
  font-size:9.5px;font-weight:800;color:var(--text3);
  text-transform:uppercase;letter-spacing:1.8px;
  padding:14px 14px 5px;
}
.sb-item{
  display:flex;align-items:center;gap:10px;
  padding:9px 12px;border-radius:10px;
  font-size:13.5px;font-weight:600;color:var(--text2);
  cursor:pointer;transition:var(--t);margin-bottom:1px;
  border:1px solid transparent;
  background:none;width:100%;text-align:right;
  position:relative;overflow:hidden;
}
.sb-item::before{
  content:'';position:absolute;right:0;top:20%;height:60%;
  width:3px;background:var(--grad);border-radius:0 3px 3px 0;
  transform:scaleY(0);transition:transform .2s;
}
.sb-item:hover{
  background:var(--glass2);color:var(--text);
  border-color:var(--border);
}
.sb-item.active{
  background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(139,92,246,.08));
  color:var(--p);
  border-color:var(--border-accent);
}
.sb-item.active::before{transform:scaleY(1)}
.sb-item .sb-icon{
  width:30px;height:30px;border-radius:8px;
  background:var(--bg3);
  display:flex;align-items:center;justify-content:center;
  font-size:14px;flex-shrink:0;transition:var(--t);
}
.sb-item.active .sb-icon{
  background:var(--grad);
  box-shadow:0 4px 12px rgba(99,102,241,.4);
}
.sb-item:hover:not(.active) .sb-icon{background:var(--bg4)}
.sb-item .sb-badge{
  margin-right:auto;min-width:19px;height:19px;
  background:var(--red);color:#fff;border-radius:99px;
  font-size:9.5px;font-weight:900;
  display:flex;align-items:center;justify-content:center;padding:0 5px;
  box-shadow:0 2px 8px rgba(239,68,68,.4);
}
.sb-item .sb-badge.yellow{background:var(--yellow);color:#000;box-shadow:0 2px 8px rgba(245,158,11,.4)}

/* Sidebar Footer */
.sb-footer{
  padding:14px 16px;
  border-top:1px solid var(--border);
  background:linear-gradient(to top,rgba(99,102,241,.04),transparent);
}
.sb-user{display:flex;align-items:center;gap:10px}
.sb-av{
  width:36px;height:36px;border-radius:10px;
  background:var(--grad);
  display:flex;align-items:center;justify-content:center;
  font-size:14px;font-weight:900;color:#fff;flex-shrink:0;
  box-shadow:0 4px 12px rgba(99,102,241,.35);
}
.sb-user-name{font-size:13px;font-weight:800;color:var(--text)}
.sb-user-role{font-size:10.5px;color:var(--text3)}
.sb-logout{
  margin-right:auto;width:30px;height:30px;
  border-radius:8px;background:transparent;
  border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  font-size:15px;color:var(--text3);cursor:pointer;
  transition:var(--t);
}
.sb-logout:hover{background:var(--red-bg);border-color:var(--red);color:var(--red)}

/* ══════════════════════════════════
   MAIN AREA
══════════════════════════════════ */
.main-area{display:flex;flex-direction:column;min-height:100vh;min-width:0}

/* Topbar */
.topbar{
  background:rgba(13,17,23,.85);
  backdrop-filter:blur(16px) saturate(180%);
  -webkit-backdrop-filter:blur(16px) saturate(180%);
  border-bottom:1px solid var(--border);
  padding:12px 24px;display:flex;align-items:center;gap:14px;
  position:sticky;top:0;z-index:100;
  box-shadow:0 1px 0 var(--border),0 4px 24px rgba(0,0,0,.2);
}
.topbar-menu-btn{
  display:none;width:36px;height:36px;border-radius:9px;
  background:var(--bg3);border:1px solid var(--border);
  align-items:center;justify-content:center;font-size:17px;
  color:var(--text2);transition:var(--t);
}
.topbar-menu-btn:hover{border-color:var(--p);color:var(--p)}
@media(max-width:900px){.topbar-menu-btn{display:flex}}

.topbar-search{flex:1;max-width:380px;position:relative}
.topbar-search input{
  width:100%;padding:9px 14px 9px 38px;border-radius:10px;
  border:1px solid var(--border);
  background:var(--bg3);color:var(--text);font-size:13px;font-family:inherit;
  transition:border-color .2s,box-shadow .2s;
}
.topbar-search input::placeholder{color:var(--text3)}
.topbar-search input:focus{
  outline:none;border-color:var(--p);
  box-shadow:0 0 0 3px var(--p-glow);
  background:var(--bg4);
}
.topbar-search .s-icon{
  position:absolute;left:12px;top:50%;transform:translateY(-50%);
  color:var(--text3);font-size:13px;pointer-events:none;
  transition:color .2s;
}
.topbar-search input:focus~.s-icon{color:var(--p)}

.topbar-right{display:flex;align-items:center;gap:8px;margin-right:auto}
.topbar-btn{
  width:36px;height:36px;border-radius:9px;
  background:var(--bg3);border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  font-size:15px;cursor:pointer;transition:var(--t);position:relative;color:var(--text2);
}
.topbar-btn:hover{background:var(--bg4);border-color:var(--border2);color:var(--text)}
.notif-dot{
  position:absolute;top:6px;right:6px;
  width:7px;height:7px;background:var(--red);border-radius:50%;
  border:2px solid var(--bg2);
  animation:pulse-dot 2s infinite;
}
@keyframes pulse-dot{
  0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.6)}
  50%{box-shadow:0 0 0 4px rgba(239,68,68,0)}
}
.topbar-divider{width:1px;height:24px;background:var(--border);margin:0 4px}
.topbar-profile{
  display:flex;align-items:center;gap:8px;cursor:pointer;
  padding:5px 10px;border-radius:10px;transition:var(--t);
  border:1px solid transparent;
}
.topbar-profile:hover{background:var(--bg3);border-color:var(--border)}
.tp-av{
  width:32px;height:32px;border-radius:9px;
  background:var(--grad);
  display:flex;align-items:center;justify-content:center;
  font-size:13px;font-weight:900;color:#fff;
  box-shadow:0 2px 10px rgba(99,102,241,.4);
}
.tp-name{font-size:12.5px;font-weight:700;color:var(--text)}

/* ══════════════════════════════════
   CONTENT
══════════════════════════════════ */
.content{padding:26px;flex:1;min-width:0}
.page{display:none}
.page.active{display:block;animation:pageIn .3s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

.page-title{font-size:22px;font-weight:900;color:#fff;margin-bottom:4px;
  background:linear-gradient(135deg,#fff 60%,var(--p));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.page-sub{font-size:13px;color:var(--text3);margin-bottom:26px;font-weight:500}

/* ══════════════════════════════════
   PAGE HEADER
══════════════════════════════════ */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.breadcrumb-admin{display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--text3);margin-bottom:5px;font-weight:600}
.breadcrumb-admin span{color:var(--p);opacity:.7}

/* ══════════════════════════════════
   STATS GRID
══════════════════════════════════ */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px;margin-bottom:24px}
.stat-card{
  background:var(--bg2);
  border:1px solid var(--border);
  border-radius:var(--r3);padding:20px;
  transition:var(--t);position:relative;overflow:hidden;
  cursor:default;
}
.stat-card::after{
  content:'';position:absolute;
  bottom:-30px;left:-30px;
  width:120px;height:120px;border-radius:50%;
  opacity:.06;transition:var(--t);
  transform:scale(1);
}
.stat-card.blue::after{background:#6366F1}
.stat-card.green::after{background:#10B981}
.stat-card.red::after{background:#EF4444}
.stat-card.yellow::after{background:#F59E0B}
.stat-card:hover{
  border-color:var(--border2);
  transform:translateY(-4px);
  box-shadow:var(--sh);
}
.stat-card:hover::after{transform:scale(1.4);opacity:.1}
.stat-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;border-radius:var(--r3) var(--r3) 0 0;
}
.stat-card.blue::before{background:var(--grad)}
.stat-card.green::before{background:linear-gradient(90deg,var(--green),#34D399)}
.stat-card.red::before{background:linear-gradient(90deg,var(--red),#F87171)}
.stat-card.yellow::before{background:linear-gradient(90deg,var(--yellow),#FCD34D)}
.stat-icon{
  width:42px;height:42px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;font-size:19px;margin-bottom:14px;
}
.stat-card.blue .stat-icon{background:linear-gradient(135deg,rgba(99,102,241,.2),rgba(139,92,246,.15))}
.stat-card.green .stat-icon{background:rgba(16,185,129,.15)}
.stat-card.red .stat-icon{background:rgba(239,68,68,.15)}
.stat-card.yellow .stat-icon{background:rgba(245,158,11,.15)}
.stat-val{font-size:26px;font-weight:900;color:#fff;margin-bottom:3px;letter-spacing:-.5px}
.stat-label{font-size:12px;color:var(--text3);font-weight:600;letter-spacing:.2px}
.stat-change{font-size:11.5px;font-weight:700;margin-top:10px;display:flex;align-items:center;gap:3px}
.stat-change.up{color:var(--green)}
.stat-change.down{color:var(--red)}
.stat-change:not(.up):not(.down){color:var(--text3)}

/* ══════════════════════════════════
   CHARTS
══════════════════════════════════ */
.charts-row{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px;min-width:0}
@media(max-width:1100px){.charts-row{grid-template-columns:1fr}}
.chart-card{
  background:var(--bg2);border:1px solid var(--border);
  border-radius:var(--r3);padding:22px;min-width:0;
}
.card-title{
  font-size:14.5px;font-weight:800;color:#fff;margin-bottom:3px;
  display:flex;align-items:center;gap:8px;
}
.card-sub{font-size:11.5px;color:var(--text3);margin-bottom:18px;font-weight:500}
.mini-chart{height:130px;display:flex;align-items:flex-end;gap:6px}
.bar{
  flex:1;border-radius:6px 6px 0 0;min-width:18px;
  background:linear-gradient(to top,rgba(99,102,241,.25),rgba(99,102,241,.7));
  transition:all .4s cubic-bezier(.34,1.56,.64,1);cursor:pointer;position:relative;
}
.bar:hover{
  background:var(--grad);
  box-shadow:0 0 16px rgba(99,102,241,.5);
}
.bar::after{
  content:attr(data-val);position:absolute;top:-22px;left:50%;transform:translateX(-50%);
  font-size:10px;font-weight:700;color:var(--text2);white-space:nowrap;
  opacity:0;transition:opacity .2s;background:var(--bg3);
  padding:2px 6px;border-radius:5px;border:1px solid var(--border);
}
.bar:hover::after{opacity:1}
.donut-wrap{display:flex;flex-direction:column;align-items:center;gap:16px}
.donut{width:120px;height:120px;border-radius:50%;position:relative}
.donut-labels{width:100%}
.donut-label{
  display:flex;align-items:center;justify-content:space-between;
  font-size:12px;padding:6px 0;border-bottom:1px solid var(--border);
}
.donut-label:last-child{border-bottom:none}
.donut-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;margin-left:6px}
.donut-label span{color:var(--text2);display:flex;align-items:center}
.donut-label strong{color:#fff;font-size:12px}

/* ══════════════════════════════════
   TABLES
══════════════════════════════════ */
.table-card{
  background:var(--bg2);border:1px solid var(--border);
  border-radius:var(--r3);overflow:hidden;margin-bottom:24px;min-width:0;
}
.table-header{
  padding:18px 22px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  background:linear-gradient(135deg,rgba(99,102,241,.04),transparent);
}
table{width:100%;border-collapse:collapse}
th{
  font-size:10.5px;font-weight:800;color:var(--text3);
  text-transform:uppercase;letter-spacing:1px;
  padding:11px 18px;text-align:right;
  background:rgba(255,255,255,.018);
  border-bottom:1px solid var(--border);
}
td{
  padding:13px 18px;font-size:13px;color:var(--text2);
  border-bottom:1px solid rgba(255,255,255,.03);
  transition:var(--t);
}
tr:hover td{background:rgba(99,102,241,.04);color:var(--text)}
tr:last-child td{border-bottom:none}
.order-id{font-weight:800;color:var(--p);font-size:13px}
.order-customer{display:flex;align-items:center;gap:10px}
.oc-av{
  width:30px;height:30px;border-radius:8px;
  background:var(--grad);
  display:flex;align-items:center;justify-content:center;
  font-size:12px;font-weight:900;color:#fff;flex-shrink:0;
}
.status-badge{
  padding:3px 11px;border-radius:99px;
  font-size:10.5px;font-weight:800;
  display:inline-flex;align-items:center;gap:4px;
}
.status-badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
.status-pending{background:var(--yellow-bg);color:var(--yellow)}
.status-pending::before{background:var(--yellow)}
.status-shipped{background:var(--blue-bg);color:var(--blue)}
.status-shipped::before{background:var(--blue)}
.status-delivered{background:var(--green-bg);color:var(--green)}
.status-delivered::before{background:var(--green)}
.status-cancelled{background:var(--red-bg);color:var(--red)}
.status-cancelled::before{background:var(--red)}

/* ══════════════════════════════════
   TOOLBAR
══════════════════════════════════ */
.toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:18px}
.search-field{
  flex:1;min-width:200px;padding:9px 14px;border-radius:10px;
  border:1px solid var(--border);background:var(--bg3);color:var(--text);
  font-size:13px;font-family:inherit;transition:var(--t);
}
.search-field:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px var(--p-glow);background:var(--bg4)}
.select-field{
  padding:9px 14px;border-radius:10px;border:1px solid var(--border);
  background:var(--bg3);color:var(--text);font-size:13px;
  font-family:inherit;cursor:pointer;transition:var(--t);
}
.select-field:focus{outline:none;border-color:var(--p)}

/* ══════════════════════════════════
   BUTTONS
══════════════════════════════════ */
.btn-add{
  display:flex;align-items:center;gap:7px;padding:9px 18px;border-radius:10px;
  background:var(--grad);color:#fff;
  font-size:13px;font-weight:800;cursor:pointer;transition:var(--t);border:none;font-family:inherit;
  box-shadow:0 4px 18px rgba(99,102,241,.3);position:relative;overflow:hidden;
}
.btn-add::before{
  content:'';position:absolute;inset:0;
  background:var(--grad-h);opacity:0;transition:opacity .2s;
}
.btn-add:hover::before{opacity:1}
.btn-add:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(99,102,241,.45)}
.btn-add:active{transform:translateY(0)}
.btn-outline{
  padding:9px 16px;border-radius:10px;border:1px solid var(--border2);
  color:var(--text2);font-size:13px;font-weight:700;cursor:pointer;
  transition:var(--t);font-family:inherit;background:transparent;
}
.btn-outline:hover{border-color:var(--p);color:var(--p);background:var(--p-glow2)}

/* ══════════════════════════════════
   PRODUCTS ADMIN GRID
══════════════════════════════════ */
.prod-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:14px}
.prod-admin-card{
  background:var(--bg2);border:1px solid var(--border);
  border-radius:var(--r2);overflow:hidden;transition:var(--t);position:relative;
}
.prod-admin-card:hover{border-color:var(--border-accent);transform:translateY(-4px);box-shadow:var(--sh)}
.pac-img{height:155px;overflow:hidden;position:relative;background:var(--bg3)}
.pac-img img{width:100%;height:100%;object-fit:contain;transition:transform .4s}
.prod-admin-card:hover .pac-img img{transform:scale(1.08)}
.pac-overlay{
  position:absolute;inset:0;background:rgba(7,11,20,.75);
  backdrop-filter:blur(4px);
  display:flex;align-items:center;justify-content:center;gap:8px;
  opacity:0;transition:opacity .25s;
}
.prod-admin-card:hover .pac-overlay{opacity:1}
.pac-action{
  width:36px;height:36px;border-radius:9px;
  background:rgba(255,255,255,.12);backdrop-filter:blur(8px);
  border:1px solid rgba(255,255,255,.18);color:#fff;font-size:15px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--t);
}
.pac-action:hover{background:rgba(255,255,255,.25);transform:scale(1.1)}
.pac-action.del:hover{background:rgba(239,68,68,.5);border-color:var(--red)}
.pac-badge{
  position:absolute;top:9px;right:9px;padding:3px 9px;
  border-radius:8px;font-size:10px;font-weight:800;
}
.pac-info{padding:13px}
.pac-cat{font-size:10px;font-weight:800;color:var(--p);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.pac-name{font-size:13px;font-weight:800;color:var(--text);margin-bottom:8px;line-height:1.4}
.pac-price{display:flex;align-items:center;justify-content:space-between}
.pac-price-main{font-size:15px;font-weight:900;color:#fff}
.pac-price-old{font-size:11.5px;color:var(--text3);text-decoration:line-through}
.pac-stats{display:flex;gap:12px;margin-top:9px;padding-top:9px;border-top:1px solid var(--border)}
.pac-stat{font-size:11px;color:var(--text3);display:flex;align-items:center;gap:3px}
.pac-stat strong{color:var(--text2)}
.pac-toggle{position:absolute;top:10px;left:10px;z-index:2}

/* ══════════════════════════════════
   TOGGLE
══════════════════════════════════ */
.toggle{position:relative;width:36px;height:20px;display:inline-block}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.toggle-slider{
  position:absolute;inset:0;background:var(--bg4);border-radius:99px;cursor:pointer;transition:var(--t);
  border:1px solid var(--border2);
}
.toggle-slider::before{
  content:'';position:absolute;width:13px;height:13px;background:#fff;border-radius:50%;
  top:3px;right:3px;transition:var(--t);box-shadow:0 1px 4px rgba(0,0,0,.3);
}
.toggle input:checked+.toggle-slider{background:var(--green);border-color:var(--green);box-shadow:0 0 10px rgba(16,185,129,.3)}
.toggle input:checked+.toggle-slider::before{transform:translateX(-16px)}

/* ══════════════════════════════════
   MODAL
══════════════════════════════════ */
.modal-bg{
  position:fixed;inset:0;background:rgba(0,0,0,.8);
  backdrop-filter:blur(10px);z-index:1000;
  display:flex;align-items:center;justify-content:center;padding:20px;
  opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s;
}
.modal-bg.open{opacity:1;visibility:visible}
.modal{
  background:var(--bg2);border:1px solid var(--border2);border-radius:24px;
  width:100%;max-width:640px;max-height:90vh;overflow-y:auto;
  transform:scale(.92) translateY(10px);transition:transform .3s cubic-bezier(.34,1.56,.64,1);
  box-shadow:var(--sh2);
}
.modal-bg.open .modal{transform:scale(1) translateY(0)}
.modal-head{
  padding:22px 26px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;background:var(--bg2);z-index:2;
  background:linear-gradient(135deg,rgba(99,102,241,.06),var(--bg2));
}
.modal-title{font-size:17px;font-weight:900;color:#fff}
.modal-close{
  width:34px;height:34px;border-radius:8px;background:var(--bg3);
  color:var(--text2);font-size:17px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--t);
  border:1px solid var(--border);
}
.modal-close:hover{background:rgba(239,68,68,.2);color:var(--red);border-color:var(--red)}
.modal-body{
  font-family:'Tajawal',sans-serif;direction:rtl;
  background:var(--bg2);color:var(--text);
  overflow-x:hidden;
}
.modal-footer{
  padding:18px 26px;border-top:1px solid var(--border);
  display:flex;gap:10px;justify-content:flex-end;
  position:sticky;bottom:0;background:var(--bg2);
}

/* ══════════════════════════════════
   FORM
══════════════════════════════════ */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:600px){.form-grid{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:5px}
.field.full{grid-column:1/-1}
.field label{font-size:11.5px;font-weight:800;color:var(--text2);letter-spacing:.2px}
.field input,.field select,.field textarea{
  padding:10px 13px;border-radius:10px;border:1px solid var(--border);
  background:var(--bg3);color:var(--text);font-size:13.5px;font-family:inherit;
  transition:border-color .2s,box-shadow .2s;
}
.field input:focus,.field select:focus,.field textarea:focus{
  outline:none;border-color:var(--p);
  box-shadow:0 0 0 3px var(--p-glow);background:var(--bg4);
}
.field textarea{resize:vertical;min-height:90px}
.field input[type="file"]{padding:8px;cursor:pointer}
.img-preview-area{
  border:2px dashed var(--border2);border-radius:14px;padding:22px;text-align:center;
  cursor:pointer;transition:var(--t);background:var(--bg3);
}
.img-preview-area:hover{border-color:var(--p);background:var(--p-glow2)}
.img-preview-area.has-img{border-style:solid;border-color:var(--p)}
.img-preview{width:100%;max-height:180px;object-fit:contain;border-radius:10px;margin-bottom:10px;display:none}
.form-section{
  background:var(--bg3);padding:18px;border-radius:14px;
  border:1px solid var(--border);margin-bottom:18px;
}
.section-title{
  font-size:14.5px;font-weight:900;color:#fff;margin-bottom:14px;
  display:flex;align-items:center;gap:7px;
  border-bottom:1px solid var(--border);padding-bottom:11px;
}
.btn-save{
  padding:11px 26px;border-radius:10px;
  background:var(--grad);color:#fff;
  font-size:13.5px;font-weight:800;border:none;cursor:pointer;
  font-family:inherit;transition:var(--t);
  box-shadow:0 4px 16px rgba(99,102,241,.3);
}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(99,102,241,.45)}
.btn-cancel{
  padding:11px 18px;border-radius:10px;border:1px solid var(--border2);
  color:var(--text2);font-size:13.5px;font-weight:700;background:transparent;cursor:pointer;
  font-family:inherit;transition:var(--t);
}
.btn-cancel:hover{border-color:var(--red);color:var(--red);background:var(--red-bg)}

/* ══════════════════════════════════
   SETTINGS
══════════════════════════════════ */
.settings-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:768px){.settings-grid{grid-template-columns:1fr}}
.setting-card{
  background:var(--bg2);border:1px solid var(--border);
  border-radius:var(--r2);padding:22px;
}
.setting-card h3{
  font-size:14px;font-weight:800;color:#fff;margin-bottom:14px;
  padding-bottom:11px;border-bottom:1px solid var(--border);
}
.setting-card.full{grid-column:1/-1}
.setting-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 0;border-bottom:1px solid var(--border);
}
.setting-row:last-child{border-bottom:none}
.setting-row-info strong{font-size:13px;color:var(--text);display:block;margin-bottom:2px}
.setting-row-info small{font-size:11px;color:var(--text3)}
.color-picker-row{display:flex;align-items:center;gap:7px}
.color-swatch{
  width:30px;height:30px;border-radius:8px;
  border:2px solid var(--border2);cursor:pointer;transition:transform .2s,border-color .2s;
}
.color-swatch:hover{transform:scale(1.12)}
.color-swatch.active{border-color:var(--p);transform:scale(1.18);box-shadow:0 0 10px rgba(99,102,241,.5)}

/* ══════════════════════════════════
   MEDIA GRID
══════════════════════════════════ */
.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:11px}
.media-item{
  border-radius:12px;overflow:hidden;position:relative;aspect-ratio:1;
  background:var(--bg3);border:2px solid var(--border);cursor:pointer;transition:var(--t);
}
.media-item:hover{border-color:var(--p);transform:scale(1.04)}
.media-item img{width:100%;height:100%;object-fit:contain}
.media-item .mi-overlay{
  position:absolute;inset:0;background:rgba(7,11,20,.8);
  backdrop-filter:blur(4px);
  opacity:0;transition:opacity .2s;display:flex;align-items:center;justify-content:center;gap:7px;
}
.media-item:hover .mi-overlay{opacity:1}
.upload-slot{
  border:2px dashed var(--border2);border-radius:12px;aspect-ratio:1;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;
  cursor:pointer;transition:var(--t);color:var(--text3);
}
.upload-slot:hover{border-color:var(--p);color:var(--p);background:var(--p-glow2)}
.upload-slot .up-icon{font-size:26px}
.upload-slot small{font-size:11px;font-weight:600}

/* ══════════════════════════════════
   MISC
══════════════════════════════════ */
.section-divider{height:1px;background:var(--border);margin:24px 0}
.empty-state{text-align:center;padding:60px 20px;color:var(--text3)}
.empty-state .es-icon{font-size:46px;margin-bottom:12px;opacity:.4}
.empty-state p{font-size:14px;font-weight:600;color:var(--text3)}
.tooltip{position:relative}
.tooltip::after{
  content:attr(data-tip);position:absolute;bottom:calc(100% + 6px);right:50%;transform:translateX(50%);
  background:var(--bg);color:#fff;font-size:11px;padding:5px 10px;border-radius:7px;
  white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .2s;
  border:1px solid var(--border2);box-shadow:var(--sh);
}
.tooltip:hover::after{opacity:1}
.chip{padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700}
.chip-blue{background:var(--blue-bg);color:var(--blue)}
.chip-green{background:var(--green-bg);color:var(--green)}
.chip-red{background:var(--red-bg);color:var(--red)}
.chip-yellow{background:var(--yellow-bg);color:var(--yellow)}

/* Notification list */
.notification-list{display:flex;flex-direction:column;gap:10px}
.notif-item{
  display:flex;align-items:flex-start;gap:12px;padding:13px;
  background:var(--bg3);border-radius:12px;border:1px solid var(--border);
  transition:var(--t);
}
.notif-item:hover{border-color:var(--border2);background:var(--bg4)}
.notif-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.notif-item strong{display:block;font-size:13px;color:var(--text);margin-bottom:2px}
.notif-item small{font-size:11px;color:var(--text3)}

/* Toast */
.toast-admin{
  position:fixed;top:22px;left:22px;z-index:9999;
  background:var(--bg2);border:1px solid var(--border-accent);
  border-radius:12px;padding:12px 18px;color:var(--text);
  font-size:13px;font-weight:700;
  display:flex;align-items:center;gap:10px;
  box-shadow:0 16px 48px rgba(0,0,0,.5),0 0 0 1px rgba(99,102,241,.1);
  transform:translateY(-90px);transition:transform .35s cubic-bezier(.34,1.56,.64,1);
  min-width:220px;
}
.toast-admin.show{transform:translateY(0)}

/* Print / View buttons */
.print-btn{
  background:var(--bg3);color:var(--text);border:1px solid var(--border);
  border-radius:7px;padding:4px 9px;font-family:inherit;font-size:11.5px;cursor:pointer;
  transition:var(--t);margin-right:4px;
}
.print-btn:hover{background:var(--bg4);border-color:var(--border2)}
.view-btn{
  background:var(--blue-bg);color:var(--blue);border:1px solid rgba(59,130,246,.2);
  border-radius:7px;padding:4px 9px;font-family:inherit;font-size:11.5px;cursor:pointer;
  transition:var(--t);margin-right:4px;
}
.view-btn:hover{background:rgba(59,130,246,.2)}

/* Badge new pulse */
@keyframes pulseNew{
  0%{box-shadow:0 0 0 0 rgba(16,185,129,.5)}
  70%{box-shadow:0 0 0 6px rgba(16,185,129,0)}
  100%{box-shadow:0 0 0 0 rgba(16,185,129,0)}
}
.badge-new{
  background:var(--green);color:white;font-size:9.5px;
  padding:2px 7px;border-radius:99px;margin-right:6px;
  animation:pulseNew 2s infinite;display:inline-block;vertical-align:middle;
}

/* Spin animation */
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
.spin{animation:spin 2s linear infinite}

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
.print-btn:hover { background: var(--bg4); border-color:var(--border2); }

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
  <div class="modal" style="max-width: 800px; width: 95%;">
    <div class="modal-head">
      <h2 class="modal-title" id="modal-title">إضافة منتج جديد</h2>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px;">
      
      <!-- القسم 1: الصور -->
      <div class="form-section">
        <h3 class="section-title">🖼️ صور المنتج</h3>
        
        <div class="field full">
          <label>الصورة الرئيسية <span style="color:var(--red)">*</span></label>
          <div class="img-preview-area" id="img-preview-area" onclick="document.getElementById('img-file').click()" style="max-width: 200px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px dashed var(--border); cursor: pointer; aspect-ratio: 1; display:flex; align-items:center; justify-content:center; background:var(--bg2);">
            <img class="img-preview" id="img-preview-el" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;"/>
            <div id="upload-placeholder" style="text-align:center;">
              <div style="font-size:32px;margin-bottom:8px">📷</div>
              <strong style="color:var(--text2);font-size:13px">اضغط لرفع صورة</strong>
              <small style="color:var(--text3);font-size:11px;display:block;margin-top:4px">أو استخدم الرابط بالأسفل</small>
            </div>
            <input type="file" id="img-file" accept="image/*" style="display:none" onchange="previewImg(this)"/>
          </div>
          <div class="field" style="margin-top:12px">
            <input type="url" id="f-img-url" placeholder="أو أدخل رابط للصورة الرئيسية هنا..." oninput="previewUrl(this.value)" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); background:var(--bg2); color:var(--text1);"/>
          </div>
        </div>

        <div class="field full" style="margin-top: 15px; border-top: 1px solid var(--border); padding-top: 15px;">
          <label style="margin-bottom:10px; display:block;">صور إضافية للمنتج (معرض الصور)</label>
          <div id="additional-images-container" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;"></div>
          <div style="display:flex; gap:10px;">
            <input type="url" id="f-new-img-url" placeholder="أدخل رابط صورة إضافية واضغط Enter..." onkeypress="if(event.key==='Enter'){event.preventDefault();addAdditionalImage(this.value);this.value='';}" style="flex:1; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
            <button type="button" onclick="document.getElementById('img-file-multi').click()" style="padding:10px 15px; border:1px solid var(--p); border-radius:8px; background:var(--p); color:white; cursor:pointer; font-weight:bold; white-space:nowrap;">+ رفع صورة</button>
            <input type="file" id="img-file-multi" accept="image/*" style="display:none" onchange="addAdditionalImageBase64(this)"/>
          </div>
        </div>
      </div>

      <!-- القسم 2: المعلومات الأساسية -->
      <div class="form-section">
        <h3 class="section-title">📝 المعلومات الأساسية</h3>
        <div class="form-grid">
          <div class="field full">
            <label>اسم المنتج <span style="color:var(--red)">*</span></label>
            <input type="text" id="f-name" placeholder="مثال: سماعات Sony WH-1000XM5"/>
          </div>
          <div class="field">
            <label>التصنيف</label>
            <input type="text" id="f-cat" list="cats-list" placeholder="اكتب أو اختر..." style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text1);">
            <datalist id="cats-list"></datalist>
          </div>
          <div class="field">
            <label>الماركة التجارية</label>
            <select id="f-brand">
              <option value="">بدون ماركة</option>
            </select>
          </div>
          <div class="field full" style="margin-top:12px">
            <label>📋 ملاحظات الرقم المرجعي (تظهر فقط في الفاتورة للطباعة والمعاينة)</label>
            <input type="text" id="f-ref-note" placeholder="مثال: رقم الرف، كود المورد الداخلي، أو أي ملاحظة خاصة"/>
          </div>
          <div class="field">
            <label>الشارة الإعلانية</label>
            <select id="f-badge">
              <option value="">بدون شارة</option>
              <option value="new">🆕 جديد</option>
              <option value="sale">🔥 تخفيض</option>
              <option value="hot">⚡ رائج</option>
              <option value="best">⭐ مميز</option>
            </select>
          </div>
          <div class="field full">
            <label>وصف المنتج الترويجي</label>
            <textarea id="f-desc" placeholder="وصف تفصيلي للمنتج، مميزاته واستخداماته..." style="min-height: 80px;"></textarea>
          </div>
        </div>
      </div>

      <!-- القسم 3: الأسعار والمخزون -->
      <div class="form-section">
        <h3 class="section-title">💰 الأسعار والمخزون</h3>
        <div class="form-grid">
          <div class="field">
            <label>السعر الحالي (₪) <span style="color:var(--red)">*</span></label>
            <input type="number" id="f-price" placeholder="0.00" min="0" step="0.01" style="font-weight:bold; color:var(--p);"/>
          </div>
          <div class="field">
            <label>السعر القديم (₪) <span style="color:var(--text3);font-size:10px">يظهر مشطوباً</span></label>
            <input type="number" id="f-old-price" placeholder="0.00" min="0" step="0.01"/>
          </div>
          <div class="field">
            <label>التكلفة / الجملة (₪) <span style="color:var(--text3);font-size:10px">لحساب الأرباح</span></label>
            <input type="number" id="f-cost-price" placeholder="0.00" min="0" step="0.01"/>
          </div>
          <div class="field">
            <label>المخزون المتوفر</label>
            <input type="number" id="f-stock" placeholder="غير محدود" min="0"/>
          </div>
          <div class="field">
            <label>عدد القطع في الكرتونة (للجملة)</label>
            <input type="number" id="f-pieces-per-carton" placeholder="1 أو فارغ للبيع بالقطعة" min="1" step="1"/>
          </div>
          <div class="field">
            <label>كود المنتج (SKU)</label>
            <input type="text" id="f-product-code" placeholder="كود المنتج الفريد"/>
          </div>
          <div class="field">
            <label>كود المصنع</label>
            <input type="text" id="f-factory-code" placeholder="كود المصنع"/>
          </div>
        </div>
      </div>

      <!-- القسم 4: الخصائص والمتغيرات -->
      <div class="form-section">
        <h3 class="section-title">🎨 خصائص المنتج (ألوان، مقاسات، أنواع...)</h3>
        <p style="font-size:12px; color:var(--text3); margin-bottom:12px;">أضف الخصائص ليتمكن العميل من اختيارها عند الشراء.</p>
        <div id="variants-container" style="display:flex; flex-direction:column; gap:12px; margin-bottom:12px;"></div>
        <button type="button" onclick="addVariantField()" style="width:100%; padding:12px; border:2px dashed var(--p); border-radius:8px; background:rgba(67, 97, 238, 0.05); color:var(--p); cursor:pointer; font-weight:bold; transition:all 0.2s;" onmouseover="this.style.background='rgba(67, 97, 238, 0.1)'" onmouseout="this.style.background='rgba(67, 97, 238, 0.05)'">+ إضافة خاصية جديدة</button>
      </div>

      <!-- القسم 5: التقييمات الوهمية -->
      <div class="form-section">
        <h3 class="section-title">⭐ التقييمات (اختياري)</h3>
        <div class="form-grid">
          <div class="field">
            <label>متوسط التقييم (1–5)</label>
            <input type="number" id="f-stars" placeholder="4.8" min="1" max="5" step="0.1"/>
          </div>
          <div class="field">
            <label>عدد المقيّمين</label>
            <input type="number" id="f-reviews" placeholder="0" min="0"/>
          </div>
        </div>
      </div>

    </div>
    <div class="modal-footer" style="padding: 15px 20px; border-top: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <button class="btn-cancel" onclick="closeModal()" style="padding:10px 20px; border-radius:8px;">إلغاء</button>
      <button class="btn-save" onclick="saveProduct()" style="padding:10px 30px; border-radius:8px; font-weight:bold; font-size:16px;">💾 حفظ المنتج بالنظام</button>
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
        <button class="sb-item" onclick="showPage('popup-banner',this)">
          <span class="sb-icon">📢</span> البنر الترحيبي المنبثق
        </button>
        <button class="sb-item" onclick="showPage('icons',this)">
          <span class="sb-icon">🖼️</span> أيقونات الرئيسية
        </button>
        <button class="sb-item" onclick="showPage('hero',this)">
          <span class="sb-icon">🖼️</span> غلاف الرئيسية (Hero)
        </button>
        <button class="sb-item" onclick="showPage('categories',this)">
          <span class="sb-icon">🗂️</span> التصنيفات
        </button>
        <button class="sb-item" onclick="showPage('brands',this)">
          <span class="sb-icon">🏷️</span> إدارة الماركات
        </button>
        <button class="sb-item" onclick="showPage('import-sections',this)">
          <span class="sb-icon">🌐</span> أقسام الاستيراد
        </button>
        <button class="sb-item" onclick="showPage('import-countries',this)">
          <span class="sb-icon">🌍</span> دول الاستيراد
        </button>
        <button class="sb-item" onclick="showPage('footer-settings',this)">
          <span class="sb-icon">📱</span> الفوتر والتواصل
        </button>
        <button class="sb-item" onclick="showPage('pages-content',this)">
          <span class="sb-icon">📄</span> من نحن & اتصل بنا
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
        <button class="sb-logout" onclick="window.location.href='admin-login.php'" title="تسجيل خروج">↩</button>
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

      <!-- ══ HERO BANNER (DESKTOP & MOBILE) ══ -->
      <div class="page" id="page-hero">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> غلاف الرئيسية</div>
            <h1 class="page-title">غلاف الصفحة الرئيسية (Hero Banner)</h1>
            <p class="page-sub">تعديل صور الغلاف المخصصة لشاشات الكمبيوتر ولشاشات الموبايل ورابط التوجيه</p>
          </div>
          <div style="display:flex; gap:10px;">
            <button class="btn-outline" onclick="adminHeroBanner.resetDefault()" style="background:transparent;">🔄 استعادة الافتراضي</button>
            <button class="btn-add" onclick="adminHeroBanner.save()" style="background:var(--blue); font-weight:800;">💾 حفظ الغلاف</button>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 20px;">
          
          <!-- Desktop Banner Card -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <h3 style="margin:0; font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px;">
                <span>🖥️</span> غلاف شاشات الكمبيوتر واللابتوب (16:9)
              </h3>
              <span style="font-size:11.5px; background:rgba(59,130,246,0.15); color:var(--blue); padding:3px 8px; border-radius:6px; font-weight:700;">Desktop</span>
            </div>

            <!-- Preview Desktop -->
            <div style="width:100%; aspect-ratio:16/9; background:#0f172a; border:1px solid var(--border); border-radius:10px; overflow:hidden; display:flex; align-items:center; justify-content:center; position:relative;">
              <img id="hero-preview-desktop" src="assets/hero_banner_import.png" alt="Desktop Preview" style="width:100%; height:100%; object-fit:cover;" />
            </div>

            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">رابط الصورة أو المسار</label>
              <input type="text" id="hero-desktop-img" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;" oninput="adminHeroBanner.updatePreview('desktop', this.value)" />
            </div>

            <div style="display:flex; gap:10px;">
              <button class="btn" style="background:var(--bg3); color:var(--text); border:1px solid var(--border); padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:700; width:100%;" onclick="document.getElementById('hero-upload-desktop').click()">
                📷 رفع صورة جديدة للكمبيوتر
              </button>
              <input type="file" id="hero-upload-desktop" accept="image/*" style="display:none;" onchange="adminHeroBanner.handleUpload('desktop', this)" />
            </div>
          </div>

          <!-- Mobile Banner Card -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <h3 style="margin:0; font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px;">
                <span>📱</span> غلاف شاشات الموبايل والهاتف (4:3)
              </h3>
              <span style="font-size:11.5px; background:rgba(34,197,94,0.15); color:#22c55e; padding:3px 8px; border-radius:6px; font-weight:700;">Mobile</span>
            </div>

            <!-- Preview Mobile -->
            <div style="width:100%; aspect-ratio:4/3; max-height:240px; margin:0 auto; background:#0f172a; border:1px solid var(--border); border-radius:10px; overflow:hidden; display:flex; align-items:center; justify-content:center; position:relative;">
              <img id="hero-preview-mobile" src="assets/hero_banner_import_mobile.png" alt="Mobile Preview" style="width:100%; height:100%; object-fit:cover;" />
            </div>

            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">رابط الصورة أو المسار</label>
              <input type="text" id="hero-mobile-img" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;" oninput="adminHeroBanner.updatePreview('mobile', this.value)" />
            </div>

            <div style="display:flex; gap:10px;">
              <button class="btn" style="background:var(--bg3); color:var(--text); border:1px solid var(--border); padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:700; width:100%;" onclick="document.getElementById('hero-upload-mobile').click()">
                📷 رفع صورة جديدة للموبايل
              </button>
              <input type="file" id="hero-upload-mobile" accept="image/*" style="display:none;" onchange="adminHeroBanner.handleUpload('mobile', this)" />
            </div>
          </div>

        </div>

        <!-- Banner Settings & Link -->
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; margin-top:20px;">
          <h3 style="margin:0 0 16px; font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px;">
            <span>🔗</span> إعدادات رابط التوجيه عند النقر على الغلاف
          </h3>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">رابط الصفحة المستهدفة (Target Link)</label>
              <input type="text" id="hero-link" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;" placeholder="مثال: contact.html أو shop.html" />
            </div>
            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">النص البديل للصورة (Alt Text)</label>
              <input type="text" id="hero-alt" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;" placeholder="مثال: إستوردلي - نستورد لك ما تحتاجه من المصدر" />
            </div>
          </div>

          <div style="margin-top:20px; display:flex; justify-content:flex-end;">
            <button class="btn-add" onclick="adminHeroBanner.save()" style="background:var(--blue); font-weight:800; padding:12px 24px;">💾 حفظ وتطبيق الغلاف فوراً</button>
          </div>
        </div>

      </div>

      <!-- ══ IMPORT SECTIONS MANAGER ══ -->
      <div class="page" id="page-import-sections">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> أقسام ومميزات الاستيراد</div>
            <h1 class="page-title">إدارة أقسام ومميزات الاستيراد</h1>
            <p class="page-sub">تعديل النصوص، الإحصائيات، الكروت والصور التوضيحية لقسمي شبكة الاستيراد العالمية والاستيراد المباشر</p>
          </div>
          <div style="display:flex; gap:10px;">
            <button class="btn-add" onclick="adminImportSections.resetDefault()" style="background:var(--bg3); border:1px solid var(--border); color:var(--text);">🔄 استعادة الافتراضي</button>
            <button class="btn-add" onclick="adminImportSections.save()" style="background:var(--blue); font-weight:800;">💾 حفظ التغييرات</button>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:25px; margin-top:20px;">
          <!-- 1. Global Network Section -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="border-bottom:1px solid var(--border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
              <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--blue);">🌐 1. قسم: شبكة استيراد عالمية</h3>
              <span style="font-size:12px; color:var(--text3);">شريط الخريطة والمميزات</span>
            </div>

            <!-- Background Image -->
            <div>
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">صورة الخلفية (خريطة العالم)</label>
              <div id="ais-gn-bg-preview" style="width:100%; height:120px; border-radius:8px; border:1px solid var(--border); background:#0b1329; background-size:cover; background-position:center; margin-bottom:8px;"></div>
              <input type="text" id="ais-gn-bg" oninput="adminImportSections.updatePreview('gn-bg', this.value)" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:6px;">
              <button type="button" class="btn" style="background:var(--bg); border:1px solid var(--border); color:var(--text); padding:7px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:700; width:100%;" onclick="document.getElementById('ais-gn-bg-file').click()">
                📷 رفع صورة خلفية جديدة
              </button>
              <input type="file" id="ais-gn-bg-file" accept="image/*" style="display:none;" onchange="adminImportSections.handleUpload('gn-bg', this)">
            </div>

            <!-- Tag & Title -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">الشارة (Tag)</label>
                <input type="text" id="ais-gn-tag" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">العنوان الرئيسي</label>
                <input type="text" id="ais-gn-title" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr; gap:10px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">النص الملون بالعنوان (Highlight)</label>
                <input type="text" id="ais-gn-highlight" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">الوصف الفرعي</label>
                <textarea id="ais-gn-desc" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; resize:vertical;"></textarea>
              </div>
            </div>

            <!-- Stats 3 -->
            <div style="border-top:1px solid var(--border); padding-top:10px;">
              <label style="font-size:13px; font-weight:800; color:var(--text); display:block; margin-bottom:8px;">📊 الأرقام والإحصائيات (3 أرقام)</label>
              <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:8px;">
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border);">
                  <input type="text" id="ais-gn-stat-num-0" placeholder="50+" style="width:100%; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:800; margin-bottom:4px;">
                  <input type="text" id="ais-gn-stat-lbl-0" placeholder="دولة مصدر" style="width:100%; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:11px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border);">
                  <input type="text" id="ais-gn-stat-num-1" placeholder="500+" style="width:100%; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:800; margin-bottom:4px;">
                  <input type="text" id="ais-gn-stat-lbl-1" placeholder="مورد موثوق" style="width:100%; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:11px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border);">
                  <input type="text" id="ais-gn-stat-num-2" placeholder="50K+" style="width:100%; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:800; margin-bottom:4px;">
                  <input type="text" id="ais-gn-stat-lbl-2" placeholder="منتج مستورد" style="width:100%; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:11px;">
                </div>
              </div>
            </div>

            <!-- Cards 4 -->
            <div style="border-top:1px solid var(--border); padding-top:10px;">
              <label style="font-size:13px; font-weight:800; color:var(--text); display:block; margin-bottom:8px;">💎 كروت المميزات الأربعة (Cards)</label>
              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; flex-direction:column; gap:4px;">
                  <div style="display:flex; gap:4px;">
                    <input type="text" id="ais-gn-card-icon-0" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                    <input type="text" id="ais-gn-card-title-0" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  </div>
                  <input type="text" id="ais-gn-card-desc-0" style="width:100%; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:11px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; flex-direction:column; gap:4px;">
                  <div style="display:flex; gap:4px;">
                    <input type="text" id="ais-gn-card-icon-1" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                    <input type="text" id="ais-gn-card-title-1" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  </div>
                  <input type="text" id="ais-gn-card-desc-1" style="width:100%; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:11px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; flex-direction:column; gap:4px;">
                  <div style="display:flex; gap:4px;">
                    <input type="text" id="ais-gn-card-icon-2" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                    <input type="text" id="ais-gn-card-title-2" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  </div>
                  <input type="text" id="ais-gn-card-desc-2" style="width:100%; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:11px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; flex-direction:column; gap:4px;">
                  <div style="display:flex; gap:4px;">
                    <input type="text" id="ais-gn-card-icon-3" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                    <input type="text" id="ais-gn-card-title-3" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  </div>
                  <input type="text" id="ais-gn-card-desc-3" style="width:100%; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:11px;">
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Direct Import Section -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="border-bottom:1px solid var(--border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
              <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--blue);">📦 2. قسم: منتجات أصلية من المصنع لباب بيتك</h3>
              <span style="font-size:12px; color:var(--text3);">صورة 3D والمميزات</span>
            </div>

            <!-- Illustration Image -->
            <div>
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">الصورة التوضيحية (3D Illustration)</label>
              <div style="width:100%; height:180px; border-radius:10px; overflow:hidden; border:1px solid var(--border); background:#0f172a; display:flex; align-items:center; justify-content:center; margin-bottom:8px;">
                <img id="ais-di-img-preview" src="assets/import_direct_3d.jpg" alt="Preview" style="width:100%; height:100%; object-fit:cover;" />
              </div>
              <input type="text" id="ais-di-img" oninput="adminImportSections.updatePreview('di-img', this.value)" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:6px;">
              <button type="button" class="btn" style="background:var(--bg); border:1px solid var(--border); color:var(--text); padding:7px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:700; width:100%;" onclick="document.getElementById('ais-di-img-file').click()">
                📷 رفع صورة توضيحية جديدة
              </button>
              <input type="file" id="ais-di-img-file" accept="image/*" style="display:none;" onchange="adminImportSections.handleUpload('di-img', this)">
            </div>

            <!-- Floating Badge On Image -->
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:8px; padding:10px;">
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">الشارة العائمة فوق الصورة (Badge)</label>
              <div style="display:grid; grid-template-columns: 50px 1fr 1fr; gap:6px;">
                <input type="text" id="ais-di-badge-icon" placeholder="✈️" style="text-align:center; padding:6px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                <input type="text" id="ais-di-badge-title" placeholder="شحن يومي" style="padding:6px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                <input type="text" id="ais-di-badge-desc" placeholder="من جميع الدول" style="padding:6px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:12px;">
              </div>
            </div>

            <!-- Tag & Title -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">الشارة (Tag)</label>
                <input type="text" id="ais-di-tag" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">العنوان الرئيسي</label>
                <input type="text" id="ais-di-title" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr; gap:10px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">النص المائل/المميز بالعنوان (Highlight)</label>
                <input type="text" id="ais-di-highlight" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">الوصف الفرعي</label>
                <textarea id="ais-di-desc" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; resize:vertical;"></textarea>
              </div>
            </div>

            <!-- Features 3 -->
            <div style="border-top:1px solid var(--border); padding-top:10px;">
              <label style="font-size:13px; font-weight:800; color:var(--text); display:block; margin-bottom:8px;">⭐ نقاط ومميزات الاستيراد (3 نقاط)</label>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; gap:6px; align-items:center;">
                  <input type="text" id="ais-di-feat-icon-0" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                  <input type="text" id="ais-di-feat-title-0" style="width:140px; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  <input type="text" id="ais-di-feat-desc-0" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:12px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; gap:6px; align-items:center;">
                  <input type="text" id="ais-di-feat-icon-1" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                  <input type="text" id="ais-di-feat-title-1" style="width:140px; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  <input type="text" id="ais-di-feat-desc-1" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:12px;">
                </div>
                <div style="background:var(--bg3); padding:8px; border-radius:8px; border:1px solid var(--border); display:flex; gap:6px; align-items:center;">
                  <input type="text" id="ais-di-feat-icon-2" style="width:40px; text-align:center; padding:5px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                  <input type="text" id="ais-di-feat-title-2" style="width:140px; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-weight:700; font-size:12px;">
                  <input type="text" id="ais-di-feat-desc-2" style="flex:1; padding:5px 8px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text2); font-size:12px;">
                </div>
              </div>
            </div>

            <!-- Button CTA -->
            <div style="border-top:1px solid var(--border); padding-top:10px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">نص الزر (CTA)</label>
                <input type="text" id="ais-di-btn-text" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">رابط الزر</label>
                <input type="text" id="ais-di-btn-link" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
            </div>
          </div>
        </div>

        <div style="margin-top:25px; display:flex; justify-content:flex-end; gap:12px;">
          <button class="btn-add" onclick="adminImportSections.resetDefault()" style="background:var(--bg3); border:1px solid var(--border); color:var(--text); padding:12px 20px;">🔄 استعادة الافتراضي</button>
          <button class="btn-add" onclick="adminImportSections.save()" style="background:var(--blue); font-weight:800; padding:12px 28px;">💾 حفظ وتطبيق التغييرات فوراً</button>
        </div>
      </div>

      <!-- ══ FOOTER & SOCIAL MEDIA MANAGER ══ -->
      <div class="page" id="page-footer-settings">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">الإعدادات <span>›</span> إعدادات الفوتر والتواصل الاجتماعي</div>
            <h1 class="page-title">إدارة الفوتر والتواصل الاجتماعي</h1>
            <p class="page-sub">تعديل روابط مواقع التواصل الاجتماعي، تطبيقات الهواتف، نصوص الفوتر وحقوق الملكية</p>
          </div>
          <div>
            <button class="btn-add" onclick="adminFooterSettings.save()">💾 حفظ التغييرات</button>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:25px; margin-top:20px;">
          <!-- Social Media -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="border-bottom:1px solid var(--border); padding-bottom:12px;">
              <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--blue);">📱 روابط التواصل الاجتماعي</h3>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">📘 فيسبوك (Facebook URL)</label>
                <input type="url" id="footer-fb-url" placeholder="https://facebook.com/yourpage" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; direction:ltr;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">📸 انستغرام (Instagram URL)</label>
                <input type="url" id="footer-ig-url" placeholder="https://instagram.com/yourpage" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; direction:ltr;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">🎵 تيك توك (TikTok URL)</label>
                <input type="url" id="footer-tt-url" placeholder="https://tiktok.com/@yourpage" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; direction:ltr;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">▶️ يوتيوب (YouTube URL)</label>
                <input type="url" id="footer-yt-url" placeholder="https://youtube.com/@yourchannel" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; direction:ltr;">
              </div>
            </div>

            <div style="border-top:1px solid var(--border); padding-top:16px; display:flex; flex-direction:column; gap:12px;">
              <h4 style="margin:0; font-size:14px; font-weight:800; color:var(--text);">📲 روابط تطبيقات الهواتف</h4>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">🍎 رابط App Store</label>
                <input type="url" id="footer-appstore-url" placeholder="https://apps.apple.com/app/..." style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; direction:ltr;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">🤖 رابط Google Play</label>
                <input type="url" id="footer-gplay-url" placeholder="https://play.google.com/store/apps/..." style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; direction:ltr;">
              </div>
            </div>
          </div>

          <!-- Texts -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="border-bottom:1px solid var(--border); padding-bottom:12px;">
              <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--blue);">✏️ نصوص الفوتر</h3>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">📝 نبذة عن المتجر (About Text)</label>
                <textarea id="footer-about-admin" rows="4" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; resize:vertical;">متجرك المفضل لكل شيء. نقدم آلاف المنتجات بأفضل الأسعار مع توصيل سريع وخدمة عملاء استثنائية.</textarea>
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">©️ نص حقوق الملكية (Copyright)</label>
                <input type="text" id="footer-copyright-admin" placeholder="© جميع الحقوق محفوظة – إستوردلي 2024" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
              </div>
            </div>

            <!-- Preview Card -->
            <div style="border-top:1px solid var(--border); padding-top:16px;">
              <h4 style="margin:0 0 10px 0; font-size:13px; font-weight:800; color:var(--text3);">👁️ معاينة سريعة للفوتر</h4>
              <div style="background:var(--bg3); border:1px solid var(--border); border-radius:10px; padding:16px;">
                <p id="footer-preview-about" style="font-size:12px; color:var(--text3); margin:0 0 12px 0; line-height:1.6;"></p>
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
                  <span style="background:var(--bg); border:1px solid var(--border); padding:5px 10px; border-radius:6px; font-size:11px; color:var(--text2);">📘 FB</span>
                  <span style="background:var(--bg); border:1px solid var(--border); padding:5px 10px; border-radius:6px; font-size:11px; color:var(--text2);">📸 IG</span>
                  <span style="background:var(--bg); border:1px solid var(--border); padding:5px 10px; border-radius:6px; font-size:11px; color:var(--text2);">🎵 TT</span>
                  <span style="background:var(--bg); border:1px solid var(--border); padding:5px 10px; border-radius:6px; font-size:11px; color:var(--text2);">▶️ YT</span>
                </div>
                <p id="footer-preview-copyright" style="font-size:11px; color:var(--text3); margin:0;"></p>
              </div>
            </div>

            <button class="btn-add" onclick="adminFooterSettings.save()" style="margin-top:auto;">💾 حفظ إعدادات الفوتر</button>
          </div>
        </div>
      </div>

      <!-- ══ IMPORT COUNTRIES MANAGER ══ -->
      <div class="page" id="page-import-countries">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> دول الاستيراد</div>
            <h1 class="page-title">إدارة قسم "نستورد من الأفضل"</h1>
            <p class="page-sub">إضافة وتعديل وحذف الدول التي تظهر في قسم مصادر الاستيراد في الصفحة الرئيسية</p>
          </div>
          <div style="display:flex; gap:10px;">
            <button class="btn-add" onclick="adminImportCountries.addCountry()" style="background:var(--green);">+ إضافة دولة</button>
            <button class="btn-add" onclick="adminImportCountries.save()">💾 حفظ التغييرات</button>
          </div>
        </div>

        <!-- Section Settings -->
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:18px; margin-top:20px; display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">عنوان القسم</label>
            <input type="text" id="import-sec-title" placeholder="نستورد من الأفضل 🌏" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
          </div>
          <div>
            <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:5px;">وصف القسم</label>
            <input type="text" id="import-sec-subtitle" placeholder="شراكات مع كبرى المصانع والموردين حول العالم" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
          </div>
        </div>

        <!-- Countries List -->
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:12px;">
            <h3 style="margin:0; font-size:15px; font-weight:800;">🌍 قائمة الدول</h3>
            <small style="color:var(--text3);">اضغط على العلم أو الاسم لتعديله مباشرة</small>
          </div>
          <div id="admin-import-countries-builder" style="display:flex; flex-direction:column; gap:10px;">
            <div style="text-align:center; padding:30px; color:var(--text3);">جاري التحميل...</div>
          </div>
        </div>
      </div>

      <!-- ══ POPUP BANNER MANAGER ══ -->
      <div class="page" id="page-popup-banner">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> البنر الترحيبي المنبثق</div>
            <h1 class="page-title">إدارة البنر الترحيبي والتنبيهي المنبثق</h1>
            <p class="page-sub">عرض نافذة إشعار أو تنبيه أو ترحيب أو عرض ترويجي منبثق للزائر فور فتح المتجر، مع إمكانية الإغلاق بسهولة بالضغط على زر ✕ أو في أي مكان خارج النافذة.</p>
          </div>
          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn-add" onclick="adminPopupBanner.testPopup()" style="background:var(--bg3); border:1px solid var(--border); color:var(--text);">👁️ معاينة فورية</button>
            <button class="btn-add" onclick="adminPopupBanner.save()" style="background:var(--blue); font-weight:800;">💾 حفظ التغييرات</button>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap:25px; margin-top:20px;">
          <!-- 1. Form Settings -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:18px;">
            <div style="border-bottom:1px solid var(--border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
              <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--blue);">⚙️ خيارات ومحتوى البنر</h3>
              <span style="font-size:12px; color:var(--text3);">التحكم بالعرض والنصوص</span>
            </div>

            <!-- Enable / Disable Switch -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px; background:var(--bg3); border-radius:10px; border:1px solid var(--border);">
              <div>
                <strong style="display:block; font-size:14px; color:var(--text);">حالة البنر المنبثق</strong>
                <small style="color:var(--text3); font-size:12px;">تفعيل أو تعطيل ظهور البنر لزوار الموقع</small>
              </div>
              <label style="position:relative; display:inline-block; width:50px; height:26px; cursor:pointer;">
                <input type="checkbox" id="apb-enabled" onchange="adminPopupBanner.updateLivePreview()" style="opacity:0; width:0; height:0;">
                <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:var(--bg); border:1px solid var(--border2); transition:.3s; border-radius:34px;" id="apb-enabled-slider"></span>
              </label>
            </div>

            <!-- Show Once Switch -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px; background:var(--bg3); border-radius:10px; border:1px solid var(--border);">
              <div>
                <strong style="display:block; font-size:14px; color:var(--text);">إظهار مرة واحدة في كل جلسة</strong>
                <small style="color:var(--text3); font-size:12px;">عدم إظهاره مجدداً لنفس الزائر بعد الإغلاق حتى يغلق المتصفح</small>
              </div>
              <label style="position:relative; display:inline-block; width:50px; height:26px; cursor:pointer;">
                <input type="checkbox" id="apb-show-once" style="opacity:0; width:0; height:0;">
                <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:var(--bg); border:1px solid var(--border2); transition:.3s; border-radius:34px;" id="apb-once-slider"></span>
              </label>
            </div>

            <!-- Tag / Badge -->
            <div>
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">الشارة العلوية (Badge / Tag)</label>
              <input type="text" id="apb-tag" placeholder="مثال: تنبيه هام 📢 أو عرض خاص 🔥" oninput="adminPopupBanner.updateLivePreview()" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
            </div>

            <!-- Title -->
            <div>
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">العنوان الرئيسي للبنر <span style="color:var(--red)">*</span></label>
              <input type="text" id="apb-title" placeholder="مثال: أهلاً بكم في متجر استوردلي" oninput="adminPopupBanner.updateLivePreview()" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; font-weight:700;">
            </div>

            <!-- Message / Notes Text -->
            <div>
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">نص الإشعار / الملاحظة / المحتوى <span style="color:var(--red)">*</span></label>
              <textarea id="apb-message" rows="4" placeholder="اكتب هنا أي ملاحظة، تنبيه، تفاصيل العرض، مواعيد العمل، إلخ..." oninput="adminPopupBanner.updateLivePreview()" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; resize:vertical; font-size:13px; line-height:1.6;"></textarea>
            </div>

            <!-- Image (Optional) -->
            <div style="border-top:1px solid var(--border); padding-top:14px;">
              <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:6px;">صورة توضيحية للبنر (اختياري)</label>
              <div style="display:flex; gap:8px; margin-bottom:8px;">
                <input type="text" id="apb-image" placeholder="رابط الصورة أو ارفع من جهازك" oninput="adminPopupBanner.updateLivePreview()" style="flex:1; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; font-size:12px;">
                <button type="button" class="btn" style="background:var(--blue-bg); border:1px solid var(--blue); color:var(--blue); padding:7px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:700; white-space:nowrap;" onclick="document.getElementById('apb-img-file').click()">
                  📷 رفع صورة
                </button>
                <button type="button" class="btn" style="background:var(--red-bg); border:1px solid rgba(239,68,68,.3); color:var(--red); padding:7px 10px; border-radius:6px; cursor:pointer; font-size:12px;" onclick="adminPopupBanner.clearImage()" title="حذف الصورة">
                  ✕
                </button>
              </div>
              <input type="file" id="apb-img-file" accept="image/*" style="display:none;" onchange="adminPopupBanner.handleUpload(this)">
            </div>

            <!-- CTA Button (Optional) -->
            <div style="border-top:1px solid var(--border); padding-top:14px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">نص زر الإجراء (اختياري)</label>
                <input type="text" id="apb-btn-text" placeholder="مثال: تصفح العروض" oninput="adminPopupBanner.updateLivePreview()" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; font-size:12px;">
              </div>
              <div>
                <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">رابط الزر (Link)</label>
                <input type="text" id="apb-btn-link" placeholder="مثال: shop.html" oninput="adminPopupBanner.updateLivePreview()" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; font-size:12px;">
              </div>
            </div>

            <div style="margin-top:10px; display:flex; justify-content:flex-end; gap:10px;">
              <button class="btn-add" onclick="adminPopupBanner.resetDefault()" style="background:var(--bg3); border:1px solid var(--border); color:var(--text); padding:10px 16px;">🔄 الافتراضي</button>
              <button class="btn-add" onclick="adminPopupBanner.save()" style="background:var(--blue); font-weight:800; padding:10px 24px;">💾 حفظ الإعدادات</button>
            </div>
          </div>

          <!-- 2. Live Mockup Preview -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:14px; padding:22px; display:flex; flex-direction:column; gap:16px;">
            <div style="border-bottom:1px solid var(--border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
              <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--green);">📱 المعاينة الحية للنافذة المنبثقة</h3>
              <span id="apb-preview-status" style="font-size:11px; padding:3px 8px; border-radius:6px; background:var(--green-bg); color:var(--green); font-weight:700;">مفعل</span>
            </div>

            <!-- Mockup Screen Container with dark backdrop blur -->
            <div style="position:relative; width:100%; min-height:460px; background:radial-gradient(circle at center, #111827 0%, #030712 100%); border-radius:12px; border:1px solid var(--border2); display:flex; align-items:center; justify-content:center; padding:20px; overflow:hidden; box-shadow:inset 0 0 30px rgba(0,0,0,0.8);">
              
              <!-- Mockup Dismissable Card -->
              <div id="apb-mockup-card" style="width:100%; max-width:380px; background:rgba(17, 24, 39, 0.95); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(59, 130, 246, 0.35); border-radius:20px; padding:22px; box-shadow:0 25px 60px -15px rgba(0, 0, 0, 0.9), 0 0 35px rgba(59, 130, 246, 0.15); position:relative; text-align:center; animation:modalPop 0.3s ease-out;">
                
                <!-- Mock Close X Button -->
                <div style="position:absolute; top:12px; left:12px; width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:#94a3b8; display:flex; align-items:center; justify-content:center; font-size:14px; cursor:pointer; font-weight:bold;">
                  ✕
                </div>

                <!-- Mock Badge -->
                <div id="apb-mock-tag" style="display:inline-block; padding:4px 12px; border-radius:50px; background:rgba(59, 130, 246, 0.15); border:1px solid rgba(59, 130, 246, 0.3); color:#60a5fa; font-size:11px; font-weight:800; margin-bottom:12px;">
                  تنبيه هام 📢
                </div>

                <!-- Mock Image -->
                <div id="apb-mock-img-wrap" style="width:100%; max-height:160px; border-radius:12px; overflow:hidden; margin-bottom:14px; border:1px solid rgba(255,255,255,0.1); display:none;">
                  <img id="apb-mock-img" src="" alt="Banner" style="width:100%; height:100%; object-fit:cover; display:block;">
                </div>

                <!-- Mock Title -->
                <h4 id="apb-mock-title" style="margin:0 0 10px 0; font-size:17px; font-weight:900; color:#f8fafc; line-height:1.4;">
                  أهلاً بكم في متجر استوردلي
                </h4>

                <!-- Mock Message -->
                <p id="apb-mock-msg" style="margin:0 0 16px 0; font-size:13px; color:#cbd5e1; line-height:1.6; white-space:pre-line; text-align:right;">
                  يسعدنا خدمتكم وتوفير أفضل منتجات الشواء والأدوات الأصلية مع شحن سريع لجميع المدن والمناطق.
                </p>

                <!-- Mock CTA Button -->
                <div id="apb-mock-btn-wrap" style="display:flex; flex-direction:column; gap:8px;">
                  <a id="apb-mock-btn" href="#" onclick="return false;" style="display:block; padding:10px 16px; border-radius:10px; background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff; font-size:13px; font-weight:800; text-decoration:none; box-shadow:0 6px 20px rgba(37,99,235,0.35);">
                    تصفح العروض
                  </a>
                  <button type="button" style="background:transparent; border:none; color:#94a3b8; font-size:12px; cursor:pointer; font-family:inherit; padding:4px;">
                    إغلاق الملاحظة
                  </button>
                </div>

              </div>
            </div>
            
            <small style="color:var(--text3); text-align:center; font-size:12px;">
              💡 يمكن للزائر إغلاق هذه النافذة فوراً عبر الضغط على أيقونة (✕) أو النقر على الخلفية المظلمة أو الضغط على زر ESC بلوحة المفاتيح.
            </small>
          </div>
        </div>
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
            <div class="breadcrumb-admin">المحتوى <span>›</span> أيقونات الصفحة الرئيسية</div>
            <h1 class="page-title">إدارة أيقونات الصفحة الرئيسية</h1>
            <p class="page-sub">تعديل أسماء الأيقونات وصورها، وتحديد المنتجات المرتبطة بكل أيقونة</p>
          </div>
        </div>

        <!-- Section 1: Names and Images Manager -->
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3 style="margin:0; color:var(--text1);">✏️ تعديل أسماء وصور الأيقونات</h3>
                <button class="btn" style="background:var(--blue); color:#fff; border:none; padding:8px 15px; border-radius:6px; cursor:pointer;" onclick="adminHomepageCategories.save()">💾 حفظ الأسماء والصور</button>
            </div>
            <div id="admin-homepage-categories-builder" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:15px;">
                <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text3)">جاري تحميل تصنيفات الأيقونات...</div>
            </div>
        </div>
        
        <!-- Section 2: Product Mapping Manager -->
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3 style="margin:0; color:var(--text1);">🔗 ربط منتجات محددة بالأيقونات (تخصيص يدوي)</h3>
                <button class="btn" style="background:var(--blue); color:#fff; border:none; padding:8px 15px; border-radius:6px; cursor:pointer;" onclick="adminIcons.saveIconsToServer()">💾 حفظ ربط المنتجات</button>
            </div>
            <div id="admin-icons-builder" style="display:flex; flex-direction:column; gap:15px;">
                <div style="text-align:center; padding:40px; color:var(--text3)">جاري تحميل ربط المنتجات...</div>
            </div>
        </div>
      </div>

      <!-- ══ BRANDS MANAGER ══ -->
      <div class="page" id="page-brands">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> إدارة الماركات</div>
            <h1 class="page-title">إدارة ماركات الرئيسية</h1>
            <p class="page-sub">إضافة، تعديل، وترتيب الماركات التجارية التي تظهر في السلايدر</p>
          </div>
          <div>
            <button class="btn-add" onclick="adminBrands.add()" style="background:var(--p); margin-left:10px;">➕ إضافة ماركة</button>
            <button class="btn-add" onclick="adminBrands.save()" style="background:var(--blue)">💾 حفظ التغييرات</button>
          </div>
        </div>
        
        <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:20px; margin-top:20px;">
            <div id="admin-brands-builder" style="display:flex; flex-direction:column; gap:10px; max-width: 600px;">
                <div style="text-align:center; padding:40px; color:var(--text3)">جاري تحميل الماركات...</div>
            </div>
        </div>
      </div>

      <!-- ══ PAGES CONTENT (ABOUT & CONTACT) ══ -->
      <div class="page" id="page-pages-content">
        <div class="page-header">
          <div>
            <div class="breadcrumb-admin">المحتوى <span>›</span> إدارة الصفحات</div>
            <h1 class="page-title">محتوى صفحتي "من نحن" و "اتصل بنا"</h1>
            <p class="page-sub">تعديل كافة النصوص، العناوين، معلومات الاتصال، وأرقام الواتساب والهاتف التي تظهر للزوار</p>
          </div>
          <div>
            <button class="btn-add" onclick="adminPagesContent.save()" style="background:var(--blue); font-weight:800;">💾 حفظ كافة التعديلات</button>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 20px;">
          
          <!-- About Us Section -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:24px;">
            <h2 style="font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
              <span>🌐</span> إعدادات صفحة "من نحن"
            </h2>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">شارة البداية (Badge)</label>
              <input type="text" id="adm-about-badge" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="مثال: بوابتك الأولى للاستيراد المباشر" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">العنوان الرئيسي</label>
              <input type="text" id="adm-about-heading" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="مثال: نحن رواد الاستيراد المباشر من المصانع العالمية" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">النص التعريفي والقصة</label>
              <textarea id="adm-about-desc" rows="4" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="اكتب نبذة عن الشركة..."></textarea>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">عنوان الرؤية</label>
                <input type="text" id="adm-about-vision-title" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" />
              </div>
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">عنوان الرسالة</label>
                <input type="text" id="adm-about-mission-title" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">نص الرؤية</label>
              <textarea id="adm-about-vision-text" rows="2" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);"></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">نص الرسالة</label>
              <textarea id="adm-about-mission-text" rows="2" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);"></textarea>
            </div>
          </div>

          <!-- Contact Us Section -->
          <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:24px;">
            <h2 style="font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
              <span>📞</span> إعدادات صفحة "اتصل بنا" ومعلومات التواصل
            </h2>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">رقم الهاتف المباشر</label>
                <input type="text" id="adm-contact-phone" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="+970 59-000-0000" />
              </div>
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">رقم الواتساب (مع كود الدولة دون +)</label>
                <input type="text" id="adm-contact-whatsapp" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="970590000000" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">البريد الإلكتروني للتواصل</label>
              <input type="email" id="adm-contact-email" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="info@estawredly.com" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">العنوان وموقع المستودعات</label>
              <input type="text" id="adm-contact-address" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="فلسطين - الضفة الغربية / كافة المدن" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">ساعات العمل وأيام الدوام</label>
              <input type="text" id="adm-contact-hours" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="السبت - الخميس: 9:00 ص - 6:00 م" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">نص زر الواتساب</label>
              <input type="text" id="adm-contact-wa-cta" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="تواصل عبر واتساب مباشرة" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">عنوان نموذج المراسلة</label>
              <input type="text" id="adm-contact-form-heading" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);" placeholder="أرسل لنا رسالة أو طلب استيراد خاص" />
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:var(--text2); margin-bottom:6px;">الوصف أسفل العنوان</label>
              <textarea id="adm-contact-desc" rows="2" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text);"></textarea>
            </div>

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
          <button class="btn-add" onclick="saveAllSettings()">💾 حفظ التغييرات</button>
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
             <div class="field" style="margin-top:12px">
               <label>سياسة الشحن</label>
               <textarea id="sett-shipping-policy" style="min-height:80px; width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1); font-family:inherit;"></textarea>
             </div>
             <div class="field" style="margin-top:12px">
               <label>سياسة الارتجاع</label>
               <textarea id="sett-return-policy" style="min-height:80px; width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1); font-family:inherit;"></textarea>
             </div>
            </div>

          <div class="setting-card">
            <h3>🔗 ربط العملة بالرنمينبي الصيني (RMB/CNY)</h3>
            <p style="font-size:12px;color:var(--text3);margin-bottom:12px">اربط أسعار منتجاتك بالشيكل مع سعر الصرف الصيني لحمايتك من تقلبات العملة تلقائياً.</p>
            <div class="setting-row">
              <div class="setting-row-info"><strong>تفعيل ربط الأسعار</strong><small>تطبيق معامل التغير على أسعار المتجر</small></div>
              <label class="toggle"><input type="checkbox" id="sett-currency-enabled" onchange="adminCurrency.toggleFields()"/><div class="toggle-slider"></div></label>
            </div>
            <div class="setting-row">
              <div class="setting-row-info"><strong>التحديث التلقائي</strong><small>جلب سعر الصرف تلقائياً من البورصة</small></div>
              <label class="toggle"><input type="checkbox" id="sett-currency-auto" onchange="adminCurrency.toggleFields()"/><div class="toggle-slider"></div></label>
            </div>
            <div class="field" style="margin-top:12px">
              <label>سعر الصرف الأساسي (Base Exchange Rate)</label>
              <input type="number" id="sett-currency-base" step="0.0001" placeholder="مثال: 0.50" value="0.50"/>
            </div>
            <div class="field" style="margin-top:12px">
              <label>سعر الصرف الحالي (Current Exchange Rate)</label>
              <input type="number" id="sett-currency-current" step="0.0001" placeholder="مثال: 0.51" value="0.50"/>
            </div>
            <div style="margin-top:12px; display:flex; gap:10px;">
              <button class="btn" style="flex:1" id="btn-currency-fetch" onclick="adminCurrency.fetchLive()">🔄 تحديث الآن من البورصة</button>
            </div>
            <div id="currency-last-update" style="font-size:11px; color:var(--text3); margin-top:8px; text-align:center;"></div>
          </div>

          <div class="setting-card">
            <h3>🚚 نصوص التوصيل وبطاقات الثقة</h3>
            <p style="font-size:12px;color:var(--text3);margin-bottom:12px">تحرير نصوص التوصيل والضمان وبطاقات الأمان في صفحة المنتج.</p>
            <div class="field"><label>عنوان التوصيل السريع</label><input type="text" id="sett-delivery-title" placeholder="مثال: وصول سريع خلال 2–5 أيام عمل"/></div>
            <div class="field" style="margin-top:10px"><label>تفاصيل الشحن (العنوان الفرعي)</label><input type="text" id="sett-delivery-subtitle" placeholder="مثال: شحن مجاني للطلبات فوق ₪200"/></div>
            
            <div style="border-top:1px solid var(--border);margin-top:15px;padding-top:15px">
              <strong style="font-size:13px;display:block;margin-bottom:10px">🔒 بطاقة الثقة 1 (🔒 دفع آمن)</strong>
              <div class="setting-row">
                <div class="setting-row-info"><strong>تفعيل ظهور البطاقة</strong></div>
                <label class="toggle"><input type="checkbox" id="sett-badge1-visible" checked/><div class="toggle-slider"></div></label>
              </div>
              <div class="field" style="margin-top:8px"><label>العنوان الرئيسي</label><input type="text" id="sett-badge1-title" value="دفع آمن"/></div>
              <div class="field" style="margin-top:8px"><label>العنوان الفرعي</label><input type="text" id="sett-badge1-subtitle" value="100% مشفر"/></div>
            </div>

            <div style="border-top:1px solid var(--border);margin-top:15px;padding-top:15px">
              <strong style="font-size:13px;display:block;margin-bottom:10px">↩️ بطاقة الثقة 2 (↩️ إرجاع مجاني)</strong>
              <div class="setting-row">
                <div class="setting-row-info"><strong>تفعيل ظهور البطاقة</strong></div>
                <label class="toggle"><input type="checkbox" id="sett-badge2-visible" checked/><div class="toggle-slider"></div></label>
              </div>
              <div class="field" style="margin-top:8px"><label>العنوان الرئيسي</label><input type="text" id="sett-badge2-title" value="إرجاع مجاني"/></div>
              <div class="field" style="margin-top:8px"><label>العنوان الفرعي</label><input type="text" id="sett-badge2-subtitle" value="خلال 30 يوم"/></div>
            </div>

            <div style="border-top:1px solid var(--border);margin-top:15px;padding-top:15px">
              <strong style="font-size:13px;display:block;margin-bottom:10px">🏅 بطاقة الثقة 3 (🏅 منتج أصلي)</strong>
              <div class="setting-row">
                <div class="setting-row-info"><strong>تفعيل ظهور البطاقة</strong></div>
                <label class="toggle"><input type="checkbox" id="sett-badge3-visible" checked/><div class="toggle-slider"></div></label>
              </div>
              <div class="field" style="margin-top:8px"><label>العنوان الرئيسي</label><input type="text" id="sett-badge3-title" value="منتج أصلي"/></div>
              <div class="field" style="margin-top:8px"><label>العنوان الفرعي</label><input type="text" id="sett-badge3-subtitle" value="ضمان الأصالة"/></div>
            </div>
          </div>

          <div class="setting-card">
            <h3>📢 شريط الإعلانات المتحرك</h3>
            <p style="font-size:12px;color:var(--text3);margin-bottom:12px">تحرير شريط التنويه المتحرك الذي يظهر في أعلى الموقع.</p>
            <div class="setting-row">
              <div class="setting-row-info"><strong>تفعيل الشريط</strong><small>إظهار أو إخفاء الشريط في أعلى المتجر</small></div>
              <label class="toggle"><input type="checkbox" id="sett-ticker-enabled"/><div class="toggle-slider"></div></label>
            </div>
            <div class="field" style="margin-top:12px">
              <label>نص الإعلان</label>
              <textarea id="sett-ticker-text" style="min-height:60px; width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1); font-family:inherit;"></textarea>
            </div>
          </div>

          <div class="setting-card">
            <h3>🌍 شريط الدول المستورد منها</h3>
            <p style="font-size:12px;color:var(--text3);margin-bottom:12px">التحكم في شريط "نستورد من" الذي يظهر في الصفحة الرئيسية.</p>
            <div class="setting-row">
              <div class="setting-row-info"><strong>تفعيل الشريط</strong><small>إظهار أو إخفاء شريط الدول المستورد منها</small></div>
              <label class="toggle"><input type="checkbox" id="sett-import-ticker-enabled"/><div class="toggle-slider"></div></label>
            </div>
            <div class="setting-row">
              <div class="setting-row-info"><strong>تفعيل الحركة الدائرية (Marquee)</strong><small>جعل النص يتحرك بشكل مستمر</small></div>
              <label class="toggle"><input type="checkbox" id="sett-import-ticker-marquee"/><div class="toggle-slider"></div></label>
            </div>
            <div class="setting-row">
              <div class="setting-row-info"><strong>عرض زر الإغلاق (X)</strong><small>السماح للزوار بإغلاق الشريط مؤقتاً</small></div>
              <label class="toggle"><input type="checkbox" id="sett-import-ticker-close"/><div class="toggle-slider"></div></label>
            </div>
            <div class="field" style="margin-top:12px">
              <label>عنوان الشريط</label>
              <input type="text" id="sett-import-ticker-label" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1);"/>
            </div>
            <div class="field" style="margin-top:12px">
              <label>قائمة الدول / النص (افصل بينها بـ | )</label>
              <textarea id="sett-import-ticker-text" style="min-height:60px; width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1); font-family:inherit;"></textarea>
            </div>
            <div class="field" style="margin-top:12px">
              <label>سرعة الحركة (بالثواني - كلما قل الرقم زادت السرعة)</label>
              <input type="number" id="sett-import-ticker-speed" min="5" max="120" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background:var(--bg2); color:var(--text1);"/>
            </div>
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
  
  if (id === 'hero')  adminHeroBanner.load();
  if (id === 'import-sections') adminImportSections.load();
  if (id === 'banners')  adminBanners.load();
  if (id === 'popup-banner') adminPopupBanner.load();
  if (id === 'brands')  adminBrands.load();
  if (id === 'pages-content') adminPagesContent.load();
  if (id === 'settings') {
      adminPolicies.load();
      adminCurrency.load();
  }

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
        itemsHtml = o.items.map(i => {
            const qty = i.quantity || i.qty || 1;
            const pcs = i.pieces_per_carton || 1;
            return `<div style="font-size:11px;color:var(--text3);margin-bottom:2px;">• ${i.name} (x${qty} ${pcs > 1 ? 'كرتونة' : 'قطعة'})</div>`;
        }).join('');
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
          <button class="del-btn" onclick="deleteOrder('${o.id}')" title="مسح الطلبية نهائياً" style="background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.3);border-radius:6px;padding:4px 8px;cursor:pointer;font-size:12px;transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">🗑️</button>
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

async function deleteOrder(id) {
  if (!confirm(`هل أنت متأكد من مسح الطلبية رقم #${id} نهائياً؟\nسيتم تحديث الإحصائيات والأرباح فوراً.`)) return;
  try {
    const formData = new FormData();
    formData.append('order_id', id);
    const res = await fetch('api/delete_order.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      if (typeof Store !== 'undefined' && typeof Store.deleteOrder === 'function') {
        Store.deleteOrder(id);
      }
      showToast('🗑️ تم مسح الطلبية بنجاح وتحديث الحسابات');
      await fetchLiveOrders(); // Recalculates stats, profit, and re-renders orders
      if (typeof renderRecentOrders === 'function') renderRecentOrders();
      if (typeof renderBigChart === 'function') renderBigChart();
    } else {
      showToast('❌ خطأ: ' + (data.message || 'فشل مسح الطلبية'));
    }
  } catch (e) {
    console.error("Error deleting order:", e);
    showToast('❌ فشل الاتصال بالخادم لمسح الطلبية');
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
  
  let multiplier = 1;
  if (typeof adminCurrency !== 'undefined' && adminCurrency.settings && adminCurrency.settings.enabled && adminCurrency.settings.base_rate > 0) {
      multiplier = adminCurrency.settings.current_rate / adminCurrency.settings.base_rate;
  }

  grid.innerHTML = list.map(p => {
    const finalPrice = parseFloat((p.price * multiplier).toFixed(2));
    const finalOldPrice = p.oldPrice ? parseFloat((p.oldPrice * multiplier).toFixed(2)) : null;
    const finalCostPrice = p.costPrice ? parseFloat((p.costPrice * multiplier).toFixed(2)) : 0;
    const disc = finalOldPrice ? Math.round((1-finalPrice/finalOldPrice)*100) : 0;
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
            <span class="pac-price-main">₪${finalPrice}</span>
            ${finalOldPrice?`<span class="pac-price-old">₪${finalOldPrice}</span>`:''}
          </div>
          <div style="font-size:11px;color:var(--text3);margin-bottom:8px">
            المربح: <strong style="color:var(--green)">₪${Math.max(0, finalPrice - finalCostPrice).toFixed(2)}</strong>
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


let currentProductVariants = [];
let currentProductImages = [];

function addAdditionalImage(url) {
    if(!url) return;
    currentProductImages.push(url);
    renderAdditionalImages();
}
function addAdditionalImageBase64(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        currentProductImages.push(e.target.result);
        renderAdditionalImages();
    };
    reader.readAsDataURL(input.files[0]);
    input.value = '';
}
function removeAdditionalImage(index) {
    currentProductImages.splice(index, 1);
    renderAdditionalImages();
}
function renderAdditionalImages() {
    const c = document.getElementById('additional-images-container');
    c.innerHTML = currentProductImages.map((img, i) => `
        <div style="position:relative; width:60px; height:60px; border-radius:8px; overflow:hidden; border:1px solid var(--border)">
            <img src="${img}" style="width:100%; height:100%; object-fit:cover;">
            <button type="button" onclick="removeAdditionalImage(${i})" style="position:absolute; top:2px; right:2px; background:red; color:white; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center;">✕</button>
        </div>
    `).join('');
}

function syncVariantsFromDOM() {
    const container = document.getElementById('variants-container');
    if (!container) return;
    const variantBlocks = container.querySelectorAll('.variant-block');
    const newVariants = [];
    variantBlocks.forEach(block => {
        const nameInput = block.querySelector('.v-name-input');
        const name = nameInput ? nameInput.value.trim() : '';
        
        const optSpans = block.querySelectorAll('.v-opt-text');
        const options = Array.from(optSpans).map(span => span.innerText.trim());
        
        const newOptInput = block.querySelector('.v-new-opt-input');
        if (newOptInput && newOptInput.value.trim() !== '') {
            options.push(newOptInput.value.trim());
            newOptInput.value = '';
        }
        
        newVariants.push({ name, options });
    });
    currentProductVariants = newVariants;
}

function addVariantField() {
    syncVariantsFromDOM();
    currentProductVariants.push({ name: '', options: [] });
    renderVariants();
}
function removeVariantField(index) {
    syncVariantsFromDOM();
    currentProductVariants.splice(index, 1);
    renderVariants();
}
function addVariantOption(index, inputEl) {
    const val = inputEl.value.trim();
    if(val) {
        inputEl.value = '';
        syncVariantsFromDOM();
        currentProductVariants[index].options.push(val);
        renderVariants();
        
        // Re-focus the input element for convenience
        setTimeout(() => {
            const inputs = document.querySelectorAll('.v-new-opt-input');
            if (inputs[index]) {
                inputs[index].focus();
            }
        }, 50);
    }
}
function removeVariantOption(vIndex, optIndex) {
    syncVariantsFromDOM();
    currentProductVariants[vIndex].options.splice(optIndex, 1);
    renderVariants();
}
function updateVariantName(index, val) {
    // we don't strictly need this now with syncVariantsFromDOM, but kept for legacy compat if needed
    if(currentProductVariants[index]) currentProductVariants[index].name = val;
}
function renderVariants() {
    const c = document.getElementById('variants-container');
    c.innerHTML = currentProductVariants.map((v, i) => `
        <div class="variant-block" style="background:var(--bg2); padding:15px; border-radius:8px; border:1px solid var(--border); position:relative;">
            <button type="button" onclick="removeVariantField(${i})" style="position:absolute; top:12px; left:12px; color:var(--red); border:none; background:transparent; cursor:pointer; font-size:12px; font-weight:bold;">✕ حذف الخاصية</button>
            <div style="margin-bottom:12px; max-width:80%;">
                <label style="font-size:13px; color:var(--text2); font-weight:bold; display:block; margin-bottom:5px;">اسم الخاصية (مثال: اللون، المقاس)</label>
                <input type="text" class="v-name-input" value="${v.name}" placeholder="مثال: اللون" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:6px; background:var(--bg); color:var(--text1);">
            </div>
            <div>
                <label style="font-size:13px; color:var(--text2); font-weight:bold; display:block; margin-bottom:5px;">الخيارات المتاحة</label>
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
                    ${v.options.map((opt, oi) => `
                        <span style="background:var(--p); color:white; padding:6px 12px; border-radius:6px; font-size:13px; display:inline-flex; align-items:center; gap:8px; font-weight:bold; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                            <span class="v-opt-text">${opt}</span> 
                            <span style="cursor:pointer; font-weight:bold; color:rgba(255,255,255,0.7);" onclick="removeVariantOption(${i}, ${oi})">×</span>
                        </span>
                    `).join('')}
                </div>
                <input type="text" class="v-new-opt-input" placeholder="اكتب خيار واضغط Enter للإضافة..." onkeypress="if(event.key==='Enter') { event.preventDefault(); addVariantOption(${i}, this); }" style="width:100%; padding:10px; border:1px dashed var(--border); border-radius:6px; background:var(--bg); color:var(--text1); font-size:13px;">
            </div>
        </div>
    `).join('');
}

function openModal(p) {
  editingId = p ? p.id : null;
  document.getElementById('modal-title').textContent = p ? '✏️ تعديل المنتج' : '➕ إضافة منتج جديد';
  document.getElementById('f-name').value = p?.name || '';
  document.getElementById('f-cat').value = p?.cat || '';
  
  // Populate Category Datalist from all products & nav
  const datalist = document.getElementById('cats-list');
  if (datalist && Array.isArray(adminProducts)) {
    const existingCats = [...new Set(adminProducts.map(x => x.cat).filter(Boolean))];
    existingCats.forEach(c => {
      if (!datalist.querySelector(`option[value="${c}"]`)) {
        datalist.insertAdjacentHTML('beforeend', `<option value="${c}">`);
      }
    });
  }

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


  let multiplier = 1;
  if (typeof adminCurrency !== 'undefined' && adminCurrency.settings && adminCurrency.settings.enabled && adminCurrency.settings.base_rate > 0) {
      multiplier = adminCurrency.settings.current_rate / adminCurrency.settings.base_rate;
  }

  document.getElementById('f-badge').value = p?.badge || '';
  document.getElementById('f-price').value = p ? parseFloat((p.price * multiplier).toFixed(2)) : '';
  document.getElementById('f-cost-price').value = (p && p.costPrice) ? parseFloat((p.costPrice * multiplier).toFixed(2)) : '';
  document.getElementById('f-old-price').value = (p && p.oldPrice) ? parseFloat((p.oldPrice * multiplier).toFixed(2)) : '';
  document.getElementById('f-stars').value = p?.stars || '';
  document.getElementById('f-reviews').value = p?.reviews || '';
  document.getElementById('f-stock').value = (p && p.stock !== undefined) ? p.stock : '';
  
  // Populate Brand Select Options
  const brandSelect = document.getElementById('f-brand');
  if (brandSelect) {
      brandSelect.innerHTML = '<option value="">بدون ماركة</option>' +
          (adminBrands.brands || []).map(b => {
              const name = typeof b === 'string' ? b : (b.name || '');
              return `<option value="${name}">${name}</option>`;
          }).join('');
  }
  document.getElementById('f-brand').value = p?.brand || '';

  document.getElementById('f-pieces-per-carton').value = p?.pieces_per_carton || '';
  document.getElementById('f-product-code').value = p?.product_code || '';
  document.getElementById('f-factory-code').value = p?.factory_code || '';
  document.getElementById('f-ref-note').value = p?.ref_note || '';

  document.getElementById('f-img-url').value = p?.img || '';
  document.getElementById('f-desc').value = p?.desc || '';

  // Reset and load Images & Variants for this product
  let imagesArr = [];
  if (p && p.images) {
    if (Array.isArray(p.images)) {
      imagesArr = [...p.images];
    } else if (typeof p.images === 'string') {
      try { imagesArr = JSON.parse(p.images); } catch(e) {}
    }
  }
  currentProductImages = Array.isArray(imagesArr) ? imagesArr : [];

  let variantsArr = [];
  if (p && p.variants) {
    if (Array.isArray(p.variants)) {
      variantsArr = JSON.parse(JSON.stringify(p.variants));
    } else if (typeof p.variants === 'string') {
      try { variantsArr = JSON.parse(p.variants); } catch(e) {}
    }
  }
  currentProductVariants = Array.isArray(variantsArr) ? variantsArr : [];

  if (typeof renderAdditionalImages === 'function') renderAdditionalImages();
  if (typeof renderVariants === 'function') renderVariants();

  const prev = document.getElementById('img-preview-el');
  const ph = document.getElementById('upload-placeholder');
  if (p?.img) { prev.src=p.img; prev.style.display='block'; ph.style.display='none'; }
  else { prev.style.display='none'; ph.style.display='block'; }
  document.getElementById('product-modal').classList.add('open');
}
function closeModal() { document.getElementById('product-modal').classList.remove('open'); }

function editProduct(id) {
  const p = adminProducts.find(x => String(x.id) === String(id));
  if (p) { showPage('products',null); openModal(p); }
}

function saveProduct() {
  if(typeof syncVariantsFromDOM === "function") syncVariantsFromDOM();
  const name = document.getElementById('f-name').value.trim();
  
  let multiplier = 1;
  if (typeof adminCurrency !== 'undefined' && adminCurrency.settings && adminCurrency.settings.enabled && adminCurrency.settings.base_rate > 0) {
      multiplier = adminCurrency.settings.current_rate / adminCurrency.settings.base_rate;
  }

  const rawPrice = parseFloat(document.getElementById('f-price').value);
  const price = isNaN(rawPrice) ? NaN : parseFloat((rawPrice / multiplier).toFixed(4));
  const rawCostPrice = parseFloat(document.getElementById('f-cost-price').value) || 0;
  const costPrice = parseFloat((rawCostPrice / multiplier).toFixed(4));
  const rawOldPrice = parseFloat(document.getElementById('f-old-price').value) || null;
  const oldPrice = rawOldPrice ? parseFloat((rawOldPrice / multiplier).toFixed(4)) : null;

  const stockStr = document.getElementById('f-stock').value;
  const stock = stockStr === '' ? null : parseInt(stockStr);
  const cartonPiecesStr = document.getElementById('f-pieces-per-carton').value;
  const piecesVal = parseInt(cartonPiecesStr);
  const pieces_per_carton = isNaN(piecesVal) || piecesVal <= 1 ? 1 : piecesVal;
  const product_code = document.getElementById('f-product-code').value.trim();
  const factory_code = document.getElementById('f-factory-code').value.trim();
  const brand = document.getElementById('f-brand').value;
  
  if (!name || isNaN(price)) { showToast('⚠️ الاسم والسعر مطلوبان!','warn'); return; }

  const pToEdit = editingId ? adminProducts.find(x => String(x.id) === String(editingId)) : null;
  const product = {
    id: editingId || (Date.now()),
    name,
    desc: document.getElementById('f-desc').value.trim() || '',
    cat: document.getElementById('f-cat').value.trim(),
    brand,
    badge: document.getElementById('f-badge').value,
    price,
    costPrice,
    oldPrice,
    stars: parseFloat(document.getElementById('f-stars').value) || 4.5,
    reviews: parseInt(document.getElementById('f-reviews').value) || 0,
    stock: stock,
    pieces_per_carton,
    product_code,
    factory_code,
    ref_note: document.getElementById('f-ref-note').value.trim(),
    tab: 'all',
    img: document.getElementById('f-img-url').value || document.getElementById('img-preview-el').src || 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80',
    active: pToEdit ? pToEdit.active : true,

    images: currentProductImages.length > 0 ? [...currentProductImages] : null,
    variants: currentProductVariants.length > 0 ? JSON.parse(JSON.stringify(currentProductVariants)) : null,
  };

  if (editingId) {
    const idx = adminProducts.findIndex(p => String(p.id) === String(editingId));
    if (idx !== -1) {
      adminProducts[idx] = product;
    } else {
      adminProducts.unshift(product);
    }
    showToast('✅ تم تحديث المنتج بنجاح!');
  } else {
    adminProducts.unshift(product);
    showToast('✅ تم إضافة المنتج للمتجر!');
  }

  saveAdminProducts();
  closeModal();
  renderProducts();
  updateStats();
}

function deleteProduct(id) {
  if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) return;
  adminProducts = adminProducts.filter(p => String(p.id) !== String(id));
  saveAdminProducts();
  renderProducts();
  updateStats();
  showToast('🗑️ تم حذف المنتج');
}

function toggleProduct(id, active) {
  const p = adminProducts.find(x => String(x.id) === String(id));
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
  if(typeof fetchMembershipRequests === "function") fetchMembershipRequests();
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
        itemsHtml = o.items.map(i => {
            const qty = i.quantity || i.qty || 1;
            const pcs = i.pieces_per_carton || 1;
            const originalProduct = (typeof adminProducts !== 'undefined') ? adminProducts.find(p => String(p.id) === String(i.id)) : null;
            const product_code = i.product_code || (originalProduct ? originalProduct.product_code : '') || '';
            const factory_code = i.factory_code || (originalProduct ? originalProduct.factory_code : '') || '';
            const ref_note = originalProduct ? (originalProduct.ref_note || '') : '';
            const codeInfo = [
                product_code ? `كود المنتج: ${product_code}` : '',
                factory_code ? `كود المصنع: ${factory_code}` : '',
                ref_note ? `الرقم المرجعي: ${ref_note}` : ''
            ].filter(Boolean).join(' | ');
            return `
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #ddd;">
                        ${i.name}
                        ${pcs > 1 ? `<br><small style="color:#666;">(كرتونة تحتوي على ${pcs} قطع - إجمالي: ${qty * pcs} قطعة)</small>` : ''}
                        ${codeInfo ? `<br><small style="color:#4361ee;font-weight:bold;">${codeInfo}</small>` : ''}
                    </td>
                    <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">${qty} ${pcs > 1 ? 'كرتونة' : 'قطعة'}</td>
                    <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">₪${i.price}</td>
                    <td style="padding:10px;border-bottom:1px solid #ddd;text-align:center;">₪${(i.price * qty * pcs).toLocaleString()}</td>
                </tr>
            `;
        }).join('');
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
        itemsHtml = o.items.map(i => {
            const qty = i.quantity || i.qty || 1;
            const pcs = i.pieces_per_carton || 1;
            const originalProduct = (typeof adminProducts !== 'undefined') ? adminProducts.find(p => String(p.id) === String(i.id)) : null;
            const product_code = i.product_code || (originalProduct ? originalProduct.product_code : '') || '';
            const factory_code = i.factory_code || (originalProduct ? originalProduct.factory_code : '') || '';
            const ref_note = originalProduct ? (originalProduct.ref_note || '') : '';
            const codeInfo = [
                product_code ? `كود المنتج: ${product_code}` : '',
                factory_code ? `كود المصنع: ${factory_code}` : '',
                ref_note ? `الرقم المرجعي: ${ref_note}` : ''
            ].filter(Boolean).join(' | ');
            return `
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <div>
                      <strong>${i.name}</strong> <span style="color:var(--text3)">(x${qty} ${pcs > 1 ? 'كرتونة' : 'قطعة'})</span>
                      ${pcs > 1 ? `<div style="font-size:11px; color:#166534; margin-top:2px;">(كرتونة تحتوي على ${pcs} قطع - إجمالي: ${qty * pcs} قطعة)</div>` : ''}
                      ${codeInfo ? `<div style="font-size:12px; color:var(--p); margin-top:2px; font-weight:bold;">${codeInfo}</div>` : ''}
                      ${i.selectedVariants && Object.keys(i.selectedVariants).length > 0 ? `<div style="font-size:12px; color:var(--text3); margin-top:2px;">` + Object.entries(i.selectedVariants).map(([k,v]) => `${k}: ${v}`).join(' | ') + `</div>` : ''}
                    </div>
                    <div>₪${((i.price || 0) * qty * pcs).toFixed(2)}</div>
                </div>
            `;
        }).join('');
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
// HERO BANNER MANAGER (DESKTOP & MOBILE)
// ==========================================

const adminHeroBanner = {
    data: {
        desktop_image: 'assets/hero_banner_import.png',
        mobile_image: 'assets/hero_banner_import_mobile.png',
        link: 'contact.html',
        alt_text: 'إستوردلي - نستورد لك ما تحتاجه من المصدر'
    },

    async load() {
        try {
            const res = await fetch('api/get_hero_banner.php?t=' + Date.now());
            const json = await res.json();
            if (json && typeof json === 'object') {
                this.data = {
                    desktop_image: json.desktop_image || 'assets/hero_banner_import.png',
                    mobile_image: json.mobile_image || 'assets/hero_banner_import_mobile.png',
                    link: json.link || 'contact.html',
                    alt_text: json.alt_text || 'إستوردلي - نستورد لك ما تحتاجه من المصدر'
                };
            }
            this.populate();
        } catch(e) {
            console.error('Failed to load hero banner', e);
        }
    },

    populate() {
        const dImg = document.getElementById('hero-desktop-img');
        const mImg = document.getElementById('hero-mobile-img');
        const link = document.getElementById('hero-link');
        const alt = document.getElementById('hero-alt');
        const dPrev = document.getElementById('hero-preview-desktop');
        const mPrev = document.getElementById('hero-preview-mobile');

        if (dImg) dImg.value = this.data.desktop_image;
        if (mImg) mImg.value = this.data.mobile_image;
        if (link) link.value = this.data.link;
        if (alt) alt.value = this.data.alt_text;

        if (dPrev) dPrev.src = this.data.desktop_image;
        if (mPrev) mPrev.src = this.data.mobile_image;
    },

    updatePreview(type, val) {
        if (type === 'desktop') {
            this.data.desktop_image = val;
            const p = document.getElementById('hero-preview-desktop');
            if (p) p.src = val;
        } else if (type === 'mobile') {
            this.data.mobile_image = val;
            const p = document.getElementById('hero-preview-mobile');
            if (p) p.src = val;
        }
    },

    handleUpload(type, input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const base64 = e.target.result;
            if (type === 'desktop') {
                this.data.desktop_image = base64;
                const dImg = document.getElementById('hero-desktop-img');
                const dPrev = document.getElementById('hero-preview-desktop');
                if (dImg) dImg.value = base64;
                if (dPrev) dPrev.src = base64;
            } else if (type === 'mobile') {
                this.data.mobile_image = base64;
                const mImg = document.getElementById('hero-mobile-img');
                const mPrev = document.getElementById('hero-preview-mobile');
                if (mImg) mImg.value = base64;
                if (mPrev) mPrev.src = base64;
            }
        };
        reader.readAsDataURL(input.files[0]);
    },

    resetDefault() {
        if (confirm('هل تريد استعادة صور وروابط الغلاف الأصلية الافتراضية؟')) {
            this.data = {
                desktop_image: 'assets/hero_banner_import.png',
                mobile_image: 'assets/hero_banner_import_mobile.png',
                link: 'contact.html',
                alt_text: 'إستوردلي - نستورد لك ما تحتاجه من المصدر'
            };
            this.populate();
        }
    },

    async save() {
        const dImg = document.getElementById('hero-desktop-img');
        const mImg = document.getElementById('hero-mobile-img');
        const link = document.getElementById('hero-link');
        const alt = document.getElementById('hero-alt');

        this.data.desktop_image = dImg ? dImg.value : this.data.desktop_image;
        this.data.mobile_image = mImg ? mImg.value : this.data.mobile_image;
        this.data.link = link ? link.value : this.data.link;
        this.data.alt_text = alt ? alt.value : this.data.alt_text;

        try {
            const res = await fetch('api/save_hero_banner.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.data)
            });
            const data = await res.json();
            if (data.success) {
                alert('✅ تم حفظ وتحديث غلاف الرئيسية بنجاح!');
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم لحفظ الغلاف!');
        }
    }
};

const adminImportSections = {
    data: null,
    
    async load() {
        try {
            const res = await fetch('api/get_import_sections.php?t=' + Date.now());
            this.data = await res.json();
            if (!this.data || !this.data.global_network) {
                this.resetDefault(false);
            } else {
                this.populate();
            }
        } catch (e) {
            console.error("Error loading import sections:", e);
        }
    },
    
    populate() {
        if (!this.data) return;
        const gn = this.data.global_network || {};
        const di = this.data.direct_import || {};
        
        function setVal(id, val) {
            const el = document.getElementById(id);
            if (el) el.value = (val !== undefined && val !== null) ? val : '';
        }
        
        // Section 1
        setVal('ais-gn-bg', gn.bg_image || '');
        setVal('ais-gn-tag', gn.tag || '');
        setVal('ais-gn-title', gn.title || '');
        setVal('ais-gn-highlight', gn.title_highlight || '');
        setVal('ais-gn-desc', gn.desc || '');
        
        this.updatePreview('gn-bg', gn.bg_image || '');
        
        // Stats
        if (Array.isArray(gn.stats)) {
            gn.stats.forEach((s, idx) => {
                setVal(`ais-gn-stat-num-${idx}`, s.num || '');
                setVal(`ais-gn-stat-lbl-${idx}`, s.label || '');
            });
        }
        
        // Cards
        if (Array.isArray(gn.cards)) {
            gn.cards.forEach((c, idx) => {
                setVal(`ais-gn-card-icon-${idx}`, c.icon || '');
                setVal(`ais-gn-card-title-${idx}`, c.title || '');
                setVal(`ais-gn-card-desc-${idx}`, c.desc || '');
            });
        }
        
        // Section 2
        setVal('ais-di-img', di.image || '');
        setVal('ais-di-tag', di.tag || '');
        setVal('ais-di-title', di.title || '');
        setVal('ais-di-highlight', di.title_highlight || '');
        setVal('ais-di-desc', di.desc || '');
        setVal('ais-di-badge-icon', di.badge_icon || '');
        setVal('ais-di-badge-title', di.badge_title || '');
        setVal('ais-di-badge-desc', di.badge_desc || '');
        setVal('ais-di-btn-text', di.btn_text || '');
        setVal('ais-di-btn-link', di.btn_link || '');
        
        this.updatePreview('di-img', di.image || '');
        
        // Features
        if (Array.isArray(di.features)) {
            di.features.forEach((f, idx) => {
                setVal(`ais-di-feat-icon-${idx}`, f.icon || '');
                setVal(`ais-di-feat-title-${idx}`, f.title || '');
                setVal(`ais-di-feat-desc-${idx}`, f.desc || '');
            });
        }
    },
    
    updatePreview(type, url) {
        if (type === 'gn-bg') {
            const el = document.getElementById('ais-gn-bg-preview');
            if (el) {
                el.style.backgroundImage = url ? `url('${url}')` : 'none';
            }
        } else if (type === 'di-img') {
            const el = document.getElementById('ais-di-img-preview');
            if (el) {
                el.src = url || '';
                el.style.display = url ? 'block' : 'none';
            }
        }
    },
    
    handleUpload(type, input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const base64 = e.target.result;
            if (type === 'gn-bg') {
                const textInput = document.getElementById('ais-gn-bg');
                if (textInput) textInput.value = base64;
                this.updatePreview('gn-bg', base64);
            } else if (type === 'di-img') {
                const textInput = document.getElementById('ais-di-img');
                if (textInput) textInput.value = base64;
                this.updatePreview('di-img', base64);
            }
        };
        reader.readAsDataURL(input.files[0]);
    },
    
    collectData() {
        function getVal(id) {
            const el = document.getElementById(id);
            return el ? el.value.trim() : '';
        }
        
        return {
            global_network: {
                tag: getVal('ais-gn-tag'),
                title: getVal('ais-gn-title'),
                title_highlight: getVal('ais-gn-highlight'),
                desc: getVal('ais-gn-desc'),
                bg_image: getVal('ais-gn-bg'),
                stats: [
                    { num: getVal('ais-gn-stat-num-0'), label: getVal('ais-gn-stat-lbl-0') },
                    { num: getVal('ais-gn-stat-num-1'), label: getVal('ais-gn-stat-lbl-1') },
                    { num: getVal('ais-gn-stat-num-2'), label: getVal('ais-gn-stat-lbl-2') }
                ],
                cards: [
                    { icon: getVal('ais-gn-card-icon-0'), title: getVal('ais-gn-card-title-0'), desc: getVal('ais-gn-card-desc-0') },
                    { icon: getVal('ais-gn-card-icon-1'), title: getVal('ais-gn-card-title-1'), desc: getVal('ais-gn-card-desc-1') },
                    { icon: getVal('ais-gn-card-icon-2'), title: getVal('ais-gn-card-title-2'), desc: getVal('ais-gn-card-desc-2') },
                    { icon: getVal('ais-gn-card-icon-3'), title: getVal('ais-gn-card-title-3'), desc: getVal('ais-gn-card-desc-3') }
                ]
            },
            direct_import: {
                tag: getVal('ais-di-tag'),
                title: getVal('ais-di-title'),
                title_highlight: getVal('ais-di-highlight'),
                desc: getVal('ais-di-desc'),
                image: getVal('ais-di-img'),
                badge_icon: getVal('ais-di-badge-icon'),
                badge_title: getVal('ais-di-badge-title'),
                badge_desc: getVal('ais-di-badge-desc'),
                features: [
                    { icon: getVal('ais-di-feat-icon-0'), title: getVal('ais-di-feat-title-0'), desc: getVal('ais-di-feat-desc-0') },
                    { icon: getVal('ais-di-feat-icon-1'), title: getVal('ais-di-feat-title-1'), desc: getVal('ais-di-feat-desc-1') },
                    { icon: getVal('ais-di-feat-icon-2'), title: getVal('ais-di-feat-title-2'), desc: getVal('ais-di-feat-desc-2') }
                ],
                btn_text: getVal('ais-di-btn-text'),
                btn_link: getVal('ais-di-btn-link')
            }
        };
    },
    
    async save() {
        const payload = this.collectData();
        try {
            const res = await fetch('api/save_import_sections.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                this.data = payload;
                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم لحفظ أقسام الاستيراد!');
        }
    },
    
    resetDefault(doConfirm = true) {
        if (doConfirm && !confirm('هل أنت متأكد من استعادة النصوص والصور الافتراضية لأقسام الاستيراد؟')) return;
        this.data = {
            global_network: {
                tag: "🌐 شبكة استيراد عالمية",
                title: "نجلب لك أفضل المنتجات",
                title_highlight: "من كل أنحاء العالم",
                desc: "منتجاتنا مستوردة مباشرة من أكثر من 50 دولة، بأعلى معايير الجودة وأفضل الأسعار",
                bg_image: "import_world.jpg",
                stats: [
                    { num: "50+", label: "دولة مصدر" },
                    { num: "500+", label: "مورد موثوق" },
                    { num: "50K+", label: "منتج مستورد" }
                ],
                cards: [
                    { icon: "🔍", title: "فحص الجودة", desc: "كل منتج مفحوص قبل الإرسال" },
                    { icon: "🚢", title: "شحن دولي", desc: "وصول ضمان في 15–30 يوم" },
                    { icon: "🏷️", title: "سعر المصنع", desc: "بدون وسطاء – مباشرة لك" },
                    { icon: "🔒", title: "ضمان الأصالة", desc: "100% منتجات أصلية مضمونة" }
                ]
            },
            direct_import: {
                tag: "📦 مستورد مباشرة",
                title: "منتجات أصلية",
                title_highlight: "من المصنع لباب بيتك",
                desc: "نتخطى كل الوسطاء ونتعامل مباشرة مع المصانع والموردين العالميين لنقدم لك أفضل سعر وأعلى جودة في السوق.",
                image: "assets/import_direct_3d.jpg",
                badge_icon: "✈️",
                badge_title: "شحن يومي",
                badge_desc: "من جميع الدول",
                features: [
                    { icon: "🌍", title: "50+ دولة مصدر", desc: "شبكة موردين معتمدين حول العالم" },
                    { icon: "⚡", title: "توصيل سريع", desc: "من 7 إلى 30 يوم حسب البلد" },
                    { icon: "🏷️", title: "أسعار لا تُنافَس", desc: "مباشرة من المصنع بلا هامش وسيط" }
                ],
                btn_text: "تسوق المنتجات المستوردة",
                btn_link: "shop.html"
            }
        };
        this.populate();
    }
};

const adminPopupBanner = {
    data: null,
    
    async load() {
        try {
            const res = await fetch('api/get_popup_banner.php?t=' + Date.now());
            this.data = await res.json();
            if (!this.data) {
                this.resetDefault(false);
            } else {
                this.populate();
            }
        } catch (e) {
            console.error("Error loading popup banner:", e);
            this.resetDefault(false);
        }
    },
    
    populate() {
        if (!this.data) return;
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = (val !== undefined && val !== null) ? val : '';
        };
        const setChecked = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.checked = !!val;
        };
        
        setChecked('apb-enabled', this.data.enabled !== false);
        setChecked('apb-show-once', !!this.data.show_once);
        setVal('apb-tag', this.data.tag || '');
        setVal('apb-title', this.data.title || '');
        setVal('apb-message', this.data.message || '');
        setVal('apb-image', this.data.image || '');
        setVal('apb-btn-text', this.data.btn_text || '');
        setVal('apb-btn-link', this.data.btn_link || '');
        
        this.updateLivePreview();
    },
    
    updateLivePreview() {
        const enabled = document.getElementById('apb-enabled') ? document.getElementById('apb-enabled').checked : true;
        const tag = (document.getElementById('apb-tag')?.value || '').trim();
        const title = (document.getElementById('apb-title')?.value || '').trim();
        const msg = (document.getElementById('apb-message')?.value || '').trim();
        const img = (document.getElementById('apb-image')?.value || '').trim();
        const btnText = (document.getElementById('apb-btn-text')?.value || '').trim();
        const btnLink = (document.getElementById('apb-btn-link')?.value || '').trim();
        
        // Status badge
        const statusEl = document.getElementById('apb-preview-status');
        if (statusEl) {
            statusEl.textContent = enabled ? 'مفعل' : 'معطل';
            statusEl.style.background = enabled ? 'var(--green-bg)' : 'var(--red-bg)';
            statusEl.style.color = enabled ? 'var(--green)' : 'var(--red)';
        }
        
        // Mockup elements
        const mockTag = document.getElementById('apb-mock-tag');
        if (mockTag) {
            mockTag.textContent = tag || 'تنبيه';
            mockTag.style.display = tag ? 'inline-block' : 'none';
        }
        
        const mockTitle = document.getElementById('apb-mock-title');
        if (mockTitle) {
            mockTitle.textContent = title || 'عنوان البنر المنبثق';
        }
        
        const mockMsg = document.getElementById('apb-mock-msg');
        if (mockMsg) {
            mockMsg.textContent = msg || 'نص الملاحظة أو الإشعار سيظهر هنا بالكامل وبشكل منسق.';
        }
        
        const mockImgWrap = document.getElementById('apb-mock-img-wrap');
        const mockImg = document.getElementById('apb-mock-img');
        if (mockImgWrap && mockImg) {
            if (img) {
                mockImg.src = img;
                mockImgWrap.style.display = 'block';
            } else {
                mockImgWrap.style.display = 'none';
            }
        }
        
        const mockBtn = document.getElementById('apb-mock-btn');
        if (mockBtn) {
            if (btnText) {
                mockBtn.textContent = btnText;
                mockBtn.href = btnLink || '#';
                mockBtn.style.display = 'block';
            } else {
                mockBtn.style.display = 'none';
            }
        }
        
        // Update slider switch UI
        const enSlider = document.getElementById('apb-enabled-slider');
        if (enSlider) {
            enSlider.style.background = enabled ? 'var(--blue)' : 'var(--bg)';
        }
        const onceSlider = document.getElementById('apb-once-slider');
        const showOnce = document.getElementById('apb-show-once')?.checked;
        if (onceSlider) {
            onceSlider.style.background = showOnce ? 'var(--blue)' : 'var(--bg)';
        }
    },
    
    clearImage() {
        const input = document.getElementById('apb-image');
        if (input) input.value = '';
        this.updateLivePreview();
    },
    
    handleUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const maxDim = 1000;
                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                const imgInput = document.getElementById('apb-image');
                if (imgInput) imgInput.value = dataUrl;
                this.updateLivePreview();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    },
    
    collectData() {
        return {
            enabled: document.getElementById('apb-enabled') ? document.getElementById('apb-enabled').checked : true,
            show_once: document.getElementById('apb-show-once') ? document.getElementById('apb-show-once').checked : false,
            tag: (document.getElementById('apb-tag')?.value || '').trim(),
            title: (document.getElementById('apb-title')?.value || '').trim(),
            message: (document.getElementById('apb-message')?.value || '').trim(),
            image: (document.getElementById('apb-image')?.value || '').trim(),
            btn_text: (document.getElementById('apb-btn-text')?.value || '').trim(),
            btn_link: (document.getElementById('apb-btn-link')?.value || '').trim()
        };
    },
    
    async save() {
        const payload = this.collectData();
        if (!payload.title && !payload.message) {
            alert('⚠️ يرجى إدخال العنوان أو نص الرسالة للبنر المنبثق على الأقل.');
            return;
        }
        try {
            const res = await fetch('api/save_popup_banner.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                this.data = payload;
                const toast = document.getElementById('admin-toast');
                if (toast) {
                    toast.textContent = '✅ ' + data.message;
                    toast.classList.add('show');
                    setTimeout(() => toast.classList.remove('show'), 3000);
                } else {
                    alert('✅ ' + data.message);
                }
            } else {
                alert('❌ ' + data.message);
            }
        } catch (e) {
            alert('❌ فشل الاتصال بالخادم لحفظ البنر المنبثق!');
        }
    },
    
    resetDefault(doConfirm = true) {
        if (doConfirm && !confirm('هل أنت متأكد من استعادة المحتوى الافتراضي للبنر المنبثق؟')) return;
        this.data = {
            enabled: true,
            show_once: false,
            tag: "تنبيه هام 📢",
            title: "أهلاً بكم في متجر استوردلي",
            message: "يسعدنا خدمتكم وتوفير أفضل منتجات الشواء والأدوات الأصلية مع شحن سريع لجميع المدن والمناطق.",
            image: "",
            btn_text: "تصفح العروض",
            btn_link: "shop.html"
        };
        this.populate();
    },
    
    testPopup() {
        window.open('index.html?test_popup=1', '_blank');
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
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:14px; padding:18px; display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <h3 style="margin:0; font-size:15px; font-weight:800;">بنر رقم ${i+1} ${b.is_big ? '<span style="font-size:11px; background:rgba(59,130,246,0.15); color:var(--blue); padding:2px 6px; border-radius:4px; margin-right:6px;">كبير</span>' : ''}</h3>
                    <span style="font-size:12px; color:var(--text2);">موقع البنر: الرئيسية</span>
                </div>

                <!-- Preview Image -->
                <div style="width:100%; height:130px; border-radius:10px; overflow:hidden; border:1px solid var(--border); background:#0f172a; position:relative; display:flex; align-items:center; justify-content:center;">
                    <img id="banner-preview-img-${i}" src="${b.image || ''}" alt="Preview" style="width:100%; height:100%; object-fit:cover; display:${b.image ? 'block' : 'none'};" onerror="this.style.display='none'; const p=document.getElementById('banner-preview-placeholder-${i}'); if(p) p.style.display='flex';" onload="this.style.display='block'; const p=document.getElementById('banner-preview-placeholder-${i}'); if(p) p.style.display='none';" />
                    <div id="banner-preview-placeholder-${i}" style="position:absolute; display:${b.image ? 'none' : 'flex'}; flex-direction:column; align-items:center; justify-content:center; color:var(--text3); font-size:12px;">
                        <span>🖼️</span>
                        <span>لا توجد صورة محددة</span>
                    </div>
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">صورة الخلفية (رابط أو مسار)</label>
                    <input type="text" id="banner-img-input-${i}" value="${b.image || ''}" oninput="adminBanners.update(${i}, 'image', this.value)" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit; margin-bottom:8px;">
                    
                    <button type="button" class="btn" style="background:var(--bg); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:700; width:100%; display:flex; align-items:center; justify-content:center; gap:6px;" onclick="document.getElementById('banner-file-input-${i}').click()">
                        📷 رفع صورة جديدة من جهازك
                    </button>
                    <input type="file" id="banner-file-input-${i}" accept="image/*" style="display:none;" onchange="adminBanners.handleImageUpload(${i}, this)">
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">الشارة (Tag)</label>
                    <input type="text" value="${b.tag || ''}" oninput="adminBanners.update(${i}, 'tag', this.value)" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">العنوان الرئيسي</label>
                    <input type="text" value="${b.title || ''}" oninput="adminBanners.update(${i}, 'title', this.value)" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">النص الفرعي (الوصف)</label>
                    <input type="text" value="${b.desc || ''}" oninput="adminBanners.update(${i}, 'desc', this.value)" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">نص الزر</label>
                        <input type="text" value="${b.btn_text || ''}" oninput="adminBanners.update(${i}, 'btn_text', this.value)" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">رابط الزر</label>
                        <input type="text" value="${b.link || ''}" oninput="adminBanners.update(${i}, 'link', this.value)" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:700; color:var(--text2); display:block; margin-bottom:4px;">لون النص (Text Color)</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="color" value="${b.text_color || '#1f2937'}" onchange="adminBanners.update(${i}, 'text_color', this.value)" style="width:50px; height:38px; border-radius:6px; border:1px solid var(--border); background:var(--bg); cursor:pointer; padding:2px;">
                        <span style="font-size:12px; color:var(--text2); font-family:monospace;">${b.text_color || '#1f2937'}</span>
                    </div>
                </div>
            </div>
        `).join('');
    },
    
    update(index, field, value) {
        this.banners[index][field] = value;
        if (field === 'image') {
            const img = document.getElementById(`banner-preview-img-${index}`);
            const ph = document.getElementById(`banner-preview-placeholder-${index}`);
            if (img) {
                img.src = value;
                img.style.display = value ? 'block' : 'none';
            }
            if (ph) {
                ph.style.display = value ? 'none' : 'flex';
            }
        }
    },

    handleImageUpload(index, input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const base64 = e.target.result;
            this.banners[index].image = base64;
            const inputEl = document.getElementById(`banner-img-input-${index}`);
            const imgEl = document.getElementById(`banner-preview-img-${index}`);
            const ph = document.getElementById(`banner-preview-placeholder-${index}`);
            
            if (inputEl) inputEl.value = base64;
            if (imgEl) {
                imgEl.src = base64;
                imgEl.style.display = 'block';
            }
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
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

const adminBrands = {
    brands: [],
    
    async load() {
        try {
            const res = await fetch('api/get_brands.php?t=' + Date.now());
            let rawBrands = await res.json();
            if (!Array.isArray(rawBrands)) rawBrands = [];
            
            // Normalize old format [ "Name1", "Name2" ] to [ { name: "Name1", logo: "" } ]
            this.brands = rawBrands.map(b => {
                if (typeof b === 'string') {
                    return { name: b, logo: '' };
                }
                return { name: b.name || '', logo: b.logo || '' };
            });
            
            this.render();
        } catch (e) {
            console.error("Error loading brands:", e);
        }
    },
    
    render() {
        const container = document.getElementById('admin-brands-builder');
        if (!container) return;
        
        if (this.brands.length === 0) {
            container.innerHTML = '<div style="text-align:center;color:var(--text3);padding:20px;">لا يوجد ماركات حالياً. اضغط على زر الإضافة لإضافة ماركة جديدة.</div>';
            return;
        }
        
        container.innerHTML = this.brands.map((b, i) => {
            const logoSrc = b.logo || 'https://via.placeholder.com/60?text=LOGO';
            return `
                <div style="background:var(--bg3); border:1px solid var(--border); border-radius:12px; padding:15px; display:flex; align-items:center; gap:15px; flex-wrap:wrap; margin-bottom:10px;">
                    <span style="font-weight:bold; color:var(--text3); min-width:30px;">#${i+1}</span>
                    
                    <!-- Logo Preview -->
                    <div style="width:60px; height:60px; border-radius:8px; border:1px solid var(--border); overflow:hidden; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; position:relative;" onclick="document.getElementById('brand-file-input-${i}').click()" title="تغيير الشعار">
                        <img src="${logoSrc}" id="brand-logo-preview-${i}" style="max-width:100%; max-height:100%; object-fit:contain;" />
                        <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.5); color:#fff; font-size:9px; text-align:center; padding:2px 0;">تعديل</div>
                    </div>
                    <input type="file" id="brand-file-input-${i}" accept="image/*" style="display:none;" onchange="adminBrands.handleLogoUpload(${i}, this)">
                    
                    <!-- Name Input -->
                    <div style="flex:1; min-width:200px; display:flex; flex-direction:column; gap:4px;">
                        <label style="font-size:11px; color:var(--text3); font-weight:bold;">اسم الماركة</label>
                        <input type="text" value="${b.name}" onchange="adminBrands.updateName(${i}, this.value)" placeholder="اسم الماركة التجارية" style="width:100%; padding:8px 12px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    
                    <!-- Logo URL Input -->
                    <div style="flex:1.5; min-width:250px; display:flex; flex-direction:column; gap:4px;">
                        <label style="font-size:11px; color:var(--text3); font-weight:bold;">رابط الشعار (أو رفعه بالضغط على الصورة)</label>
                        <input type="text" value="${b.logo}" onchange="adminBrands.updateLogo(${i}, this.value)" placeholder="رابط صورة الشعار (URL)" style="width:100%; padding:8px 12px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:inherit;">
                    </div>
                    
                    <button class="btn" style="background:var(--red); color:#fff; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; margin-top:15px;" onclick="adminBrands.delete(${i})">🗑️ حذف</button>
                </div>
            `;
        }).join('');
    },
    
    updateName(index, value) {
        this.brands[index].name = value.trim();
    },

    updateLogo(index, value) {
        this.brands[index].logo = value.trim();
        const img = document.getElementById(`brand-logo-preview-${index}`);
        if (img) img.src = value.trim() || 'https://via.placeholder.com/60?text=LOGO';
    },

    handleLogoUpload(index, input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const base64 = e.target.result;
            this.brands[index].logo = base64;
            const img = document.getElementById(`brand-logo-preview-${index}`);
            if (img) img.src = base64;
        };
        reader.readAsDataURL(input.files[0]);
    },
    
    add() {
        this.brands.push({ name: "ماركة جديدة", logo: "" });
        this.render();
    },
    
    delete(index) {
        this.brands.splice(index, 1);
        this.render();
    },
    
    async save() {
        try {
            const res = await fetch('api/save_brands.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.brands)
            });
            const data = await res.json();
            if (data.success) {
                showToast('✅ تم حفظ قائمة الماركات بنجاح!');
                this.load();
            } else {
                showToast('❌ فشل في حفظ الماركات: ' + data.message, 'error');
            }
        } catch(e) {
            showToast('❌ خطأ في الاتصال بالسيرفر', 'error');
        }
    }
};

const adminPagesContent = {
    data: {
        about: {},
        contact: {}
    },

    async load() {
        try {
            const res = await fetch('api/get_pages_content.php?t=' + Date.now());
            this.data = await res.json();
            if (!this.data) this.data = {};
            if (!this.data.about) this.data.about = {};
            if (!this.data.contact) this.data.contact = {};

            const a = this.data.about;
            const c = this.data.contact;

            const el = id => document.getElementById(id);
            if (el('adm-about-badge')) el('adm-about-badge').value = a.badge || '';
            if (el('adm-about-heading')) el('adm-about-heading').value = a.main_heading || '';
            if (el('adm-about-desc')) el('adm-about-desc').value = a.description || '';
            if (el('adm-about-vision-title')) el('adm-about-vision-title').value = a.vision_title || '';
            if (el('adm-about-vision-text')) el('adm-about-vision-text').value = a.vision_text || '';
            if (el('adm-about-mission-title')) el('adm-about-mission-title').value = a.mission_title || '';
            if (el('adm-about-mission-text')) el('adm-about-mission-text').value = a.mission_text || '';

            if (el('adm-contact-phone')) el('adm-contact-phone').value = c.phone || '';
            if (el('adm-contact-whatsapp')) el('adm-contact-whatsapp').value = c.whatsapp || '';
            if (el('adm-contact-email')) el('adm-contact-email').value = c.email || '';
            if (el('adm-contact-address')) el('adm-contact-address').value = c.address || '';
            if (el('adm-contact-hours')) el('adm-contact-hours').value = c.working_hours || '';
            if (el('adm-contact-wa-cta')) el('adm-contact-wa-cta').value = c.whatsapp_cta || '';
            if (el('adm-contact-form-heading')) el('adm-contact-form-heading').value = c.form_heading || '';
            if (el('adm-contact-desc')) el('adm-contact-desc').value = c.description || '';

        } catch (e) {
            console.error("Error loading pages content:", e);
        }
    },

    async save() {
        const el = id => document.getElementById(id);
        
        if (!this.data.about) this.data.about = {};
        if (!this.data.contact) this.data.contact = {};

        this.data.about.badge = el('adm-about-badge') ? el('adm-about-badge').value : '';
        this.data.about.main_heading = el('adm-about-heading') ? el('adm-about-heading').value : '';
        this.data.about.description = el('adm-about-desc') ? el('adm-about-desc').value : '';
        this.data.about.vision_title = el('adm-about-vision-title') ? el('adm-about-vision-title').value : '';
        this.data.about.vision_text = el('adm-about-vision-text') ? el('adm-about-vision-text').value : '';
        this.data.about.mission_title = el('adm-about-mission-title') ? el('adm-about-mission-title').value : '';
        this.data.about.mission_text = el('adm-about-mission-text') ? el('adm-about-mission-text').value : '';

        this.data.contact.phone = el('adm-contact-phone') ? el('adm-contact-phone').value : '';
        this.data.contact.whatsapp = el('adm-contact-whatsapp') ? el('adm-contact-whatsapp').value : '';
        this.data.contact.email = el('adm-contact-email') ? el('adm-contact-email').value : '';
        this.data.contact.address = el('adm-contact-address') ? el('adm-contact-address').value : '';
        this.data.contact.working_hours = el('adm-contact-hours') ? el('adm-contact-hours').value : '';
        this.data.contact.whatsapp_cta = el('adm-contact-wa-cta') ? el('adm-contact-wa-cta').value : '';
        this.data.contact.form_heading = el('adm-contact-form-heading') ? el('adm-contact-form-heading').value : '';
        this.data.contact.description = el('adm-contact-desc') ? el('adm-contact-desc').value : '';

        try {
            const res = await fetch('api/save_pages_content.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.data)
            });
            const data = await res.json();
            if (data.success) {
                showToast('✅ تم حفظ محتوى صفحتي من نحن واتصل بنا بنجاح!');
                this.load();
            } else {
                showToast('❌ فشل في حفظ البيانات: ' + data.message, 'error');
            }
        } catch (e) {
            showToast('❌ خطأ في الاتصال بالسيرفر', 'error');
        }
    }
};

const adminPolicies = {
    async load() {
        try {
            const res = await fetch('api/get_policies.php?t=' + Date.now());
            const data = await res.json();
            const shippingEl = document.getElementById('sett-shipping-policy');
            const returnEl = document.getElementById('sett-return-policy');
            if (shippingEl) shippingEl.value = data.shipping || '';
            if (returnEl) returnEl.value = data.returns || '';

            // Delivery Box
            const delTitleEl = document.getElementById('sett-delivery-title');
            const delSubEl = document.getElementById('sett-delivery-subtitle');
            if (delTitleEl) delTitleEl.value = data.delivery_title || 'وصول سريع خلال 2–5 أيام عمل';
            if (delSubEl) delSubEl.value = data.delivery_subtitle || 'شحن مجاني للطلبات فوق ₪200';

            // Badge 1
            const b1Visible = document.getElementById('sett-badge1-visible');
            const b1Title = document.getElementById('sett-badge1-title');
            const b1Sub = document.getElementById('sett-badge1-subtitle');
            if (b1Visible) b1Visible.checked = data.badge1_visible !== false && data.badge1_visible !== 'false' && data.badge1_visible !== '';
            if (b1Title) b1Title.value = data.badge1_title || 'دفع آمن';
            if (b1Sub) b1Sub.value = data.badge1_subtitle || '100% مشفر';

            // Badge 2
            const b2Visible = document.getElementById('sett-badge2-visible');
            const b2Title = document.getElementById('sett-badge2-title');
            const b2Sub = document.getElementById('sett-badge2-subtitle');
            if (b2Visible) b2Visible.checked = data.badge2_visible !== false && data.badge2_visible !== 'false' && data.badge2_visible !== '';
            if (b2Title) b2Title.value = data.badge2_title || 'إرجاع مجاني';
            if (b2Sub) b2Sub.value = data.badge2_subtitle || 'خلال 30 يوم';

            // Badge 3
            const b3Visible = document.getElementById('sett-badge3-visible');
            const b3Title = document.getElementById('sett-badge3-title');
            const b3Sub = document.getElementById('sett-badge3-subtitle');
            if (b3Visible) b3Visible.checked = data.badge3_visible !== false && data.badge3_visible !== 'false' && data.badge3_visible !== '';
            if (b3Title) b3Title.value = data.badge3_title || 'منتج أصلي';
            if (b3Sub) b3Sub.value = data.badge3_subtitle || 'ضمان الأصالة';

            // Load Ticker settings
            try {
                const tickerRes = await fetch('api/get_ticker.php?t=' + Date.now());
                const tickerData = await tickerRes.json();
                const tickerEnabledEl = document.getElementById('sett-ticker-enabled');
                const tickerTextEl = document.getElementById('sett-ticker-text');
                if (tickerEnabledEl) tickerEnabledEl.checked = tickerData.enabled !== false;
                if (tickerTextEl) tickerTextEl.value = tickerData.text || '';
            } catch (err) {
                console.error("Error loading ticker settings in admin:", err);
            }

            // Load Import Ticker settings
            try {
                const importRes = await fetch('api/get_import_countries.php?t=' + Date.now());
                const importData = await importRes.json();
                const importEnabledEl = document.getElementById('sett-import-ticker-enabled');
                const importMarqueeEl = document.getElementById('sett-import-ticker-marquee');
                const importCloseEl = document.getElementById('sett-import-ticker-close');
                const importLabelEl = document.getElementById('sett-import-ticker-label');
                const importTextEl = document.getElementById('sett-import-ticker-text');
                const importSpeedEl = document.getElementById('sett-import-ticker-speed');
                
                if (importEnabledEl) importEnabledEl.checked = importData.enabled !== false;
                if (importMarqueeEl) importMarqueeEl.checked = importData.enable_marquee !== false;
                if (importCloseEl) importCloseEl.checked = importData.show_close !== false;
                if (importLabelEl) importLabelEl.value = importData.label || '🌍 نستورد من:';
                if (importTextEl) importTextEl.value = importData.text || '';
                if (importSpeedEl) importSpeedEl.value = importData.speed || 30;
            } catch (err) {
                console.error("Error loading import ticker settings in admin:", err);
            }
        } catch(e) {
            console.error('Error loading policies:', e);
        }
    },

    async save() {
        const shipping = document.getElementById('sett-shipping-policy')?.value || '';
        const returns = document.getElementById('sett-return-policy')?.value || '';
        const delivery_title = document.getElementById('sett-delivery-title')?.value || '';
        const delivery_subtitle = document.getElementById('sett-delivery-subtitle')?.value || '';
        
        const badge1_visible = document.getElementById('sett-badge1-visible')?.checked || false;
        const badge1_title = document.getElementById('sett-badge1-title')?.value || '';
        const badge1_subtitle = document.getElementById('sett-badge1-subtitle')?.value || '';

        const badge2_visible = document.getElementById('sett-badge2-visible')?.checked || false;
        const badge2_title = document.getElementById('sett-badge2-title')?.value || '';
        const badge2_subtitle = document.getElementById('sett-badge2-subtitle')?.value || '';

        const badge3_visible = document.getElementById('sett-badge3-visible')?.checked || false;
        const badge3_title = document.getElementById('sett-badge3-title')?.value || '';
        const badge3_subtitle = document.getElementById('sett-badge3-subtitle')?.value || '';

        // Save Ticker settings
        const ticker_enabled = document.getElementById('sett-ticker-enabled')?.checked || false;
        const ticker_text = document.getElementById('sett-ticker-text')?.value || '';
        try {
            await fetch('api/save_ticker.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ enabled: ticker_enabled, text: ticker_text })
            });
        } catch (err) {
            console.error("Error saving ticker settings in admin:", err);
        }

        // Save Import Ticker settings
        const import_enabled = document.getElementById('sett-import-ticker-enabled')?.checked || false;
        const import_marquee = document.getElementById('sett-import-ticker-marquee')?.checked || false;
        const import_close = document.getElementById('sett-import-ticker-close')?.checked || false;
        const import_label = document.getElementById('sett-import-ticker-label')?.value || '🌍 نستورد من:';
        const import_text = document.getElementById('sett-import-ticker-text')?.value || '';
        const import_speed = parseInt(document.getElementById('sett-import-ticker-speed')?.value) || 30;
        
        try {
            await fetch('api/save_import_countries.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    enabled: import_enabled, 
                    enable_marquee: import_marquee,
                    show_close: import_close,
                    label: import_label,
                    text: import_text,
                    speed: import_speed
                })
            });
        } catch (err) {
            console.error("Error saving import ticker settings in admin:", err);
        }

        try {
            const res = await fetch('api/save_policies.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    shipping, 
                    returns,
                    delivery_title,
                    delivery_subtitle,
                    badge1_visible,
                    badge1_title,
                    badge1_subtitle,
                    badge2_visible,
                    badge2_title,
                    badge2_subtitle,
                    badge3_visible,
                    badge3_title,
                    badge3_subtitle
                })
            });
            const data = await res.json();
            if (data.success) {
                showToast('✅ تم حفظ الإعدادات بنجاح!');
            } else {
                showToast('❌ فشل في حفظ الإعدادات: ' + (data.message || ''), 'error');
            }
        } catch(e) {
            showToast('❌ خطأ في الاتصال بالسيرفر', 'error');
        }
    }
};

const adminCurrency = {
    async load() {
        try {
            const res = await fetch('api/get_currency.php?t=' + Date.now());
            const data = await res.json();
            
            const enabledEl = document.getElementById('sett-currency-enabled');
            const autoEl = document.getElementById('sett-currency-auto');
            const baseEl = document.getElementById('sett-currency-base');
            const currentEl = document.getElementById('sett-currency-current');
            const updateEl = document.getElementById('currency-last-update');
            
            if (enabledEl) enabledEl.checked = data.enabled || false;
            if (autoEl) autoEl.checked = data.auto_fetch || false;
            if (baseEl) baseEl.value = data.base_rate || 0.50;
            if (currentEl) currentEl.value = data.current_rate || 0.50;
            if (updateEl && data.last_updated) {
                updateEl.textContent = 'آخر تحديث تلقائي: ' + data.last_updated;
            } else if (updateEl) {
                updateEl.textContent = '';
            }
            this.toggleFields();
        } catch(e) {
            console.error('Error loading currency settings:', e);
        }
    },

    toggleFields() {
        const enabled = document.getElementById('sett-currency-enabled')?.checked || false;
        const auto = document.getElementById('sett-currency-auto')?.checked || false;
        
        const autoEl = document.getElementById('sett-currency-auto');
        const baseEl = document.getElementById('sett-currency-base');
        const currentEl = document.getElementById('sett-currency-current');
        const fetchBtn = document.getElementById('btn-currency-fetch');
        
        if (autoEl) autoEl.disabled = !enabled;
        if (baseEl) baseEl.disabled = !enabled;
        
        if (currentEl) {
            currentEl.disabled = !enabled || auto;
        }
        if (fetchBtn) {
            fetchBtn.disabled = !enabled || !auto;
        }
    },

    async fetchLive() {
        const fetchBtn = document.getElementById('btn-currency-fetch');
        if (fetchBtn) {
            fetchBtn.disabled = true;
            fetchBtn.textContent = '⏳ جاري الجلب...';
        }
        try {
            const res = await fetch('api/save_currency.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ force_fetch: true })
            });
            const data = await res.json();
            if (data.success) {
                showToast('✅ تم جلب سعر الصرف وتحديثه بنجاح!');
                this.load();
            } else {
                showToast('❌ فشل الجلب: ' + (data.message || ''), 'error');
            }
        } catch(e) {
            showToast('❌ خطأ في الاتصال بالسيرفر', 'error');
        } finally {
            if (fetchBtn) {
                fetchBtn.textContent = '🔄 تحديث الآن من البورصة';
                this.toggleFields();
            }
        }
    },

    async save() {
        const enabled = document.getElementById('sett-currency-enabled')?.checked || false;
        const auto = document.getElementById('sett-currency-auto')?.checked || false;
        const base = parseFloat(document.getElementById('sett-currency-base')?.value || '0.50');
        const current = parseFloat(document.getElementById('sett-currency-current')?.value || '0.50');
        
        try {
            const res = await fetch('api/save_currency.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    enabled,
                    auto_fetch: auto,
                    base_rate: base,
                    current_rate: current
                })
            });
            const data = await res.json();
            return data.success;
        } catch(e) {
            console.error('Error saving currency settings:', e);
            return false;
        }
    }
};

async function saveAllSettings() {
    const shipping = document.getElementById('sett-shipping-policy')?.value || '';
    const returns = document.getElementById('sett-return-policy')?.value || '';
    const delivery_title = document.getElementById('sett-delivery-title')?.value || '';
    const delivery_subtitle = document.getElementById('sett-delivery-subtitle')?.value || '';
    
    const badge1_visible = document.getElementById('sett-badge1-visible')?.checked || false;
    const badge1_title = document.getElementById('sett-badge1-title')?.value || '';
    const badge1_subtitle = document.getElementById('sett-badge1-subtitle')?.value || '';

    const badge2_visible = document.getElementById('sett-badge2-visible')?.checked || false;
    const badge2_title = document.getElementById('sett-badge2-title')?.value || '';
    const badge2_subtitle = document.getElementById('sett-badge2-subtitle')?.value || '';

    const badge3_visible = document.getElementById('sett-badge3-visible')?.checked || false;
    const badge3_title = document.getElementById('sett-badge3-title')?.value || '';
    const badge3_subtitle = document.getElementById('sett-badge3-subtitle')?.value || '';

    let success = true;
    
    // Save Policies
    try {
        const res = await fetch('api/save_policies.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                shipping, 
                returns,
                delivery_title,
                delivery_subtitle,
                badge1_visible,
                badge1_title,
                badge1_subtitle,
                badge2_visible,
                badge2_title,
                badge2_subtitle,
                badge3_visible,
                badge3_title,
                badge3_subtitle
            })
        });
        const data = await res.json();
        if (!data.success) success = false;
    } catch(e) {
        success = false;
    }

    // Save Ticker settings
    const ticker_enabled = document.getElementById('sett-ticker-enabled')?.checked || false;
    const ticker_text = document.getElementById('sett-ticker-text')?.value || '';
    try {
        const res = await fetch('api/save_ticker.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ enabled: ticker_enabled, text: ticker_text })
        });
        const data = await res.json();
        if (!data.success) success = false;
    } catch (err) {
        console.error("Error saving ticker settings:", err);
        success = false;
    }

    // Save Import Ticker settings
    const import_enabled = document.getElementById('sett-import-ticker-enabled')?.checked || false;
    const import_marquee = document.getElementById('sett-import-ticker-marquee')?.checked || false;
    const import_close = document.getElementById('sett-import-ticker-close')?.checked || false;
    const import_label = document.getElementById('sett-import-ticker-label')?.value || '';
    const import_text = document.getElementById('sett-import-ticker-text')?.value || '';
    const import_speed = parseInt(document.getElementById('sett-import-ticker-speed')?.value) || 30;
    
    try {
        const res = await fetch('api/save_import_countries.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                enabled: import_enabled, 
                enable_marquee: import_marquee,
                show_close: import_close,
                label: import_label,
                text: import_text,
                speed: import_speed
            })
        });
        const data = await res.json();
        if (!data.success) success = false;
    } catch (err) {
        console.error("Error saving import ticker settings:", err);
        success = false;
    }

    const currencySuccess = await adminCurrency.save();
    if (!currencySuccess) success = false;

    if (success) {
        showToast('✅ تم حفظ كافة الإعدادات بنجاح!');
        adminCurrency.load();
    } else {
        showToast('❌ حدث خطأ أثناء حفظ بعض الإعدادات', 'error');
    }
}


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
                if (result.message === 'Unauthorized') {
                    showToast('⚠️ انتهت جلسة تسجيل الدخول. يرجى تحديث الصفحة (Refresh) وتسجيل الدخول', 'error');
                } else {
                    showToast('❌ فشل الحفظ: ' + result.message, 'error');
                }
            }
        } catch(e) {
            showToast('❌ حدث خطأ أثناء الحفظ', 'error');
        }
    }
};

window.addEventListener('DOMContentLoaded', () => {
    adminNav.load();
    adminHomepageCategories.load();
    adminIcons.load();
    adminBrands.load();
    adminFooterSettings.load();
    adminImportCountries.load();
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

const adminHomepageCategories = {
    categories: [],
    allNavCategories: [],
    
    async load() {
        try {
            const res = await fetch('api/get_homepage_categories.php?t=' + Date.now());
            this.categories = await res.json();
            if (!Array.isArray(this.categories)) this.categories = [];
            
            // Load nav categories
            const navRes = await fetch('api/get_nav.php?t=' + Date.now());
            const navData = await navRes.json();
            
            this.allNavCategories = [];
            if (Array.isArray(navData)) {
                navData.forEach(item => {
                    const title = item.title || '';
                    if (title) {
                        this.allNavCategories.push(title);
                    }
                    if (item.subLinks) {
                        item.subLinks.forEach(sub => {
                            if (sub.title) {
                                this.allNavCategories.push(sub.title);
                            }
                        });
                    } else if (item.columns) {
                        item.columns.forEach(col => {
                            if (col.links) {
                                col.links.forEach(sub => {
                                    if (sub.title) {
                                        this.allNavCategories.push(sub.title);
                                    }
                                });
                            }
                        });
                    }
                });
            }
            // Remove duplicates
            this.allNavCategories = [...new Set(this.allNavCategories)].filter(s => s.trim().length > 0);
            
            this.render();
        } catch(e) {
            console.error("Error loading homepage categories:", e);
        }
    },
    
    render() {
        const container = document.getElementById('admin-homepage-categories-builder');
        if (!container) return;
        
        if (this.categories.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:var(--text3); padding:20px;">لا يوجد تصنيفات حالياً.</div>';
            return;
        }
        
        container.innerHTML = this.categories.map((c, i) => {
            const isEmoji = c.img && (c.img.length <= 4 || !c.img.includes('/') && !c.img.startsWith('data:'));
            const imgHtml = isEmoji 
                ? `<div style="font-size:32px; width:60px; height:60px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:8px; background:#fff;">${c.img}</div>`
                : `<img src="${c.img || 'https://via.placeholder.com/60?text=ICON'}" id="cat-img-preview-${i}" style="width:60px; height:60px; object-fit:cover; border:1px solid var(--border); border-radius:8px; background:#fff;" />`;
            
            // Linked Category Select Dropdown Options
            let optionsHtml = '<option value="">-- بدون ربط تلقائي --</option>';
            this.allNavCategories.forEach(catName => {
                const selected = c.linkedCategory === catName ? 'selected' : '';
                optionsHtml += `<option value="${catName}" ${selected}>${catName}</option>`;
            });

            return `
                <div style="background:var(--bg3); border:1px solid var(--border); border-radius:10px; padding:15px; display:flex; gap:12px; align-items:center; position:relative;">
                    <!-- Image/Emoji preview -->
                    <div style="cursor:pointer; position:relative;" onclick="document.getElementById('cat-file-input-${i}').click()" title="رفع صورة جديدة">
                        ${imgHtml}
                        <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.5); color:#fff; font-size:8px; text-align:center; padding:1px 0; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">تعديل</div>
                    </div>
                    <input type="file" id="cat-file-input-${i}" accept="image/*" style="display:none;" onchange="adminHomepageCategories.handleImageUpload(${i}, this)">
                    
                    <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                        <!-- Name Input -->
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <span style="font-size:10px; color:var(--text3); font-weight:bold;">اسم الأيقونة (بالعربية)</span>
                            <input type="text" value="${c.title}" onchange="adminHomepageCategories.updateTitle(${i}, this.value)" style="width:100%; padding:6px 10px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-family:inherit;">
                        </div>
                        
                        <!-- Link Category Dropdown -->
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <span style="font-size:10px; color:var(--text3); font-weight:bold;">🔗 ربط بقسم/تصنيف تلقائي</span>
                            <select onchange="adminHomepageCategories.updateLinkedCategory(${i}, this.value)" style="width:100%; padding:6px 10px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-family:inherit; cursor:pointer;">
                                ${optionsHtml}
                            </select>
                        </div>
                        
                        <!-- ID/Key Info -->
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px;">
                            <span style="color:var(--text3);">مفتاح الربط: <strong style="color:var(--p);">${c.id}</strong></span>
                            <label style="display:flex; align-items:center; gap:4px; cursor:pointer;">
                                <input type="checkbox" ${c.active ? 'checked' : ''} onchange="adminHomepageCategories.updateActive(${i}, this.checked)" style="width:14px; height:14px;"> إظهار
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    },
    
    updateTitle(index, value) {
        this.categories[index].title = value.trim();
    },
    
    updateLinkedCategory(index, value) {
        this.categories[index].linkedCategory = value;
    },
    
    updateActive(index, value) {
        this.categories[index].active = value;
    },
    
    handleImageUpload(index, input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const base64 = e.target.result;
            this.categories[index].img = base64;
            this.render();
        };
        reader.readAsDataURL(input.files[0]);
    },
    
    async save() {
        try {
            const res = await fetch('api/save_homepage_categories.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.categories)
            });
            const data = await res.json();
            if (data.success) {
                showToast('✅ تم حفظ أسماء وصور الأيقونات بنجاح!');
                this.load();
            } else {
                showToast('❌ فشل حفظ التعديلات: ' + data.message, 'error');
            }
        } catch(e) {
            showToast('❌ خطأ في الاتصال بالسيرفر', 'error');
        }
    }
};



const adminFooterSettings = {
    async load() {
        try {
            const res = await fetch('api/get_footer_settings.php?t=' + Date.now());
            const data = await res.json();
            if (!data) return;

            if (data.social) {
                if (document.getElementById('footer-fb-url')) document.getElementById('footer-fb-url').value = data.social.facebook || '';
                if (document.getElementById('footer-ig-url')) document.getElementById('footer-ig-url').value = data.social.instagram || '';
                if (document.getElementById('footer-tt-url')) document.getElementById('footer-tt-url').value = data.social.tiktok || '';
                if (document.getElementById('footer-yt-url')) document.getElementById('footer-yt-url').value = data.social.youtube || '';
            }
            if (data.apps) {
                if (document.getElementById('footer-appstore-url')) document.getElementById('footer-appstore-url').value = data.apps.app_store || '';
                if (document.getElementById('footer-gplay-url')) document.getElementById('footer-gplay-url').value = data.apps.google_play || '';
            }
            if (document.getElementById('footer-about-admin')) document.getElementById('footer-about-admin').value = data.about_text || '';
            if (document.getElementById('footer-copyright-admin')) document.getElementById('footer-copyright-admin').value = data.copyright || '';
            // Update preview
            if (document.getElementById('footer-preview-about')) document.getElementById('footer-preview-about').textContent = data.about_text || '';
            if (document.getElementById('footer-preview-copyright')) document.getElementById('footer-preview-copyright').textContent = data.copyright || '';
        } catch(e) {
            console.error('Error loading footer settings:', e);
        }
    },
    async save() {
        const payload = {
            about_text: document.getElementById('footer-about-admin')?.value || '',
            copyright: document.getElementById('footer-copyright-admin')?.value || '',
            social: {
                facebook: document.getElementById('footer-fb-url')?.value || '',
                instagram: document.getElementById('footer-ig-url')?.value || '',
                tiktok: document.getElementById('footer-tt-url')?.value || '',
                youtube: document.getElementById('footer-yt-url')?.value || ''
            },
            apps: {
                app_store: document.getElementById('footer-appstore-url')?.value || '',
                google_play: document.getElementById('footer-gplay-url')?.value || ''
            }
        };
        try {
            const res = await fetch('api/save_footer_settings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                showToast('✅ تم حفظ إعدادات الفوتر بنجاح!');
                // Update preview
                if (document.getElementById('footer-preview-about')) document.getElementById('footer-preview-about').textContent = payload.about_text;
                if (document.getElementById('footer-preview-copyright')) document.getElementById('footer-preview-copyright').textContent = payload.copyright;
            } else {
                showToast('❌ فشل الحفظ: ' + (data.message || ''), 'error');
            }
        } catch(e) {
            showToast('❌ خطأ في الاتصال', 'error');
        }
    }
};

const adminImportCountries = {
    data: { section_title: '', section_subtitle: '', countries: [] },

    async load() {
        try {
            const res = await fetch('api/get_import_countries.php?t=' + Date.now());
            this.data = await res.json();
            if (!this.data.countries) this.data.countries = [];
            this.render();
        } catch(e) {
            console.error('Error loading import countries:', e);
        }
    },

    render() {
        // Fill section title/subtitle
        const titleInput = document.getElementById('import-sec-title');
        const subtitleInput = document.getElementById('import-sec-subtitle');
        if (titleInput) titleInput.value = this.data.section_title || '';
        if (subtitleInput) subtitleInput.value = this.data.section_subtitle || '';

        const container = document.getElementById('admin-import-countries-builder');
        if (!container) return;

        if (this.data.countries.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text3);">لا يوجد دول. اضغط على "+ إضافة دولة" للبدء.</div>';
            return;
        }

        container.innerHTML = this.data.countries.map((c, i) => `
            <div style="background:var(--bg3); border:1px solid var(--border); border-radius:10px; padding:14px; display:flex; gap:12px; align-items:center;">
                <!-- Flag/Emoji -->
                <div>
                    <label style="font-size:10px; color:var(--text3); display:block; margin-bottom:3px;">علم/رمز</label>
                    <input type="text" value="${c.flag || '🌐'}" onchange="adminImportCountries.update(${i}, 'flag', this.value)" style="width:50px; text-align:center; font-size:22px; padding:4px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                </div>
                <!-- Name -->
                <div style="flex:1;">
                    <label style="font-size:10px; color:var(--text3); display:block; margin-bottom:3px;">اسم الدولة</label>
                    <input type="text" value="${c.name || ''}" onchange="adminImportCountries.update(${i}, 'name', this.value)" placeholder="الصين" style="width:100%; padding:7px 10px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:14px; font-weight:700;">
                </div>
                <!-- Categories -->
                <div style="flex:2;">
                    <label style="font-size:10px; color:var(--text3); display:block; margin-bottom:3px;">التصنيفات</label>
                    <input type="text" value="${c.categories || ''}" onchange="adminImportCountries.update(${i}, 'categories', this.value)" placeholder="مماسح • جملة" style="width:100%; padding:7px 10px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;">
                </div>
                <!-- Badge -->
                <div style="flex:1;">
                    <label style="font-size:10px; color:var(--text3); display:block; margin-bottom:3px;">شارة التميز</label>
                    <input type="text" value="${c.badge || ''}" onchange="adminImportCountries.update(${i}, 'badge', this.value)" placeholder="جودة عالية" style="width:100%; padding:7px 10px; border-radius:6px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:12px;">
                </div>
                <!-- Active -->
                <div style="text-align:center;">
                    <label style="font-size:10px; color:var(--text3); display:block; margin-bottom:3px;">ظهور</label>
                    <input type="checkbox" ${c.active !== false ? 'checked' : ''} onchange="adminImportCountries.update(${i}, 'active', this.checked)" style="width:18px; height:18px; cursor:pointer;">
                </div>
                <!-- Delete -->
                <button onclick="adminImportCountries.remove(${i})" style="background:rgba(239,68,68,0.1); border:1px solid var(--red); color:var(--red); padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:bold; font-size:16px;">×</button>
            </div>
        `).join('');
    },

    update(index, key, value) {
        this.data.countries[index][key] = value;
    },

    addCountry() {
        this.data.countries.push({
            id: 'country_' + Date.now(),
            flag: '🌐',
            name: 'دولة جديدة',
            categories: '',
            badge: '',
            active: true
        });
        this.render();
    },

    remove(index) {
        if (confirm('هل تريد حذف هذه الدولة؟')) {
            this.data.countries.splice(index, 1);
            this.render();
        }
    },

    async save() {
        // Collect current title/subtitle from inputs
        const titleVal = document.getElementById('import-sec-title')?.value || this.data.section_title;
        const subtitleVal = document.getElementById('import-sec-subtitle')?.value || this.data.section_subtitle;
        this.data.section_title = titleVal;
        this.data.section_subtitle = subtitleVal;

        try {
            const res = await fetch('api/save_import_countries.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.data)
            });
            const result = await res.json();
            if (result.success) {
                showToast('✅ تم حفظ دول الاستيراد بنجاح!');
            } else {
                showToast('❌ فشل الحفظ', 'error');
            }
        } catch(e) {
            showToast('❌ خطأ في الاتصال', 'error');
        }
    }
};

</script>
</body>
</html>
