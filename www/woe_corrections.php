<?php

	include("include/init.php");

	loadlib("corrections");
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

		$map = corrections_perspective_filter_map('string keys');

		$pid = (isset($map[$p])) ? $map[$p] : null;

		if ($pid){
			$more['perspective'] = $pid;
		}
	}

	$rsp = corrections_get_for_woe($mock_woe, $more);
	$GLOBALS['smarty']->assign_by_ref("corrections", $rsp['rows']);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_woe_corrections.txt");
	exit();
?>
