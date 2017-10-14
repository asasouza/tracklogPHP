<?php 
class CSV extends Tracklog{
	public function __construct($file){
		try {
			$this->validate($file);
			$csv = file_get_contents($file);
			$csv = trim($csv);
			$content = preg_split('/\s+/', $csv);
			$index = explode(',', $content[0]);
			//without header expects the parameters follow the order latitude, longitude, elevation, time.
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
						$coordinates = explode(',', $value);
						$trackPoint = new TrackPoint();
						$trackPoint->setLatitude($coordinates[$latIndex]);
						$trackPoint->setLongitude($coordinates[$lonIndex]);
						isset($coordinates[$eleIndex]) ? $trackPoint->setElevation($coordinates[$eleIndex]) : 0;
						isset($coordinates[$timeIndex]) ? $trackPoint->setTime($coordinates[$timeIndex]) : 0;
						array_push($this->trackData, $trackPoint);
					}
				}else{
					throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
				}
			}else{
				if (!empty($content)) {
					foreach ($content as $key => $value) {
						$coordinates = explode(',', $value);
						$trackPoint = new TrackPoint();
						$trackPoint->setLatitude($coordinates[0]);
						$trackPoint->setLongitude($coordinates[1]);
						isset($coordinates[2]) ? $trackPoint->setElevation($coordinates[2]) : 0;
						isset($coordinates[3]) ? $trackPoint->setTime($coordinates[3]) : 0;
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