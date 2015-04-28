<?php
	namespace System\Terminal\Command;

	/**
	 * Terminal command: logout (loggs out current user)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Logout extends \System\Terminal\Command {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('It\'s easy, just read again left column');
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$status = new \System\Terminal\Status();
			$status->clear = true;
			$status->buffer('Bye!');
			$status->prompt = '';
			$status->update();
			
			$this->getSession()->push($status);
		}
	}
?>