<?php
	namespace System;
	use \System\Config\Exception;
	use \System\Config\File;
	
	/**
	 * Config files reader and editor
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Config extends Container {
		/**
		 * Requested config file is in system config directory
		 * @var int
		 */
		const SYSTEM = 1;
		
		/**
		 * Requested config file is in application config directory
		 * @var int
		 */
		const APPLICATION = 2;
		
		/**
		 * Requested config file has custom path
		 * @var int
		 */
		const CUSTOM = 3;
		
		/**
		 * APC configuration file caching, hidden feature
		 * @var bool
		 */
		const APC = __TURBO_MODE;
		
		/**
		 * APC configuration file caching lifetime, in seconds
		 * @var int
		 */
		const APC_LIFETIME = 60;
		
		/**
		 * APC configuration file caching prefix
		 * @var string
		 */
		const APC_PREFIX = 'gOPF-CONFIG-';
		
		/**
		 * Creates and initiates config file object
		 * 
		 * @param string $filename Name of configuration file
		 * @param int $location Location of configuration file (Config::SYSYTEM, Config::APPLICATION, Config:CUSTOM)
		 * @param bool $editable Allow to edit configuration file
		 * @return \System\Config\File Object with content of configuration file
		 */
		public static function factory($filename, $location = self::SYSTEM, $editable = false) {
			$path = self::generatePath($filename, $location);
			
			if (self::APC && !$editable) {
				$id = self::APC_PREFIX.sha1($path);	
				
				if ($cached = apc_fetch($id)) {
					$content = $cached;
				} else {
					self::checkFile($filename, $path);
					$content = self::loadFile($path);
					
					apc_store($id, $content, self::APC_LIFETIME);
				}
			} else {
				self::checkFile($filename, $path);
				$content = self::loadFile($path);
			}
			
			return new File($content, $editable, $path);
		}
		
		/**
		 * Merges new configuration file into older file
		 * 
		 * @param \System\Config\File $new New file which you want to merge into older
		 * @param \System\Config\File $old Older file
		 **/
		public static function merge(File $new, File $old, $removes = false) {
			$old->merge($new, $removes);
		}
		
		/**
		 * Checks configuration file, when not found throws and exception
		 * 
		 * @param $filename Name of configuration file
		 * @param $path Path to configuration file
		 * @throws \System\Config\Exception
		 */
		private static function checkFile($filename, $path) {
			if (!Filesystem::checkFile($path)) {
				throw new Exception(\System\I18n::translate('CONFIG_NOT_FOUND', array($filename)), $filename);
			}
		}
		
		/**
		 * Loads configration file content
		 * 
		 * @param string $path Path to configuration file
		 * @return array Configuration file content
		 */
		private static function loadFile($path) {
			$content = array();
			
			foreach (parse_ini_file($path, true) as $key=>$value) {
				$content[$key] = $value;
			}
			
			return $content;
		}
		
		/**
		 * Generates path to configuration file
		 * 
		 * @param string $filename Name of configuration file
		 * @param int $location Configuration file location
		 * @return string Configuration file path
		 */
		private static function generatePath($filename, $location) {
			switch ($location) {
				case self::SYSTEM:
					$path = __SYSTEM_PATH;
					break;
			
				case self::APPLICATION:
					$path = __APPLICATION_PATH;
					break;
			
				case self::CUSTOM:
					$path = $file;
					break;
			}
			
			if ($location != self::CUSTOM) {
				$path .= DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$filename;
			}
			
			return $path;
		}
	}
?>