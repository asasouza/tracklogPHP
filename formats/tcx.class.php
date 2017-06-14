<?php 

class TCX extends Tracklog{
	
	public function __construct($file){
		$file_content = simplexml_load_file($file) or die ("File not found!");
		$content = $file_content->Courses->Course->Track;
		$i = 0;
		foreach ($content->Trackpoint as $trackpoint) {
			$this->trackData[$i]['lat'] = (float) $trackpoint->Position->LatitudeDegrees;
			$this->trackData[$i]['lon'] = (float) $trackpoint->Position->LongitudeDegrees;;
			$this->trackData[$i]['ele'] = (float) $trackpoint->AltitudeMeters;
			$this->trackData[$i]['dstc'] = (float) $trackpoint->DistanceMeters;
			$this->trackData[$i]['time'] = (string) $trackpoint->Time;

			$i++;
		}
		return $this;
	}
}
?>