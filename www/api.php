<?php

	include("include/init.php");

	features_ensure_enabled("api");

	$GLOBALS['smarty']->display("page_api.txt");
	exit();
?>
