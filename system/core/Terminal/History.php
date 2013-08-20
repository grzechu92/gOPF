<?php 
	namespace System\Terminal;
	use \System\Terminal;
	
	/**
	 * Terminal history storage
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class History {
		/**
		 * History container
		 * @var array
		 */
		public $content = array();
		
		/**
		 * Current history offset
		 * @var int
		 */
		public $position;
		
		/**
		 * Adds command to history
		 * 
		 * @param string $command Command to add
		 */
		public function push($command) {
			$this->content[] = $command;
			$this->position = count($this->content);
			
			Terminal::$session->write();
		}
		
		/**
		 * Returns next history element
		 * 
		 * @return string History element
		 */
		public function next() {
			$this->position++;
			
			if ($this->position >= count($this->content)) {
				$this->position = count($this->content);
				$return =  '';
			} else {
				$return = $this->content[$this->position];
			}
			
			Terminal::$session->write();
			return $return;
		}
		
		/**
		 * Returns previous history element
		 * 
		 * @return string History element
		 */
		public function previous() {
			$this->position--;
				
			if ($this->position < 0) {
				$this->position = -1;
				$return = '';
			} else {
				$return = $this->content[$this->position];
			}
			
			Terminal::$session->write();
			return $return;
		}
	}
?>