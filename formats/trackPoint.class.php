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
		$this->latitude = $latitude;
	}

	public function setLongitude($longitude){
		$this->longitude = $longitude;
	}

	public function setElevation($elevation){
		$this->elevation = $elevation;
	}

	public function setTime($time){
		$this->time = $time;
	}

	public function setDistance($distance){
		$this->distance = $distance;
	}
}
?>