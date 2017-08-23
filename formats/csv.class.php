<?php 
class CSV extends Tracklog{
	public function __construct($file){
		try {
			$csv = file_get_contents($file);
			$content = preg_split('/\s+/', $csv);
			$index = explode(',', $content[0]);

			if (strpos($content[0], 'Lon') !== false) {
				$latIndex = $lonIndex = $eleIndex = $timeIndex = '';
				foreach ($index as $key => $value) {
					$latIndex = is_int(strpos(strtolower($value), 'lat')) ? $key : $latIndex;
					$lonIndex = is_int(strpos(strtolower($value), 'lon')) ? $key : $lonIndex;
					$eleIndex = is_int(strpos(strtolower($value), 'ele')) ? $key : $eleIndex;
					$timeIndex = is_int(strpos(strtolower($value), 'time')) ? $key : $timeIndex;
				}
			}

			foreach ($content as $key => $value) {
				// $coordinates = explode(',', $value);
				// $this->trackData[$key]['lat'] = $coordinates[$latIndex];
				// $this->trackData[$key]['lon'] = $coordinates[$lonIndex];
				// $this->trackData[$key]['ele'] = $coordinates[$eleIndex];
			}
			// print_r($this->trackData);
			for ($i = 0; $i < count($content); $i++) { 
				// $coordinates = explode(',', $content[$i+1]);
				// print_r($coordinates);
				// $this->trackData[$i]['lat'] = $coordinates[0];
				// $this->trackData[$i]['lon'] = $coordinates[1];
				// $this->trackData[$i]['ele'] = $coordinates[2];
			}
			$this->populateDistance();
			return $this;	

		} catch (TracklogPhpException $e) {
			throw new TracklogPhpException("Invalid CSV file.");
		}	
	}

	public function getTime(){
		throw new TracklogPhpException("CSV files don't support time manipulations");
	}

	public function getPace(){
		throw new TracklogPhpException("CSV files don't support time manipulations");
	}

	public function getTotalTime(){
		throw new TracklogPhpException("CSV files don't support time manipulations");
	}

	protected function write($file_path = null){
		$trackData = 'Latitude,Longitude,Elevation ';
		foreach ($this->trackData as $trackdata) {
			$trackData .= $trackdata['lat'].','.$trackdata['lon'].','.$trackdata['ele'].' ';
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

	protected function validate($file){
		// set_error_handler(array('Tracklog', 'error_handler'));
		// $dom = new DOMDocument;
		// if (!file_exists($file)) {
		// 	throw new Exception('Failed to load external entity "' . $file . '"');
		// }else{
		// 	$dom->load($file);	
		// }		
		// try {			
		// 	$dom->schemaValidate("xsd_files/". get_class($this) .".xsd");
		// } catch (Exception $e) {
		// 	throw new TracklogPhpException("This isn't a valid " . get_class($this) . " file.");
		// }	
	}
}
?>