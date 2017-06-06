<?php
require("autoloader.php");

$file = 'test.kml';

$kml = new KML($file);

echo count($kml->getPoints());

echo '<br>';

foreach ($kml->getPoints() as $key => $points) {
	echo $points['lat'];
}


?>