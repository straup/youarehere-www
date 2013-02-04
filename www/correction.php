<?php

	include("include/init.php");

	loadlib("corrections");
	loadlib("corrections_export");

	$id = get_int64("id");

	if ((! $id) && ($code = get_str("code"))){
		$id = base58_decode($code);
	}

	if (! $id){
		error_404();
	}

	$correction = corrections_get_by_id($id);

	if (! $correction){
		error_404();
	}

	$GLOBALS['smarty']->assign_by_ref("correction", $correction);

	# Not a feature. Please fix me... (20130204/straup)
	$corrections = array($correction);
	$GLOBALS['smarty']->assign_by_ref("corrections", $corrections);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_correction.txt");
	exit();

?>
