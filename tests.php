<?php
require_once('lib/tracklogPhp.main.php');


// $file = 'test_files/external_files/test_322/322km';
// $file = "test_files/external_files_avbox/certo - trekking-morro-do-bau-ilhota-sc1-morro-do-bau";
// $file = "test_files/external_files_avbox/certo - travessia-toca-do-lopo-a-casa-de-pedra-travessia-toca-do-lopo-a-casa-de-pedra";
// $file = "test_files/external_files_avbox/errado - elevacao - cachoeira-do-gato-e-praia-do-gato-ilhabela-tracklog-cachoeira-do-gato";
// $file = "test_files/external_files_avbox/corrida-rustica-ilhabela-ponta-das-canas-corrida-rustica-ilhabela-ponta-das-canas-10km";

$kml = new KML("C:\Users\Alex Sandro A. Sozua\Downloads/travessia-marinzinho-x-marins-marinzinho-x-marins-by-boney1.kml");
// $gpx = new GPX($file.'.gpx');
// $csv = new CSV($file.'.csv');
// $tcx = new TCX($file.'.tcx');
// $json = new GeoJson($file.'.js');
// $json2 = new GeoJson($file.'.2.js');

// echo count($kml->getPoints('kilometers')) . "<br>";
// echo $kml->getElevationGain() . "<br>";
// echo $kml->getElevationLoss() . "<br>";
// echo count($kml->getPoints());
print_r($kml->getPoints());
print_r($kml->getElevations());
// echo count($kml->getPaces());
// print_r($kml->getPaces());
// print_r($kml->getAverageSpeed());

// $paces = $kml->getPaces("minutes", true);
// foreach ($paces as $key => $value) {
// 	echo number_format($value,2,",","") . "<br>";
// }

// for ($i=0; $i < count($paces); $i++) {
// 	if(isset($paces[$i-1]) && isset($paces[$i-2]) && isset($paces[$i+1]) && isset($paces[$i+2])){
// 		echo number_format((($paces[$i-1]+$paces[$i-2]+$paces[$i]+$paces[$i+1]+$paces[$i+2])/5)/60, 2, ",", "") . "<br>";
// 	}elseif ($i == 0) {
// 		echo number_format((($paces[$i+1]+$paces[$i+2]+$paces[$i]+$paces[$i+3]+$paces[$i+4])/5)/60, 2, ",", "") . "<br>";
// 	}elseif ($i == 1) {
// 		echo number_format((($paces[$i-1]+$paces[$i+1]+$paces[$i]+$paces[$i+2]+$paces[$i+3])/5)/60, 2, ",", "") . "<br>";
// 	}elseif ($i == count($paces)-1) {
// 		echo number_format((($paces[$i-1]+$paces[$i-2]+$paces[$i]+$paces[$i-3]+$paces[$i-4])/5)/60, 2, ",", "") . "<br>";
// 	}elseif ($i == count($paces)-2) {
// 		echo number_format((($paces[$i+1]+$paces[$i-1]+$paces[$i]+$paces[$i-2]+$paces[$i-3])/5)/60, 2, ",", "") . "<br>";
// 	}
// 	// if ($i%5 != 0 || $i == 0) {
// 	// 	$pace += $paces[$i];
// 	// }else{
// 	// 	$pace += $paces[$i];
// 	// 	echo number_format(($pace/5)/60,2,",","") . "<br>";
// 	// 	$pace = 0;
// 	// }
// }

// echo count($gpx->getPoints('kilometers')) . "<br>";
// echo count($csv->getPoints('kilometers')) . "<br>";
// echo count($tcx->getPoints('kilometers')) . "<br>";
// echo count($json->getPoints('kilometers')) . "<br>";
// echo count($json2->getPoints('kilometers')) . "<br>";

// print_r($gpx->getPaces("seconds"));
// print_r($gpx->getTimes());


?>