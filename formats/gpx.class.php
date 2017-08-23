<?php 
class GPX extends Tracklog{

	public function __construct($file){
		try {
			parent::validate($file);
			$xml = simplexml_load_file($file);
			$xml->registerXPathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
			if(!empty($content = $xml->xpath('//gpx:trkseg'))){
				$i = 0;
				foreach ($content[0] as $trackpoint) {
					$this->trackData[$i]['lat'] = (float) $trackpoint['lat'];
					$this->trackData[$i]['lon'] = (float) $trackpoint['lon'];
					$this->trackData[$i]['ele'] = (float) $trackpoint->ele;
					$this->trackData[$i]['time'] = (string) $trackpoint->time;

					$i++;
				}
				$this->populateDistance();
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
			$metadata = $gpx->addChild('metadata');
				if ($this->hasTime()) {
					$time = $metadata->addChild('time', $this->getTimes()[0]);
				}				
			$trk = $gpx->addChild('trk');
				if (isset($this->trackData['meta_tag']['name'])) {
					$trk->addChild('name', $this->trackData['meta_tag']['name']);
				}
			$trkseg = $trk->addChild('trkseg');
				foreach ($this->trackData as $trackdata) {
					$trkpt = $trkseg->addChild('trkpt');
					$trkpt->addAttribute('lat', $trackdata['lat']);
					$trkpt->addAttribute('lon', $trackdata['lon']);
						$ele = $trkpt->addChild('ele', $trackdata['ele']);
						if ($this->hasTime()) {
							$trkpt->addChild('time', $trackdata['time']);
						}
				}

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom_xml = dom_import_simplexml($gpx);
		$dom_xml = $dom->importNode($dom_xml, true);
		$dom_xml = $dom->appendChild($dom_xml);
		if (!is_null($file_path)) {
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
			$dom->schemaValidate("xsd_files/". get_class($this) .".xsd");
		} catch (Exception $e) {
			throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
		}	
	}
}
?>