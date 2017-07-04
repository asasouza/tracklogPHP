<?php
require("autoloader.php");

// //KML tests
// $file = 'test_files/test.kml';
// $kml = new KML($file);

// $file = 'test_files/test2.kml';
// $kml = new KML($file);
// print_r($kml->getPoints());
// print_r($kml->getEles());
// print_r($kml->getLons());
// print_r($kml->getLats());
// print_r($kml->getTime());
// echo ($kml->getPace());
// echo ($kml->getTotalTime());
// echo $kml->getTotalDistance('meters');
// echo $kml->getMaxHeight();
// echo $kml->write('kml');
// echo htmlentities($kml->out('kml'));
// print_r($kml->getDistances());
// htmlentities($kml->out('tcx', 'converted_files/test2.tcx'));
// echo $kml->validate($file);
print("<br>");

// //TCX tests
$file = 'converted_files/test2.tcx';
$tcx = new TCX($file);
// print_r($tcx->getPoints());
// $tcx->getEles();
// $tcx->getLons();
// $tcx->getLats();
// print_r($tcx->getTimes());
// $tcx->getTotalDistance('kilometers');
// echo $tcx->getTotalTime();
// $tcx->getPace();
// $tcx->getMaxHeight();
// echo $tcx->validate($file);
htmlentities($tcx->out('kml', 'converted_files/test.kml'));

print("<br>");

// //GPX tests
// $file = 'converted_files/test2.gpx';
// $gpx = new GPX($file);
// $gpx->getPoints();
// $gpx->getEles();
// $gpx->getLons();
// $gpx->getLats();
// $gpx->getTime();
// echo $gpx->getTotalDistance('kilometers');
// echo $gpx->getTotalTime();
// echo $gpx->getPace();
// $gpx->getMaxHeight();
// echo $gpx->validate($file);
// echo htmlentities($tcx->out('gpx', 'converted_files/test.gpx'));

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
// echo $tcx->out('geoJson', 'converted_files/test.js');

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
// echo $tcx->out('csv');

?>