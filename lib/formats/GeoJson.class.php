<?php
/**
* Class that represents a GeoJson Tracklog file.
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
class GeoJson extends Tracklog{

	/**
	*Constructs the object based on a JS file and populates the $trackData array.
	*The json file must be in one the follows schemas, both supported by the GeoJson documentation. 
	* {"data":{"trackData":[[{"lon":00.00000,"lat":00.0000,"ele":00.0}]]}}
	* {"features": ["geometry": {"coordinates": [[[00.00000, 00.0000, 00.0]]]}]}
	*
	*@param $file The path of the file to be parsed.
	*
	*@return A GeoJson object.
	*/		
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
					foreach ($content as $trackSegment) {
						$trackData = array();
						foreach ($trackSegment as $pointData) {
							$trackPoint = new TrackPoint();
							$trackPoint->setLongitude($pointData[0]);
							$trackPoint->setLatitude($pointData[1]);							
							isset($pointData[2]) ? $trackPoint->setElevation($pointData[2]) : 0;
							array_push($trackData, $trackPoint);
						}
						array_push($this->trackData, $trackData);
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

	/**
	*Write the GeoJson file based on the $trackData array.
	*
	*@param $file_path (optional) Path to save the created file.
	*
	*@return Returns a string containing the content of the created file.
	*/
	protected function write($file_path = null){
		$trackData = array();
		foreach ($this->trackData as $trackSegment) {
			$json = [];
			foreach ($trackSegment as $key => $trackPoint) {				
				$json[$key]['lon'] = $trackPoint->getLongitude();
				$json[$key]['lat'] = $trackPoint->getLatitude();
				$this->hasElevation() ? $json[$key]['ele'] = $trackPoint->getElevation() : 0;
			}
			array_push($trackData, $json);
		}		
		$trackData = ['trackData' => [$trackData]];
		$data = ['data' => $trackData];
		$json = json_encode($data);
		if (!is_null($file_path)) {
			file_put_contents($file_path.".js", $json);
		}
		return $json;
	}

	/** Validates a GeoJson file expecting it follows the constructor function schemas */
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
				foreach ($content as $trackSegment) {
					foreach ($trackSegment as $pointData) {
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