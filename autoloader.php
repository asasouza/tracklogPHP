<?php

function tracklog_autoloader($class_name){
	if (file_exists('formats/' . $class_name . '.class.php')) {
		require_once('formats/' . $class_name . '.class.php');
	}	
}

spl_autoload_register("tracklog_autoloader");

?>