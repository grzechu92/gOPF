<?php
	namespace System\Terminal;
	
	/**
	 * Terminal output parser
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Parser {
		/**
		 * available styles and tabs
		 * @var array
		 */
		private $tags = array(
			'red' => 'color: red !important',
			'green' => 'color: green !important',
			'bold' => 'font-weight: bold'
		);
		
		/**
		 * Parses terminal output
		 * 
		 * @param string $output Output to parse
		 * @return string Parsed output
		 */
		public function parse($output) {
			foreach ($this->tags as $tag=>$style) {
				$output = str_replace('</'.$tag.'>', '</span>', $output);
				$output = str_replace('<'.$tag.'>', '<span style="'.$style.'">', $output);
			}
			
			return $output;
		}
	}
?>