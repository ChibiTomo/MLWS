<?php
function autoloader($class) {
	$file_path = 'include/class/'.ucfirst(strtolower($class)).'.class.php';
	if (file_exists($file_path)) {
		require_once($file_path);
	}
}

spl_autoload_register('autoloader');

function tab($qty = 1) {
	if (!is_int($qty)) {
		throw new Exception('tab expect Argument 1 to be integer');
	}

	$result = '';
	for ($i = 0; $i < $qty; $i++) {
		$result .= '&emsp;';
	}
	return $result;
}

function a2s($txt, $only_values = false) {
	$array = array();
	foreach ($txt as $key => $value) {
		if (is_array($value)) {
			$value = Debugger::a2s($value, $only_values);
		}
		if (is_string($value)) {
			$value = preg_replace('#\\\\#', '\\\\\\', $value);
			$value = preg_replace('#\'#', "\\'", $value);
			$value = "'".$value."'";
		}

		if ($only_values) {
			$array[] = $value;
		} else {
			$array[] = '['.$key.']=>'.$value;
		}
	}
	$str = '';
	if ($only_values) {
		$str = join(',', $array);
	} else {
		$str = join('|', $array);
	}
	return $str;
}

function redirect_to($path) {
	header('Location: ' . $path);
	header('Cache-Control: no-cache');
}

function set_host() {
	if (isset($_SERVER["HTTPS"])) {
		$base_url = HTTPS_ACCESS;
	} else {
		$base_url = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);
	}
	
	define('HOST', $base_url);
}

function set_base_dir() {
	define('BASE_DIR', getcwd());
}

function init() {
	set_host();
}
?>