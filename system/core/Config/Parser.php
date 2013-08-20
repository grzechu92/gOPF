<?php
	namespace System\Config;
	use \System\Config\Parser\Line;
	use \System\Config\Parser\Group;
	use \System\Queue;
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
		 * Elements of configuration file
		 * @var \System\Queue
		 */
		private $elements;
		
		/**
		 * Initiates parser object
		 * 
		 * @param string $path Path to parsed file
		 */
		public function __construct($path) {
			$this->path = $path;
			$this->elements = new Queue();
			$group = false;
			
			foreach (file($this->path) as $line) {
				$line = new Line($line);
				$name = $line->common ? $line->name : count($this->elements)+1;
				
				if ($line->array) {
					$group = $line->name;
					$this->elements->push(new Element($group, new Group($group)));
					$this->elements->get($group)->push(new Element($line->name, $line));
					continue;
				}

				$element = new Element($name, $line);
				
				if ($group) {
					$content = $this->elements->get($group)->push($element);
				} else {
					$this->elements->push($element);
				}
			}
		}
		
		/**
		 * Saves change in parsed file
		 */
		public function __destruct() {
			if (!empty($this->elements)) {
				$content = '';
				
				foreach ($this->elements as $line) {
					$line = $line->value;
					
					if ($line instanceof Line) {
						$content .= $line->build()."\n";
					}
					
					if ($line instanceof Group) {
						$group = $line;
												
						foreach ($group as $line) {
							$line = $line->value;
							
							$content .= $line->build()."\n";
						}
					}
				}
				
				echo nl2br($content);
				
				\System\Filesystem::write($this->path, $content);
			}
		}
		
		/**
		 * Processes lines with file
		 * 
		 * @param array $values Lines with changes
		 */
		public function merge(Array $values) {
			if (count($values) > 0) {
				foreach ($values as $name=>$value) {
					if (is_array($value)) {
						if (!$this->elements->exist($name)) {
							$element = new Element($name, new Group($name));
							$this->elements->push($element, Queue::BOTTOM);
							$this->elements->get($name)->push(new Element($name, new Line('['.$name.']')));
						}
						
						$group = $this->elements->get($name);
						
						foreach ($value as $n=>$v) {
							$line = new Line();
							$line->init($n, $v);
							
							$group->push(new Element($n, $line));
						}
					} else {
						$line = new Line();
						$line->init($name, $value);
						
						$element = new Element($name, $line);
						
						if (!$this->elements->exist($name)) {
							$this->elements->push($element, Queue::TOP);
						} else {
							$this->elements->set($element);
						}
					}
				}
			}			
		}
	}
?>