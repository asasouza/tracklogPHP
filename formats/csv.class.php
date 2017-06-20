<?php 
class CSV extends Tracklog{
	public function __construct($file){
		$csv = file_get_contents($file);

		$content = preg_split('/\s+/', $csv);

		for ($i=0; $i < count($content)-2; $i++) { 
			$coordinates = explode(',', $content[$i+1]);
			$this->trackData[$i]['lat'] = $coordinates[0];
			$this->trackData[$i]['lon'] = $coordinates[1];
			$this->trackData[$i]['ele'] = $coordinates[2];
		}
	}

	public function getTime(){
		throw new Exception("CSV files don't support time manipulations", 1);
	}

	public function getPace(){
		throw new Exception("CSV files don't support time manipulations", 1);
	}

	public function getTotalTime(){
		throw new Exception("CSV files don't support time manipulations", 1);
	}
}
?>