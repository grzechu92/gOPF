<?php
	namespace System;
	use System\Config\Exception;
	
	/**
	 * Framework config file parser
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
		 * Allow to edit configuration file
		 * @var bool
		 */
		private $edit;
		
		/**
		 * Name of configuration file
		 * @var string
		 */
		private $file;
		
		/**
		 * Path to configuration file generated by constructor
		 * @var string
		 */
		private $path;
		
		/**
		 * Lines in configuration file (holds comments, ect.)
		 * @var array
		 */
		private $lines = array();
		
		/**
		 * Loads required config file
		 * 
		 * @param string $file Name of configuration file
		 * @param int $location Location of configuration file (Config::SYSYTEM or Config::APPLICATION)
		 * @param bool $edit Allow to edit
		 */
		public function __construct($file, $location = self::SYSTEM, $edit = false) {
			$this->edit = $edit;
			$this->file = $file;
			
			switch ($location) {
				case self::SYSTEM:
					$this->path = __SYSTEM_PATH;
					break;
					
				case self::APPLICATION:
					$this->path = __APPLICATION_PATH;
					break;
			}
			
			$this->path .= DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$file;

			if (self::APC && !$edit) {
				$id = self::APC_PREFIX.sha1($this->path);
				
				if ($cached = apc_fetch($id)) {
					$this->container = $cached;
				} else {
					$this->checkFile();
					$this->load();
						
					apc_store($id, $this->container, self::APC_LIFETIME);
				}
			} else {
				$this->checkFile();
				$this->load();
			}
			
		}
		
		/**
		 * When edit mode is enabled, saves changes to into configuration file
		 */
		public function __destruct() {
			if ($this->edit) {
				$this->save();
			}
		}
		
		/**
		 * Returns variables from configuration file
		 * 
		 * @return array Array of variables with values
		 */
		public function getArray() {
			return $this->container;
		}
		
		/**
		 * Returns all lines with in confguration file
		 * 
		 * @return array Lines in file
		 */
		public function getLines() {
			return $this->lines;
		}
		
		/**
		 * Adds new line into configuration file
		 * 
		 * @param string $key Name of variable
		 * @param mixed $value Value of variable
		 */
		public function add($key, $value) {
			$this->lines[] = "\n$key = $value";
		}
		
		/**
		 * Edits line in configuration file
		 * 
		 * @param string $key Name of variable
		 * @param mixed $value Name of variable
		 */
		public function edit($key, $value) {
			foreach ($this->lines as $num=>$line) {
				if (strpos($line, $key) === 0) {
					$this->lines[$num] = "$key = $value\n";
				}
			}
		}
		
		/**
		 * Removes line from configuration file
		 * 
		 * @param string $key Name of variable
		 */
		public function remove($key) {
			foreach ($this->lines as $num=>$line) {
				if (strpos($line, $key) === 0) {
					unset($this->lines[$num]);
				}
			}
		}
		
		/**
		 * Checks configuration file, when not found throws and exception
		 * @throws \System\Config\Exception
		 */
		private function checkFile() {
			if (!Filesystem::checkFile($this->path)) {
				throw new Exception(I18n::translate('CONFIG_NOT_FOUND', array($this->file)), $this->file);
			}
		}
		
		/**
		 * Load reqired configuration file and get variables from it
		 */
		private function load() {
			foreach (parse_ini_file($this->path, true) as $key=>$value) {
				$this->container[$key] = $value;
			}
			
			if ($this->edit) {
				foreach (file($this->path) as $num=>$line) {
					$this->lines[$num] = $line;
				}
			}
		}
		
		/**
		 * Rebuilds configuration file and saves it into file
		 */
		private function save() {
			$content = '';
			
			foreach ($this->lines as $line) {
				$content .= $line;
			}
			
			Filesystem::write($this->path, $content);
		}
	}
?>