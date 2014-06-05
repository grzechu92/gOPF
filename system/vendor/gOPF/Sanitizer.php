<?php 
	namespace gOPF;
	
	/**
	 * Sanitizer class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Sanitizer {
		/**
		 * Removes unexpected chars from text, which doesn't fit to URL
		 * 
		 * @param string $url Text to clean
		 * @param int|bool $length Crop to specified length
		 * @return string Sanitized text
		 */
		public static function URL($url, $length = false) {
			$url = trim($url);
			$from = array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż',' ',"\n", '-', '"','\'');
			$to =	array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z','_','_', '__', '', '');
			$url = str_replace($from, $to, $url);
			$url = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $url);
			$url = preg_replace('/_+/', '_', $url);
		
			if ($length && strlen($url) > $length) {
				$url = substr($url, 0, $length);
			}
		
			return filter_var(strtolower($url), FILTER_SANITIZE_URL);
		}
		
		/**
		 * Returns YouTube video ID from any link
		 * 
		 * @param string $url YouTube video link
		 * @return string YouTube video id, false when link is not valid
		 */
		public static function YouTube($url) {
			if (empty($url)) {
				return false;
			}
			
			preg_match('#(youtu\.be/|youtube\.com/embed/|youtube\.com/v/|youtube\.com/watch\?.*v=)([A-Za-z0-9_-]{5,11})#', $url, $matches);
			
			if (isset($matches[2]) && !empty($matches[2])) {
				return $matches[2];
			} else {
				return false;
			}
		}
	}
?>