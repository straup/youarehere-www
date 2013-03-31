<?php

	include("include/init.php");

	loadlib("corrections");
	loadlib("reverse_geocode");

	$id = get_int64("id");

	if ((! $id) && ($code = get_str("code"))){
		$id = base58_decode($code);
	}

	if (! $id){
		error_404();
	}

	$correction = corrections_get_by_id($id);

	if (! $correction){

		if ($redacted = corrections_redacted_get_by_id($id)){
			error_410();
		}

		error_404();
	}

	$is_own = (($GLOBALS['cfg']['user']) && (corrections_is_own($correction, $GLOBALS['cfg']['user']))) ? 1 : 0;
	$GLOBALS['smarty']->assign("is_own", $is_own);

	if ($is_own){

		$crumb_key = 'delete';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

		if (post_isset('delete') && crumb_check($crumb_key)){

			$rsp = corrections_delete_correction($correction);

			if ($rsp['ok']){

				$redir = "{$GLOBALS['cfg']['abs_root_url']}corrections/me?deleted=1";
				header("location: {$redir}");
				exit();
			}

			$GLOBALS['smarty']->assign("delete_error", $rsp['error']);
		}
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
