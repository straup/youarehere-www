<?php

	include("include/init.php");

	loadlib("twitter_users");
	loadlib("twitter_oauth");
	loadlib("random");

	# Some basic sanity checking like are you already logged in?

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$GLOBALS['cfg']['abs_root_url']}");
		exit();
	}


	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit();
	}

	# See the notes in signin_oauth.php about cookies and request
	# tokens.

	if (! $GLOBALS['cfg']['crypto_oauth_cookie_secret']){
		$GLOBALS['error']['oauth_missing_secret'] = 1;
		$GLOBALS['smarty']->display("page_auth_callback_twitter_oauth.txt");
		exit();
	}

	# Grab the cookie and blow it away. This makes things a little
	# bit of a nuisance if something goes wrong below because you'll
	# need to re-auth a user but there you go.

	$oauth_cookie = login_get_cookie('o');
	login_unset_cookie('o');

	if (! $oauth_cookie){
		$GLOBALS['error']['oauth_missing_cookie'] = 1;
		$GLOBALS['smarty']->display("page_auth_callback_twitter_oauth.txt");
		exit();
	}

	$request = crypto_decrypt($oauth_cookie, $GLOBALS['cfg']['crypto_oauth_cookie_secret']);
	$request = explode(":", $request, 2);

	# Make sure that we've got the minimum set of parameters
	# we expect Twitter to send back.

	$verifier = get_str('oauth_verifier');
	$token = get_str('oauth_token');

	if ((! $verifier) || (! $token)){
		$GLOBALS['error']['oauth_missing_args'] = 1;
		$GLOBALS['smarty']->display("page_auth_callback_twitter_oauth.txt");
		exit();
	}

	# Now we exchange the request token/secret for a more permanent set
	# of OAuth credentials. In plain old Twitter auth language this is
	# where we exchange the frob (the oauth_verifier) for an auth token.
	# The only difference is that we sign the request using both the app's
	# signing secret and the user's (temporary) request secret.

	$user_keys = array(
		'oauth_token' => $request[0],
		'oauth_secret' => $request[1],
	);

	$args = array(
		'oauth_verifier' => $verifier,
		'oauth_token' => $token,
	);

	$rsp = twitter_oauth_get_access_token($args, $user_keys);

	if (! $rsp['ok']){
		$GLOBALS['error']['oauth_access_token'] = 1;
		$GLOBALS['smarty']->display("page_auth_callback_twitter_oauth.txt");
		exit();
	}

	# Hey look! If we've gotten this far then that means we've been able
	# to use the Twitter API to validate the user and we've got an OAuth
	# key/secret pair.

	$data = $rsp['data'];

	$twitter_id = $data['user_id'];
	$username = $data['screen_name'];

	# The first thing we do is check to see if we already have an account
	# matching that user's Twitter ID.

	$twitter_user = twitter_users_get_by_twitter_id($twitter_id);

	if ($user_id = $twitter_user['user_id']){
		$user = users_get_by_id($user_id);
	}

	# If we don't ensure that new users are allowed to create
	# an account (locally).

	else if (! $GLOBALS['cfg']['enable_feature_signup']){
		$GLOBALS['smarty']->display("page_signup_disabled.txt");
		exit();
	}

	# Hello, new user! This part will create entries in two separate
	# databases: Users and TwitterUsers that are joined by the primary
	# key on the Users table.

	else {

		$password = random_string(32);

		$rsp = users_create_user(array(
			"username" => $username,
			"email" => "{$username}@donotsend-twitter.com",
			"password" => $password,
		));

		if (! $rsp['ok']){
			$GLOBALS['error']['dberr_user'] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_twitter_oauth.txt");
			exit();
		}

		$user = $rsp['user'];

		$twitter_user = twitter_users_create_user(array(
			'user_id' => $user['id'],
			'twitter_id' => $twitter_id,
			'oauth_token' => $data['oauth_token'],
			'oauth_secret' => $data['oauth_token_secret'],
		));

		if (! $twitter_user){
			$GLOBALS['error']['dberr_twitteruser'] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_twitter_oauth.txt");
			exit();
		}
	}

	# Okay, now finish logging the user in (setting cookies, etc.) and
	# redirecting them to some specific page if necessary.

	$redir = '';

	if ($redir_cookie = login_get_cookie('r')){
		login_unset_cookie('r');
		$redir = crypto_decrypt($redir_cookie, $GLOBALS['cfg']['crypto_oauth_cookie_secret']);
	}

	login_do_login($user, $redir);
	exit();
