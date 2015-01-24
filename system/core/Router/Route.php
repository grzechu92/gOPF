<?php
	namespace System\Router;
	use \System\Request;
	
	/**
	 * Router route object and parser
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Route {
		/**
		 * Rule tag pattern
		 * @var string
		 */
		const RULE_PATTERN = '/\<(\w+)\:(numeric|alpha|alphanumeric|any|language)\>/';
		
		/**
		 * Raw URL rule
		 * @var string
		 */
		public $rule;
		
		/**
		 * Raw values string
		 * @var string
		 */
		public $raw;
		
		/**
		 * Parsed values
		 * @var \stdClass
		 */
		public $values;
		
		/**
		 * Parsed URL rule
		 * @var string
		 */
		private $pattern;

		/**
		 * Create route object, parse URL pattern
		 * 
		 * @param string $rule URL rule string
		 * @param string $values Rule values string
		 */
		public function __construct($rule, $values) {
			$this->pattern = preg_replace(
				array('/@alphanumeric/', '/@numeric/', '/@alpha/', '/@any/', '/@language/'),
				array('(\w+)', '(\d+)', '(\D+)', '(.+)', '(\w{2})'),
				preg_replace(self::RULE_PATTERN, '@$2', str_replace('/', '\\/', $rule))
			).'(|\/(.*))';

			$this->rule = $rule;
			$this->raw = $values;
		}
		
		/**
		 * Checks if rule matches to URL
		 * 
		 * @return bool
		 */
		public function match() {
			return preg_match('/^'.$this->pattern.'$/', Request::$URL);
		}
		
		/**
		 * Parses URL and value string
		 */
		public function parse() {
			$this->values = new \stdClass();
			
			$this->parseValues($this->raw);
			$this->parseRoute($this->rule);
		}

		/**
		 * Translate path
		 *
		 * @param array $i18n URL translate map
		 * @throws \System\Router\Exception
		 */
		public function translate($i18n) {
			$this->values->i18n = urldecode($this->values->i18n);

			if (!isset($i18n[$this->values->i18n])) {
				throw new Exception(\System\I18n::translate('ROUTE_I18N_NOT_FOUND', array($this->values->i18n)));
			}

			$translated = explode(':', $i18n[$this->values->i18n]);
			$this->values->controller = $translated[0];
			$this->values->action = $translated[1];
		}
		
		/**
		 * Parses URL with parsed pattern
		 * 
		 * @param string $rule Raw rule to parse
		 */
		private function parseRoute($rule) {
			if (!empty($rule)) {
				$names = array();
				preg_match_all(self::RULE_PATTERN, $rule, $names);
				
				$values = array();
				preg_match_all('#'.preg_replace(self::RULE_PATTERN, '(?P<$1>[^\/]+)', $rule).'#', Request::$URL, $values, PREG_SET_ORDER);
				
				foreach ($names[1] as $name) {
					$this->values->{$name} = trim($values[0][$name]);
				}
			}
		}
		
		/**
		 * Parses value string
		 *
		 * @param string $raw Value string
		 */
		private function parseValues($raw) {
			foreach (explode(',', $raw) as $string) {
				$this->parseValueString($string);
			}
		}

		/**
		 * Parse value string (key:value)
		 *
		 * @param string $string Value string
		 */
		private function parseValueString($string) {
			$separated = explode(':', $string);
			$value = trim($separated[1]);

			$this->values->{trim($separated[0])} = (strpos($value, ' ') > 0) ? explode(' ', $value) : $value;
		}
	}
?>