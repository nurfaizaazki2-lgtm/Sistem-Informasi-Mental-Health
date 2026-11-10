<?php require_once 'config.php'; require_once 'functions.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$pdo->prepare("UPDATE bobot SET bobot=? WHERE kriteria=?");
  foreach(($_POST['w']??[]) as $k=>$v) $u->execute([(int)nilai($v,0,100),$k]);
  header('Location: spk.php'); exit;
}
include 'header.php'; $w=bobot($pdo); $t=array_sum($w)?:1; $R=ranking($pdo); $all=isset($_GET['all']); ?>
<div class="kick">05 · sistem pendukung keputusan</div><h1>Siapa yang <em>didahulukan</em>?</h1>
<p class="lead">Metode SAW (Simple Additive Weighting) meranking mahasiswa dari tes terbarunya. Konselor menentukan bobot tiap kriteria; peringkat dihitung ulang otomatis.</p>
<div class="sec"><h2>Bobot kriteria</h2>
<form method="post" class="f" style="margin-top:12px">
<?php foreach(LBL as $k=>$l): ?><div><label><?=e($l)?> · <b><?=round($w[$k]/$t*100)?>%</b></label><input type="number" name="w[<?=e($k)?>]" min="0" max="100" value="<?=(int)$w[$k]?>"></div><?php endforeach; ?>
<div style="align-self:end"><button class="b p">Simpan bobot</button></div></form>
<p class="sub" style="max-width:75ch;margin-top:14px">Normalisasi: nilai ÷ maksimum (PHQ-9 ÷ 27, GAD-7 ÷ 21, stres ÷ 10); tidur = (8 − jam tidur) ÷ 8; dukungan = (10 − skor) ÷ 10. Skor V = Σ (bobot × risiko). Ambang: ≥ 0,50 Tinggi · ≥ 0,30 Sedang · lainnya Rendah. Aturan keselamatan: jawaban butir 9 PHQ-9 yang lebih dari 0 selalu masuk kategori Tinggi dan berada di urutan paling atas, apa pun skornya.</p></div>
<div class="sec"><h2>Peringkat prioritas (<?=count($R)?> mahasiswa)</h2><?php tabelRank($all?$R:array_slice($R,0,25)); ?>
<p><?php if(!$all&&count($R)>25) echo '<a class="b" href="?all=1">Tampilkan semua</a>'; elseif($all) echo '<a class="b" href="spk.php">Hanya 25 teratas</a>'; ?></p></div>
<?php include 'footer.php'; ?>
