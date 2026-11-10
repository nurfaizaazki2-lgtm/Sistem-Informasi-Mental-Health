<?php require_once 'config.php'; require_once 'functions.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
    $ph=array_map('intval',$_POST['phq']??[]); $ga=array_map('intval',$_POST['gad']??[]);
    if(count($ph)!=9||count($ga)!=7) throw new Exception('Semua pertanyaan PHQ-9 dan GAD-7 harus dijawab.');
    foreach(array_merge($ph,$ga) as $x) if($x<0||$x>3) throw new Exception('Jawaban tidak valid.');
    ksort($ph); ksort($ga);
    $pdo->beginTransaction();
    $id=(int)($_POST['id_mahasiswa']??0);
    if(!$id){ // mahasiswa baru
      $nim=trim($_POST['nim']??''); $nama=trim($_POST['nama']??''); $prodi=$_POST['prodi']??'';
      if($nim===''||$nama===''||!isset(PRODI[$prodi])) throw new Exception('Lengkapi NIM, nama, dan program studi untuk mahasiswa baru.');
      $pdo->prepare("INSERT INTO mahasiswa(nim,nama,jk,prodi,fakultas,semester) VALUES(?,?,?,?,?,?)")
          ->execute([$nim,$nama,(($_POST['jk']??'')=='P')?'P':'L',$prodi,PRODI[$prodi],(int)nilai($_POST['semester']??1,1,14)]);
      $id=(int)$pdo->lastInsertId();
    }
    $pdo->prepare("INSERT INTO asesmen(id_mahasiswa,tanggal,phq9,gad7,stres,jam_tidur,dukungan,item9,item_phq,item_gad) VALUES(?,CURDATE(),?,?,?,?,?,?,?,?)")
        ->execute([$id,array_sum($ph),array_sum($ga),(int)nilai($_POST['stres'],0,10),nilai($_POST['jam_tidur'],0,14),(int)nilai($_POST['dukungan'],0,10),$ph[8],implode(',',$ph),implode(',',$ga)]);
    $aid=$pdo->lastInsertId(); $pdo->commit();
    header('Location: hasil.php?id='.$aid); exit;
  }catch(PDOException $x){ if($pdo->inTransaction())$pdo->rollBack(); $err=$x->getCode()=='23000'?'NIM tersebut sudah terdaftar. Pilih namanya dari daftar mahasiswa terdaftar.':'Gagal menyimpan data.'; }
  catch(Exception $x){ if($pdo->inTransaction())$pdo->rollBack(); $err=$x->getMessage(); }
}
$mhs=$pdo->query("SELECT id_mahasiswa,nim,nama FROM mahasiswa ORDER BY nama")->fetchAll();
include 'header.php'; ?>
<div class="kick">02 · tes baru</div><h1>Mulai <em>tes</em> psikologi.</h1>
<p class="lead">Dua skala skrining baku (PHQ-9 dan GAD-7) ditambah tiga pertanyaan gaya hidup. Pilih mahasiswa yang sudah terdaftar, atau daftarkan mahasiswa baru langsung dari halaman ini.</p>
<?php if($err) echo '<p class="err">'.e($err).'</p>'; ?>
<form method="post">
<div class="sec"><h2>1. Siapa yang tes?</h2>
<div class="f" style="display:grid;gap:18px 22px;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));margin-top:12px">
<div style="grid-column:1/-1"><label>Mahasiswa terdaftar (<?=count($mhs)?>)</label><select name="id_mahasiswa"><option value="0">— atau daftarkan mahasiswa baru di bawah —</option>
<?php foreach($mhs as $m) echo '<option value="'.$m['id_mahasiswa'].'"'.(($_POST['id_mahasiswa']??'')==$m['id_mahasiswa']?' selected':'').'>'.e($m['nama'].' · '.$m['nim']).'</option>'; ?></select></div>
<div><label>NIM (mahasiswa baru)</label><input name="nim" value="<?=old('nim')?>"></div>
<div><label>Nama lengkap</label><input name="nama" value="<?=old('nama')?>"></div>
<div><label>Jenis kelamin</label><select name="jk"><option value="L">Laki-laki</option><option value="P"<?=(($_POST['jk']??'')=='P')?' selected':''?>>Perempuan</option></select></div>
<div><label>Program studi</label><select name="prodi"><option value="">— pilih —</option>
<?php $g=[]; foreach(PRODI as $p=>$f)$g[$f][]=$p; foreach($g as $f=>$ps){ echo '<optgroup label="'.e($f).'">'; foreach($ps as $p) echo '<option'.(($_POST['prodi']??'')==$p?' selected':'').'>'.e($p).'</option>'; echo '</optgroup>'; } ?></select></div>
<div><label>Semester</label><input type="number" name="semester" min="1" max="14" value="<?=old('semester')?:1?>"></div></div></div>

<div class="sec"><h2>2. Dua minggu terakhir (PHQ-9)</h2><p class="sub">Seberapa sering Anda terganggu oleh hal berikut dalam 2 minggu terakhir?</p>
<?php foreach(PHQ as $i=>$t){ echo '<div class="q"><p><b>'.str_pad($i+1,2,'0',STR_PAD_LEFT).'</b>'.e($t).'</p><div class="op">'; for($o=0;$o<4;$o++)echo radio('phq',$i,$o); echo '</div></div>'; } ?></div>

<div class="sec"><h2>3. Kecemasan (GAD-7)</h2><p class="sub">Dalam 2 minggu terakhir, seberapa sering Anda mengalami hal berikut?</p>
<?php foreach(GAD as $i=>$t){ echo '<div class="q"><p><b>'.str_pad($i+1,2,'0',STR_PAD_LEFT).'</b>'.e($t).'</p><div class="op">'; for($o=0;$o<4;$o++)echo radio('gad',$i,$o); echo '</div></div>'; } ?></div>

<div class="sec"><h2>4. Keseharian</h2>
<div style="display:grid;gap:22px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-top:12px">
<div><label>Stres akademik (0 = tenang, 10 = sangat tertekan): <b id="v1">5</b></label><input type="range" name="stres" min="0" max="10" value="5" oninput="v1.textContent=this.value"></div>
<div><label>Rata-rata jam tidur per hari</label><input type="number" name="jam_tidur" min="0" max="14" step="0.5" value="<?=old('jam_tidur')?:7?>"></div>
<div><label>Dukungan sosial (0 = tidak ada, 10 = sangat kuat): <b id="v2">5</b></label><input type="range" name="dukungan" min="0" max="10" value="5" oninput="v2.textContent=this.value"></div></div>
<p style="margin-top:30px"><button class="b p">Lihat hasil tes</button></p></div>
</form>
<?php include 'footer.php'; ?>
