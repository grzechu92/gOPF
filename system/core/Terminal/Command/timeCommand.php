<?php
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: time (prints current time)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class timeCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			if ($this->getParameter('readable')) {
				$session->buffer(date('H:m:s'));
			} else {
				$session->buffer(time());
			}
		}
	}
?>