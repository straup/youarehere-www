<?php

	include("include/init.php");

	login_ensure_loggedin();

	loadlib("corrections");
	loadlib("reverse_geocode");

	$user = $GLOBALS['cfg']['user'];

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	if ($p = get_str("perspective")){

		$p = "{$p}s";

		$map = corrections_perspective_filter_map('string keys');

		if (array_key_exists($p, $map)){
			$more['perspective'] = $map[$p];
		}
	}

	$rsp = corrections_get_for_user($user, $more);
	$GLOBALS['smarty']->assign_by_ref("corrections", $rsp['rows']);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_corrections_me.txt");
	exit();
?>
