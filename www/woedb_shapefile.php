<?php

	include("include/init.php");

	exit();

	loadlib("woedb");

	$woeid = get_int32("woeid");

	if (! $woeid){
		error_404();
	}

	$tree = woedb_id2path($woeid);
	$fname = "{$woeid}.json";

	# root...

	$path = $tree . $fname;

	if (! file_exists($path)){
		error_404();
	}

	# Get file length
	# CORS

	header("Content-Type: text/json");

	http_send_file($path);
	exit();

?>
