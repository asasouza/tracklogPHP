<?php 
/**
* Class that represents a TCX Tracklog file.
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
class TCX extends Tracklog{
	
	/**
	*Constructs the object based on a TCX file and populates the $trackData array.
	*
	*@param $file The path of the file to be parsed.
	*
	*@return A TCX object.
	*/
	public function __construct($file){
		try {
			$this->validate($file);
			$xml = simplexml_load_file($file);
			$xml->registerXPathNamespace('tcx', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
			if (!empty($content = $xml->xpath('//tcx:Track'))) {
				foreach ($content as $track) {
					$trackData = array();
					foreach ($track as $pointData) {
						if (!empty($pointData->Position)) {
							$trackPoint = new TrackPoint();
							$trackPoint->setLatitude($pointData->Position->LatitudeDegrees);
							$trackPoint->setLongitude($pointData->Position->LongitudeDegrees);
							$trackPoint->setTime($pointData->Time);
							!empty($pointData->AltitudeMeters) ? $trackPoint->setElevation($pointData->AltitudeMeters) : 0;
							array_push($trackData, $trackPoint);
							
						}
					}
					array_push($this->trackData, $trackData);
				}				
				$this->populateDistance();
				isset($xml->xpath('//tcx:Course/tcx:Name')[0]) ? $this->trackName = $xml->xpath('//tcx:Course/tcx:Name')[0] : 0;
				return $this;
			}else{
				throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	*Write the XML of a TCX file based on the $trackData array.
	*
	*@param $file_path (optional) Path to save the created file.
	*
	*@return Returns a string containing the content of the created file.
	*/
	protected static function write($file_path = null, $tracklog){
		$tcx = new SimpleXMLElement('<TrainingCenterDatabase/>');
		$tcx->addAttribute('xmlns', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
		$tcx->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$tcx->addAttribute('xsi:xsi:schemaLocation', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd');
		$courses = $tcx->addChild('Courses');
		$course = $courses->addChild('Course');
		$course->addChild('Name', isset($tracklog->trackName) ? $tracklog->trackName : 'TrackLogPHPConv');
		$lap = $course->addChild('Lap');
		$lap->addChild('TotalTimeSeconds', ($tracklog->hasTime()) ? $tracklog->getTotalTime('seconds') : 0.0);
		
		$lap->addChild('Intensity', 'Active');
		
		if (!empty($tracklog->trackData)) {
			$lap->addChild('DistanceMeters', $tracklog->getTotalDistance('meters'));
			$begginPosition = $lap->addChild('BeginPosition');
			$begginPosition->addChild('LatitudeDegrees', $tracklog->trackData[0][0]->getLatitude());
			$begginPosition->addChild('LongitudeDegrees', $tracklog->trackData[0][0]->getLongitude());
			$endPosition = $lap->addChild('EndPosition');
			$lastTrackData = end($tracklog->trackData);
			$endPosition->addChild('LatitudeDegrees', end($lastTrackData)->getLatitude());
			$endPosition->addChild('LongitudeDegrees', end($lastTrackData)->getLongitude());
			
			foreach ($tracklog->trackData as $trackSegment) {
				$track = $course->addChild('Track');
				foreach ($trackSegment as $trackPoint) {
					$trackpoint = $track->addChild('Trackpoint');
					$tracklog->hasTime() ? $trackpoint->addChild('Time', $trackPoint->getTime()) : $trackpoint->addChild('Time', date('Y-m-d\T00:00:00\Z'));
					$position = $trackpoint->addChild('Position');
					$position->addChild('LatitudeDegrees', $trackPoint->getLatitude());
					$position->addChild('LongitudeDegrees', $trackPoint->getLongitude());
					$tracklog->hasElevation() ? $trackpoint->addChild('AltitudeMeters', $trackPoint->getElevation()) : 0;
					$trackpoint->addChild('DistanceMeters', $trackPoint->getDistance());
				}
			}	
		}
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom_xml = dom_import_simplexml($tcx);
		$dom_xml = $dom->importNode($dom_xml, true);
		$dom_xml = $dom->appendChild($dom_xml);
		if (!is_null($file_path)) {
			$dom->save($file_path.".tcx");
		}
		return $dom->saveXML();
	}

	/** Validates a TCX file based on the oficial XSD schema of the format. */
	protected function validate($file){
		set_error_handler(array('Tracklog', 'error_handler'));
		$dom = new DOMDocument;
		if (!file_exists($file)) {
			throw new Exception('Failed to load external entity "' . $file . '"');
		}else{
			$dom->load($file);	
		}
		try {			
			$dom->schemaValidate("lib/formats/xsd_files/". get_class($this) .".xsd");
		} catch (TracklogPhpException $e) {
			$e->setMessage("This isn't a valid " . get_class($this) . " file.");
			throw $e;
		}
		restore_error_handler();
	}
}
?>