<?php
/**
 *
 */
require_once 'include/misc.inc';
require_once 'include/global.inc';
require_once 'include/debugging.inc';

//header("Content-Type: text/plain");
try {
	debug('toto');
	debug($g_debugger);
	debug_flush();
	debug_flush();
	debug('toto');
	debug_flush();
} catch (Exception $e) {
	error($e);
}
?>