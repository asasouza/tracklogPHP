<?php
require("autoloader.php");

//KML tests
$file = 'test_files/test.kml';
$kml = new KML($file);
$kml->getEles();
$kml->getLons();
$kml->getLats();


//TCX tests
$file = 'test_files/test.tcx';
$tcx = new TCX($file);
$tcx->getEles();
$tcx->getLons();
$tcx->getLats();
$tcx->getTime();
?>