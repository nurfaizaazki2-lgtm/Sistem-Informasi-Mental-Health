<?php include 'header.php';
$R=ranking($pdo); $n=count($R)?:1;
$jt=$pdo->query("SELECT COUNT(*) FROM asesmen")->fetchColumn(); $tt=$pdo->query("SELECT MAX(tanggal) FROM asesmen")->fetchColumn();
$avg=array_sum(array_column($R,'skor'))/$n; $cu=cuaca($avg);
$tinggi=count(array_filter($R,fn($r)=>kategori($r)[0]=='Tinggi')); $seg=count(array_filter($R,fn($r)=>$r['segera']));
$grp=[]; foreach($R as $r) $grp[$r['fakultas']][]=$r; ksort($grp); ?>
<div class="kick">Beranda · cuaca batin kampus</div>
<h1>Hari ini kampus <em><?=e($cu[0])?></em>.</h1>
<p class="lead"><?=e($cu[1])?> Indeks rata-rata <?=number_format($avg,2)?> dari <?=count($R)?> mahasiswa yang pernah tes.</p>
<div class="st">
<div><b><?=count($R)?></b><span>mahasiswa dites</span></div><div><b><?=$jt?></b><span>total tes</span></div>
<div><b><?=$tinggi?></b><span>kategori tinggi</span></div><div><b style="color:var(--clay)"><?=$seg?></b><span>perlu tindak lanjut segera</span></div>
<div><b style="font-size:26px;padding-top:12px"><?=e($tt)?></b><span>tes terakhir</span></div></div>

<div class="sec"><h2>Peta cuaca batin</h2><p class="lead" style="margin-bottom:8px">Satu titik adalah satu mahasiswa (tes terbaru), diurutkan dari skor tertinggi per fakultas. Arahkan kursor untuk melihat nama.</p>
<?php foreach($grp as $f=>$L): ?><div class="fk"><span><b><?=e($f)?></b><br><span class="sub"><?=count($L)?> mahasiswa</span></span><div class="dots">
<?php foreach($L as $r){ $k=kategori($r); echo '<a class="dot '.$k[1].($r['segera']?' s':'').'" href="hasil.php?id='.$r['id_asesmen'].'" title="'.e($r['nama'].' · '.number_format($r['skor'],2)).'"></a>'; } ?></div></div><?php endforeach; ?>
<div class="lg"><span><i class="dot g"></i>Rendah</span><span><i class="dot y"></i>Sedang</span><span><i class="dot r"></i>Tinggi</span><span><i class="dot r s"></i>Butuh tindak lanjut segera</span></div></div>

<div class="sec"><h2>Grafik</h2><?php include 'charts.php'; ?></div>
<div class="sec"><h2>Lima prioritas pertama</h2><?php tabelRank(array_slice($R,0,5)); ?><p><a class="b" href="spk.php">Lihat seluruh peringkat</a></p></div>
<?php include 'footer.php'; ?>
