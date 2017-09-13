<?php
class GeoJson extends Tracklog{
	public function __construct($file){
		try {
			$this->validate($file);
			$json = file_get_contents($file);
			$json = json_decode($json);
			$content = $json->{'data'}->{'trackData'}[0];
			if (!empty($content)) {
				foreach ($content as $key => $pointData) {
					$trackPoint = new TrackPoint();
					$trackPoint->setLatitude($pointData->lat);
					$trackPoint->setLongitude($pointData->lon);
					isset($pointData->ele) ? $trackPoint->setElevation($pointData->ele) : 0;
					array_push($this->trackData, $trackPoint);
				}
				$this->populateDistance();
				return $this;
			}else{
				throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
			}			
		} catch (Exception $e) {
			throw $e;
		}
	}

	protected function write($file_path = null){
		$json;
		foreach ($this->trackData as $key => $trackPoint) {
			$json[$key]['lon'] = $trackPoint->getLongitude();
			$json[$key]['lat'] = $trackPoint->getLatitude();
			$this->hasElevation() ? $json[$key]['ele'] = $trackPoint->getElevation() : 0;
		}
		$trackData = ['trackData' => [$json]];
		$data = ['data' => $trackData];
		$json = json_encode($data);
		if (!is_null($file_path)) {
			file_put_contents($file_path, $json);
		}
		return $json;
	}

	protected function validate($file){
		set_error_handler(array('Tracklog', 'error_handler'));
		if (!file_exists($file)) {
			throw new Exception('Failed to load external entity "' . $file . '"');
		}else{
			$json = file_get_contents($file);
			$json = trim($json);
			$json = json_decode($json);
			$content = $json->{'data'}->{'trackData'}[0];
			foreach ($content as $key => $pointData) {
				if (!isset($pointData->lat) || !isset($pointData->lon)) {
					throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
				}
			}
		}
		restore_error_handler();
	}
}
?>