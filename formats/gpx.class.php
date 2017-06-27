<?php 
class GPX extends Tracklog{

	public function __construct($file){
		$xml = simplexml_load_file($file) or die ("File not found!");
		$xml->registerXPathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
		$content = $xml->xpath('//gpx:trkseg');
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
	}

	protected function write($file_path = null){
		$gpx = new SimpleXMLElement('<gpx />');
		$gpx->addAttribute('version', '1.1');
		$gpx->addAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
		$gpx->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$gpx->addAttribute('xsi:schemaLocation', 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd');
			$metadata = $gpx->addChild('metadata');
				$time = $metadata->addChild('time', $this->getTime()[0]); // vai dar erro;
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
						if (get_class($this) != 'KML' && get_class($this) != 'GeoJson') { //mudar posteriormente
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
	}

	?>