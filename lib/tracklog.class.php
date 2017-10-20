<?php
abstract class Tracklog {

	protected $trackData = array();
	protected $trackName;

	protected abstract function __construct($file);

	protected abstract function write();

	protected abstract function validate($file);

	public function error_handler($errno, $message){
		// if (!(error_reporting() & $errno)) {
		// 	return;
		// }
		// echo $message;
		// throw new TracklogPhpException();
	}

	protected function populateDistance(){
		$distance = 0;

		$this->trackData[0]->setDistance($distance);
		for ($i=0; $i < count($this->trackData)-1; $i++) { 			
			$distance += $this->haversineFormula($this->trackData[$i]->getLatitude(), 
				$this->trackData[$i]->getLongitude(), 
				$this->trackData[$i+1]->getLatitude(), 
				$this->trackData[$i+1]->getLongitude());			
			$this->trackData[$i+1]->setDistance((string) $distance);
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
		return !is_null($this->trackData[0]->getTime());
	}

	protected function hasElevation(){
		return !is_null($this->trackData[0]->getElevation());
	}

	protected function hasDistance(){
		return !is_null($this->trackData[0]->getDistance());
	}

	public function getPoints(){
		$points;
		foreach ($this->trackData as $key => $trackPoint) {
			$points[$key]['latitude'] = $trackPoint->getLatitude();
			$points[$key]['longitude'] = $trackPoint->getLongitude();
			$points[$key]['elevation'] = $trackPoint->getElevation();
			$points[$key]['time'] = $trackPoint->getTime();
			$points[$key]['distance'] = $trackPoint->getDistance();
		}
		return $points;
	}

	public function getLatitudes(){
		$latitudes;
		foreach ($this->trackData as $trackPoint) {
			$latitudes[] = $trackPoint->getLatitude();
		}
		return $latitudes;
	}

	public function getLongitudes(){
		$longitudes;
		foreach ($this->trackData as $trackPoint) {
			$longitudes[] = $trackPoint->getLongitude();
		}
		return $longitudes;
	}

	public function getElevations(){
		if($this->hasElevation()){
			$elevations;
			foreach ($this->trackData as $trackPoint) {
				$elevations[] = floatval($trackPoint->getElevation());
			}
			return $elevations;
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have elevation data");
		}
	}

	public function getTimes(){
		if ($this->hasTime()) {
			$time;
			foreach ($this->trackData as $trackPoint) {
				$time[] = $trackPoint->getTime();
			}
			return $time;	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}		
	}

	public function getDistances(){
		$distances;
		foreach ($this->trackData as $trackPoint) {
			$distances[] = $trackPoint->getDistance();
		}
		return $distances;
	}

	public function getPaces( $format_output = "timestamp", $distanceToCalc = 100){
		$paces = array();
		$distances = $this->getDistances();
		$times = $this->getTimes();
		$distanceDiff = 0;
		$timeDiff = new DateTime('0000-00-00 00:00:00');
		for ($i=0; $i < count($distances) - 1; $i++) {
			$dateB = new DateTime($this->trackData[$i]->getTime());
			$dateE = new DateTime($this->trackData[$i+1]->getTime());
			$timeDiff->add($dateB->diff($dateE));
			$distanceDiff += $distances[$i + 1] - $distances[$i];
			if ($distanceDiff >= $distanceToCalc) {
				$timeInSeconds = $timeDiff->format("h") * 3600;
				$timeInSeconds += $timeDiff->format("i") * 60;
				$timeInSeconds += $timeDiff->format("s");
				$pacePerDistance = $timeInSeconds * (1000/$distanceToCalc);
				switch ($format_output) {
					case 'timestamp':
					array_push($paces, gmdate("0000-00-00TH:i:sZ", $pacePerDistance));
					break;
					case 'seconds':
					array_push($paces, $pacePerDistance);
					break;
					default:
					throw new TracklogPhpException("Invalid output format", 1);
					break;					
				}
				
				$distanceDiff = 0;
				$timeDiff = new DateTime('0000-00-00 00:00:00');
			}else{
				isset($paces[count($paces)-1]) ? $paces[] = $paces[count($paces)-1] : 0;
			}
		}
		$arrayDiff = count($distances)-count($paces);
		for ($i=0; $i < $arrayDiff; $i++) {
			$paces[] = $paces[count($paces)-1];
		}
		return $paces;
	}

	public function getTotalDistance($unit = "meters"){
		$totalDistance = $this->trackData[count($this->trackData)-1]->getDistance();
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
			throw new TracklogPhpException("Unit format not recognized");			
			break;
		}
	}

	public function getMaxElevation(){
		if($this->hasElevation()){
			return max($this->getElevations());
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have elevation data.");
		}
	}

	public function getPace(){
		if($this->hasTime()){
			$time = new DateTime($this->getTotalTime());
			$hour = $time->format('H') * 60;
			$minute = $time->format('i');
			$second = $time->format('s') / 60;
			$totalTime = $hour + $minute + $second;
			$pace = $totalTime / $this->getTotalDistance('kilometers');
			$pace = ((($pace - intval($pace)) * 60) / 100) + intval($pace); 
			return number_format($pace, 2, ":", "");
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

	public function getTotalTime($format = null){
		if($this->hasTime()){
			$dateDiff = new DateTime('0000-00-00 00:00:00');
			for ($i=0; $i < count($this->trackData)-1; $i++) { 
				$dateB = new DateTime($this->trackData[$i]->getTime());
				$dateE = new DateTime($this->trackData[$i+1]->getTime());
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
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

	public function getMarkers(){}	

	public function out($output, $file_path = null){
		$output = strtoupper($output);
		switch ($output) {
			case 'KML':
			return KML::write($file_path);
			break;
			case 'GPX':
			return GPX::write($file_path);
			break;
			case 'TCX':
			return TCX::write($file_path);
			break;
			case 'GEOJSON':
			return GeoJson::write($file_path);
			break;
			case 'CSV':
			return CSV::write($file_path);
			break;
			default:
			throw new TracklogPhpException("Output type invalid!", 1);				
			break;
		}
	}

	public function getTrackName(){
		if (isset($this->trackName)) {
			return $this->trackName;
		}else{
			return "TracklogPhpFile";
		}		
	}

	public function getElevationGain(){
		if ($this->hasElevation()) {
			$elevationGain = 0;
			for ($i = 0; $i < count($this->trackData)-1; $i++) { 
				if ($this->trackData[$i]->getElevation() < $this->trackData[$i+1]->getElevation() ) {
					$elevationGain += $this->trackData[$i+1]->getElevation() - $this->trackData[$i]->getElevation();
				}
			}
			return $elevationGain;
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have elevation data.");
			
		}
		
	}

	public function getElevationLoss(){
		if ($this->hasElevation()) {
			$elevationLoss = 0;
			for ($i = 0; $i < count($this->trackData)-1; $i++) { 
				if ($this->trackData[$i]->getElevation() > $this->trackData[$i+1]->getElevation() ) {
					$elevationLoss += $this->trackData[$i]->getElevation() - $this->trackData[$i+1]->getElevation();
				}
			}
			return $elevationLoss;	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have elevation data.");
		}
		
	}
}

?>