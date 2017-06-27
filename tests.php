<?php
require("autoloader.php");

// //KML tests
// $file = 'test_files/test.kml';
// $kml = new KML($file);
// print_r($kml->getPoints());
// print_r($kml->getEles());
// print_r($kml->getLons());
// print_r($kml->getLats());
// echo $kml->getTotalDistance('meters');
// echo $kml->getMaxHeight();
// echo $kml->write('kml');
// echo htmlentities($kml->out('kml'));
// print_r($kml->getDistances());
// echo htmlentities($kml->out('tcx', 'converted_files/test.tcx'));
print("<br>");

// //TCX tests
$file = 'test_files/test.tcx';
$tcx = new TCX($file);
// $tcx->getPoints();
// $tcx->getEles();
// $tcx->getLons();
// $tcx->getLats();
// $tcx->getTime();
// $tcx->getTotalDistance('kilometers');
// echo $tcx->getTotalTime();
// $tcx->getPace();
// $tcx->getMaxHeight();
// echo htmlentities($tcx->out('tcx', 'converted_files/test.tcx'));

print("<br>");

// //GPX tests
// $file = 'test_files/test.gpx';
// $gpx = new GPX($file);
// $gpx->getPoints();
// $gpx->getEles();
// $gpx->getLons();
// $gpx->getLats();
// $gpx->getTime();
// $gpx->getTotalDistance('kilometers');
// $gpx->getTotalTime();
// $gpx->getPace();
// $gpx->getMaxHeight();
echo htmlentities($tcx->out('gpx', 'converted_files/test.gpx'));

print("<br>");

//GeoJSON tests
// $file = 'test_files/test.js';
// $json = new GeoJson($file);
// $json->getPoints();
// $json->getEles();
// $json->getLons();
// $json->getLats();
// $json->getTotalDistance('kilometers');
// $json->getMaxHeight();

print("<br>");

//CSV Tests
// $file = 'test_files/test.csv';
// $csv = new CSV($file);
//$csv->getPoints();
//$csv->getEles();
//$csv->getLons();
//$csv->getLats();
// $csv->getTotalDistance('kilometers');
//$csv->getMaxHeight();



?>