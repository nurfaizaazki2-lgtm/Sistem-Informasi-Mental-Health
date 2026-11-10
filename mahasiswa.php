<?php require_once 'config.php'; require_once 'functions.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
    if(isset($_POST['hapus'])) $pdo->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa=?")->execute([$_POST['hapus']]);
    else{
      $pr=$_POST['prodi']??''; if(!isset(PRODI[$pr])) throw new Exception('Prodi tidak valid.');
      $d=[trim($_POST['nim']),trim($_POST['nama']),$_POST['jk']=='P'?'P':'L',$pr,PRODI[$pr],(int)nilai($_POST['semester'],1,14)];
      if(!empty($_POST['id'])) $pdo->prepare("UPDATE mahasiswa SET nim=?,nama=?,jk=?,prodi=?,fakultas=?,semester=? WHERE id_mahasiswa=?")->execute(array_merge($d,[$_POST['id']]));
      else $pdo->prepare("INSERT INTO mahasiswa(nim,nama,jk,prodi,fakultas,semester) VALUES(?,?,?,?,?,?)")->execute($d);
    }
    header('Location: mahasiswa.php'); exit;
  }catch(Exception $x){ $err='Gagal menyimpan: NIM mungkin sudah terdaftar atau data tidak valid.'; }
}
$e=['id_mahasiswa'=>'','nim'=>'','nama'=>'','jk'=>'L','prodi'=>'','semester'=>1];
if(isset($_GET['edit'])){ $s=$pdo->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa=?"); $s->execute([$_GET['edit']]); $e=$s->fetch()?:$e; }
$q='%'.($_GET['q']??'').'%'; $per=20; $p=max(1,(int)($_GET['p']??1));
$c=$pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE nama LIKE ? OR nim LIKE ? OR prodi LIKE ? OR fakultas LIKE ?"); $c->execute([$q,$q,$q,$q]); $tot=$c->fetchColumn();
$s=$pdo->prepare("SELECT * FROM mahasiswa WHERE nama LIKE ? OR nim LIKE ? OR prodi LIKE ? OR fakultas LIKE ? ORDER BY nama LIMIT $per OFFSET ".(($p-1)*$per)); $s->execute([$q,$q,$q,$q]); $rows=$s->fetchAll();
include 'header.php'; ?>
<div class="kick">03 · data induk</div><h1>Mahasiswa.</h1>
<div class="sec"><h2><?=$e['id_mahasiswa']?'Ubah data':'Tambah mahasiswa'?></h2><?php if(!empty($err)) echo '<p class="err">'.e($err).'</p>'; ?>
<form method="post" class="f" style="margin-top:12px"><input type="hidden" name="id" value="<?=e($e['id_mahasiswa'])?>">
<div><label>NIM</label><input name="nim" value="<?=e($e['nim'])?>" required></div>
<div><label>Nama</label><input name="nama" value="<?=e($e['nama'])?>" required></div>
<div><label>Jenis kelamin</label><select name="jk"><option value="L">Laki-laki</option><option value="P"<?=$e['jk']=='P'?' selected':''?>>Perempuan</option></select></div>
<div><label>Program studi</label><select name="prodi" required><option value="">— pilih —</option><?php foreach(PRODI as $pr=>$f) echo '<option'.($e['prodi']==$pr?' selected':'').'>'.e($pr).'</option>'; ?></select></div>
<div><label>Semester</label><input type="number" name="semester" min="1" max="14" value="<?=e($e['semester'])?>"></div>
<div style="align-self:end"><button class="b p">Simpan</button> <?php if($e['id_mahasiswa']) echo '<a class="b" href="mahasiswa.php">Batal</a>'; ?></div></form></div>
<div class="sec"><h2><?=$tot?> mahasiswa</h2><form method="get" style="margin:10px 0"><input name="q" placeholder="Cari nama / NIM / prodi / fakultas, lalu Enter" value="<?=e($_GET['q']??'')?>"></form>
<div class="tw"><table><tr><th>NIM</th><th>Nama</th><th>JK</th><th>Prodi</th><th>Smt</th><th></th></tr>
<?php foreach($rows as $r): ?><tr><td class="mono"><?=e($r['nim'])?></td><td><b><?=e($r['nama'])?></b></td><td><?=e($r['jk'])?></td><td><?=e($r['prodi'])?><br><span class="sub"><?=e($r['fakultas'])?></span></td><td><?=e($r['semester'])?></td>
<td style="white-space:nowrap"><a class="b s" href="?edit=<?=$r['id_mahasiswa']?>">Ubah</a> <form method="post" style="display:inline" onsubmit="return confirm('Hapus mahasiswa beserta seluruh riwayat tesnya?')"><button class="b s d" name="hapus" value="<?=$r['id_mahasiswa']?>">Hapus</button></form></td></tr><?php endforeach; ?></table></div>
<?=halaman($tot,$per,$p)?></div>
<?php include 'footer.php'; ?>
