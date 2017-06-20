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
		return $this;

	}
}

?>