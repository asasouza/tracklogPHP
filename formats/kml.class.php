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

	public function write(){
		$xml_header = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/kml/2.2 http://schemas.opengis.net/kml/2.2.0/ogckml22.xsd"><Document>';
  		$xml_body = '<Placemark>';

  		if (isset($this->trackData["meta_data"])) {
  			//será utilizado para pegar os dados de name, alguns markers, etc.
  		}

  		// $xml_body += isset($this->trackData['meta_data']['name']) ? '<name>'.$this->trackData['meta_data']['name'].'</name>' : '';
  		$xml_body += '<LineString><extrude>true</extrude><tessellate>true</tessellate><coordinates>';
  		foreach ($this->trackData as $coordinates) {
  			// $xml_body;
  		}

  		$xml_footer = '</LineString></Placemark></Document></kml>';
  		var_dump($xml_body);
  		// return $xml_header . $xml_body . $xml_footer;
	}
}
?>