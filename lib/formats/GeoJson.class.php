<?php
class GeoJson extends Tracklog{
	public function __construct($file){
		try {
			$this->validate($file);
			$json = file_get_contents($file);
			$json = json_decode($json);
			if (isset($json->{'data'}->{'trackData'})) {
				$content = $json->{'data'}->{'trackData'};
				if (!empty($content[0])) {
					foreach ($content as $linestring) {
						foreach ($linestring as $pointData) {
							$trackPoint = new TrackPoint();
							$trackPoint->setLatitude($pointData->lat);
							$trackPoint->setLongitude($pointData->lon);
							isset($pointData->ele) ? $trackPoint->setElevation($pointData->ele) : 0;
							array_push($this->trackData, $trackPoint);	
						}						
					}
					$this->populateDistance();
					return $this;
				}else{
					throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
				}
			}elseif(isset($json->{'features'}[0]->{'geometry'}->{'coordinates'})){
				$content = $json->{'features'}[0]->{'geometry'}->{'coordinates'};
				if (!empty($content[0])) {
					foreach ($content as $linestring) {
						foreach ($linestring as $pointData) {
							$trackPoint = new TrackPoint();
							$trackPoint->setLongitude($pointData[0]);
							$trackPoint->setLatitude($pointData[1]);							
							isset($pointData[2]) ? $trackPoint->setElevation($pointData[2]) : 0;
							array_push($this->trackData, $trackPoint);
						}
					}
					$this->populateDistance();
					return $this;
				}else{
					throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
				}				
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
			file_put_contents($file_path.".js", $json);
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
			if (isset($json->{'data'}->{'trackData'})) {
				$content = $json->{'data'}->{'trackData'};
				foreach ($content as $linestring) {
					foreach ($linestring as $pointData) {
						if (!isset($pointData->lat) || !isset($pointData->lon)) {
							throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
						}
					}	
				}				
			}elseif(isset($json->{'features'}[0]->{'geometry'}->{'coordinates'})){
				$content = $json->{'features'}[0]->{'geometry'}->{'coordinates'};
				foreach ($content as $linestring) {
					foreach ($linestring as $pointData) {
						if (!isset($pointData[0]) || !isset($pointData[1])) {
							throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
						}
					}
				}
			}else{
				throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
			}
		}
		restore_error_handler();
	}
}
?>