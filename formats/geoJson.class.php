<?php
class GeoJson extends Tracklog{

	public function __construct($file){
		$json = file_get_contents($file);
		$object = json_decode($json);
		$object = $object->{'data'}->{'trackData'}[0];

		for ($i=0; $i < count($object); $i++) { 
			$this->trackData[$i]['lat'] = $object[$i]->lat;
			$this->trackData[$i]['lon'] = $object[$i]->lon;
			$this->trackData[$i]['ele'] = $object[$i]->ele;
		}

		$this->populateDistance();

		return $this;
	}

	public function getTime(){
		throw new Exception("GeoJson files don't support time manipulations", 1);
	}

	public function getPace(){
		throw new Exception("GeoJson files don't support time manipulations", 1);
	}

	public function getTotalTime(){
		throw new Exception("GeoJson files don't support time manipulations", 1);
	}
}
?>