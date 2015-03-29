<?php
	namespace System;
	use \System\I18n\Selected;
	
	/**
	 * Internationalization module of framework
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 *
	 */
	class I18n extends Singleton {
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
		 * Session I18n identifier
		 * @var string
		 */
		const SESSION = '__I18N';
		
		/**
		 * Array of available language strings
		 * @var array
		 */
		public $strings = array();
		
		/**
		 * Config of internationalization module
		 * @var \System\Config
		 */
		private $config;
		
		/**
		 * Languages selected by script, depends on user preferences and language file availability
		 * @var \System\I18n\Selected
		 */
		private $selected;
		
		/**
		 * Constructor of internationalization module
		 */
		protected function __construct() {
			$this->config = Config::factory('i18n.ini', Config::APPLICATION);
			$this->selected = Session::get(self::SESSION);

			if (!($this->selected instanceof Selected)) {
				$this->selected = new Selected($this->config->system, $this->config->application);

				if ($this->config->preference) {
					$this->setPreferredLanguage();
				}
			}
		}

		/**
		 * Save selected language to session
		 */
		public function __destruct() {
			Session::set(self::SESSION, $this->selected);
		}
		
		/**
		 * Static wrapper for I18n::get() method
		 *
		 * @param string $index ID of internationalized string
		 * @param array $vars Variables in internationalized string
		 * @return string Internationalized string
		 */
		public static function translate($index, $vars = array()) {
			return self::instance()->get($index, $vars);
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
				$this->load();
			}
			
			$string = isset($this->strings[$index]) ? $this->strings[$index] : '??? '.$index.' ???';
			
			if (count($vars) > 0) {
				foreach ($vars as $id=>$var) {
					$translated = $bold ? '<strong>'.$var.'</strong>' : $var;
					$string = str_replace('$'.($id+1), $translated, $string);
				}
			}
			
			return $string;
		}

		/**
		 * Set selected by user language for application
		 *
		 * @param string $language Selected application language
		 */
		public function set($language) {
			if ($this->selected->application != $language) {
				$this->selected->application = $language;
				$this->load();
			}
		}

		/**
		 * Return selected language object
		 *
		 * @return \System\I18n\Selected
		 */
		public function selected() {
			return $this->selected;
		}
		
		/**
		 * Load selected language strings
		 */
		private function load() {
			$this->strings = array();

			foreach ($this->selected as $type => $language) {
				$path = (($type == 'system') ? __SYSTEM_PATH : __APPLICATION_PATH).'/i18n/'.$language.'.json';
				$id = sha1(self::APC_PREFIX.__ID.$path);

				if (self::APC) {
					if ($cached = apc_fetch($id)) {
						$this->strings = array_merge($this->strings, $cached);
						continue;
					}
				}

				$strings = (array) json_decode(Filesystem::read($path, true));

				if (self::APC) {
					apc_store($id, $strings, self::APC_LIFETIME);
				}

				$this->strings = array_merge($this->strings, $strings);
			}
		}

		/**
		 * Select available and most preferred application language for user
		 */
		private function setPreferredLanguage() {
			$preferred = $this->getPreferredLanguages();

			if (count($preferred) > 0) {
				foreach ($preferred as $language) {
					if ($this->isLanguageAvailable($language)) {
						$this->set($language);
						break;
					}
				}
			}
		}

		/**
		 * Getting languages preferred by user
		 *
		 * @return array Languages preferred by user
		 */
		private function getPreferredLanguages() {
			preg_match_all('/[a-z]{2}/i', ((isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) : ''), $preferred);

			return array_unique($preferred[0]);
		}

		/**
		 * Is language available?
		 *
		 * @param string $language Language code
		 * @return bool Is language available?
		 */
		private function isLanguageAvailable($language) {
			return Filesystem::checkFile(__APPLICATION_PATH.'/i18n/'.$language.'.json');
		}
	}
?>