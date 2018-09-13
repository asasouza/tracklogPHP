<?php
/**
* An object that represents a trackpoint element
*
*@author Alex Sandro de Araujo Souza - @asasouza
*@version 1.0 2017/12/05
*/
class TrackMarker extends TrackPoint {

	private $name = null;
	private $styleUrl = null;

	public function getName(){
		return $this->name;
	}

	public function getStyleUrl(){
		return $this->styleUrl;
	}

	public function setName($name){
		if (($name)) {
			$this->name = (string) $name;
		}else{
			throw new Exception("Invalid trackpoint name format");			
		}
	}

	public function setStyleUrl($url){
		if (($url)) {
			$this->styleUrl = (string) $url;
		}else{
			throw new Exception("Invalid trackpoint StyleURL format");			
		}
	}

}
?>