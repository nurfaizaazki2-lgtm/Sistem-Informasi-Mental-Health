<?php require_once 'config.php'; require_once 'functions.php';
$s=$pdo->prepare("SELECT a.*,m.nim,m.nama,m.prodi,m.fakultas FROM asesmen a JOIN mahasiswa m USING(id_mahasiswa) WHERE a.id_asesmen=?");
$s->execute([(int)($_GET['id']??0)]); $a=$s->fetch(); if(!$a){ header('Location: index.php'); exit; }
$w=bobot($pdo); $h=hitung($a,$w); $k=kategori($h);
$s=$pdo->prepare("SELECT * FROM asesmen WHERE id_mahasiswa=? AND (tanggal<? OR (tanggal=? AND id_asesmen<?)) ORDER BY tanggal DESC,id_asesmen DESC LIMIT 1");
$s->execute([$a['id_mahasiswa'],$a['tanggal'],$a['tanggal'],$a['id_asesmen']]); $p=$s->fetch(); $ph=$p?hitung($p,$w):null;
$kon=$h['kon']; arsort($kon);
include 'header.php'; ?>
<div class="kop"><b>MindCampus · Layanan Konseling Mahasiswa</b><br><span>Lembar hasil skrining psikologi · dicetak <?=date('d-m-Y')?></span></div>
<div class="kick">Hasil tes · <?=e($a['tanggal'])?></div>
<h1><?=e($a['nama'])?></h1><p class="lead"><?=e($a['nim'])?> · <?=e($a['prodi'])?> · <?=e($a['fakultas'])?></p>
<?php if($h['segera']): ?><div class="note"><b>Satu jawaban membutuhkan perhatian khusus.</b><br>Pada butir terakhir PHQ-9, jawaban menunjukkan adanya pikiran bahwa lebih baik tidak hidup atau ingin melukai diri. Perasaan itu perlu didengar. Segera bicarakan dengan konselor kampus, tenaga kesehatan, atau orang tepercaya. Jika ada bahaya langsung, hubungi 112, atau layanan kesehatan jiwa Kemenkes di 119 ext. 8.</div><?php endif; ?>
<div class="st">
<div><b class="big" style="font-size:54px"><?=number_format($h['skor'],2)?></b><span>skor SPK (V)</span></div>
<div><b><span class="bd <?=$k[1]?>" style="font-size:20px;padding:4px 16px"><?=$k[0]?></span></b><span>kategori risiko</span></div>
<div><b><?=$a['phq9']?></b><span>PHQ-9 · <?=sevPHQ($a['phq9'])?></span></div>
<div><b><?=$a['gad7']?></b><span>GAD-7 · <?=sevGAD($a['gad7'])?></span></div>
<?php if($ph): $d=$h['skor']-$ph['skor']; ?><div><b style="color:var(--<?=$d>0.005?'clay':($d<-0.005?'moss':'mu')?>)"><?=($d>0?'+':'').number_format($d,2)?></b><span>dibanding tes sebelumnya (<?=e($p['tanggal'])?>)</span></div><?php endif; ?></div>

<div class="sec"><h2>Apa yang paling berpengaruh</h2><p class="sub">Kontribusi tiap indikator terhadap skor V (bobot × risiko).</p>
<?php $mx=max($kon)?:1; foreach($kon as $c=>$v) echo '<div class="ind"><span>'.e(LBL[$c]).'</span><i class="mb"><i style="width:'.round($v/$mx*100).'%"></i></i><span class="mono">'.number_format($v,3).'</span></div>'; ?></div>

<div class="sec"><h2>Rencana tindak lanjut</h2>
<p><b><?=e($k[2])?>.</b></p><ul><?php foreach(array_slice(array_keys($kon),0,2) as $c) echo '<li>'.e(ucfirst(REK[$c])).'</li>'; ?></ul>
<p class="noprint"><button class="b p" onclick="window.print()">Cetak lembar hasil</button> <a class="b" href="tes.php">Tes mahasiswa lain</a> <a class="b" href="index.php">Kembali ke beranda</a></p></div>
<div class="ttd"><div>Mahasiswa<br><br><br><br>( <?=e($a['nama'])?> )</div><div>Konselor<br><br><br><br>(………………………)</div></div>
<?php include 'footer.php'; ?>
