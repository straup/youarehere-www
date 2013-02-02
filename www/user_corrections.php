<?php

	include("include/init.php");

	features_ensure_enabled("show_users");

	$id = get_int32("id");

	if (! $id){
		error_404();
	}	

	$user = users_get_by_id($id);

	if (! $user){
		error_404();
	}

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	$rsp = corrections_get_for_user($user, $more);
	$GLOBALS['smarty']->assign_by_ref("corrections", $rsp['rows']);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_user_corrections.txt");
	exit();
?>
