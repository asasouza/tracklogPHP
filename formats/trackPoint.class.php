<?php
class TrackPoint{
	private $latitude;
	private $longitude;
	private $elevation = null;
	private $time = null;
	private $distance = null;

	private function isFloat($float){		
		return ($float == (string)(float)$float || ((string)(float)$float - $float) < 0.1);
	}

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
		if($this->isFloat($latitude) && $latitude >= -90 && $latitude <= 90 ){
			$this->latitude = number_format((float) $latitude, 7);
		}else{
			throw new TracklogPhpException("Invalid latitude point format.");
		}
	}

	public function setLongitude($longitude){
		if ($this->isFloat($longitude) && $longitude >= -180 && $longitude <= 180) {
			$this->longitude = number_format((float) $longitude, 7);;
		}else{
			throw new TracklogPhpException("Invalid longitude point format.");
		}
	}

	public function setElevation($elevation){
		if ($this->isFloat($elevation)) {
			$this->elevation = number_format((float) $elevation, 6);;
		}else{
			throw new TracklogPhpException("Invalid elevation point format.");
		}		
	}

	public function setTime($time){
		$time = (string) $time;
		if (preg_match('(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z)', $time)) {
			$this->time = $time;
		}else{
			throw new TracklogPhpException("Invalid time format");			
		}		
	}

	public function setDistance($distance){
		if ($this->isFloat($distance)) {
			$this->distance = number_format((float) $distance, 3, '.', '');;
		}else{
			throw new TracklogPhpException("Invalid distance format");			
		}
	}
}
?>