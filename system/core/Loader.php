<?php
	namespace System;
	use \System\Filesystem;
	
	/**
	 * Framework libraries loader, based on personalized PSR-0 implementation
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Loader {
		/**
		 * Holds predefined and reserved System namespace name
		 * @var string
		 */
		const SYSTEM_NAMESPACE = 'System';
		
		/**
		 * Holds predefined and reserved Controllers namespace name
		 * @var unknown_type
		 */
		const CONTROLLERS_NAMESPACE = 'Controllers';
		
		/**
		 * Holds predefined and reserved Models namespace name
		 * @var unknown_type
		 */
		const MODELS_NAMESPACE = 'Models';
		
		/**
		 * Holds predefined and reserved applications classes namespace name
		 * @var unknown_type
		 */
		const CLASSES_NAMESPACE = 'Application';
		
		/**
		 * Registers framework loader in PHP loaders registry
		 */
		public function __construct() {
			spl_autoload_register(array($this, 'loadClass'));
		}
		
		/**
		 * Loads required class in PSR-0 pattern
		 * 
		 * @param string $className Class name to load
		 */
		public function loadClass($className) {
			$className = ltrim($className, '\\');
			
			if ($separator = strripos($className, '\\')) {
				$namespace = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, 0, $separator));
      	 		$file = str_replace('_', DIRECTORY_SEPARATOR, substr($className, $separator + 1)).'.php';
			} else {
				$namespace = '';
				$file = '';
			}
			
			foreach (array(self::SYSTEM_NAMESPACE, self::CONTROLLERS_NAMESPACE, self::MODELS_NAMESPACE, self::CLASSES_NAMESPACE) as $reserved) {
				if (strpos($namespace, $reserved) === 0) {
					$namespace = substr($namespace, strlen($reserved));
					
					switch ($reserved) {
						case self::SYSTEM_NAMESPACE:
							$path = __CORE_PATH.$namespace.DIRECTORY_SEPARATOR.$file;
							break;
							
						case self::CONTROLLERS_NAMESPACE:
							$path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.'controllers'.$namespace.DIRECTORY_SEPARATOR.$file;
							break;
							
						case self::MODELS_NAMESPACE:
							$path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.'models'.$namespace.DIRECTORY_SEPARATOR.$file;
							break;
							
						case self::CLASSES_NAMESPACE:
							$path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.'classes'.$namespace.DIRECTORY_SEPARATOR.$file;
							break;
					}
				}
			}
			
			if (empty($path)) {
				$path = __VENDOR_PATH.DIRECTORY_SEPARATOR.$namespace.DIRECTORY_SEPARATOR.$file;
			}
		
			$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
			
			if (__STAGE == __PRODUCTION || Filesystem::checkFile($path)) {
				require $path;
			} else {
				throw new \System\Loader\Exception(\System\I18n::translate('LOADER_UNABLE', array($path)));
			}
		}
	}
?>