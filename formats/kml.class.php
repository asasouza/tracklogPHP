<?php
class KML extends Tracklog{

	public function __construct($file){
		$xml = simplexml_load_file($file) or die ("File not found!");

		$xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

		$content = $xml->xpath('//kml:coordinates');
		$content = preg_replace('/\s+/', ',', $content);
		$coordinates = explode(',', $content[0]);

		$y = 0;
		for ($i=0; $i < count($coordinates);) {			
			$this->trackData[$y]['lon'] = (float) $coordinates[$i];
			$this->trackData[$y]['lat'] = (float) $coordinates[$i+1];
			$this->trackData[$y]['ele'] = (float) $coordinates[$i+2];
			$i = $i+3;
			$y++;
		}
		return $this;
	}

	public function getTime(){
		throw new Exception("KML files don't support time manipulations", 1);
	}

	public function getPace(){
		throw new Exception("KML files don't support time manipulations", 1);
	}

	public function getTotalTime(){
		throw new Exception("KML files don't support time manipulations", 1);
	}
}
?>