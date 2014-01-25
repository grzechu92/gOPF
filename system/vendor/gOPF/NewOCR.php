<?php 
	namespace gOPF;
	
	/**
	 * NewOCR API class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class NewOCR {
		/**
		 * URL to NewOCR
		 * @var string
		 */
		const URL = 'http://www.newocr.com';
		
		/**
		 * Translates image into text from image file
		 * 
		 * @param string $file Full path to image
		 * @return string Translated text
		 */
		public function translateImageFile($file) {
			$result = $this->sendRequest('', array(
				'userfile' => '@'.$file.';type='.mime_content_type($file),
				'l' => 'pol',
				'ocr' => 1
			), array(CURLOPT_HTTPHEADER => array('Content-type: multipart/form-data')));
			
			return $this->getTranslatedText($result);
		}
		/**
		 * Translates image into text from URL
		 *
		 * @param string $url Full path to image
		 * @return string Translated text
		 */
		public function translateImageURL($url) {
			$result = $this->sendRequest('', array(
				'l' => 'pol',
				'url' => $url,
				'ocr' => 1
			));
				
			return $this->getTranslatedText($result);
		}
		
		/**
		 * Sends request to NewOCR
		 *
		 * @param string $url Path to page (for example: /login)
		 * @param array $variables Variables to post (key => value)
		 * @param array $opts Custom CURLOPT's (CURLOPT_* => value)
		 * @return string Requested content
		 */
		public function sendRequest($url, $variables = array(), $opts = array()) {
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, self::URL.$url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
		
			if (!empty($variables)) {
				curl_setopt($c, CURLOPT_POST, 1);
				curl_setopt($c, CURLOPT_POSTFIELDS, $variables);
			}
		
			if (!empty($opts)) {
				foreach ($opts as $opt=>$value) {
					curl_setopt($c, $opt, $value);
				}
			}
		
			return curl_exec($c);
		}
		
		/**
		 * Returns translated text from page source code
		 * 
		 * @param string $content Page source code
		 * @return string Translated text
		 */
		private function getTranslatedText($content) {
			if (preg_match_all('#\<div class="well ocr-result"\>(.*?)\<\/div\>#s', $content, $matches)) {
				return $matches[1][0];
			}
		}
	}
?>