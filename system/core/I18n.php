<?php
	namespace System;
	
	/**
	 * Internationalization module of framework
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class I18n {
		/**
		 * APC internationalized file caching, hidden feature
		 * @var bool
		 */
		const APC = __TURBO_MODE;
		
		/**
		 * APC internationalized file caching lifetime, in seconds
		 * @var int
		 */
		const APC_LIFETIME = 600;
		
		/**
		 * APC internationalized file caching prefix
		 * @var string
		 */
		const APC_PREFIX = 'gOPF-I18N-';
		
		/**
		 * I18n object instance
		 * @var \System\I18n
		 */
		private static $instance;
		
		/**
		 * Array of availiable language strings
		 * @var array
		 */
		public $strings = array();
		
		/**
		 * Config of internationalization module
		 * @var \System\Config
		 */
		private $config;
		
		/**
		 * Languages accepted by user
		 * @var array
		 */
		private $accepted = array();
		
		/**
		 * Languages selected by script, depends on user preferences and language file availability
		 * @var array
		 */
		private $selected = array();
		
		/**
		 * Constructor of internationalization module
		 */
		public function __construct() {
			self::$instance = $this;
			
			$this->config = Config::factory('i18n.ini', Config::SYSTEM);
			$this->accept = $this->getAcceptedLanguages();
		}
		
		/**
		 * Static wrapper for I18n::get() method
		 *
		 * @param string $index ID of internationalized string
		 * @param array $vars Variables in internationalized string
		 * @return string Internationalized string
		 */
		public static function translate($index, $vars = array()) {
			return self::$instance->get($index, $vars);
		}
		
		/**
		 * Search and complete selected language string, with variables
		 * 
		 * @param string $index ID of internationalized string
		 * @param array $vars Variables in internationalized string
		 * @param bool $bold Bold variables in string using <strong /> tag
		 * @return string Internationalized string
		 */
		public function get($index, $vars = array(), $bold = true) {
			if (count($this->strings) === 0) {
				$this->loadStrings();
			}
			
			$string = isset($this->strings[$index]) ? $this->strings[$index] : '???';
			
			if (count($vars) > 0) {
				foreach ($vars as $id=>$var) {
					$translated = $bold ? '<strong>'.$var.'</strong>' : $var;
					$string = str_replace('$'.($id+1), $translated, $string);
				}
			}
			
			return $string;
		}
		
		/**
		 * Getting languages accepted by user
		 * 
		 * @return array Languages accepted by user
		 */
		private function getAcceptedLanguages() {
			preg_match_all('/[a-z]{2}/i', ((isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) : ''), $langs);
			
			return array_unique($langs[0]);
		}
		
		/**
		 * Load strings depends on user defined language
		 */
		private function loadStrings() {
			$this->getSystemLanguage();
			
			$session = Session::get('__I18N');
			
			if (empty($session['system']) && empty($session['application'])) {
				$this->getAvailableLanguages();
			} else {
				$this->getSelectedLanguage();
			}
						
			$this->strings = $this->getStrings();
		}
		
		/**
		 * Load strings from files selected by script
		 * 
		 * @return array Internationalized strings
		 */
		private function getStrings() {
			$strings = array();
			$paths = array();
			
			foreach ($this->selected as $type=>$language) {
				$paths[$type] = (($type = 'system') ? __SYSTEM_PATH : __APPLICATION_PATH).'/i18n/'.$language.'.json';
			}
	
			foreach ($paths as $type=>$path) {
				$id = sha1(self::APC_PREFIX.$path);
				
				if (self::APC) {
					if ($cached = apc_fetch($id)) {
						$strings[$type] = $cached;
					}
				}
				
				if (!isset($strings[$type]) && Filesystem::checkFile($path)) {
					$strings[$type] = (array) json_decode(Filesystem::read($path, true));
					
					if (self::APC) {
						apc_store($id, $strings[$type], self::APC_LIFETIME);
					}
				}
			}
			
			return array_merge((isset($strings['application']) ? $strings['application'] : array()), $strings['system']);
		}
		
		/**
		 * When user has defined own language, use it to internationalize
		 */
		private function getSelectedLanguage() {
			$session = Session::get('__I18N');
			
			if (isset($session['system'])) {
				$this->selected['system'] = $session['system'];
			}
			
			$this->selected['application'] = $session['application'];
		}
		
		/**
		 * When user hasn't defined own language, script will select best language depends on web browser headers
		 */
		private function getAvailableLanguages() {
			$selected = $this->config->application;
			
			if ($this->config->dynamic) {
				foreach ($this->accept as $language) {
					if (Filesystem::checkFile(__APPLICATION_PATH.'/i18n/'.$language.'.json')) {
						$selected = $language;
						
						break;
					}
				}
			}
						
			$this->selected['application'] = $selected;
		}
		
		/**
		 * Sets system language, depends on configuration
		 */
		private function getSystemLanguage() {
			$this->selected['system'] = $this->config->system;
		}
	}
?>