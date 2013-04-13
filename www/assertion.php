<?php

	include("include/init.php");

	loadlib("assertions");
	loadlib("reverse_geocode");

	$id = get_int64("id");

	if ((! $id) && ($code = get_str("code"))){
		$id = base58_decode($code);
	}

	if (! $id){
		error_404();
	}

	$assertion = assertions_get_by_id($id);

	if (! $assertion){

		if ($redacted = assertions_redacted_get_by_id($id)){
			error_410();
		}

		error_404();
	}

	$is_own = (($GLOBALS['cfg']['user']) && (assertions_is_own($assertion, $GLOBALS['cfg']['user']))) ? 1 : 0;
	$GLOBALS['smarty']->assign("is_own", $is_own);

	if ($is_own){

		$crumb_key = 'delete';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

		if (post_isset('delete') && crumb_check($crumb_key)){

			$rsp = assertions_delete_assertion($assertion);

			if ($rsp['ok']){

				$redir = "{$GLOBALS['cfg']['abs_root_url']}assertions/me?deleted=1";
				header("location: {$redir}");
				exit();
			}

			$GLOBALS['smarty']->assign("delete_error", $rsp['error']);
		}
	}

	$GLOBALS['smarty']->assign_by_ref("assertion", $assertion);

	# Not a feature. Please fix me... (20130204/straup)
	$assertions = array($assertion);
	$GLOBALS['smarty']->assign_by_ref("assertions", $assertions);

	$map = assertions_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_assertion.txt");
	exit();

?>
