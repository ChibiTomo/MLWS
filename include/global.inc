<?php
	$g_debugger = new Debugger();
	$g_debugger->on();
	
	if (file_exists(BASE_DIR . "/" . CONFIG_FILE)) {
		require_once(BASE_DIR . "/" . CONFIG_FILE);
		try {
			$g_pdo = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DBNAME.';charset=UTF-8', MYSQL_USER, MYSQL_PASSWORD);
			$g_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$g_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$g_pdo->exec("set names utf8"); // ovh php 5.2
		} catch (Exception $e) {
		}
	}
?>