<?php

	include("include/init.php");

	loadlib("corrections");
	loadlib("reverse_geocode");

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	if ($p = get_str("perspective")){

		$map = corrections_perspective_filter_map('string keys');

		$pid = (isset($map[$p])) ? $map[$p] : null;

		if ($pid){
			$more['perspective'] = $pid;
		}
	}

	$rsp = corrections_get_recent($more);
	$GLOBALS['smarty']->assign_by_ref("corrections", $rsp['rows']);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_corrections.txt");
	exit();

?>	
