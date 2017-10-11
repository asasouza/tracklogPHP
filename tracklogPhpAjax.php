<?php
require_once("lib/tracklogPhp.main.php");
if (isset($_FILES)) {
	$extensions = ['kml', 'tcx', 'gpx', 'csv', 'js'];
	$extension = pathinfo($_FILES["tracklogFile"]["name"], PATHINFO_EXTENSION);
	if (in_array($extension, $extensions)) {
		$file_path = $_FILES["tracklogFile"]["tmp_name"];
		$tracklog = new $extension($file_path);

		try {
			$response["info_board"]["data_total_time"] = ["success", $tracklog->getTotalTime()];
			$response["info_board"]["data_pace"] = ["success", $tracklog->getPace()];
			$response["data_paces"] = ["success", $tracklog->getPaces()];
		} catch (Exception $e) {
			$response["info_board"]["data_total_time"] = ["error", $e->getMessage()];
			$response["info_board"]["data_pace"] = ["error", $e->getMessage()];
			$response["data_paces"] = ["error", $e->getMessage()];
		}

		try {
			$response["data_elevations"] = ["success", $tracklog->getElevations()];
			$response["info_board"]["data_elevation_gain"] = ["success", $tracklog->getElevationGain()];
			$response["info_board"]["data_elevation_loss"] = ["success", $tracklog->getElevationLoss()];
		} catch (Exception $e) {
			$response["data_elevations"] = ["error", $e->getMessage()];
			$response["info_board"]["data_elevation_gain"] = ["error", $e->getMessage()];
			$response["info_board"]["data_elevation_loss"] = ["error", $e->getMessage()];
		}
		
		$response["info_board"]["data_total_distance"] = $tracklog->getTotalDistance("kilometers");
		$response["data_distances"] = $tracklog->getDistances();

		$path = str_replace(" ", "", "tmp_files/".date("Y-m-d").$tracklog->getTrackName());

		$tracklog->out("kml", $path);

		$response["data_kml"] = "http://".$_SERVER["HTTP_HOST"]."/".$path;

		echo json_encode($response);	
	}else{
		$response["error"] = "Invalid extension.";
		echo json_encode($response);
	}
	
}else{
	echo 'no';
}

?>