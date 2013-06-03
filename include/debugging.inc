<?php
function error(Exception $e) {
	global $g_debugger;

	$g_debugger->error($e);
}

function debug($txt) {
	global $g_debugger;

	$g_debugger->debug($txt, false, new Exception(''));
}

function debug_now($txt) {
	global $g_debugger;

	$g_debugger->debug($txt, true, new Exception(''));
}

function debug_request($request, $replacements) {
	foreach ($replacements as $key => $value) {
		$request = str_replace($key, $value, $request);
	}
	$e = new Exception("");
	debug($request, $e);
}

function debug_flush() {
	global $g_debugger;

	echo $g_debugger->flush();
}
?>