<?php

	include("include/init.php");

	loadlib("corrections");
	loadlib("reverse_geocode");

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	$rsp = corrections_get_recent($more);
	$GLOBALS['smarty']->assign_by_ref("corrections", $rsp['rows']);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_corrections.txt");
	exit();

?>	
