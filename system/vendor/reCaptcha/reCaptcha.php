<?php
	namespace reCaptcha;
	use \System\Request;
	use \reCaptcha\reCaptcha\Exception;
	
	/**
	 * reCAPTCHA gOPF API class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class reCaptcha {
		/**
		 * Private application key
		 * @var string
		 */
		public static $privateKey = '';
		
		/**
		 * Public application key
		 * @var string
		 */
		public static $publicKey = '';
		
		/**
		 * reCAPTCHA API server URL
		 * @var string
		 */
		const RECAPTCHA_API_SERVER = 'https://www.google.com/recaptcha/api';
		
		/**
		 * reCAPTCHA verification server URL
		 * @var string
		 */
		const RECAPTCHA_VERIFY_SERVER = 'www.google.com';
		
		/**
		 * reCaptcha challenge generate path
		 * @var string
		 */
		const CHALLENGE_PATH = '/challenge?k=';
		
		/**
		 * reCaptcha captcha image generate path
		 * @var string
		 */
		const IMAGE_PATH = '/image?c=';
		
		/**
		 * Initiates reCAPTCHA object
		 * 
		 * @param string $public Public application key
		 * @param string $private Private application key
		 */
		public function __construct($public, $private) {
			self::$privateKey = $private;
			self::$publicKey = $public;
		}
		
		/**
		 * Generates path to captcha image
		 * 
		 * @param string $challenge Captcha challenge
		 * @return string Path to captcha image
		 */
		public static function getCaptchaImage($challenge) {
			return self::RECAPTCHA_API_SERVER.self::IMAGE_PATH.$challenge;
		}
		
		/**
		 * Generate captcha challenge for selected key
		 * 
		 * @param string|false $key Public key
		 * @return string Captcha challenge
		 */
		public static function getCaptchaChallenge($key = false) {
			if (!$key) {
				$key = self::$publicKey;
			}
			
			$content = file_get_contents(self::RECAPTCHA_API_SERVER.self::CHALLENGE_PATH.$key);
				
			if (preg_match_all('#challenge : \'(.*?)\'#s', $content, $matches)) {
				return $matches[1][0];
			} else {
				throw new Exception('Unable to get challenge key');
			}
		}
		
		/**
		 * Returns HTML code with captcha form
		 * 
		 * @return string HTML Code
		 */
		public function getCaptcha() {
			return '
				<script type="text/javascript" src="'.self::RECAPTCHA_API_SERVER.'/challenge?k='.self::$publicKey.'"></script>
				
				<noscript>
					<iframe src="'.self::RECAPTCHA_API_SERVER.'/noscript?k='.self::$publicKey.'" height="300" width="500" frameborder="0"></iframe><br/>
					<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
					<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
				</noscript>
			';
		}
		
		/**
		 * Checks if captcha response entered by user is valid
		 * 
		 * @return bool Is valid?
		 */
		public function isValid() {
			$challenge = Request::$post['recaptcha_challenge_field'];
			$response = Request::$post['recaptcha_response_field'];
			
			if (empty($challenge) || empty($response)) {
				return false;
			}
			
			$response = $this->request(self::RECAPTCHA_VERIFY_SERVER, '/recaptcha/api/verify', array(
				'privatekey' => self::$privateKey,
				'remoteip' => $_SERVER['REMOTE_ADDR'],
				'challenge' => $challenge,
				'response' => $response
			));
			
			$answers = explode ("\n", $response[1]);
			
			return ($answers[0] == 'true') ? true : false;
		}
		
		/**
		 * Encodes the given data into a query string format
		 * 
		 * @param array $data Array of string elements to be encoded
 		 * @return string Encoded request
		 */
		private function encode($data) {
			$req = '';
			
			foreach ($data as $key => $value) {
				$req .= $key.'='.urlencode(stripslashes($value)).'&';
			}
			
			$req = substr($req, 0, strlen($req)-1);
			
			return $req;
		}
		
		/**
		 * Submits an HTTP POST to a reCAPTCHA server
		 * 
		 * @param string $host Request host address
		 * @param string $path Path to script
		 * @param string $data Request data
		 * @param int $port Request port
		 * @return array Response data
		 */
		private function request($host, $path, $data, $port = 80) {
			$req = $this->encode($data);
			
			$http  = "POST $path HTTP/1.0\r\n";
			$http .= "Host: $host\r\n";
			$http .= "Content-Type: application/x-www-form-urlencoded;\r\n";
			$http .= "Content-Length: ".strlen($req)."\r\n";
			$http .= "User-Agent: reCAPTCHA/PHP\r\n";
			$http .= "\r\n";
			$http .= $req;
			
			$response = '';
			
			$socket = @fsockopen($host, $port, $errno, $errstr, 10);
			fwrite($socket, $http);
			
			while (!feof($socket)) {
				$response .= fgets($socket, 1160);
			}
			
			fclose($socket);
			
			$response = explode("\r\n\r\n", $response, 2);
			
			return $response;
		}
	}
?>
