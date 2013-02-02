<?php

	include("include/init.php");

	loadlib("corrections");
	loadlib("corrections_export");

	$id = get_int64("id");

	if (! $id){
		error_404();
	}

	$correction = corrections_get_by_id($id);

	if (! $correction){
		error_404();
	}

	$geojson = corrections_export_as_geojson(array($correction));
	$geojson = json_encode($geojson);

	# header("Access-Control-Allow-Origin: *");

	header("Content-Type: text/json");
	header("Content-Length: " . strlen($geojson));

	echo $geojson;
	exit();

?>
