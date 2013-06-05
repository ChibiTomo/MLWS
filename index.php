<?php
/**
 *
 */

require_once 'include/misc.inc';

set_base_dir();

require_once 'include/include.inc';

try {
	init();
	//install_check();
	debug(HOST);
	debug(BASE_DIR);
	debug_flush();
} catch (Exception $e) {
	error($e);
}
?>