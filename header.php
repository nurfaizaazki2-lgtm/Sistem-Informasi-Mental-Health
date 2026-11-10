<?php require_once 'config.php'; require_once 'functions.php'; $hal=basename($_SERVER['SCRIPT_NAME']); if($hal=='hasil.php')$hal='tes.php'; ?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>MindCampus · Sistem Psikologi Mahasiswa</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:opsz,wght@9..144,400;9..144,600&display=swap" rel="stylesheet">
<style>
:root{--paper:#f3eee4;--card:#faf6ee;--ink:#1f2a2c;--mu:#6b7270;--line:#d8cfbd;--moss:#2f5d50;--clay:#c4553a;--ochre:#d9a441;--sage:#8fb39a}
*{box-sizing:border-box}html{scroll-behavior:smooth}
body{margin:0;background:var(--paper);color:var(--ink);font:15px/1.6 "DM Sans",system-ui,sans-serif}
.side{position:fixed;inset:0 auto 0 0;width:236px;padding:30px 24px;border-right:1px solid var(--line);display:flex;flex-direction:column;gap:26px}
.logo{font:600 27px/1 Fraunces,Georgia,serif;letter-spacing:-.03em;text-decoration:none;color:var(--ink)}.logo i{color:var(--clay);font-style:normal}
.tag{font-size:12px;color:var(--mu);margin-top:6px;line-height:1.4}
.side nav a{display:flex;gap:12px;padding:8px 0;color:var(--ink);text-decoration:none;border-bottom:1px solid var(--line)}
.side nav a b{font:400 11px ui-monospace,monospace;color:var(--mu);padding-top:4px}.side nav a.on{color:var(--moss);font-weight:700}.side nav a.on b{color:var(--clay)}
main{margin-left:236px;padding:38px 46px 60px;max-width:1180px}
h1,h2,h3{font-family:Fraunces,Georgia,serif;font-weight:400;letter-spacing:-.02em;margin:0}
h1{font-size:clamp(32px,5vw,54px);line-height:1.04}h1 em{color:var(--clay);font-style:italic}h2{font-size:26px}h3{font-size:19px;margin-bottom:8px}
.kick{font:600 11px ui-monospace,monospace;letter-spacing:.15em;text-transform:uppercase;color:var(--clay);margin-bottom:10px}
.sec{border-top:1.5px solid var(--ink);padding-top:14px;margin-top:40px}.lead{max-width:60ch;color:var(--mu);margin:10px 0 0}
.st{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));border-top:1.5px solid var(--ink);border-bottom:1px solid var(--line);margin-top:28px}
.st div{padding:14px 12px 14px 0}.st b{display:block;font:400 42px/1.1 Fraunces,Georgia,serif}.st span{font-size:12px;color:var(--mu)}
.fk{display:grid;grid-template-columns:200px 1fr;gap:14px;padding:11px 0;border-bottom:1px solid var(--line);align-items:center}.fk>span{font-size:13px}
.dots{display:flex;flex-wrap:wrap;gap:5px}.dot{width:14px;height:14px;border-radius:50%;display:block}
.dot.r{background:var(--clay)}.dot.y{background:var(--ochre)}.dot.g{background:var(--sage)}.dot.s{box-shadow:0 0 0 2px var(--paper),0 0 0 3.5px var(--clay)}
.lg{display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--mu);margin-top:12px}.lg .dot{display:inline-block;width:10px;height:10px;margin-right:5px;vertical-align:-1px}
.ch{display:grid;gap:30px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));margin-top:14px}.cv{position:relative;height:250px}
table{border-collapse:collapse;width:100%;font-size:14px}th{font:600 11px ui-monospace,monospace;letter-spacing:.1em;text-transform:uppercase;color:var(--mu);text-align:left;padding:8px 8px 8px 0;border-bottom:1.5px solid var(--ink)}
td{padding:10px 8px 10px 0;border-bottom:1px solid var(--line);vertical-align:top}td a{color:var(--ink);text-decoration:none}td a:hover{color:var(--clay)}
.mono{font-family:ui-monospace,monospace;font-size:13px}.sub{color:var(--mu);font-size:12.5px}.tw{overflow-x:auto}
.mb{display:block;height:4px;background:var(--line);border-radius:4px;margin-top:5px;width:90px}.mb i{display:block;height:4px;border-radius:4px;background:var(--moss)}
.bd{padding:2px 10px;border-radius:99px;font-size:12px;font-weight:700;border:1.5px solid}.bd.r{color:var(--clay);border-color:var(--clay)}.bd.y{color:#9a6b00;border-color:var(--ochre)}.bd.g{color:var(--moss);border-color:var(--sage)}
.seg{font-size:12px;color:var(--clay);font-weight:700}
form.f{display:grid;gap:18px 22px;grid-template-columns:repeat(auto-fit,minmax(190px,1fr))}
label{display:block;font-size:12px;color:var(--mu)}
input,select{width:100%;padding:8px 2px;border:0;border-bottom:1.5px solid var(--ink);border-radius:0;background:transparent;color:var(--ink);font:inherit}
input:focus,select:focus{outline:0;border-bottom-color:var(--clay)}input[type=range]{padding:0;accent-color:var(--moss)}
.b{border:1.5px solid var(--ink);border-radius:99px;padding:9px 22px;background:transparent;color:var(--ink);font:inherit;font-weight:700;cursor:pointer;text-decoration:none;display:inline-block}
.b:hover{background:var(--moss);border-color:var(--moss);color:#fff}.b.p{background:var(--ink);color:var(--paper)}.b.p:hover{background:var(--moss)}.b.s{padding:3px 12px;font-size:13px}.b.d{color:var(--clay);border-color:var(--clay)}.b.d:hover{background:var(--clay);color:#fff}
.err{color:var(--clay);font-weight:700}
.q{padding:13px 0;border-bottom:1px solid var(--line)}.q p{margin:0 0 8px}.q p b{font:400 12px ui-monospace,monospace;color:var(--clay);margin-right:8px}
.op{display:flex;gap:6px;flex-wrap:wrap}.op label{position:relative;cursor:pointer;color:var(--ink);font-size:13px}.op input{position:absolute;opacity:0;width:1px;height:1px}
.op span{display:block;border:1.5px solid var(--line);border-radius:99px;padding:5px 14px}.op span:hover{border-color:var(--moss)}
.op input:checked+span{background:var(--moss);border-color:var(--moss);color:#fff}.op input:focus-visible+span{outline:2px solid var(--clay);outline-offset:2px}
.big{font:400 clamp(64px,10vw,110px)/1 Fraunces,Georgia,serif}.note{border:1.5px solid var(--clay);padding:16px 18px;margin:22px 0;border-radius:4px;background:var(--card)}
.pg{display:flex;gap:4px;flex-wrap:wrap;margin-top:14px}.pg a{padding:3px 10px;border:1px solid var(--line);border-radius:99px;color:var(--ink);text-decoration:none;font-size:13px}.pg a.on{background:var(--ink);color:var(--paper);border-color:var(--ink)}
.ind{display:grid;grid-template-columns:190px 1fr 44px;gap:12px;align-items:center;font-size:13px;margin:6px 0}.ind .mb{width:auto;height:8px}.ind .mb i{height:8px}
@media(max-width:860px){.side{position:static;width:auto;border:0;border-bottom:1px solid var(--line);padding:18px 18px 8px}.side nav{display:flex;flex-wrap:wrap;gap:0 14px}.side nav a{border:0}main{margin:0;padding:22px 18px 50px}.fk{grid-template-columns:1fr}.ind{grid-template-columns:1fr 1fr 40px}}
.kop,.ttd{display:none}
@page{size:A4;margin:15mm}
@media print{.side,.noprint{display:none!important}main{margin:0;padding:0;max-width:none}body{background:#fff;font-size:12px;color:#000}
.kop{display:block;border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:18px}.kop b{font:600 18px Fraunces,Georgia,serif}.kop span{font-size:11px}
.ttd{display:flex;justify-content:space-between;margin-top:44px;break-inside:avoid}.ttd div{width:44%;text-align:center}
thead{display:table-header-group}tr,.fk,.note,.st{break-inside:avoid}h1{font-size:26px}.sec{margin-top:22px}h2{break-after:avoid}
.ch{grid-template-columns:1fr 1fr;gap:14px}.cv{height:190px}.lead{color:#333}
*{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style></head><body>
<aside class="side"><div><a class="logo" href="index.php">mind<i>·</i>campus</a><div class="tag">Sistem psikologi mahasiswa:<br>skrining, prioritas, tindak lanjut.</div></div>
<nav><?php foreach(['index.php'=>['01','Beranda'],'tes.php'=>['02','Tes Baru'],'mahasiswa.php'=>['03','Mahasiswa'],'asesmen.php'=>['04','Riwayat Tes'],'spk.php'=>['05','Prioritas (SPK)'],'laporan.php'=>['06','Laporan']] as $u=>$t) echo '<a href="'.$u.'" class="'.($hal==$u?'on':'').'"><b>'.$t[0].'</b>'.e($t[1]).'</a>'; ?></nav></aside>
<main>
