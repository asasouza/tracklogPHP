<?php
	$output = [];
	exec('git --version', $output);

	print_r($output);
?>