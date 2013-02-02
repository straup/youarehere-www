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

		$cache_key = "twitter_user_{$user['twitter_id']}";
		cache_set($cache_key, $user, "cache locally");

		$cache_key = "twitter_user_{$user['id']}";
		cache_set($cache_key, $user, "cache locally");

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

			$cache_key = "twitter_user_{$twitter_user['twitter_id']}";
			cache_unset($cache_key);

			$cache_key = "twitter_user_{$twitter_user['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}

	#################################################################

	function twitter_users_get_by_twitter_id($twitter_id){

		$cache_key = "twitter_user_{$twitter_id}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_twitter_id = AddSlashes($twitter_id);

		$sql = "SELECT * FROM TwitterUsers WHERE twitter_id='{$enc_twitter_id}'";
		$rsp = db_fetch($sql);
		$user = db_single($rsp);

		cache_set($cache_key, $user, "cache locally");
		return $user;
	}

	#################################################################

	function twitter_users_get_by_user_id($user_id){

		$cache_key = "twitter_user_{$user_id}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_id = AddSlashes($user_id);

		$sql = "SELECT * FROM TwitterUsers WHERE user_id='{$enc_id}'";

		$rsp = db_fetch($sql);
		$user = db_single($rsp);

		cache_set($cache_key, $user, "cache locally");
		return $user;
	}

	#################################################################

	# the end
