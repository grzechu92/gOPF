<?php
	namespace gOPF;
	use gOPF\Validate\Exception;

	/**
	 * Validate class
	 * 
	 * Parameters to check:
	 * 
	 * length: [integer] <- string must have specified length
	 * max-length: [integer] <- string length must be below (or equal) that value
	 * min-length: [integer] <- string length must be over (or equal) that value 
	 * content: alpha-numeric-dash-space-null-uppercase-lowercase <- string must contain specific content
	 * e-mail <- given string must be correct e-mail addess
	 * not-empty <- given string can not be empty
	 * ip <- given data must be valid IP address
	 * url <- gidvn data must be valid URL
	 * time <-  given time must be valid
	 * data <- given data must be valid
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Validate {
		/**
		 * Parameter names for class method wrapper
		 * @var array
		 */
		static private $wrapper = array(
			'min-length' => 'minLength',
			'max-length' => 'maxLength',
			'length' => 'length',
			'content' => 'content',
			'e-mail' => 'eMail',
			'not-empty' => 'notEmpty',
			'ip' => 'IP',
			'url' => 'URL',
			'time' => 'time',
			'date' => 'date'
		); 
		
		/**
		 * Check that if given string passes validate rules
		 * 
		 * @param string $string String to validate
		 * @param string $parameters Parameters to check, CSS property style
		 * @param string $name Variable name, used in throwing exceptions
		 * @throws \gOPF\Valiate\Exception
		 */
		public static function check($string, $parameters, $name = false) {			
			$parameters = array_filter(explode(';', str_replace(' ', '', $parameters)));
			
			foreach ($parameters as $parameter) {
				if (strpos($parameter, ':')) {
					list($property, $value) = explode(':', str_replace(' ', '', $parameter));
				} else {
					$property = $parameter;
					$value = null; 
				}
				
				$method = self::$wrapper[$property];
				
				if (!empty($method) && !self::$method($string, $value)) {
					throw new Exception($property, $name);
				}
			}
		}
		
		/**
		 * Checks if given string length is bigger than given length
		 *  
		 * @param string $string String to check
		 * @param int $length Minimal length (or equal)
		 * @return bool String length is more (or equal) than minimal length 
		 */
		private static function minLength($string, $length) {
			return (strlen($string) >= $length);
		}
		
		/**
		 * Checks if given string length is smaller than given length
		 * 
		 * @param string $string String to check
		 * @param int $length Maximal length (or equal)
		 * @return bool String length is lower (or equal) than maximal length
		 */
		private static function maxLength($string, $length) {
			return (strlen($string) <= $length);
		}
		
		/**
		 * Checks if given string length is equal to given length
		 * 
		 * @param string $string String to check
		 * @param int $length Required length
		 * @return bool String length is equal to required length
		 */
		private static function length($string, $length) {			
			return (strlen($string) == $length);
		}
		
		/**
		 * Checks if given string contains allowed values
		 * 
		 * @param string $string String to check
		 * @param string $parameters Allowed values
		 * @return bool String contains allowed values
		 */
		private static function content($string, $parameters) {
			$patterns = array(
				'alpha' => 'a-zA-Z',
				'numeric' => '0-9',
				'dash' => '-_',
				'null' => null,
				'space' => ' ',
				'lowercase' => 'a-z',
				'uppercase' => 'A-Z'
			);
			
			if (strpos($parameters, 'null')) {
				if (empty($string)) {
					return true;
				}
			}
			
			$pattern = '';
			
			foreach (explode('-', $parameters) as $parameter) {
				$pattern .= $patterns[$parameter];
			}
			
			return preg_match('/^([-'.$pattern.'])+$/', $string);
		}
		
		/**
		 * Checks if given string is valid e-mail address
		 * 
		 * @param string $string String to check
		 * @return bool String is valid e-mail address
		 */
		private static function eMail($string) {
			if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
				$explode = explode('@', $string);
				
				return checkdnsrr($explode[1]); 
			} else {
				return false;
			}
		}
		
		/**
		 * Checks if given string is not empty
		 * 
		 * @param string $string String to check
		 * @return bool String is not empty
		 */
		private static function notEmpty($string) {
			return !empty($string);
		}
		
		/**
		 * Checks if given string is valid time
		 * 
		 * @param string $string String to check
		 * @return bool String is valid
		 */
		private static function time($string) {
			return (bool) strtotime($string);
		}
		
		/**
		 * Checks if given string is valid date (format: DD.MM.YYYY or DD/MM/YYYY)
		 * 
		 * @param string $string String to check
		 * @return bool String is valid
		 */
		private static function date($string) {
			if (!preg_match("/^[0-3][0-9](.|\/)[0-3][0-9](.|\/)(?:[0-9][0-9])?[0-9][0-9]$/", $string)) {
				return false;
			}
			
			list($day, $month, $year) = explode('.', $string);
			
			return (bool) checkdate($month, $day, $year);
		}
		
		/**
		 * Checks if given string is valid IP address
		 * 
		 * @param string $string String to check
		 * @return bool String is valid
		 */
		private static function IP($string) {
			return (bool) filter_var($string, FILTER_VALIDATE_IP);
		}
		
		/**
		 * Checks if given string is valid URL address
		 * 
		 * @param string $string String to check
		 * @return bool String is valid
		 */
		private static function URL($string) {
			return (bool) filter_var($string, FILTER_VALIDATE_URL);
		}
	}
?>