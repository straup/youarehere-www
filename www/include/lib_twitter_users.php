<?php

	#################################################################

	function twitter_users_create_user($user){

		$hash = array();

		foreach ($user as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TwitterUsers', $hash);

		if (!$rsp['ok']){
			return null;
		}

		return $user;
	}

	#################################################################

	function twitter_users_update_user(&$twitter_user, $update){

		$hash = array();
		
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($twitter_user['user_id']);
		$where = "user_id='{$enc_id}'";

		$rsp = db_update('TwitterUsers', $hash, $where);

		if ($rsp['ok']){

			$twitter_user = array_merge($twitter_user, $update);
		}

		return $rsp;
	}

	#################################################################

	function twitter_users_get_by_twitter_id($twitter_id){

		$enc_twitter_id = AddSlashes($twitter_id);

		$sql = "SELECT * FROM TwitterUsers WHERE twitter_id='{$enc_twitter_id}'";

		$rsp = db_fetch($sql);
		$user = db_single($rsp);

		return $user;
	}

	#################################################################

	function twitter_users_get_by_user_id($user_id){

		$enc_id = AddSlashes($user_id);

		$sql = "SELECT * FROM TwitterUsers WHERE user_id='{$enc_id}'";

		$rsp = db_fetch($sql);
		$user = db_single($rsp);

		return $user;
	}

	#################################################################

	function twitter_users_delete_user(&$twitter_user){

		$enc_user = AddSlashes($twitter_user['user_id']);

		$sql = "DELETE FROM TwitterUsers WHERE user_id='{$enc_user}'";
		$rsp = db_write($sql);

		return $rsp;
	}

	#################################################################

	# the end
