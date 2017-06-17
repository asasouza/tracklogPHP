<?php
require("autoloader.php");

//KML tests
$file = 'test_files/test.kml';
$kml = new KML($file);
$kml->getPoints();
$kml->getEles();
$kml->getLons();
$kml->getLats();
$kml->getTotalDistance('miles');
$kml->getMaxHeight();

print("<br>");

//TCX tests
$file = 'test_files/test.tcx';
$tcx = new TCX($file);
$tcx->getPoints();
$tcx->getEles();
$tcx->getLons();
$tcx->getLats();
$tcx->getTime();
$tcx->getTotalDistance('kilometers');
$tcx->getTotalTime();
$tcx->getPace();
$tcx->getMaxHeight();
?>