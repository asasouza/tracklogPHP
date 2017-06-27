<?php
class KML extends Tracklog{

	public function __construct($file){
		if ($this->validate($file)) {
			$xml = simplexml_load_file($file) or die ("File not found!");

			$xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
			$xml->registerXPathNamespace('gx', 'http://www.google.com/kml/ext/2.2');

			$content = $xml->xpath('//kml:coordinates');
			if (!empty($content)) {
				$content = preg_replace('/\s+/', ',', $content);
				$coordinates = explode(',', $content[0]);
				$y = 0;
				for ($i=0; $i < count($coordinates);) {			
					$this->trackData[$y]['lon'] = number_format((float) $coordinates[$i], 7);
					$this->trackData[$y]['lat'] = number_format((float) $coordinates[$i+1], 7);
					$this->trackData[$y]['ele'] = number_format((float) $coordinates[$i+2], 6);
					$i = $i+3;
					$y++;
				}
				$this->populateDistance();
				return $this;
			}else{
				$times = $xml->xpath('//gx:Track/kml:when');				
				$coordinates = $xml->xpath('//gx:Track/gx:coord');				
				foreach ($coordinates as $i => $coordinate) {
					$coordinate = explode(' ', $coordinate);
					$this->trackData[$i]['lon'] = number_format((float) $coordinate[0], 7);
					$this->trackData[$i]['lat'] = number_format((float) $coordinate[1], 7);
					$this->trackData[$i]['ele'] = number_format((float) $coordinate[2], 6);
					$this->trackData[$i]['time'] = (string) $times[$i];
				}
				$this->populateDistance();
				return $this;
			}

			
		}else{
			$erros = libxml_get_errors();
			foreach ($erros as $erro) {
				print_r($erro);
				echo '<br>';
			}
		}		
	}

	public function getTime(){
		if (!empty($this->trackData[0]['time'])) {
			return parent::getTime();
		}else{
			throw new Exception("This KML file don't support time manipulations", 1);	
		}
	}

	public function getPace(){
		if (!empty($this->trackData[0]['time'])) {
			return parent::getPace();
		}else{
			throw new Exception("This KML file don't support time manipulations", 1);	
		}
	}

	public function getTotalTime($format = null){
		if (!empty($this->trackData[0]['time'])) {
			return parent::getTotalTime($format);
		}else{
			throw new Exception("This KML file don't support time manipulations", 1);	
		}
	}

	protected function write($file_path = null){
		$kml = new SimpleXMLElement('<kml/>');	
		$kml->addAttribute('xmlns','http://www.opengis.net/kml/2.2');
		$kml->addAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$kml->addAttribute('xsi:schemaLocation','http://www.opengis.net/kml/2.2 http://schemas.opengis.net/kml/2.2.0/ogckml22.xsd http://www.google.com/kml/ext/2.2 http://developers.google.com/kml/schema/kml22gx.xsd">');
			$document = $kml->addChild('Document');
				$placemark = $document->addChild('Placemark');
					if (isset($this->trackData['meta_tag']['name'])) {
						$placemark->addChild('name', $this->trackData['meta_tag']['name']);
					}
					$placemark->addChild('visibility', 1);
					$placemark->addChild('open', 1);
					$linestring = $placemark->addChild('LineString');
						$linestring->addChild('extrude', 'true');
						$linestring->addChild('tessellate', 'true');						
						$trackData = '';
						foreach ($this->trackData as $coordinates) {
							$trackData = $trackData . $coordinates['lon'].','.$coordinates['lat'].','.$coordinates['ele']. '&#10;';
						}
						$coordinates = $linestring->addChild('coordinates', $trackData);							

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
		libxml_use_internal_errors(true);
		$dom = new DOMDocument;
		$dom->load($file);
		if ($dom->schemaValidate('xsd_files/kml.xsd')) {
			return true;
		}else{
			return false;
		}
	}
}
?>