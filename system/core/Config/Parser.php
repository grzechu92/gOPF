<?php
	namespace System\Config;
	use \System\Config\Parser\Line;
	use \System\Queue\Element;
	
	/**
	 * Parser for config file
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Parser {
		/**
		 * Path to parsed file
		 * @var string
		 */
		private $path;
		
		/**
		 * Lines from configuration file
		 * @var \System\Queue
		 */
		private $lines;
		
		/**
		 * Initiates parser object
		 * 
		 * @param string $path Path to parsed file
		 */
		public function __construct($path) {
			$this->path = $path;
			
			$this->lines = new \System\Queue();
			
			foreach (file($this->path) as $line) {
				$line = new Line($line);
				$name = $line->common ? $line->name : count($this->lines)+1;
				
				$this->lines->push(new Element($name, $line));
			}
		}
		
		/**
		 * Saves change in parsed file
		 */
		public function __destruct() {
			if (!empty($this->lines)) {
				$content = '';
				
				foreach ($this->lines as $element) {
					$line = $element->value;
					
					$content .= $line->build().($line->common ? "\n" : '');
				}
				
				\System\Filesystem::write($this->path, $content);
			}
		}
		
		/**
		 * Processes lines with file
		 * 
		 * @param array $lines Lines with changes
		 */
		public function process(Array $lines) {
			if (count($lines) > 0) {
				foreach ($lines as $name=>$value) {					
					$line = new Line();
					$line->init($name, $value);
					
					$this->lines->set(new Element($name, $line));
				}
			}
		}
	}
?>