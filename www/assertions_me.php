<?php

	include("include/init.php");

	login_ensure_loggedin();

	loadlib("assertions");
	loadlib("reverse_geocode");

	$user = $GLOBALS['cfg']['user'];

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	if ($p = get_str("perspective")){

		$p = "{$p}s";

		$map = assertions_perspective_filter_map('string keys');

		if (array_key_exists($p, $map)){
			$more['perspective'] = $map[$p];
		}
	}

	$rsp = assertions_get_for_user($user, $more);
	$GLOBALS['smarty']->assign_by_ref("assertions", $rsp['rows']);

	$map = assertions_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->assign("filter_root", "{$GLOBALS['cfg']['abs_root_url']}assertions/me/");

	$GLOBALS['smarty']->display("page_assertions_me.txt");
	exit();
?>
