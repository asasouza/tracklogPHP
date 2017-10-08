<?php
require_once("cvcvcvlib/tracklogPhp.main.php");
if (isset($_FILES)) {
	$extension = pathinfo($_FILES["tracklogFile"]["name"], PATHINFO_EXTENSION);
	$file_path = $_FILES["tracklogFile"]["tmp_name"];
	$tracklog = new $extension($file_path);
	$response["info_board"]["data_pace"] = $tracklog->getPace();
	$response["info_board"]["data_total_time"] = $tracklog->getTotalTime();
	$response["info_board"]["data_total_distance"] = $tracklog->getTotalDistance("kilometers");
	$response["info_board"]["data_elevation_gain"] = $tracklog->getElevationGain();
	$response["info_board"]["data_elevation_loss"] = $tracklog->getElevationLoss();
	$response["data_elevations"] = $tracklog->getElevations();
	$response["data_distances"] = $tracklog->getDistances();

	$path = str_replace(" ", "", "tmp_files/".date("Y-m-d").$tracklog->getTrackName());

	$tracklog->out("kml", $path);

	$response["data_kml"] = $_SERVER["HTTP_HOST"]."/".$path;
	
	echo json_encode($response);
}else{
	echo 'no';
}

?>