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

		$this->populateDistance();

		return $this;
	}

	public function getTime(){
		throw new TracklogPhpException("CSV files don't support time manipulations", 1);
	}

	public function getPace(){
		throw new TracklogPhpException("CSV files don't support time manipulations", 1);
	}

	public function getTotalTime(){
		throw new TracklogPhpException("CSV files don't support time manipulations", 1);
	}

	protected function write($file_path = null){
		$trackData = 'Latitude,Longitude,Elevation ';
		foreach ($this->trackData as $trackdata) {
			$trackData = $trackData.$trackdata['lat'].','.$trackdata['lon'].','.$trackdata['ele'].' ';
		}

		if (!empty($file_path)) {
			$content = preg_split('/\s+/', $trackData);
			$file = fopen($file_path, 'w');
			foreach ($content as $value) {
				fputcsv($file, explode(',', $value));	
			}
			fclose($file);
		}

		return $trackData;

	}
}
?>