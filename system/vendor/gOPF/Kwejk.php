<?php 
	namespace gOPF;
	use \gOPF\Kwejk\Exception;
	use \gOPF\Kwejk\Captcha;
	use \reCaptcha\reCaptcha;
	
	/**
	 * Kwejk API class
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Kwejk {
		/**
		 * Kwejk.pl URL
		 * @var string
		 */
		const URL = 'http://kwejk.pl';
		
		/**
		 * Kwejk reCaptcha public key
		 * @var string
		 */
		const CAPTCHA_KEY = '6LfE4tYSAAAAALDY5gzhh92ar4UVQW3YgVA9-iZ_';
		
		/**
		 * Sleep a few seconds after request
		 * @var bool
		 */
		public static $safe = false;
		
		/**
		 * Debugging mode, prints output of every request
		 * @var bool
		 */
		public static $debug = false;
		
		/**
		 * Session cookie string
		 * @var string
		 */
		private $cookie;
		
		/**
		 * Proxy IP adress and port separated by double colon
		 * @var string
		 */
		private $proxy;
		
		/**
		 * Initiates Kwejk object instance
		 * 
		 * @param string $cookie Defines cookie path
		 * @param string $proxy Proxy IP adress and port separated by colon 
		 * @param bool $safe Sleep a few seconds after request
		 * @oaran bool $debug Debugging mode, prints out every request content
		 */
		public function __construct($cookie = '', $proxy = '', $safe = true, $debug = false) {
			if (!empty($proxy)) {
				$this->proxy = $proxy;
			}
			
			self::$safe = $safe;
			self::$debug = $debug;
			$this->cookie = ($cookie == '') ? '/tmp/'.microtime(true) : $cookie;
			
			$this->sendRequest('/');
		}
		
		/**
		 * Returns message from Kwejk by searching in source code
		 *
		 * @param string $content Kwejk page source code
		 * @return string Message
		 */
		public static function getMessage($content) {
			if (preg_match_all('#\<li class="success"\>(.*?)\<\/li\>#s', $content, $matches)) {
				return trim($matches[1][0]);
			}
		}
		
		/**
		 * Returns new captcha challengr from Kwejk reCaptcha key
		 * 
		 * @return string reCaptcha challenge key
		 */
		public static function getCaptchaChallenge() {
			return reCaptcha::getCaptchaChallenge(self::CAPTCHA_KEY);
		}
		
		/**
		 * Logs in user
		 * 
		 * @param string $username User username
		 * @param string $password User password
		 * @throws \gOPF\Kwejk\Exception
		 */
		public function login($username, $password) {
			$content = $this->sendRequest('/login', array(
				'utf8' => '✓',
				'authenticity_token' => $this->getAuthenticityToken($this->sendRequest('/login')),
				'user[username]' => $username,
				'user[password]' => $password,
				'user[remember_me]' => '0',
				'commit' => 'Zaloguj się'
			));
			
			$message = $this->getMessage($content);
			
			if (!empty($message)) {
				throw new Exception($message);
			}
		}
		
		/**
		 * Logs user out
		 */
		public function logout() {
			$this->sendRequest('/logout');
		}
		
		/**
		 * Sends request to Kwejk
		 *
		 * @param string $url Path to page (for example: /login)
		 * @param array $variables Variables to post (key => value)
		 * @param array $opts Custom CURLOPT's (CURLOPT_* => value)
		 * @return string Requested content with HTTP Headers
		 */
		public function sendRequest($url, $variables = array(), $opts = array()) {
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, self::URL.$url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_COOKIEJAR, $this->cookie);
			curl_setopt($c, CURLOPT_COOKIEFILE, $this->cookie);
			curl_setopt($c, CURLOPT_USERAGENT, 'KwejkAPI BOT v2.0 by Grze_chu <mail@grze.ch>');
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($c, CURLOPT_HEADER, 1);
			
			if (!empty($this->proxy)) {
				list($ip, $proxy) = explode(':', $this->proxy); 
				
				curl_setopt($c, CURLOPT_PROXY, $ip);
				curl_setopt($c, CURLOPT_PROXYPORT, $proxy);
			}
		
			if (!empty($variables)) {
				curl_setopt($c, CURLOPT_POST, 1);
				curl_setopt($c, CURLOPT_POSTFIELDS, $variables);
			}
		
			if (!empty($opts)) {
				foreach ($opts as $opt=>$value) {
					curl_setopt($c, $opt, $value);
				}
			}
		
			$content = curl_exec($c);
			
			if (empty($content)) {
				throw new Exception('Connection error.');
			}
				
			if (self::$safe) {
				sleep(rand(5, 15));
			}
			
			if (self::$debug) {
				echo $content;
			}
			
			return $content;
		}
		
		/**
		 * Sends image to Kwejk.pl/obrazek
		 *
		 * @param \gOPF\Kwejk\Captcha $captcha Captcha to pass while sending image
		 * @param string $file Full path to image file
		 * @param string $title Image title
		 * @param string $source Image source
		 * @throws \gOPF\Kwejk\Exception
		 */
		public function sendImage(Captcha $captcha, $file, $title, $source = '') {
			$content = $this->sendRequest('/dodaj');
			
 			$result = $this->sendRequest('/obrazek', array(
 				'utf8' => '✓',
 				'authenticity_token' => $this->getAuthenticityToken($content),
 				'media_object[title]' => $title,
 				'media_object[source]' => $source,
 				'media_object[image]' => new \CurlFile($file, mime_content_type($file), basename($file)),
 				'media_object[object_type]' => 0,
 				'recaptcha_challenge_field' => $captcha->challengeID,
 				'recaptcha_response_field' => $captcha->solved,
 				'commit' => 'Wyślij'
 			), array(CURLOPT_HTTPHEADER => array('Content-type: multipart/form-data')));
 			
 			$message = self::getMessage($result);
 			
 			if (!empty($message)) {
 				throw new Exception($message);
 			}
 				
 			if (strpos($result, 'http://kwejk.pl/ban.html')) {
 				throw new Exception('Banned!');
 			}
		}
		
		/**
		 * Returns page authenticity token from source code
		 * 
		 * @param string $content Page source code
		 * @return string Authencity token
		 */
		private function getAuthenticityToken($content) {
			if (preg_match_all('#\<input name="authenticity_token" (.*?) value="(.*?)"\ \/>#s', $content, $matches)) {
				return $matches[2][0];
			}
		}
	}
?>