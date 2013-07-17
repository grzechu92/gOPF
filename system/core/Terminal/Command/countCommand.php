<?php
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: count (counts from 0 to value in 1 second interval)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class countCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			for ($i = 1; $i <= $this->value; $i++) {
				sleep(1);
				$session->buffer($i);
				
				if ($this->getParameter('clear')) {
					$session->clear = true;
				}
			}
		}
	}
?>