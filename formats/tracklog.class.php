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
	public function getPoints(){
		return $this->trackData;
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

	public function getTime(){
		$time;
		foreach ($this->trackData as $point) {
			$time[] = $point['time'];
		}
		return $time;
	}

	public function getTotalDistance(){
	}

	public function getMaxHeight(){
	}

	public function getPace(){
	}

	public function getTotalTime(){}

	public function getMarkers(){}

	public function convert($file, $output){}
}

?>