<?php 

class TCX extends Tracklog{
	
	public function __construct($file){
		$file_content = simplexml_load_file($file) or die ("File not found!");
		$file_content->registerXPathNamespace('tcx', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
		$content = $file_content->xpath('//tcx:Track');
		$i = 0;
		
		foreach ($content[0] as $trackpoint) {
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