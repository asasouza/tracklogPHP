<?php
abstract class Tracklog {

	protected $trackData = array();

	protected abstract function __construct($file);

	protected abstract function write();

	protected abstract function validate($file);

	public function error_handler($errno, $message){
		if (!(error_reporting() & $errno)) {
			return;
		}
		throw new TracklogPhpException("");
	}

	protected function populateDistance(){
		$distance = number_format(0.000, 3, '.', '');
		$this->trackData[0]['dstc'] = $distance;
		for ($i=0; $i < count($this->trackData)-1; $i++) { 
			$distance += $this->haversineFormula($this->trackData[$i]['lat'], 
				$this->trackData[$i]['lon'], 
				$this->trackData[$i+1]['lat'], 
				$this->trackData[$i+1]['lon']);
			$this->trackData[$i+1]['dstc'] = number_format($distance, 3, '.', '');
		}
	}

	private function haversineFormula($latB, $lonB, $latE, $lonE){
		$earthRadius = 6371000;
		$latB = deg2rad($latB);
		$lonB = deg2rad($lonB);
		$latE = deg2rad($latE);
		$lonE = deg2rad($lonE);
		$latDelta = $latE - $latB;
		$lonDelta = $lonE - $lonB;
		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
			cos($latB) * cos($latE) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius;
	}

	protected function hasTime(){
		return !empty($this->trackData[0]['time']);
	}

	public function getPoints(){		
		return $this->trackData;
	}

	public function getLatitudes(){
		$latitudes;
		foreach ($this->trackData as $point) {
			$latitudes[] = $point['lat'];
		}
		return $latitudes;
	}

	public function getLongitudes(){
		$longitudes;
		foreach ($this->trackData as $point) {
			$longitudes[] = $point['lon'];
		}
		return $longitudes;
	}

	public function getElevations(){
		$elevations;
		foreach ($this->trackData as $point) {
			$elevations[] = $point['ele'];
		}
		return $elevations;
	}

	public function getTimes(){
		$time;
		foreach ($this->trackData as $point) {
			$time[] = $point['time'];
		}
		return $time;
	}

	public function getDistances(){
		$distances;
		foreach ($this->trackData as $point) {
			$distances[] = $point['dstc'];
		}
		return $distances;
	}

	public function getTotalDistance($unit = "meters"){
		$totalDistance = $this->trackData[count($this->trackData)-1]['dstc'];
		switch ($unit) {
			case 'meters':
			return number_format($totalDistance, 2, '.', '');
			break;
			case 'kilometers':
			return number_format($totalDistance/1000, 2, '.', '');
			break;
			case 'miles':
			return number_format($totalDistance/1609.34, 2, '.', '');
			break;
			default:
			throw new TracklogPhpException("Unit format not recognized", 1);			
			break;
		}
	}

	public function getMaxElevation(){
		return number_format(max($this->getElevations()), 2);
	}

	public function getPace(){
		$time = new DateTime($this->getTotalTime());
		$hour = $time->format('H') * 60;
		$minute = $time->format('i');
		$second = $time->format('s') / 60;
		$totalTime = $hour + $minute + $second;
		$pace = $totalTime / $this->getTotalDistance('kilometers');
		$pace = ((($pace - intval($pace)) * 60) / 100) + intval($pace); 
		return number_format($pace, 2, ":", "");
	}

	public function getTotalTime($format = null){
		$dateDiff = new DateTime('0000-00-00 00:00:00');
		for ($i=0; $i < count($this->trackData)-1; $i++) { 
			$dateB = new DateTime($this->trackData[$i]['time']);
			$dateE = new DateTime($this->trackData[$i+1]['time']);
			$difference = $dateB->diff($dateE);	
			$dateDiff->add($difference);
		}
		$hours = $dateDiff->format('H');
		$minutes = $dateDiff->format('i');
		$seconds = $dateDiff->format('s');
		switch ($format) {
			case 'seconds':
			return number_format($seconds = $seconds + ($hours*3600) + ($minutes*60), 1, '', '.');
			break;
			case 'minutes':
			return number_format($minutes = $minutes + ($hours*60) + ($seconds/60), 1);
			break;
			case 'hours':
			return number_format($hours = $hours + ($minutes/60), 1);
			break;
			default:
			return $dateDiff->format('H:i:s');
			break;
		}		
	}

	public function getMarkers(){}	

	public function out($output, $file_path = null){
		switch ($output) {
			case 'kml':
			return KML::write($file_path);
			break;
			case 'gpx':
			return GPX::write($file_path);
			break;
			case 'tcx':
			return TCX::write($file_path);
			break;
			case 'geoJson':
			return GeoJson::write($file_path);
			break;
			case 'csv':
			return CSV::write($file_path);
			break;
			default:
			throw new TracklogPhpException("Output type invalid!", 1);				
			break;
		}
	}

	public function getTrackName(){
		return 0;
	}
}

?>