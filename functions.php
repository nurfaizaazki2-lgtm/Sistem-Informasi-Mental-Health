<?php
const LBL = ['phq9'=>'Depresi (PHQ-9)','gad7'=>'Kecemasan (GAD-7)','stres'=>'Stres akademik','tidur'=>'Kurang tidur','dukungan'=>'Minim dukungan sosial'];
const REK = ['phq9'=>'skrining lanjutan suasana hati & aktivasi perilaku','gad7'=>'latihan relaksasi & pernapasan','stres'=>'konseling manajemen waktu & beban kuliah','tidur'=>'edukasi sleep hygiene','dukungan'=>'hubungkan ke kelompok sebaya / peer support'];
const OPSI = ['Tidak pernah','Beberapa hari','Lebih dari separuh hari','Hampir setiap hari'];
const PHQ = ['Kurang berminat atau bergairah melakukan sesuatu','Merasa murung, sedih, atau putus asa','Sulit tidur, sering terbangun, atau tidur terlalu banyak','Merasa lelah atau kurang berenergi','Nafsu makan menurun atau makan berlebihan','Merasa buruk tentang diri sendiri, merasa gagal, atau mengecewakan keluarga','Sulit berkonsentrasi, misalnya saat membaca atau mengerjakan tugas','Bergerak/berbicara sangat lambat, atau justru gelisah tidak bisa diam','Terpikir bahwa lebih baik tidak hidup, atau ingin melukai diri sendiri'];
const GAD = ['Merasa gugup, cemas, atau tegang','Tidak mampu menghentikan atau mengendalikan rasa khawatir','Terlalu mengkhawatirkan banyak hal','Sulit untuk rileks','Sangat gelisah sehingga sulit duduk diam','Mudah kesal atau mudah marah','Merasa takut seolah akan terjadi sesuatu yang buruk'];
const PRODI=[
  'Pendidikan Dokter'=>'Kedokteran & Kesehatan',
  'Keperawatan'=>'Kedokteran & Kesehatan',
  'Farmasi'=>'Kedokteran & Kesehatan',
  'Kesehatan Masyarakat'=>'Kedokteran & Kesehatan',
  'Teknik Informatika'=>'Teknik',
  'Teknik Sipil'=>'Teknik',
  'Teknik Mesin'=>'Teknik',
  'Teknik Elektro'=>'Teknik',
  'Teknik Industri'=>'Teknik',
  'Manajemen'=>'Ekonomi & Bisnis',
  'Akuntansi'=>'Ekonomi & Bisnis',
  'Ekonomi Pembangunan'=>'Ekonomi & Bisnis',
  'Ilmu Hukum'=>'Hukum',
  'Psikologi'=>'Psikologi',
  'Ilmu Komunikasi'=>'Ilmu Sosial & Politik',
  'Hubungan Internasional'=>'Ilmu Sosial & Politik',
  'Administrasi Publik'=>'Ilmu Sosial & Politik',
  'Pendidikan Matematika'=>'Keguruan & Ilmu Pendidikan',
  'Pendidikan Bahasa Inggris'=>'Keguruan & Ilmu Pendidikan',
  'PGSD'=>'Keguruan & Ilmu Pendidikan',
  'Biologi'=>'MIPA',
  'Kimia'=>'MIPA',
  'Statistika'=>'MIPA'
];

function nilai($v,$a,$b){ return min($b,max($a,(float)$v)); }
function bobot($pdo){ return $pdo->query("SELECT kriteria,bobot FROM bobot")->fetchAll(PDO::FETCH_KEY_PAIR); }
function sevPHQ($s){ return $s<5?'Minimal':($s<10?'Ringan':($s<15?'Sedang':($s<20?'Sedang-berat':'Berat'))); }
function sevGAD($s){ return $s<5?'Minimal':($s<10?'Ringan':($s<15?'Sedang':'Berat')); }

// SAW: tiap kriteria dinormalisasi jadi tingkat risiko 0-1
function risiko($r){
    return ['phq9'=>$r['phq9']/27,'gad7'=>$r['gad7']/21,'stres'=>$r['stres']/10,
            'tidur'=>max(0,8-$r['jam_tidur'])/8,'dukungan'=>(10-$r['dukungan'])/10];
}
// V = sum(bobot ternormalisasi x risiko). Butir 9 PHQ-9 > 0 => penanda "segera" (aturan keselamatan, di luar skor)
function hitung($r,$w){
    $k=risiko($r); $t=array_sum($w)?:1; $s=0; $dom=''; $m=-1; $kon=[];
    foreach($k as $c=>$v){ $x=$w[$c]/$t*$v; $kon[$c]=$x; $s+=$x; if($x>$m){$m=$x;$dom=$c;} }
    return $r+['skor'=>$s,'dom'=>$dom,'risk'=>$k,'kon'=>$kon,'segera'=>((int)($r['item9']??0))>0];
}
function kategori($r){
    if(!empty($r['segera'])) return ['Tinggi','r','Prioritas segera: temui konselor / tenaga profesional secepatnya'];
    $s=$r['skor'];
    return $s>=.5?['Tinggi','r','Rujuk ke psikolog / psikiater']:($s>=.3?['Sedang','y','Konseling terjadwal']:['Rendah','g','Psikoedukasi & pantau berkala']);
}
function cuaca($a){
    return $a<.25?['Cerah','Sebagian besar mahasiswa dalam kondisi baik.']:($a<.35?['Berawan','Sebagian mahasiswa perlu dipantau.']:['Mendung','Banyak mahasiswa membutuhkan dukungan.']);
}
// Tes terbaru tiap mahasiswa; "segera" dahulu, lalu skor tertinggi
function ranking($pdo){
    $w=bobot($pdo);
    $rows=$pdo->query("SELECT m.*, a.* FROM asesmen a JOIN mahasiswa m USING(id_mahasiswa)
        WHERE a.id_asesmen=(SELECT a2.id_asesmen FROM asesmen a2 WHERE a2.id_mahasiswa=a.id_mahasiswa
        ORDER BY a2.tanggal DESC, a2.id_asesmen DESC LIMIT 1)")->fetchAll();
    $rows=array_map(fn($r)=>hitung($r,$w),$rows);
    usort($rows,fn($a,$b)=>[(int)$b['segera'],$b['skor']]<=>[(int)$a['segera'],$a['skor']]);
    return $rows;
}
function tabelRank($R){
    echo '<div class="tw"><table><thead><tr><th>#</th><th>Mahasiswa</th><th>Skor V</th><th>Kategori</th><th>Tindak lanjut</th></tr></thead><tbody>';
    foreach($R as $i=>$r){ $k=kategori($r);
        echo '<tr><td class="mono">'.str_pad($i+1,2,'0',STR_PAD_LEFT).'</td><td><a href="hasil.php?id='.$r['id_asesmen'].'"><b>'.e($r['nama']).'</b></a><br><span class="sub">'.e($r['nim']).' · '.e($r['prodi']).'</span></td>'
            .'<td><span class="mono">'.number_format($r['skor'],3).'</span><i class="mb"><i style="width:'.round($r['skor']*100).'%"></i></i></td>'
            .'<td><span class="bd '.$k[1].'">'.$k[0].'</span>'.(!empty($r['segera'])?' <span class="seg">⚑ segera</span>':'').'</td>'
            .'<td>'.e($k[2]).'<br><span class="sub">fokus: '.e(REK[$r['dom']]).'</span></td></tr>'; }
    echo '</tbody></table></div>';
}
function halaman($total,$per,$p){
    $n=max(1,(int)ceil($total/$per)); $o='<div class="pg">';
    for($i=1;$i<=$n;$i++){ $q=$_GET; $q['p']=$i; $o.='<a class="'.($i==$p?'on':'').'" href="?'.e(http_build_query($q)).'">'.$i.'</a>'; }
    return $o.'</div>';
}
function radio($nm,$i,$o){ return '<label><input type="radio" name="'.$nm.'['.$i.']" value="'.$o.'" required'.((isset($_POST[$nm][$i])&&$_POST[$nm][$i]==(string)$o)?' checked':'').'><span>'.e(OPSI[$o]).'</span></label>'; }
function old($k){ return e($_POST[$k]??''); }
