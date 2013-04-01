<?
	#
	# $Id$
	#

	include("include/init.php");

	features_ensure_enabled("signin");
	features_ensure_enabled("account_delete");

	login_ensure_loggedin();
	
	loadlib("twitter_users");

	#
	# generate a crumb
	#

	$crumb_key = 'account_delete';
	$GLOBALS['smarty']->assign('crumb_key', $crumb_key);


	#
	# delete account?
	#

	if (post_str('delete') && crumb_check($crumb_key)){

		if (post_str('confirm')){

			$ok = 1;

			$tw_user = twitter_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

			if (! $tw_user){
				$ok = 0;
			}

			if ($ok){
				$tw_rsp = twitter_users_delete_user($tw_user);
				$ok = $tw_rsp['ok'];
			}

			if ($ok){
				$ok = users_delete_user($GLOBALS['cfg']['user']);
			}

			if ($ok){
				login_do_logout();

				$GLOBALS['smarty']->display('page_account_delete_done.txt');
				exit;
			}

			$GLOBALS['smarty']->assign('error_deleting', 1);

			$GLOBALS['smarty']->display('page_account_delete.txt');

			exit();
		}

		$GLOBALS['smarty']->display('page_account_delete_confirm.txt');
		exit();
	}

	$GLOBALS['smarty']->display("page_account_delete.txt");
	exit();
