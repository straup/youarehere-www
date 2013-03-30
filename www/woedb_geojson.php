<?php

	include("include/init.php");
	loadlib("woedb");

	features_ensure_enabled("woedb_static");

	$woeid = get_int32("id");

	if (! $woeid){
		error_404();
	}

	$path = woedb_id2fq_path($woeid);

	if (! file_exists($path)){
		error_404();
	}

	$length = filesize($path);
	$type = (get_isset("text")) ? "text/plain" : "application/json";

	if ($_SERVER['REQUEST_METHOD'] == 'HEAD'){
		header("HTTP/1.0 200 OK");
		header("content-type: {$type}");
		header("content-length: {$length}");
		exit();
	}

	ob_start("ob_gzhandler");

	header("HTTP/1.0 200 OK");
	header("content-type: {$type}");
	header("Access-Control-Allow-Origin: *");

	echo file_get_contents($path);

	ob_end_flush();
	exit();

?>
