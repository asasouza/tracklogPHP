<?php

class KML extends Tracklog{

	public function __construct($file){
		parent::__construct($file);
	}

	public function getPoints(){
		$content = $this->content->Document->Placemark->MultiGeometry->LineString->coordinates;

		$content = preg_replace('/\s+/', ',', $content);

		$coordinates = explode(',', $content);

		$points;

		for ($i=0; $i < count($coordinates);) { 

			$points[$i]['lat'] = $coordinates[$i];
			$points[$i]['long'] = $coordinates[$i+1];
			$points[$i]['ele'] = $coordinates[$i+2];

			$i = $i+3;
		}

		return $points;
	}

} 


?>