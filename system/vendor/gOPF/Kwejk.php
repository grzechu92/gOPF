<?php 
	namespace gOPF;
	use \gOPF\Kwejk\Exception;
	use \gOPF\Kwejk\Captcha;
	
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
		 * @param string $proxy Proxy IP adress and port separated by colon 
		 * @param bool $safe leep a few seconds after request
		 * @oaran bool $debug Debugging mode, prints out every request content
		 */
		public function __construct($proxy = '', $safe = false, $debug = false) {
			if (!empty($proxy)) {
				$this->proxy = $proxy;
			}
			
			self::$safe = $safe;
			self::$debug = $debug;
			
			$this->sendRequest('/');
			$this->getCookie($this->sendRequest('/login/', array()));
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
		 * Logs in user
		 * 
		 * @param string $username User username
		 * @param string $password User password
		 * @throws \gOPF\Kwejk\Exception
		 */
		public function login($username, $password) {
			$content = $this->sendRequest('/login', array(
				'user[username]' => $username,
				'user[password]' => $password,
				'authenticity_token' => $this->getAuthenticityToken($this->sendRequest('/login'))
			));
			
			$message = self::getMessage($content);
			
			if (!empty($message)) {
				throw new Exception($message);
			}
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
			curl_setopt($c, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($c, CURLOPT_USERAGENT, 'KwejkAPI BOT v1.8 by Grze_chu <mail@grze.ch>');
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
			$this->getCookie($content);
				
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
 				'media_object[image]' => '@'.$file.';type='.mime_content_type($file),
 				'media_object[object_type]' => 0,
 				'recaptcha_challenge_field' => $captcha->challengeID,
 				'recaptcha_response_field' => $captcha->solved
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
		 * $param string $content Page source code
		 * @return string Authencity token
		 */
		private function getAuthenticityToken($content) {
			if (preg_match_all('#\<input name="authenticity_token" (.*?) value="(.*?)"\ \/>#s', $content, $matches)) {
				return $matches[2][0];
			}
		}
		
		/**
		 * Returns captcha image URL from page
		 * 
		 * @param string $content Page content
		 * @return string Image URL
		 */
		private function getCaptchaURL($content) {
			if (preg_match_all('#\<img src=(\'|")(.*?)(\'|")(.*?)\>#s', $content, $matches)) {
				return self::URL.$matches[2][1];
			}
		}
		
		/**
		 * Gets cookie string from HTTP headers
		 *
		 * @param string $headers Page source code with HTTP headers
		 */
		private function getCookie($headers) {
			if (preg_match('/^Set-Cookie: (.*?);/m', $headers, $matches)) {
				$this->cookie = $matches[1];
			}
		}
	}
?>