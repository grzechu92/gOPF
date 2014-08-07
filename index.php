<?php
	/**
	 * Main configuration of framework, allows to set main config of framework core
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */

	/**
	 * Production stage 
	 * @var int
	 */
	define('__PRODUCTION', 1);
	
	/**
	 * Development stage
	 * @var int
	 */
	define('__DEVELOPMENT', 2);
	
	/**
	 * Selected stage (__PRODUCTION or __DEVELOPMENT)
	 * @var int
	 */
	define('__STAGE', __DEVELOPMENT);

	/**
	 * Set error reporting level
	 */
	error_reporting(__STAGE == __PRODUCTION ? 0 : E_ALL);
	
	/**
	 * Set default timezone
	 */
	date_default_timezone_set('Europe/Warsaw');
	
	/**
	 * Set default encoding
	 */
	mb_internal_encoding('UTF-8');
	
	/**
	 * Filesystem path to root directory
	 * @var string
	 */
	define('__ROOT_PATH', dirname(__FILE__));

    /**
     * Unique application ID generated from __ROOT_PATH
     * @var string
     */
    define('__ID', sha1(__ROOT_PATH));
	
	/**
	 * Filesystem path to application directory
	 * @var string
	 */
	define('__APPLICATION_PATH', __ROOT_PATH.DIRECTORY_SEPARATOR.'application');
	
	/**
	 * Filesystem path to system directory
	 * @var string
	 */
	define('__SYSTEM_PATH', __ROOT_PATH.DIRECTORY_SEPARATOR.'system');
	
	/**
	 * Filesystem path to core directory
	 * @var string
	 */
	define('__CORE_PATH', __SYSTEM_PATH.DIRECTORY_SEPARATOR.'core');
	
	/**
	 * Filesystem path to vendor directory
	 * @var string
	 */
	define('__VENDOR_PATH', __SYSTEM_PATH.DIRECTORY_SEPARATOR.'vendor');
	
	/**
	 * Filesystem path to variable directory
	 * @var string
	 */
	define('__VARIABLE_PATH', __SYSTEM_PATH.DIRECTORY_SEPARATOR.'var');
	
	/**
	 * HTTP path to page with framework instance
	 * @var string
	 */
	define('__WWW_PATH', isset($_SERVER['HTTP_HOST']) ? ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST']) : '');
	
	/**
	 * Time when generating page has been started, in timestamp miliseconds
	 * @var float
	 */
	define('__START_TIME', microtime(true));
	
	/**
	 * Enable hidden features to speed up framework core
	 * @var bool
	 */
	define('__TURBO_MODE', false);
	
	require __APPLICATION_PATH.'/init.php';
	require __SYSTEM_PATH.'/init.php';
?>