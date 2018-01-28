<?php
require_once('lib/tracklogPhp.main.php');

$tcx = new GeoJSON('test_files/27 (1).js');
echo count($tcx->getPoints());

$tcx->out("GeoJSON", "test_files/teste");

$tcx = new GeoJSON('test_files/teste.js');
echo count($tcx->getPoints());

// $extensions = ['kml', 'tcx', 'gpx', 'csv', 'js'];
// $files = scandir("test_files/external_files_diversos");
// sort($files, SORT_NUMERIC);
// $array = array();
// foreach ($files as $key => $file) {
// 	$extension = pathinfo($file, PATHINFO_EXTENSION);
// 	if (in_array($extension, $extensions)) {
// 		$file_path = "test_files/external_files_diversos/".$file;
// 		try {
// 			($extension == "js") ? $extension = "geojson" : 0;
// 			echo "Inicio $file <br>";
// 			$time_start = microtime(true);
// 			$tracklog = new $extension($file_path);
			
// 			$array[$key]["Nome"] = $file;
// 			$array[$key]["Distance"] = number_format($tracklog->getTotalDistance("kilometers"), 2, ",", "");

// 			try {
// 				$array[$key]["Elevacao"] = number_format($tracklog->getElevationGain(), 2, ",","") . " / " . number_format($tracklog->getElevationLoss(), 2, ",", "");
// 				$array[$key]["Elevacao Máxima"] = number_format($tracklog->getMaxElevation(), 2, ",", "");
// 			} catch (Exception $e) {
// 				$array[$key]["Elevation"] = " - ";
// 				$array[$key]["Elevação Máxima"] = " - ";
// 			}

// 			try {
// 				$array[$key]["Pace"] = $tracklog->getPace();
// 				$array[$key]["Velocidade"] = $tracklog->getAverageSpeed();	
// 			} catch (Exception $e) {
// 				$array[$key]["Pace"] = " - ";
// 				$array[$key]["Velocidade"] = " - ";
// 			}
			
// 			$array[$key]["Pontos de GPS"] = count($tracklog->getPoints());

// 		}catch(Exception $e){
// 			$array[$key]["Error"] = $e->getMessage();
// 		}
// 		$array[$key]["Tempo Execução"] = number_format((microtime(true) - $time_start), 10, ",", "");
// 		echo "Fim $file <br>";
// 	}
// }

// $file = fopen("test_files/results.csv", 'w');
// fputcsv($file, ["Nome", "Total Distance", "ElevationGain/ElevationLoss", "Elevacao Maxima", "Pace", "Velocidade", "Ponto de GPS", "Tempo de Execucao", "Error"], ";");
// foreach ($array as $key => $value) {
// 	fputcsv($file, $value, ";");
// }
// fclose($file);


?>