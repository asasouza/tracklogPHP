<?php
require("autoloader.php");

// //KML tests
// $file = 'test_files/kml/test_correct_time.kml';
// $file = 'test_files/kml/test_correct_no_time.kml';
$file = "test_files/external_files/test_322/322km.kml";
$kml = new KML($file);
echo count($kml->getPoints());
echo "<br>";
// echo count($kml->getElevations());
// print_r($kml->getLongitudes());
// print_r($kml->getLatitudes());
// print_r($kml->getTimes());
// echo $kml->getPace();
// echo ($kml->getTotalTime());
echo $kml->getTotalDistance('kilometers');
// echo $kml->getMaxElevation();
// echo $kml->write('kml');
// echo htmlentities($kml->out('kml'));
// print_r($kml->getDistances());
// htmlentities($kml->out('kml', 'converted_files/test2.kml'));
// $kml = new KML('converted_files/test2.kml');
// print_r($kml->getPoints());
// echo $kml->getPace();
// echo $kml->validate($file);
// echo $kml->getElevationGain() . "<br>";
// echo $kml->getElevationLoss() . "<br>";
print("<br>");

// TCX tests
// $file = 'test_files/tcx/test_correct.tcx';
// $file = "test_files/external_files/test_322/322km.tcx";
// $tcx = new TCX($file);
// print_r($tcx->getPoints());
// $tcx->getElevations();
// $tcx->getLongitudes();
// $tcx->getLatitudes();
// print_r($tcx->getTimes());
// echo $tcx->getTotalDistance('kilometers') ."<br>";
// echo $tcx->getTotalTime() ."<br>";
// echo $tcx->getPace() ."<br>";
// echo $tcx->getElevationGain() ."<br>";
// echo $tcx->getElevationLoss() ."<br>";
// $tcx->getMaxHeight();
// echo $tcx->validate($file);
// htmlentities($tcx->out('tcx', 'converted_files/test.tcx'));
// $file = 'converted_files/test.tcx';
// $tcx = new TCX($file);
// print_r($tcx->getPoints());
// echo $tcx->getElevationLoss();
// echo $tcx->getElevationGain();

print("<br>");

// //GPX tests
// $file = "test_files/gpx/test_correct.gpx";
// $file = "test_files/external_files/test_322/322km.gpx";
// $gpx = new GPX($file);
// print_r($gpx->getPoints());
// print_r($gpx->getElevations());
// print_r($gpx->getLongitudes());
// print_r($gpx->getLatitudes());
// print_r($gpx->getTimes());
// echo $gpx->getTotalDistance('kilometers'); echo '<br>';
// echo $gpx->getTotalTime(); echo '<br>';
// echo $gpx->getPace(); echo '<br>';
// echo $gpx->getElevationGain(); echo '<br>';
// echo $gpx->getElevationLoss(); echo '<br>';
// echo $gpx->getMaxElevation(); echo '<br>';
// echo $gpx->validate($file);
// htmlentities($gpx->out('gpx', 'converted_files/test.gpx'));
// $file = 'converted_files/test.gpx';
// $gpx = new GPX($file);
// print_r($gpx->getPoints());

print("<br>");

//GeoJSON tests
// $file = 'test_files/geoJson/test_correct_no_elevation.js';
$file = "test_files/external_files/test_322/322km.2.js";
$json = new GeoJson($file);
echo count($json->getPoints()) . "<br>";
// $json->getElevations();
// $json->getLongitudes();
// $json->getLatitudes();
echo $json->getTotalDistance('kilometers');
// $json->getMaxHeight();
// htmlentities($json->out('tcx', 'converted_files/test.tcx'));
// $tcx = new TCX("converted_files/test.tcx");
// print_r($tcx->getPoints());

// echo $kml->getPoints() == $json->getPoints();

$resul = array_diff($kml->getPoints(), $kml->getPoints());

print_r($resul);

print("<br>");

//CSV Tests
// $file = 'test_files/csv/test_correct.csv';
// $csv = new CSV($file);
// print_r($csv->getPoints());
// print_r($csv->getElevations());
//$csv->getLongitudes();
//$csv->getLatitudes();
// $csv->getTotalDistance('kilometers');
//$csv->getMaxHeight();
// echo $csv->out('csv', 'converted_files/test.csv');



?>