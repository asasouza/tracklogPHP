<?php
/**
* Class that represents a track
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
abstract class Tracklog {

	/**
	*An array of track segments, contaning TrackPoints objects.
	*/
	protected $trackData = array();

	protected $trackName;

	protected $trackMarkers = array();

	private $totalSeconds = 0;

	protected abstract function __construct($file);

	protected abstract function write();

	protected abstract function validate($file);

	public function error_handler($errno, $message){
	}

	/**
	* Populates the distance attribute of the TrackPoints objects in the $trackData array.
	*/
	protected function populateDistance(){
		if (!empty($this->trackData)) {
			$distance = 0;
			foreach ($this->trackData as $key => $trackSegment) {
				if (count($trackSegment) == 1) {
					$trackSegment[0]->setDistance((string) $distance);
				}
				for ($i=0; $i < count($trackSegment)-1; $i++) {
					$distance += $this->haversineFormula($trackSegment[$i]->getLatitude(), 
						$trackSegment[$i]->getLongitude(), 
						$trackSegment[$i+1]->getLatitude(), 
						$trackSegment[$i+1]->getLongitude());			
					$trackSegment[$i+1]->setDistance((string) $distance);
				}
			}
		}
	}

	protected function hasTime(){
		if (!empty($this->trackData)) {
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					if (empty($trackPoint->getTime())) {
						return false;
						break;
					}
				}
			}
			return true;
		}
	}

	protected function hasElevation(){
		if (!empty($this->trackData)) {
			return !is_null($this->trackData[0][0]->getElevation());
		}else{
			return false;
		}
	}

	protected function hasDistance(){
		if (!empty($this->trackData)) {
			return !is_null($this->trackData[0][0]->getDistance());
		}else{
			return false;
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

	/** 
	*Returns an array of average values according to their closests siblings
	*
	*@param $array Array to be smoothed by the values inside of it.
	*@param $range The number of siblings to calculate the average.
	*
	*@return An array of same lenght with smoothed values.
	*/
	private function smoothArray($array, $range = null){
		if (is_null($range)) {
			intval(sqrt(count($array))) % 2 != 0 ? $range = intval(sqrt(count($array))) : $range = intval(sqrt(count($array))) + 1;	
		}else{
			intval($range) % 2 != 0 ? $range = intval($range) : $range = intval($range)+1;
		}
		$smoothedArray = [];
		$rule = intval($range/2); //rule to get the values of closests elements in the array
		for ($i=0; $i < count($array); $i++) { 
			$sum = 0;
			if ($i < $rule) { //if these are the first values
				for ($y=0; $y < $range; $y++) { 
					$sum += $array[$y];
				}
				array_push($smoothedArray, $sum/$range);
			}elseif ($i >= count($array)-$rule) { // if these are the last values
				for ($y = count($array)-$range; $y < count($array) ; $y++) { 
					$sum += $array[$y];
				}
				array_push($smoothedArray, $sum/$range);
			}else{
				for($y = ($i-$rule); $y <= ($i + $rule); $y++){
					$sum += $array[$y];
				}
				array_push($smoothedArray, $sum/$range);
			}
		}
		return $smoothedArray;	
	}

	/**
	*Returns all the trackpoints of $trackData in a array
	*/
	public function getPoints(){
		if (!empty($this->trackData)) {
			$points;
			$i = 0;
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$points[$i]['latitude'] = $trackPoint->getLatitude();
					$points[$i]['longitude'] = $trackPoint->getLongitude();
					$points[$i]['elevation'] = $trackPoint->getElevation();
					$points[$i]['time'] = $trackPoint->getTime();
					$points[$i]['distance'] = $trackPoint->getDistance();
					$i++;
				}
			}
			return $points;	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have track data");
		}
	}

	/**
	*Returns all the latitudes data of $trackData in a array
	*/
	public function getLatitudes(){
		if (!empty($this->trackData)) {
			$latitudes;
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$latitudes[] = $trackPoint->getLatitude();
				}			
			}
			return $latitudes;	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have track data");
		}
	}

	/**
	*Returns all the longitudes data of $trackData in a array
	*/
	public function getLongitudes(){
		if (!empty($this->trackData)) {
			$longitudes;
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$longitudes[] = $trackPoint->getLongitude();
				}			
			}
			return $longitudes;	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have track data");
		}
	}

	/**
	*Returns all the elevations data of $trackData in a array
	*/
	public function getElevations(){
		if($this->hasElevation()){
			$elevations;
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$elevations[] = floatval($trackPoint->getElevation());
				}				
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
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$time[] = $trackPoint->getTime();
				}				
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
		if (!empty($this->trackData)) {
			$distances;
			foreach ($this->trackData as $trackSegment) {
				foreach ($trackSegment as $trackPoint) {
					$distances[] = $trackPoint->getDistance();
				}			
			}
			return $distances;	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have track data");
		}
	}

	/**
	*Returns all the track markers data in a array
	*/
	public function getMarkers() {
		return $this->trackMarkers;
	}

	/** Returns the average pace of the track in minutes per kilometer. */
	public function getPace(){
		if($this->hasTime()){
			$totalTime = $this->getTotalTime('minutes');
			$pace = $totalTime / $this->getTotalDistance('kilometers');
			//transform the float pace into a valid time unit.
			$pace = ((($pace - intval($pace)) * 60) / 100) + intval($pace); 
			return number_format($pace, 2, ":", "");
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

	/**
	*Returns the pace for each trackPoint of the tracklog in minutes or seconds per kilometer.
	*
	*@param $unit Time unity "seconds | minutes";
	*@param $smoothed Changes the returned array to smoothed data in an average metric.
	*
	*@return An array of paces.
	*/
	public function getPaces($unit = "minutes", $smoothed = false){
		if ($this->hasTime()) {
			$paces = array();
			foreach ($this->trackData as $trackSegment) {
				for ($i=0; $i < count($trackSegment) - 1; $i++) {
					$timeBeggining = new DateTime($trackSegment[$i]->getTime());
					$timeEnding = new DateTime($trackSegment[$i+1]->getTime());
					$timeDiff = $timeBeggining->diff($timeEnding);
				$timeDiff = ($timeDiff->h*60) + ($timeDiff->i) + ($timeDiff->s/60); //time in minutes
				$distanceDiff = ($trackSegment[$i + 1]->getDistance() - $trackSegment[$i]->getDistance()) /1000; //distance in kilometers;
				($distanceDiff != 0 ) ? $pace = ($timeDiff) / ($distanceDiff) : $pace = 0; //minutes per kilometer
				switch ($unit) {
					case 'minutes':
					$pace = number_format(((($pace - intval($pace)) * 60) / 100) + intval($pace),2);
					break;
					case 'seconds':
					$pace = $pace * 60;
					break;
					default:
					throw new TracklogPhpException("Unit format not recognized");
					break;
				}
				array_push($paces, $pace);
			}	
		}
			if ($smoothed) {
				return $this->smoothArray($paces);
			}else{
				return $paces;
			}
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

/** Returns the average speed of the track in kilometers per hour. */
	public function getAverageSpeed(){
		if($this->hasTime()){
			$totalTime = $this->getTotalTime('hours');
			$speed = $this->getTotalDistance('kilometers') / $totalTime;
			return number_format($speed, 2);
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

	/**
	*Returns the average speed for each trackPoint of the tracklog in kilometers per hour.
	*
	*@param $smoothed Changes the returned array to smoothed data in an average metric.
	*
	*@return An array of averageSpeeds.
	*/
	public function getAverageSpeeds($smoothed = false){
		if ($this->hasTime()) {
			$speeds = array();
			foreach ($this->trackData as $trackSegment) {
				for ($i=0; $i < count($trackSegment) - 1; $i++) {
					$timeBeggining = new DateTime($trackSegment[$i]->getTime());
					$timeEnding = new DateTime($trackSegment[$i+1]->getTime());
					$timeDiff = $timeBeggining->diff($timeEnding);
				$timeDiff = ($timeDiff->h) + ($timeDiff->i/60) + ($timeDiff->s/3600); //time in hours
				$distanceDiff = ($trackSegment[$i + 1]->getDistance() - $trackSegment[$i]->getDistance())/1000; //kilometers
				($timeDiff != 0 ) ? $speed = number_format(($distanceDiff) / ($timeDiff),2) : $speed = 0; //kilometers per hour
				array_push($speeds, $speed);	
				}
			}
			if ($smoothed) {
				return $this->smoothArray($speeds);
			}else{
				return $speeds;	
			}			
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't support time manipulations");
		}
	}

	/**
	*Returns the total distance of the tracklog.
	*
	*@param $unit Metric unit to be returned "meters | kilometers | miles".
	*
	*@return The total distance of the tracklog in float.
	*/
	public function getTotalDistance($unit = "meters"){
		if (!empty($this->trackData)) {
			$totalDistance = $this->trackData[count($this->trackData)-1][count($this->trackData[count($this->trackData)-1])-1]->getDistance();
			switch ($unit) {
				case 'meters':
				return number_format($totalDistance, 2, ",", "");
				break;
				case 'kilometers':
				return number_format($totalDistance/1000, 2, ",", "");
				break;
				case 'miles':
				return number_format($totalDistance/1609.34, 2, ",", "");
				break;
				default:
				throw new TracklogPhpException("Unit format not recognized");			
				break;
			}	
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have track data");
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
			if($this->totalSeconds == 0) {
				foreach ($this->trackData as $trackSegment) {
					for ($i=0; $i < count($trackSegment)-1; $i++) { 
						$dateB = new DateTime($trackSegment[$i]->getTime());
						$dateE = new DateTime($trackSegment[$i+1]->getTime());
						$this->totalSeconds += $dateE->getTimestamp() - $dateB->getTimestamp();
					}
				}
			}
			$hours = floor($this->totalSeconds / 3600);
+			$minutes = floor(($this->totalSeconds / 60) % 60);
+			$seconds = $this->totalSeconds % 60;
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
				return "$hours:$minutes:$seconds";
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

	public function getMaxElevation(){
		if($this->hasElevation()){
			return max($this->getElevations());
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have elevation data.");
		}
	}

	private function lowPass($array, $alpha){
		$smoothedArray = [];
		array_push($smoothedArray, $array[0]);
		for ($i=1; $i < count($array); $i++) { 
			$smoothed = $smoothedArray[$i-1]+($array[$i]-$smoothedArray[$i-1])*$alpha;
			array_push($smoothedArray, $smoothed);
		}
		return $smoothedArray;
	}

	/** Returns the elevation gain of the track in meters */
	public function getElevationGain(){
		if ($this->hasElevation()) {
			$elevations = $this->getElevations();
			$points = count($elevations);
			if ($points > 1500) {
				$elevations = $this->lowPass($elevations, 0.3);
			}elseif ($points > 500 && $points < 1500) {
				$elevations = $this->lowPass($elevations, 0.2);
			}else{
				$elevations = $this->lowPass($elevations, 0.1);
			}
			$elevationGain = 0;
			for ($i = 0; $i < count($elevations)-1; $i++) { 
				if ($elevations[$i] < $elevations[$i+1]) {
					$elevationGain += $elevations[$i+1] - $elevations[$i];
				}
			}
			return number_format($elevationGain, 2, ",", "");
		}else{
			throw new TracklogPhpException("This ".get_class($this)." file don't have elevation data.");
		}
	}

	/** Returns the elevation loss of the track in meters */
	public function getElevationLoss(){
		if ($this->hasElevation()) {
			$elevations = $this->getElevations();
			$points = count($elevations);
			if ($points > 1500) {
				$elevations = $this->lowPass($elevations, 0.3);
			}elseif ($points > 500 && $points < 1500) {
				$elevations = $this->lowPass($elevations, 0.2);
			}else{
				$elevations = $this->lowPass($elevations, 0.1);
			}
			$elevationLoss = 0;
			for ($i = 0; $i < count($elevations)-1; $i++) { 
				if ($elevations[$i] > $elevations[$i+1]) {
					$elevationLoss += $elevations[$i] - $elevations[$i+1];
				}
			}
			return number_format($elevationLoss, 2, ",", "");
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