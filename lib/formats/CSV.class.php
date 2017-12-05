<?php 
/**
* Class that represents a CSV Tracklog file.
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
class CSV extends Tracklog{

	/**
	*Constructs the object based on a CSV file and populates the $trackData array.
	*
	*@param $file The path of the file to be parsed.
	*
	*@return A CSV object.
	*/
	public function __construct($file){
		try {
			$this->validate($file);
			$csv = file_get_contents($file);
			$csv = trim($csv);
			$content = preg_split('/\s+/', $csv);
			$index = explode(',', $content[0]);
			//Checks if the file has a header, if not expects the 
			//parameters must follow the order Latitude, Longitude, Elevation and Time.
			if (strpos($content[0], 'Lon') !== false) {
				$latIndex = $lonIndex = $eleIndex = $timeIndex = '';
				foreach ($index as $key => $value) {
					$latIndex = is_int(strpos(strtolower($value), 'lat')) ? $key : $latIndex;
					$lonIndex = is_int(strpos(strtolower($value), 'lon')) ? $key : $lonIndex;
					$eleIndex = is_int(strpos(strtolower($value), 'ele')) ? $key : $eleIndex;
					$timeIndex = is_int(strpos(strtolower($value), 'time')) ? $key : $timeIndex;
				}
				unset($content[0]);
				if (!empty($content)) {
					foreach ($content as $key => $value) {
						$pointData = explode(',', $value);
						$trackPoint = new TrackPoint();
						$trackPoint->setLatitude($pointData[$latIndex]);
						$trackPoint->setLongitude($pointData[$lonIndex]);
						isset($pointData[$eleIndex]) ? $trackPoint->setElevation($pointData[$eleIndex]) : 0;
						isset($pointData[$timeIndex]) ? $trackPoint->setTime($pointData[$timeIndex]) : 0;
						array_push($this->trackData, $trackPoint);
					}
				}else{
					throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
				}
			}else{
				if (!empty($content)) {
					foreach ($content as $key => $value) {
						$pointData = explode(',', $value);
						$trackPoint = new TrackPoint();
						$trackPoint->setLatitude($pointData[0]);
						$trackPoint->setLongitude($pointData[1]);
						isset($pointData[2]) ? $trackPoint->setElevation($pointData[2]) : 0;
						isset($pointData[3]) ? $trackPoint->setTime($pointData[3]) : 0;
						array_push($this->trackData, $trackPoint);
					}
				}else{
					throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
				}
			}
			$this->populateDistance();
			return $this;	
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	*Write the CSV file based on the $trackData array.
	*
	*@param $file_path (optional) Path to save the created file.
	*
	*@return Returns a string containing the content of the created file.
	*/
	protected function write($file_path = null){
		$trackData = 'Latitude,Longitude';
		$trackData .= $this->hasElevation() ? ',Elevation' : '';
		$trackData .= $this->hasTime() ? ',Time' : '';
		$trackData .= ',Distance ';
		foreach ($this->trackData as $trackPoint) {
			$trackData .= $trackPoint->getLatitude().','.$trackPoint->getLongitude();
			$trackData .= $this->hasElevation() ? ','.$trackPoint->getElevation() : '';
			$trackData .= $this->hasTime() ? ','.$trackPoint->getTime() : '';
			$trackData .= ','.$trackPoint->getDistance().' ';
		}
		if (!empty($file_path)) {
			$content = preg_split('/\s+/', $trackData);
			$file = fopen($file_path.".csv", 'w');
			foreach ($content as $value) {
				fputcsv($file, explode(',', $value));	
			}
			fclose($file);
		}
		return $trackData;
	}

	/** Validates a CSV file expecting at less two columns, that represent latitude and longitude. */
	protected function validate($file){
		set_error_handler(array('Tracklog', 'error_handler'));
		if (!file_exists($file)) {
			throw new Exception('Failed to load external entity "' . $file . '"');
		}else{
			$csv = file_get_contents($file);
			$csv = trim($csv);
			$content = preg_split('/\s+/', $csv);
			foreach ($content as $key => $value) {
				if (count(explode(',', $value)) < 2) {
					throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
				}
			}
		}
		restore_error_handler();
	}
}
?>