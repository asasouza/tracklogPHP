<?php
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
	public function getTime(){
		$time;
		foreach ($this->trackData as $point) {
			$time[] = $point['time'];
		}
		return $time;
	}

	public function getTotalDistance($unit = "meters"){
		$totalDistance = 0;
		$earthRadius = 6371000;
		for ($i=0; $i < count($this->trackData)-1; $i++) { 
			//begging parameters
			$latB = deg2rad($this->trackData[$i]['lat']);
			$lonB = deg2rad($this->trackData[$i]['lon']);
			//ending parameters
			$latE = deg2rad($this->trackData[$i+1]['lat']);
			$lonE = deg2rad($this->trackData[$i+1]['lon']);

			$latDelta = $latE - $latB;
			$lonDelta = $lonE - $lonB;

			$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
				cos($latB) * cos($latE) * pow(sin($lonDelta / 2), 2)));
			$totalDistance += $angle * $earthRadius;
		}
		switch ($unit) {
			case 'meters':
				return number_format($totalDistance, 2);
			break;
			case 'kilometers':
				return number_format($totalDistance/1000, 2);
			break;
			case 'miles':
				return number_format($totalDistance/1609.34, 2);
			break;
			default:
				throw new Exception("Unit format not recognized", 1);			
			break;
		}		
	}

	public function getMaxHeight(){
	}

	//Não suportado por KML
	public function getPace(){
	}

	//Não suportado por KML
	public function getTotalTime(){}

	public function getMarkers(){}

	public function convert($output){}
}

?>