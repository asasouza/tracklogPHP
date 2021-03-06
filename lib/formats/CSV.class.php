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
			$content = preg_split('#\n\s*\n#Uis', $csv);
			$array;
			foreach ($content as $key => $trackSegment) {
				$array[$key] = preg_split('/\s+/', trim($trackSegment));
			}
			$content = $array;
			$index = $content[0][0];

			//Checks if the file has a header, if not expects the 
			//parameters must follow the order Latitude, Longitude, Elevation and Time.
			if (strpos($index, 'Lon') !== false) {
				$latIndex = $lonIndex = $eleIndex = $timeIndex = '';
				foreach (explode(',', $index) as $key => $value) {
					$latIndex = is_int(strpos(strtolower($value), 'lat')) ? $key : $latIndex;
					$lonIndex = is_int(strpos(strtolower($value), 'lon')) ? $key : $lonIndex;
					$eleIndex = is_int(strpos(strtolower($value), 'ele')) ? $key : $eleIndex;
					$timeIndex = is_int(strpos(strtolower($value), 'time')) ? $key : $timeIndex;
				}
				unset($content[0][0]);
				if (!empty($content[0])) {
					foreach ($content as $trackSegment) {
						$trackData = array();
						foreach ($trackSegment as $pointData) {
							$pointData = explode(',', $pointData);
							$trackPoint = new TrackPoint();
							$trackPoint->setLatitude($pointData[$latIndex]);
							$trackPoint->setLongitude($pointData[$lonIndex]);
							isset($pointData[$eleIndex]) ? $trackPoint->setElevation($pointData[$eleIndex]) : 0;
							isset($pointData[$timeIndex]) ? $trackPoint->setTime($pointData[$timeIndex]) : 0;
							array_push($trackData, $trackPoint);
						}
						array_push($this->trackData, $trackData);
					}
				}else{
					throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
				}
			}else{
				if (!empty($content[0])) {
					foreach ($content as $trackSegment) {
						$trackData = array();
						foreach ($trackSegment as $pointData) {
							$pointData = explode(',', $pointData);
							$trackPoint = new TrackPoint();
							$trackPoint->setLatitude($pointData[0]);
							$trackPoint->setLongitude($pointData[1]);
							isset($pointData[2]) ? $trackPoint->setElevation($pointData[2]) : 0;
							isset($pointData[3]) ? $trackPoint->setTime($pointData[3]) : 0;
							array_push($trackData, $trackPoint);
						}
						array_push($this->trackData, $trackData);	
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
	protected static function write($file_path = null, $tracklog){
		$trackData = 'Latitude,Longitude';
		$trackData .= $tracklog->hasElevation() ? ',Elevation' : '';
		$trackData .= $tracklog->hasTime() ? ',Time' : '';
		$trackData .= ',Distance'."\r\n";
		if (!empty($tracklog->trackData)) {
			foreach ($tracklog->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$trackData .= $trackPoint->getLatitude().','.$trackPoint->getLongitude();
					$trackData .= $tracklog->hasElevation() ? ','.$trackPoint->getElevation() : '';
					$trackData .= $tracklog->hasTime() ? ','.$trackPoint->getTime() : '';
					$trackData .= ','.$trackPoint->getDistance()."\r\n";
				}
				$trackData .= "\r\n";
			}	
		}
		if (!empty($file_path)) {
			$file = fopen($file_path.".csv", 'w');
			fwrite($file, $trackData);
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