<?php
/**
* Class that represents a track
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
abstract class Tracklog {

	/**
	*An array of TrackPoints objects.
	*/
	protected $trackData = array();

	protected $trackName;

	protected abstract function __construct($file);

	protected abstract function write();

	protected abstract function validate($file);

	public function error_handler($errno, $message){
	}

	/**
	* Populates the distance attribute of the TrackPoints objects in the $trackData array.
	*/
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

	/**
	* Formula to calculate the distance between two coordinates pair (latitude, longitude)
	*
	*@param $latB The initial latitude.
	*@param $lonB The initial longitude.
	*@param $latE The ending latitude.
	*@param $lonE The ending longitude.
	*
	*@return The distance between the points in meters.
	*/
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

	/**
	*Returns all the trackpoints of $trackData in a array
	*/
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

	/**
	*Returns all the latitudes data of $trackData in a array
	*/
	public function getLatitudes(){
		$latitudes;
		foreach ($this->trackData as $trackPoint) {
			$latitudes[] = $trackPoint->getLatitude();
		}
		return $latitudes;
	}

	/**
	*Returns all the longitudes data of $trackData in a array
	*/
	public function getLongitudes(){
		$longitudes;
		foreach ($this->trackData as $trackPoint) {
			$longitudes[] = $trackPoint->getLongitude();
		}
		return $longitudes;
	}

	/**
	*Returns all the elevations data of $trackData in a array
	*/
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

	/**
	*Returns all the time data of $trackData in a array
	*/
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

	/**
	*Returns all the distances data of $trackData in a array
	*/
	public function getDistances(){
		$distances;
		foreach ($this->trackData as $trackPoint) {
			$distances[] = $trackPoint->getDistance();
		}
		return $distances;
	}

	/**
	*Returns the pace for each trackPoint of the tracklog, according to a determinate distance to calculate.
	*@param $format_output The format of representation of the pace time "timestap | seconds".
	*@param $distanceToCalc The reference distance to calculate the pace.
	*@return An array of paces.
	*/
	public function getPaces( $format_output = "timestamp", $distanceToCalc = 100){
		$paces = array();
		$distances = $this->getDistances();
		$times = $this->getTimes();
		for ($i=0; $i < count($distances) - 1; $i++) {
			$distanceDiff = 0;
			$timeBeggining = new DateTime($this->trackData[$i]->getTime());
			$timeEnding = new DateTime($this->trackData[$i+1]->getTime());

			$timeDiff = $timeBeggining->diff($timeEnding);

			$distanceDiff = $distances[$i + 1] - $distances[$i];
			
			$timeDiff = ($timeDiff->h*3600) + ($timeDiff->i*60) + $timeDiff->s;

			$pace = number_format(($distanceDiff/1000) / ($timeDiff/3600),2);

			array_push($paces, $pace);

			// 3600 segundos --- 1hr
			// 10 segundos --- x = 0,00277777777777777777777777777778;
			// 1000 metros --- 1km
			// 9.647 metros --- x = 0,009647
			// 3 min/km -- 20km/h
			// x min/km -- 3.47km/h
			// $timeInSeconds = $timeDiff->format("h") * 3600 . "<br>";
			// $timeInSeconds += $timeDiff->format("i") * 60;
			// $timeInSeconds += $timeDiff->format("s");			
			// echo $pacePerDistance = $timeInSeconds * (1000/$distanceToCalc) . "<br>";
			// if ($distanceDiff >= $distanceToCalc) {
			// 	$timeInSeconds = $timeDiff->format("h") * 3600;
			// 	$timeInSeconds += $timeDiff->format("i") * 60;
			// 	$timeInSeconds += $timeDiff->format("s");
			// 	$pacePerDistance = $timeInSeconds * (1000/$distanceToCalc);
			// 	switch ($format_output) {
			// 		case 'timestamp':
			// 		array_push($paces, gmdate("0000-00-00TH:i:sZ", $pacePerDistance));
			// 		break;
			// 		case 'seconds':
			// 		array_push($paces, $pacePerDistance);
			// 		break;
			// 		default:
			// 		throw new TracklogPhpException("Invalid output format", 1);
			// 		break;					
			// 	}

			// 	$distanceDiff = 0;
			// 	$timeDiff = new DateTime('0000-00-00 00:00:00');
			// }else{
			// 	isset($paces[count($paces)-1]) ? $paces[] = $paces[count($paces)-1] : 0;
			// }
		}
		// $arrayDiff = count($distances)-count($paces);
		// for ($i=0; $i < $arrayDiff; $i++) {
		// 	$paces[] = $paces[count($paces)-1];
		// }
		return $paces;
	}

	/**
	*Returns the total distance of the tracklog.
	*
	*@param $unit Metric unit to be returned "meters | kilometers | miles".
	*
	*@return The total distance of the tracklog in float.
	*/
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

	/** Returns the pace of the track. */
	public function getPace(){
		if($this->hasTime()){
			$time = new DateTime($this->getTotalTime());
			$hour = $time->format('H') * 60;
			$minute = $time->format('i');
			$second = $time->format('s') / 60;
			$totalTime = $hour + $minute + $second;
			$pace = $totalTime / $this->getTotalDistance('kilometers');
			echo $pace;
			//transform the float pace into a valid time unit.
			$pace = ((($pace - intval($pace)) * 60) / 100) + intval($pace); 
			return number_format($pace, 2, ":", "");
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

	/**
	* Returns the total time of the tracklog.
	*
	*@param $unit The time unit to be returned "seconds | minutes | hours".
	*
	*@return The total time to complete the tracklog according to the $unit parameter.
	*/
	public function getTotalTime($unit = null){
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
			switch ($unit) {
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

	public function getTrackName(){
		if (isset($this->trackName)) {
			return $this->trackName;
		}else{
			return "TracklogPhpFile";
		}		
	}

	/** Returns the elevation gain of the track in meters */
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

	/** Returns the elevation loss of the track in meters */
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

	/**
	*Converts the data from $trackData into other formats.
	*
	*@param $output The tracklog format to be converted "KML | GPX | TCX | GeoJson | CSV".
	*@param $file_path (optional) Path to save the converted file.
	*
	*@return Returns a string containing the content of the converted file.
	*/
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

}
?>