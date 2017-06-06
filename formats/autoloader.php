<?php

function tracklog_autoloader($class_name){
	require_once($class_name . '.class.php');
}

spl_autoload_register("tracklog_autoloader");

?>