<?php 
class GPX extends Tracklog{

	public function __construct($file){
		try {
			$this->validate($file);
			$xml = simplexml_load_file($file);
			$xml->registerXPathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
			if(!empty($content = $xml->xpath('//gpx:trkseg/gpx:trkpt'))){
				foreach ($content as $pointData) {
					$trackPoint = new TrackPoint();
					$trackPoint->setLatitude($pointData['lat']);
					$trackPoint->setLongitude($pointData['lon']);
					!empty($pointData->ele) ? $trackPoint->setElevation($pointData->ele) : 0;
					!empty($pointData->time) ? $trackPoint->setTime($pointData->time) : 0;
					array_push($this->trackData, $trackPoint);
				}
				$this->populateDistance();
				isset($xml->xpath('//gpx:trk/gpx:name')[0]) ? $this->trackName = $xml->xpath('//gpx:trk/gpx:name')[0] : 0;
				return $this;
			}else{
				throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");			
			}			
		} catch (TracklogPhpException $e) {
			throw $e;			
		}		
	}

	protected function write($file_path = null){
		$gpx = new SimpleXMLElement('<gpx />');
		$gpx->addAttribute('creator', 'TracklogPHP');
		$gpx->addAttribute('version', '1.1');
		$gpx->addAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
		$gpx->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$gpx->addAttribute('xsi:xsi:schemaLocation', 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd');
			if($this->hasTime()){
			$metadata = $gpx->addChild('metadata');				
					$time = $metadata->addChild('time', $this->trackData[0]->getTime());				
			}
			$trk = $gpx->addChild('trk');
				if (isset($this->trackName)) {
					$trk->addChild('name', $this->trackName);
				}
			$trkseg = $trk->addChild('trkseg');
				foreach ($this->trackData as $trackPoint) {
					$trkpt = $trkseg->addChild('trkpt');
					$trkpt->addAttribute('lat', $trackPoint->getLatitude());
					$trkpt->addAttribute('lon', $trackPoint->getLongitude());
						$this->hasElevation() ? $trkpt->addChild('ele', $trackPoint->getElevation()) : 0;
						$this->hasTime() ? $trkpt->addChild('time', $trackPoint->getTime()) : 0;						
				}
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom_xml = dom_import_simplexml($gpx);
		$dom_xml = $dom->importNode($dom_xml, true);
		$dom_xml = $dom->appendChild($dom_xml);
		if (!is_null($file_path)){
			$dom->save($file_path);
		}
		return $dom->saveXML();
	}
	
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