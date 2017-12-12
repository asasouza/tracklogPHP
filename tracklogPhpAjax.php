<?php
//include the loader 
require_once("lib/tracklogPhp.main.php");
if (isset($_FILES["tracklogFile"])) {	
	$extensions = ['kml', 'tcx', 'gpx', 'csv', 'js'];
	$extension = pathinfo($_FILES["tracklogFile"]["name"], PATHINFO_EXTENSION);
	if (in_array($extension, $extensions)) {
		$file_path = $_FILES["tracklogFile"]["tmp_name"];
		try {
			($extension == "js") ? $extension = "geojson" : 0;
			$tracklog = new $extension($file_path);	
		} catch (Exception $e) {
			$response["error"] = $e->getMessage();
			echo json_encode($response);
			exit;
		}
		//if the user want do download the file
		//else get the informations and send to UI.
		if (isset($_POST["extension_to_download"])) {
			$extension_to_download = strtolower($_POST["extension_to_download"]);
			$path = preg_replace("( |:|\.|ª|º|&|¨|#|@)", "", "tmp_files/".date("Y-m-d").$tracklog->getTrackName());
			$tracklog->out($extension_to_download, $path);
			($extension_to_download == "geojson") ? $extension_to_download = "js" : 0;
			$response["download_file_path"] = "http://".$_SERVER["HTTP_HOST"]."/".$path.".".$extension_to_download;
			echo json_encode($response);
		}else{
			try {
				$response["info_board"]["data_total_time"] = ["success", $tracklog->getTotalTime()];
				$response["info_board"]["data_pace"] = ["success", $tracklog->getPace()];
				$response["data_paces"] = ["success", $tracklog->getPaces("seconds", true)];
				$response["data_speeds"] = ["success", $tracklog->getAverageSpeeds()];
			} catch (Exception $e) {
				$response["info_board"]["data_total_time"] = ["error", $e->getMessage()];
				$response["info_board"]["data_pace"] = ["error", $e->getMessage()];
				$response["data_paces"] = ["error", $e->getMessage()];
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
			$response["data_kml"] = "http://".$_SERVER["HTTP_HOST"]."/".$path.".kml";
			echo json_encode($response);	
		}
	}else{
		$response["error"] = "Invalid extension.";
		echo json_encode($response);
		exit;
	}	
}else{
	$response["error"] = "No file submited.";
	echo json_encode($response);	
}

?>