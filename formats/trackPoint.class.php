<?php
class TrackPoint{
	private $latitude;
	private $longitude;
	private $elevation;
	private $time;
	private $distance;

	public function getLatitude(){
		return $this->latitude;
	}
	public function getLongitude(){
		return $this->longitude;
	}
	public function getElevation(){
		return $this->elevation;
	}
	public function getTime(){
		return $this->time;
	}
	public function getDistance(){
		return $this->distance;
	}
	
	public function setLatitude($latitude){
		$latitude = number_format((float) $latitude, 7);
		if(is_float($latitude) && $latitude >= -90 && $latitude <= 90 ){
			$this->latitude = $latitude;
		}else{
			throw new TracklogPhpException("Invalid latitude point.");
		}
	}

	public function setLongitude($longitude){
		$longitude = number_format((float) $longitude, 7);
		if (is_float($longitude) && $longitude >= -180 && $longitude <= 180) {
			$this->longitude = $longitude;
		}else{
			throw new TracklogPhpException("Invalid longitude point.");
		}
	}

	public function setElevation($elevation){
		$elevation = number_format((float) $elevation, 6);
		if (is_float($elevation)) {
			$this->elevation = $elevation;
		}else{
			throw new TracklogPhpException("Invalid elevation point.");
		}		
	}

	public function setTime($time){
		if (preg_match(pattern, $time)) {
			$this->time = $time;
		}else{
			throw new TracklogPhpException("Invalid time point");			
		}		
	}

	public function setDistance($distance){
		if (is_float($distance)) {
			$this->distance = $distance;
		}else{
			throw new TracklogPhpException("Invalid distance point");			
		}		
	}
}
?>