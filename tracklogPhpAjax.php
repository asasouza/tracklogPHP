<?php
require_once("lib/tracklogPhp.main.php");
if (isset($_FILES)) {
	$extension = pathinfo($_FILES["tracklogFile"]["name"], PATHINFO_EXTENSION);
	$file_path = $_FILES["tracklogFile"]["tmp_name"];
	$tracklog = new $extension($file_path);
	$response["info_board"]["data_pace"] = $tracklog->getPace();
	$response["info_board"]["data_total_time"] = $tracklog->getTotalTime();
	$response["info_board"]["data_total_distance"] = $tracklog->getTotalDistance("kilometers");
	$response["info_board"]["data_elevation_gain"] = $tracklog->getElevationGain();
	$response["info_board"]["data_elevation_loss"] = $tracklog->getElevationLoss();
	$response["data_point"] = $tracklog->getPoints();
	$response["data_elevation"] = $tracklog->getElevations();
	echo json_encode($response);
}else{
	echo 'no';
}

?>