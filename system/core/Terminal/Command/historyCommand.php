<?php
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: history (shows terminal commands history)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class historyCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = \System\Terminal::$session;
			
			foreach ($session->history()->content as $command) {
				$session->buffer($command);
			}
		}
	}
?>