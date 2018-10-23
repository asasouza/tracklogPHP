<?php 
/**
* Class that represents a GPX Tracklog file.
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
class GPX extends Tracklog{

	/**
	*Constructs the object based on a GPX file and populates the $trackData array.
	*
	*@param $file The path of the file to be parsed.
	*
	*@return A GPX object.
	*/
	public function __construct($file){
		try {
			$this->validate($file);
			$xml = simplexml_load_file($file);
			$xml->registerXPathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
			if(!empty($content = $xml->xpath('//gpx:trkseg')) && !empty($content[0])){
				foreach ($content as $trackSegment) {
					$trackData = array();
					foreach ($trackSegment as $pointData) {
						$trackPoint = new TrackPoint();
						$trackPoint->setLatitude($pointData['lat']);
						$trackPoint->setLongitude($pointData['lon']);
						!empty($pointData->ele) ? $trackPoint->setElevation($pointData->ele) : 0;
						!empty($pointData->time) ? $trackPoint->setTime($pointData->time) : 0;
						array_push($trackData, $trackPoint);
					}
					array_push($this->trackData, $trackData);
				}
			}elseif(empty($markers = $xml->xpath('//gpx:wpt'))){
				throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");			
			}

			/** Populate the distance atrribute of the trackpoints */
			$this->populateDistance();

			/** Get the name of the track. */
			isset($xml->xpath('//gpx:trk/gpx:name')[0]) ? $this->trackName = $xml->xpath('//gpx:trk/gpx:name')[0] : 0;

			/** Get the markers of the track */
			if (!empty($markers = $xml->xpath('//gpx:wpt'))) {
				foreach ($markers as $marker) {
					$trackMarker = new TrackMarker();
					$trackMarker->setLatitude($marker['lat']);
					$trackMarker->setLongitude($marker['lon']);
					!empty($marker->name) ? $trackMarker->setName($marker->name[0]) : 0;
					!empty($marker->ele) ? $trackMarker->setElevation($marker->ele) : 0;
					!empty($marker->time) ? $trackMarker->setTime($marker->time) : 0;
					array_push($this->trackMarkers, $trackMarker);
				}
			}
			return $this;
		} catch (TracklogPhpException $e) {
			throw $e;			
		}		
	}

	/**
	*Write the XML of a GPX file based on the $trackData array.
	*
	*@param $file_path (optional) Path to save the created file.
	*
	*@return Returns a string containing the content of the created file.
	*/
	protected function write($file_path = null){
		$gpx = new SimpleXMLElement('<gpx />');
		$gpx->addAttribute('creator', 'TracklogPHP');
		$gpx->addAttribute('version', '1.1');
		$gpx->addAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
		$gpx->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$gpx->addAttribute('xsi:xsi:schemaLocation', 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd');
		if($this->hasTime()){
			$metadata = $gpx->addChild('metadata');				
			$time = $metadata->addChild('time', $this->trackData[0][0]->getTime());				
		}
		if (!empty($this->trackMarkers)) {
			foreach ($this->trackMarkers as $marker) {
				$wpt = $gpx->addChild('wpt');
				$wpt->addAttribute('lat', $marker->getLatitude());
				$wpt->addAttribute('lon', $marker->getLongitude());
				$this->hasElevation() ? $wpt->addChild('ele', $marker->getElevation()) : 0;
				$this->hasTime() ? $wpt->addChild('time', $marker->getTime()) : 0;
				!is_null($marker) ? $wpt->addChild('name', $marker->getName()) : 0;
			}
		}

		$trk = $gpx->addChild('trk');
		if (isset($this->trackName)) {
			$trk->addChild('name', $this->trackName);
		}
		
		if (!empty($this->trackData)) {
			foreach ($this->trackData as $trackSegment) {
				$trkseg = $trk->addChild('trkseg');
				foreach ($trackSegment as $trackPoint) {
					$trkpt = $trkseg->addChild('trkpt');
					$trkpt->addAttribute('lat', $trackPoint->getLatitude());
					$trkpt->addAttribute('lon', $trackPoint->getLongitude());
					$this->hasElevation() ? $trkpt->addChild('ele', $trackPoint->getElevation()) : 0;
					$this->hasTime() ? $trkpt->addChild('time', $trackPoint->getTime()) : 0;						
				}	
			}	
		}
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom_xml = dom_import_simplexml($gpx);
		$dom_xml = $dom->importNode($dom_xml, true);
		$dom_xml = $dom->appendChild($dom_xml);
		if (!is_null($file_path)){
			$dom->save($file_path.".gpx");
		}
		return $dom->saveXML();
	}
	
	/** Validates a GPX file based on the oficial XSD schema of the format. */
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