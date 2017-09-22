<?php
require("autoloader.php");


$file = 'test_files/external_files/test_322/322km';

$kml = new KML($file.'.kml');
$gpx = new GPX($file.'.gpx');
$csv = new CSV($file.'.csv');
$tcx = new TCX($file.'.tcx');
$json = new GeoJson($file.'.js');
$json2 = new GeoJson($file.'.2.js');

echo $kml->getTotalDistance('kilometers') . "<br>";
echo $gpx->getTotalDistance('kilometers') . "<br>";
echo $csv->getTotalDistance('kilometers') . "<br>";
echo $tcx->getTotalDistance('kilometers') . "<br>";
echo $json->getTotalDistance('kilometers') . "<br>";
echo $json2->getTotalDistance('kilometers') . "<br>";

?>