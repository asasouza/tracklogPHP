<?php
require_once('lib/tracklogPhp.main.php');

$gpx = new GPX("test_files/gpx/test_correct.gpx");

// $data = $gpx->getPaces("seconds");

// $data = $gpx->movingAverage($gpx->getPaces("seconds"));

// $data = $gpx->lowPass($gpx->getPaces("seconds"));

$data = $gpx->qFilter($gpx->getPaces("seconds"));

// $data = $gpx->qFilter([1,4,5,6,5,9,2,3,4,5]);



foreach ($data as $key => $value) {
	if ($key % 2 != 0) {
		echo number_format($value,6,",","")."<br>";
	}	
}

?>