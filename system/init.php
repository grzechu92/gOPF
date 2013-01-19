<?php
	/**
	 * Framework core initiator
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */

	namespace System;

	/**
	 * Load required functions
	 */
	require __SYSTEM_PATH.'/functions.php';
	
	/**
	 * Initialize framework PSR-0 loader
	 */
	require __CORE_PATH.'/Filesystem.php';
	require __CORE_PATH.'/Loader.php';
	$loader = new Loader();
	
	/**
	 * Run framework system
	 */
	try {
		$core = new Core();
		
		$core->initialize();
		$core->run();
	} catch (\Exception $exception) {
		new Error($exception);
	}
?>