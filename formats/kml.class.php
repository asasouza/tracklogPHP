<?php
class KML extends Tracklog{

	public function __construct($file){
		$file_content = simplexml_load_file($file) or die ("File not found!");;

		$content = $file_content->Document->Placemark->MultiGeometry->LineString->coordinates;

		$content = preg_replace('/\s+/', ',', $content);

		$coordinates = explode(',', $content);

		$y = 0;
		for ($i=0; $i < count($coordinates);) {			
			$this->trackData[$y]['lat'] = $coordinates[$i];
			$this->trackData[$y]['lon'] = $coordinates[$i+1];
			$this->trackData[$y]['ele'] = $coordinates[$i+2];

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