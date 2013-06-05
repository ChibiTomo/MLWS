<?php
	function install_check() {
		debug('Check installation');
		if (!file_exists(CONFIG_FILE)) {
			install_configuration();
		}
		if (!admin_exists()) {
			redirect_to('install.php?action=get_form&type=admin');
			install_admin();
		}
		debug('All is installed');
	}
	
	function install() {
		debug('Start installation.');
		switch ($_GET['action']) {
			case 'install':
				install_action();
				break;
			case 'get_form':
				install_get_form();
				break;
		}
		debug('Installation done.');
		redirect_to('');
	}
	
	function install_action() {
		if (!isset($_GET['type'])) {
			throw new Exception('No type to install specified.');
		}
	}
	
	function install_get_form() {
		if (!isset($_GET['type'])) {
			throw new Exception('No form type specified.');
		}
	}
	
	function install_configuration() {
		
	}
	
	function install_admin() {
	
	}
	
	function admin_exists() {
		global $g_pdo;
	}
?>