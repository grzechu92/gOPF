<?php
	namespace System\Config\Parser;
	
	/**
	 * Line class for config parser
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Line {
		/**
		 * Line name
		 * @var string
		 */
		public $name;
		
		/**
		 * Line value
		 * @var string
		 */
		public $value;
		
		/**
		 * Is common? (common means, it has name and value, for example not comments line or empty lines)
		 * @var bool
		 */
		public $common = false;
		
		/**
		 * Raw line content
		 * @var string
		 */
		private $content;
		
		/**
		 * Initiates line with content
		 * 
		 * @param string $line Line content
		 */
		public function __construct($line = '') {
			$this->content = $line;
			$element = explode('=', $line, 2);
			
			if (count($element) == 2) {
				$this->init(trim($element[0]), trim($element[1]));
			}
		}
		
		/**
		 * Initiates line with specified values
		 * 
		 * @param string $name Line name
		 * @param string $value Line value
		 */
		public function init($name, $value) {
			$this->common = true;
			$this->name = $name;
			$this->value = $value;
		}
		
		/**
		 * Creates raw line content to insert into file
		 * 
		 * @return string Raw line content
		 */
		public function build() {
			if ($this->common) {
				return $this->name.' = '.$this->value;
			} else {
				return $this->content;
			}
		}
	}
?>