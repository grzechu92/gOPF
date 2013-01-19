<?php
	/**
	 * Functions which are available in whole system and application
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */	

	/**
	 * Converts bytes to other units
	 * 
	 * @param int $size Amount of bytes
	 * @return string Converted bytes
	 */
	function convertBytes($size) {
		if ($size !== 0) {
			if ($size < 0) {
				$invert = true;
				$size = -$size;
			}
			
			$unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
			$return = round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
			
			return ($invert) ? '-'.$return : $return;
		} else {
			return '0 B';
		}
	}
	
	/**
	 * @see \System\I18n::translate()
	 */
	function __($index, $vars) {
		return \System\I18n::translate($index, $vars);
	}
?>