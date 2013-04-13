<?php

	include("include/init.php");

	loadlib("assertions");
	loadlib("reverse_geocode");

	$id = get_int64("id");

	$mock_woe = array(
		'woe_id' => $id,
	);

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

	$rsp = assertions_get_for_woe($mock_woe, $more);
	$GLOBALS['smarty']->assign_by_ref("assertions", $rsp['rows']);

	$map = assertions_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->assign("filter_root", "{$GLOBALS['cfg']['abs_root_url']}assertions/woe/{$mock_woe['woe_id']}/");

	$GLOBALS['smarty']->display("page_assertions_woe.txt");
	exit();
?>
