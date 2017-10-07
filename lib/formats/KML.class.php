<?php
class KML extends Tracklog{

	public function __construct($file){
		try {
			$this->validate($file);
			$xml = simplexml_load_file($file);
			$xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
			$xml->registerXPathNamespace('gx', 'http://www.google.com/kml/ext/2.2');

			if (!empty($content = $xml->xpath('//kml:LineString/kml:coordinates')) && strlen($content[0]) > 0) {
				foreach ($content as $linestring) {
					$linestring = preg_split('/\s+/', trim($linestring));					
					foreach ($linestring as $linestring) {
						$pointData = explode(',', $linestring);	
						$trackPoint = new TrackPoint();
						$trackPoint->setLongitude($pointData[0]);
						$trackPoint->setLatitude($pointData[1]);
						isset($pointData[2]) ? $trackPoint->setElevation($pointData[2]) : 0;
						array_push($this->trackData, $trackPoint);
					}					
				}
			}elseif(!empty($times = $xml->xpath('//gx:Track/kml:when')) && !empty($points = $xml->xpath('//gx:Track/gx:coord')) && count($times) == count($points)){
				foreach ($points as $i => $pointData) {
					$pointData = explode(' ', $pointData);
					$trackPoint = new TrackPoint();
					$trackPoint->setLongitude($pointData[0]);
					$trackPoint->setLatitude($pointData[1]);
					isset($pointData[2]) ? $trackPoint->setElevation($pointData[2]) : 0; //verify if exists elevation data.
					$trackPoint->setTime($times[$i]);
					array_push($this->trackData, $trackPoint);
				}
			}else{
				throw new TracklogPhpException("This file doesn't appear to have any tracklog data.");
			}
			$this->populateDistance();
			$this->trackName = $xml->xpath('//kml:Document/kml:name')[0];
			return $this;

		} catch (TracklogPhpException $e) {
			throw $e;
		}
	}

	protected function write($file_path = null){
		$kml = new SimpleXMLElement('<kml/>');	
		$kml->addAttribute('xmlns','http://www.opengis.net/kml/2.2');
		$kml->addAttribute('xmlns:xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$kml->addAttribute('xmlns:xmlns:gx','http://www.google.com/kml/ext/2.2');
		$kml->addAttribute('xsi:xsi:schemaLocation','http://www.opengis.net/kml/2.2 http://schemas.opengis.net/kml/2.2.0/ogckml22.xsd http://www.google.com/kml/ext/2.2 http://developers.google.com/kml/schema/kml22gx.xsd">');
			$document = $kml->addChild('Document');
				if ($this->hasTime()) {
				$folder = $document->addChild('Folder');
					if (isset($this->trackName)) {
					$folder->addChild('name', $this->trackName);
					}
					$folder->addChild('open', 1);
						$placemark = $folder->addChild('Placemark');
							$gxtrack = $placemark->addChild('gx:gx:Track');
								foreach ($this->trackData as $trackPoint) {
									$gxtrack->addChild('when', $trackPoint->getTime());
								}
								foreach ($this->trackData as $trackPoint) {
									$coordinates = $trackPoint->getLongitude().' '.$trackPoint->getLatitude();
									$coordinates .= $this->hasElevation() ? ' '.$trackPoint->getElevation() : "";
									$gxtrack->addChild('gx:gx:coord', $coordinates);
								}
				}else{
				$placemark = $document->addChild('Placemark');
					if (isset($this->trackName)) {
						$placemark->addChild('name', $this->trackName);
					}
					$placemark->addChild('visibility', 1);
					$placemark->addChild('open', 1);
					$linestring = $placemark->addChild('LineString');
						$linestring->addChild('extrude', 'true');
						$linestring->addChild('tessellate', 'true');						
						$trackData = '';
						foreach ($this->trackData as $trackPoint) {
							$trackData .= $trackPoint->getLongitude().','.$trackPoint->getLatitude();
							$trackData .= $this->hasElevation() ? ',' . $trackPoint->getElevation().'&#10;' : '&#10;';
						}
						$coordinates = $linestring->addChild('coordinates', $trackData);							
				}

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom_xml = dom_import_simplexml($kml);
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
			$dom->schemaValidate("lib/formats/xsd_files/". get_class($this) .".xsd");
		} catch (TracklogPhpException $e) {
			$e->setMessage("This isn't a valid " . get_class($this) . " file.");
			throw $e;
		}
		restore_error_handler();
	}
}
?>