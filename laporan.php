<?php include 'header.php';
$sem=ranking($pdo);
$dari=$_GET['dari']??''; $smp=$_GET['smp']??''; $fak=$_GET['fak']??''; $kat=$_GET['kat']??'';
$R=array_values(array_filter($sem,fn($r)=>(!$dari||$r['tanggal']>=$dari)&&(!$smp||$r['tanggal']<=$smp)&&(!$fak||$r['fakultas']==$fak)&&(!$kat||kategori($r)[0]==$kat)));
$n=count($R)?:1; $avg=array_sum(array_column($R,'skor'))/$n; $cu=cuaca($avg);
$cnt=['Tinggi'=>0,'Sedang'=>0,'Rendah'=>0]; foreach($R as $r)$cnt[kategori($r)[0]]++;
$seg=count(array_filter($R,fn($r)=>$r['segera']));
$rp=array_sum(array_column($R,'phq9'))/$n; $rg=array_sum(array_column($R,'gad7'))/$n; $rt=array_sum(array_column($R,'jam_tidur'))/$n;
$fk=[]; foreach($R as $r){ $f=&$fk[$r['fakultas']]; $f['n']=($f['n']??0)+1; $f['s']=($f['s']??0)+$r['skor']; $f['t']=($f['t']??0)+(kategori($r)[0]=='Tinggi'?1:0); unset($f); } ksort($fk);
$fl=array_unique(array_column($sem,'fakultas')); sort($fl);
$filter=array_filter([$dari||$smp?'tes terakhir '.($dari?:'…').' s.d. '.($smp?:'…'):'',$fak?'fakultas '.$fak:'',$kat?'kategori '.$kat:'']); ?>
<div class="kop"><b>MindCampus · Layanan Konseling Mahasiswa</b><br><span>Laporan skrining kesehatan mental mahasiswa · dicetak <?=date('d-m-Y')?></span></div>

<form method="get" class="f noprint" style="margin-bottom:26px">
<div><label>Tes terakhir dari</label><input type="date" name="dari" value="<?=e($dari)?>"></div>
<div><label>sampai</label><input type="date" name="smp" value="<?=e($smp)?>"></div>
<div><label>Fakultas</label><select name="fak"><option value="">Semua</option><?php foreach($fl as $f) echo '<option'.($fak==$f?' selected':'').'>'.e($f).'</option>'; ?></select></div>
<div><label>Kategori</label><select name="kat"><option value="">Semua</option><?php foreach(['Tinggi','Sedang','Rendah'] as $k) echo '<option'.($kat==$k?' selected':'').'>'.$k.'</option>'; ?></select></div>
<div style="align-self:end"><button class="b">Terapkan</button> <a class="b" href="laporan.php">Reset</a> <button type="button" class="b p" onclick="window.print()">Cetak / Simpan PDF</button></div></form>

<div class="kick">06 · laporan</div><h1>Laporan skrining <em>kesehatan mental</em> mahasiswa.</h1>
<p class="lead"><?=$filter?'Filter: '.e(implode(' · ',$filter)).'. ':'Seluruh mahasiswa, berdasarkan tes terbaru masing-masing. '?>Dari <b><?=count($R)?></b> mahasiswa, <b><?=$cnt['Tinggi']?></b> kategori tinggi (<?=$seg?> perlu tindak lanjut segera), <b><?=$cnt['Sedang']?></b> sedang, <b><?=$cnt['Rendah']?></b> rendah. Indeks rata-rata <?=number_format($avg,2)?>, kondisi: <b><?=e($cu[0])?></b>.</p>
<div class="st"><div><b><?=count($R)?></b><span>mahasiswa</span></div><div><b><?=number_format($rp,1)?></b><span>rata-rata PHQ-9</span></div><div><b><?=number_format($rg,1)?></b><span>rata-rata GAD-7</span></div><div><b><?=number_format($rt,1)?></b><span>rata-rata jam tidur</span></div><div><b style="color:var(--clay)"><?=$seg?></b><span>segera</span></div></div>

<div class="sec"><h2>Grafik</h2><?php include 'charts.php'; ?></div>
<div class="sec"><h2>Ringkasan per fakultas</h2><div class="tw"><table><thead><tr><th>Fakultas</th><th>Mahasiswa</th><th>Rata-rata skor</th><th>Kategori tinggi</th></tr></thead><tbody>
<?php foreach($fk as $f=>$d) echo '<tr><td><b>'.e($f).'</b></td><td>'.$d['n'].'</td><td class="mono">'.number_format($d['s']/$d['n'],3).'</td><td>'.$d['t'].'</td></tr>'; ?></tbody></table></div></div>
<div class="sec"><h2>Daftar prioritas penanganan (<?=count($R)?> mahasiswa)</h2><?php tabelRank($R); ?></div>
<div class="sec"><h3>Catatan metode</h3><p class="sub">Skor V dihitung dengan metode SAW dari PHQ-9, GAD-7, stres akademik, jam tidur, dan dukungan sosial (bobot dapat diatur di menu SPK). Ambang: ≥ 0,50 Tinggi, ≥ 0,30 Sedang, lainnya Rendah. Jawaban butir 9 PHQ-9 yang lebih dari 0 selalu ditandai segera. Laporan ini alat bantu skrining, bukan diagnosis.</p></div>
<div class="ttd"><div>Mengetahui,<br>Kepala Layanan Konseling<br><br><br><br>(………………………)</div><div>………………, <?=date('d-m-Y')?><br>Konselor Penyusun<br><br><br><br>(………………………)</div></div>
<?php include 'footer.php'; ?>
