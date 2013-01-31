<?php

	include("include/init.php");
	loadlib("corrections");

	$id = get_int64("id");

	if (! $id){
		error_404();
	}

	$correction = corrections_get_by_id($id);

	if (! $correction){
		error_404();
	}

	$GLOBALS['smarty']->assign_by_ref("correction", $correction);

	$GLOBALS['smarty']->display("page_correction.txt");
	exit();

?>
