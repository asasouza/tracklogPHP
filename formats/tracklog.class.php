<?php

abstract class Tracklog{

	public $content;

	protected function __construct($file){
		$this->content = simplexml_load_file($file);
	}

	public abstract function getPoints();

	// public abstract function getLat();

	// public abstract function getLong();

	// public abstract function getTime();

	// public abstract function getHeight();

	// public abstract function getTotalDistance();

	// public abstract function getMaxHeight();

	// public abstract function getPace();

	// public abstract function getTotalTime();

	// public abstract function getMarkers();

	// public abstract function convert($file, $output);
}

?>