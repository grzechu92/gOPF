<?php
	namespace System\Terminal\Command;
	use \System\Terminal\Help\Line;
	
	/**
	 * Terminal command: count (counts from 0 to value in 1 second interval)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class countCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$lines = array();
			$help = new \System\Terminal\Help('Another not important command, counts with 1 second interval');
		
			$lines[] = new Line('count [number]', 'counts to number');
			$lines[] = new Line('count [number] -clear', 'with each iteration terminal console will be cleared');
			
			$help->addLines($lines);
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			for ($i = 1; $i <= $this->value; $i++) {
				if ($session->abort) {
					break;
				}
				
				sleep(1);
				$session->buffer($i);
				
				if ($this->getParameter('clear')) {
					$session->clear = true;
				}
			}
		}
	}
?>