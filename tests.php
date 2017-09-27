<?php
require("autoloader.php");


$file = 'test_files/external_files/test_322/322km';

$kml = new KML($file.'.kml');
$gpx = new GPX($file.'.gpx');
$csv = new CSV($file.'.csv');
$tcx = new TCX($file.'.tcx');
$json = new GeoJson($file.'.js');
$json2 = new GeoJson($file.'.2.js');

// echo count($kml->getPoints('kilometers')) . "<br>";
// echo count($gpx->getPoints('kilometers')) . "<br>";
// echo count($csv->getPoints('kilometers')) . "<br>";
// echo count($tcx->getPoints('kilometers')) . "<br>";
// echo count($json->getPoints('kilometers')) . "<br>";
// echo count($json2->getPoints('kilometers')) . "<br>";




?>