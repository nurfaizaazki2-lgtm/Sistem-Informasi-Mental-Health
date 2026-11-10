<?php require_once 'config.php'; require_once 'functions.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
    if(isset($_POST['hapus'])) $pdo->prepare("DELETE FROM asesmen WHERE id_asesmen=?")->execute([$_POST['hapus']]);
    else $pdo->prepare("UPDATE asesmen SET tanggal=?,phq9=?,gad7=?,stres=?,jam_tidur=?,dukungan=?,item9=?,catatan=? WHERE id_asesmen=?")
        ->execute([$_POST['tanggal'],(int)nilai($_POST['phq9'],0,27),(int)nilai($_POST['gad7'],0,21),(int)nilai($_POST['stres'],0,10),nilai($_POST['jam_tidur'],0,14),(int)nilai($_POST['dukungan'],0,10),(int)nilai($_POST['item9'],0,3),trim($_POST['catatan'])?:null,$_POST['id']]);
    header('Location: asesmen.php'); exit;
  }catch(Exception $x){ $err='Gagal menyimpan data tes.'; }
}
$e=null; if(isset($_GET['edit'])){ $s=$pdo->prepare("SELECT a.*,m.nama FROM asesmen a JOIN mahasiswa m USING(id_mahasiswa) WHERE id_asesmen=?"); $s->execute([$_GET['edit']]); $e=$s->fetch()?:null; }
$q='%'.($_GET['q']??'').'%'; $per=20; $p=max(1,(int)($_GET['p']??1));
$c=$pdo->prepare("SELECT COUNT(*) FROM asesmen a JOIN mahasiswa m USING(id_mahasiswa) WHERE m.nama LIKE ? OR m.nim LIKE ? OR m.prodi LIKE ?"); $c->execute([$q,$q,$q]); $tot=$c->fetchColumn();
$s=$pdo->prepare("SELECT a.*,m.nim,m.nama,m.prodi FROM asesmen a JOIN mahasiswa m USING(id_mahasiswa) WHERE m.nama LIKE ? OR m.nim LIKE ? OR m.prodi LIKE ? ORDER BY a.tanggal DESC,a.id_asesmen DESC LIMIT $per OFFSET ".(($p-1)*$per)); $s->execute([$q,$q,$q]); $rows=$s->fetchAll();
include 'header.php'; ?>
<div class="kick">04 · arsip</div><h1>Riwayat tes.</h1><p class="lead">Tes baru dibuat lewat menu <a href="tes.php">Tes Baru</a>. Di sini data hanya dikoreksi atau dihapus.</p>
<?php if($e): ?><div class="sec"><h2>Koreksi tes: <?=e($e['nama'])?></h2><?php if(!empty($err)) echo '<p class="err">'.e($err).'</p>'; ?>
<form method="post" class="f" style="margin-top:12px"><input type="hidden" name="id" value="<?=$e['id_asesmen']?>">
<div><label>Tanggal</label><input type="date" name="tanggal" value="<?=e($e['tanggal'])?>" required></div>
<div><label>PHQ-9 (0-27)</label><input type="number" name="phq9" min="0" max="27" value="<?=e($e['phq9'])?>"></div>
<div><label>GAD-7 (0-21)</label><input type="number" name="gad7" min="0" max="21" value="<?=e($e['gad7'])?>"></div>
<div><label>Butir 9 PHQ-9 (0-3)</label><input type="number" name="item9" min="0" max="3" value="<?=e($e['item9'])?>"></div>
<div><label>Stres (0-10)</label><input type="number" name="stres" min="0" max="10" value="<?=e($e['stres'])?>"></div>
<div><label>Jam tidur</label><input type="number" name="jam_tidur" min="0" max="14" step="0.5" value="<?=e($e['jam_tidur'])?>"></div>
<div><label>Dukungan (0-10)</label><input type="number" name="dukungan" min="0" max="10" value="<?=e($e['dukungan'])?>"></div>
<div><label>Catatan</label><input name="catatan" maxlength="255" value="<?=e($e['catatan'])?>"></div>
<div style="align-self:end"><button class="b p">Simpan</button> <a class="b" href="asesmen.php">Batal</a></div></form></div><?php endif; ?>
<div class="sec"><h2><?=$tot?> tes tercatat</h2><form method="get" style="margin:10px 0"><input name="q" placeholder="Cari nama / NIM / prodi, lalu Enter" value="<?=e($_GET['q']??'')?>"></form>
<div class="tw"><table><tr><th>Tanggal</th><th>Mahasiswa</th><th>PHQ</th><th>GAD</th><th>Stres</th><th>Tidur</th><th>Dukung</th><th></th></tr>
<?php foreach($rows as $r): ?><tr><td class="mono"><?=e($r['tanggal'])?></td><td><a href="hasil.php?id=<?=$r['id_asesmen']?>"><b><?=e($r['nama'])?></b></a><br><span class="sub"><?=e($r['nim'])?> · <?=e($r['prodi'])?></span></td>
<td><?=$r['phq9']?><?=$r['item9']>0?' <span class="seg">⚑</span>':''?></td><td><?=$r['gad7']?></td><td><?=$r['stres']?></td><td><?=e($r['jam_tidur'])?></td><td><?=$r['dukungan']?></td>
<td style="white-space:nowrap"><a class="b s" href="?edit=<?=$r['id_asesmen']?>">Koreksi</a> <form method="post" style="display:inline" onsubmit="return confirm('Hapus data tes ini?')"><button class="b s d" name="hapus" value="<?=$r['id_asesmen']?>">Hapus</button></form></td></tr><?php endforeach; ?></table></div>
<?=halaman($tot,$per,$p)?></div>
<?php include 'footer.php'; ?>
