<?php
require("autoloader.php");

// //KML tests
$file = 'test_files/kml/test_correct_no_time.kml';
$kml = new KML($file);
print_r($kml->getPoints());
// echo "<br>";
// print_r($kml->getElevations());
// print_r($kml->getLongitudes());
// print_r($kml->getLatitudes());
// print_r($kml->getTimes());
// echo $kml->getPace();
// echo ($kml->getTotalTime());
// echo $kml->getTotalDistance('miles');
// echo $kml->getMaxHeight();
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
// $tcx = new TCX($file);
// print_r($tcx->getPoints());
// $tcx->getElevations();
// $tcx->getLongitudes();
// $tcx->getLatitudes();
// print_r($tcx->getTimes());
// echo $tcx->getTotalDistance('kilometers');
// echo $tcx->getTotalTime();
// echo $tcx->getPace();
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
// $gpx = new GPX($file);
// print_r($gpx->getPoints());
// print_r($gpx->getElevations());
// print_r($gpx->getLongitudes());
// print_r($gpx->getLatitudes());
// print_r($gpx->getTimes());
// echo $gpx->getTotalDistance('kilometers'); echo '<br>';
// echo $gpx->getTotalTime(); echo '<br>';
// echo $gpx->getPace(); echo '<br>';
// echo $gpx->getMaxElevation(); echo '<br>';
// echo $gpx->validate($file);
// htmlentities($gpx->out('gpx', 'converted_files/test.gpx'));
// $file = 'converted_files/test.gpx';
// $gpx = new GPX($file);
// print_r($gpx->getPoints());

print("<br>");

//GeoJSON tests
// $file = 'test_files/geoJson/test_correct_no_elevation.js';
// $json = new GeoJson($file);
// print_r($json->getPoints());
// $json->getElevations();
// $json->getLongitudes();
// $json->getLatitudes();
// $json->getTotalDistance('kilometers');
// $json->getMaxHeight();
// htmlentities($json->out('tcx', 'converted_files/test.tcx'));
// $tcx = new TCX("converted_files/test.tcx");
// print_r($tcx->getPoints());

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