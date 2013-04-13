<?php

	include("include/init.php");

	loadlib("assertions");
	loadlib("reverse_geocode");

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	if ($p = get_str("perspective")){

		$map = assertions_perspective_filter_map('string keys');

		if (array_key_exists($p, $map)){
			$more['perspective'] = $map[$p];
		}
	}

	$rsp = assertions_get_recent($more);
	$GLOBALS['smarty']->assign_by_ref("assertions", $rsp['rows']);

	$map = assertions_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->assign("filter_root", $GLOBALS['cfg']['abs_root_url'] . 'assertions/');

	$GLOBALS['smarty']->display("page_assertions.txt");
	exit();

?>	
