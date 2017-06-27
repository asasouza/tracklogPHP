<?php 

class TCX extends Tracklog{
	
	public function __construct($file){
		$xml = simplexml_load_file($file) or die ("File not found!");
		$xml->registerXPathNamespace('tcx', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
		$content = $xml->xpath('//tcx:Track');
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

	protected function write($file_path = null){
		$tcx = new SimpleXMLElement('<TrainingCenterDatabase/>');
		$tcx->addAttribute('xmlns', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');
		$tcx->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$tcx->addAttribute('xsi:schemaLocation', 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd');
		$courses = $tcx->addChild('Courses');
			$course = $courses->addChild('Course');
				if (isset($this->trackData['meta_tag']['name'])) {
					$course->addChild('Name', $this->trackData['meta_tag']['name']);
				}
				$lap = $course->addChild('Lap');
					$lap->addChild('TotalTimeSeconds', 'TEMPO EM SEGUNDOS');
						$lap->addChild('DistanceMeters', $this->getTotalDistance('meters'));
						$begginPosition = $lap->addChild('BegginPosition');
							$begginPosition->addChild('LatitudeDegrees', $this->trackData[0]['lat']);
							$begginPosition->addChild('LongitudeDegrees', $this->trackData[0]['lon']);
						$endPosition = $lap->addChild('EndPosition');
							$endPosition->addChild('LatitudeDegrees', $this->trackData[count($this->trackData)-1]['lat']);
							$endPosition->addChild('LongitudeDegrees', $this->trackData[count($this->trackData)-1]['lon']);
						$lap->addChild('Intensity', 'Active');
				$track = $course->addChild('Track');
				foreach ($this->trackData as $trackdata) {
					$trackpoint = $track->addChild('Trackpoint');
						if (get_class($this) != 'KML' && get_class($this) != 'GeoJson') { //mudar posteriormente
							$trackpoint->addChild('Time', $trackdata['time']);
						}
						$position = $trackpoint->addChild('Position');
							$position->addChild('LatitudeDegrees', $trackdata['lat']);
							$position->addChild('LongitudeDegrees', $trackdata['lon']);
						$trackpoint->addChild('AltitudeMeters', $trackdata['ele']);							
						$trackpoint->addChild('DistanceMeters', $trackdata['dstc']);
				}
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom_xml = dom_import_simplexml($tcx);
		$dom_xml = $dom->importNode($dom_xml, true);
		$dom_xml = $dom->appendChild($dom_xml);
		if (!is_null($file_path)) {
			$dom->save($file_path);
		}
		return $dom->saveXML();
	}
}
?>