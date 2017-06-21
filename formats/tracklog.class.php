<?php
//TO DO 'getTotalTime': precisa formatar o horário para retornar tempo total em minutos, horas, segundos e tempo formatado

abstract class Tracklog{
	//Array com todos os dados disponiveis no arquivo de log
	//lat - latitude
	//log - longitude
	//ele - altura/elevação
	//time - tempo/horario
	//dstc - distancia
	protected $trackData;

	//A classe nunca é criada diretamente, ficando a cargo da classe filha;
	//No metodo construtor a classe filha captura os dados de acordo com sua estrutura xml e popula o vetor $trackData;
	protected abstract function __construct($file);

	//Função de escrita de xml pela classe filha
	protected abstract function write();

	//Função para popular o vetor TrackData com a variavel distancia [dstc] para os arquivos 
	//que não possuem tal informação (KML, GPX, GeoJson);
	protected function populateDistance(){
		$distance = number_format(0.000, 3, '.', '');
		$this->trackData[0]['dstc'] = $distance;
		for ($i=0; $i < count($this->trackData)-1; $i++) { 
			$distance += $this->haversineFormula($this->trackData[$i]['lat'], 
				$this->trackData[$i]['lon'], 
				$this->trackData[$i+1]['lat'], 
				$this->trackData[$i+1]['lon']);
			$this->trackData[$i+1]['dstc'] = number_format($distance, 3, '.', '');
		}
	}

	//Função para calculo de distancia entre duas lat/lon (utilizado em populateDistance);
	protected function haversineFormula($latB, $lonB, $latE, $lonE){
		$earthRadius = 6371000;
		//begging parameters
		$latB = deg2rad($latB);
		$lonB = deg2rad($lonB);
			//ending parameters
		$latE = deg2rad($latE);
		$lonE = deg2rad($lonE);
			//deltas
		$latDelta = $latE - $latB;
		$lonDelta = $lonE - $lonB;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
			cos($latB) * cos($latE) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius;
	}

	//Os métodos abaixo são comuns a todos os tipos de formato, sofrendo variancia de acordo com as informações
	//disponiveis para cada tipo de arquivo;
	public function getPoints($output = "array"){
		switch ($output) {
			case 'array':
			return $this->trackData;
			break;
			case 'json':
			return json_encode($this->trackData);
			break;
			default:
			throw new Exception("Output format not recognized", 1);			
			break;
		}
	}

	public function getLats(){
		$latitudes;
		foreach ($this->trackData as $point) {
			$latitudes[] = $point['lat'];
		}
		return $latitudes;
	}

	public function getLons(){
		$longitudes;
		foreach ($this->trackData as $point) {
			$longitudes[] = $point['lon'];
		}
		return $longitudes;
	}

	public function getEles(){
		$elevations;
		foreach ($this->trackData as $point) {
			$elevations[] = $point['ele'];
		}
		return $elevations;
	}

	//Não suportado por KML
	//Não suportado por GeoJson
	public function getTime(){
		$time;
		foreach ($this->trackData as $point) {
			$time[] = $point['time'];
		}
		return $time;
	}

	public function getDistances(){
		$distances;
		foreach ($this->trackData as $point) {
			$distances[] = $point['dstc'];
		}
		return $distances;
	}

	public function getTotalDistance($unit = "meters"){
		$totalDistance = $this->trackData[count($this->trackData)-1]['dstc'];
		switch ($unit) {
			case 'meters':
			return number_format($totalDistance, 2, '.', '');
			break;
			case 'kilometers':
			return number_format($totalDistance/1000, 2, '.', '');
			break;
			case 'miles':
			return number_format($totalDistance/1609.34, 2, '.', '');
			break;
			default:
			throw new Exception("Unit format not recognized", 1);			
			break;
		}
	}

	public function getMaxHeight(){
		return number_format(max($this->getEles()), 2);
	}

	//Não suportado por KML
	//Não suportado por GeoJson
	public function getPace(){
		$time = new DateTime($this->getTotalTime());
		//multiplicar horas para transformar em minutos;
		$hour = $time->format('H') * 60;
		$minute = $time->format('i');
		//dividir segundos para transformar em frações de minutos;
		$second = $time->format('s') / 60;
		$totalTime = $hour + $minute + $second;
		$pace = $totalTime / $this->getTotalDistance('kilometers');
		$pace = ((($pace - intval($pace)) * 60) / 100) + intval($pace); 
		return number_format($pace, 2);
	}

	//Não suportado por KML
	//Não suportado por GeoJson
	public function getTotalTime(){
		$dateDiff = new DateTime('0000-00-00 00:00:00');
		for ($i=0; $i < count($this->trackData)-1; $i++) { 
			$dateB = new DateTime($this->trackData[$i]['time']);
			$dateE = new DateTime($this->trackData[$i+1]['time']);
			$difference = $dateB->diff($dateE);	
			$dateDiff->add($difference);
		}
		return $dateDiff->format('H:i:s');
	}

	public function getMarkers(){}	

	public function out($output, $file_path = null){
		switch ($output) {
			case 'kml':
			return KML::write($file_path);
			break;
			case 'gpx':
			return GPX::write($file_path);
			break;
			case 'tcx':
			return TCX::write($file_path);
			break;
			case 'geoJson':
			return GeoJson::write($file_path);
			break;
			case 'csv':
			return CSV::write($file_path);
			break;
			default:
			throw new Exception("Output type invalid!", 1);				
			break;
		}
	}
}

?>