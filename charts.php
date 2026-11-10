<?php // Butuh $R (hasil ranking) dan $pdo
$w=bobot($pdo); $n=count($R)?:1; $c=['Tinggi'=>0,'Sedang'=>0,'Rendah'=>0];
foreach($R as $r) $c[kategori($r)[0]]++;
$av=[]; foreach(array_keys(LBL) as $k) $av[]=round(array_sum(array_column(array_column($R,'risk'),$k))/$n*100);
$bt=[]; foreach($pdo->query("SELECT * FROM asesmen")->fetchAll() as $a) $bt[date('o-\WW',strtotime($a['tanggal']))][]=hitung($a,$w)['skor']; ksort($bt);
$tg=array_map(fn($k)=>'Mgg '.substr($k,-2),array_keys($bt)); $tv=array_map(fn($v)=>round(array_sum($v)/count($v),3),array_values($bt));
$fm=[]; foreach($R as $r) $fm[$r['fakultas']][]=$r['skor'];
$fl=array_keys($fm); $fv=array_map(fn($v)=>round(array_sum($v)/count($v),3),array_values($fm));
?>
<div class="ch">
<div><h3>Sebaran kategori</h3><div class="cv"><canvas id="c1"></canvas></div></div>
<div><h3>Risiko rata-rata per indikator (%)</h3><div class="cv"><canvas id="c2"></canvas></div></div>
<div><h3>Tren skor rata-rata per minggu</h3><div class="cv"><canvas id="c3"></canvas></div></div>
<div><h3>Skor rata-rata per fakultas</h3><div class="cv"><canvas id="c4"></canvas></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.maintainAspectRatio=false;Chart.defaults.animation=false;Chart.defaults.font.family='"DM Sans",system-ui,sans-serif';Chart.defaults.color='#6b7270';
const P={moss:'#2f5d50',clay:'#c4553a',ochre:'#d9a441',sage:'#8fb39a'};
new Chart(c1,{type:'doughnut',data:{labels:['Tinggi','Sedang','Rendah'],datasets:[{data:<?=json_encode(array_values($c))?>,backgroundColor:[P.clay,P.ochre,P.sage],borderColor:'#f3eee4',borderWidth:3}]},options:{cutout:'62%'}});
new Chart(c2,{type:'bar',data:{labels:<?=json_encode(array_values(LBL))?>,datasets:[{data:<?=json_encode($av)?>,backgroundColor:P.moss,borderRadius:3}]},options:{indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,max:100}}}});
new Chart(c3,{type:'line',data:{labels:<?=json_encode($tg)?>,datasets:[{data:<?=json_encode($tv)?>,borderColor:P.clay,backgroundColor:'rgba(196,85,58,.12)',fill:true,tension:.3,pointRadius:3}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,max:1}}}});
new Chart(c4,{type:'bar',data:{labels:<?=json_encode($fl)?>,datasets:[{data:<?=json_encode($fv)?>,backgroundColor:P.ochre,borderRadius:3}]},options:{indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,max:1}}}});
</script>
