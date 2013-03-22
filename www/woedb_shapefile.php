<?php

	include("include/init.php");
	loadlib("woedb");

	features_ensure_enabled("woedb_static");

	$woeid = get_int32("woeid");

	if (! $woeid){
		error_404();
	}

	$path = woedb_id2fq_path($woeid);

	if (! file_exists($path)){
		error_404();
	}

	$length = filesize($path);
	$type = (get_isset("text")) ? "text/plain" : "application/json";

	header("HTTP/1.0 200 OK");
	header("content-type: {$type}");
	header("content-length: {$length}");

	if ($_SERVER['REQUEST_METHOD'] == 'HEAD'){
		exit();
	}

	header("Access-Control-Allow-Origin: *");

	$fh = fopen($path, 'r');
	echo fread($fh, $length);
	fclose($fh);

	exit();

?>
