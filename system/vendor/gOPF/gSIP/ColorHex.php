<?php 
	namespace gOPF\gSIP;
	
	/**
	 * gSIP Hex Color class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class ColorHex extends Color {
		/**
		 * Converts hex color format into RGBA values
		 * Accepted formats:
		 *  - #RGB
		 *  - #RGBA
		 *  - #RRGGBB
		 *  - #RRGGBBAA
		 * 
		 * @param string $colorHex Color hex string
		 * @throws Exception
		 */
		public function __construct($colorHex) {
			$colorHex = str_replace('#', '', $colorHex);

			switch (strlen($colorHex)) {
				case 3:
					$this->parseShortHex($colorHex);
					break;
					
				case 4:
					$this->parseShortHexAlpha($colorHex);
					break;
					
				case 6:
					$this->parseHex($colorHex);
					break;
					
				case 8:
					$this->parseHexAlpha($colorHex);
					break;
					
				default:
					throw new Exception('Unknown hex color format: '.$colorHex);
					break;
			}
		}
		
		/**
		 * Parses short hex format: #RGB
		 * 
		 * @param string $hex Color hex string
		 */
		private function parseShortHex($hex) {
			$this->parseShortHexAlpha($hex.'0');
		}
		
		/**
		 * Parses short hex format with alpha: #RGBA
		 * 
		 * @param string $hex Color hex string
		 */
		private function parseShortHexAlpha($hex) {
			$this->convertHexColor($hex, false);
		}
		
		/**
		 * Parses normal hex format: #RRGGBB
		 * 
		 * @param string $hex Color hex string
		 */
		private function parseHex($hex) {
			$this->parseHexAlpha($hex.'00');
		}
		
		/**
		 * Parses normal hex format with alpha: #RRGGBBAA
		 * 
		 * @param string $hex Color hex string
		 */
		private function parseHexAlpha($hex) {
			$this->convertHexColor($hex, true);
		}
		
		/**
		 * Converts bases in hex string
		 * 
		 * @param string $hex Color hex string
		 * @param bool $double Is double value format? 
		 */
		private function convertHexColor($hex, $double) {
			$fields = array('red', 'green', 'blue', 'alpha');
			$field = 0;
			
			for ($pos = 0, $length = (strlen($hex)-1); $pos<=$length; $pos++) {
				if ($double) {
					$value = hexdec($hex[$pos].$hex[++$pos]);
				} else {
					$value = hexdec($hex[$pos])*17;
				}
				
				$this->{$fields[$field++]} = $value;
			}
			
			$this->alpha = round($this->alpha/2)-1;
		}
	}
?>