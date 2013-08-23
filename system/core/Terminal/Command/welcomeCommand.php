<?php
	namespace System\Terminal\Command;
	use \System\Terminal\Help;
	
	/**
	 * Terminal command: welcome (displays terminal welcome message)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class welcomeCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new Help(Help::INTERNAL);
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			$message = "\n".str_pad('gOPF Terminal v'.\System\Core::VERSION, 50, ' ', STR_PAD_BOTH)."\n";
			$message .= "\n".str_pad('Blablabla. There is no help command, yet.', 50, ' ', STR_PAD_BOTH)."\n";
			
			$session->clear = true;
			$session->buffer($message);
		}
	}
?>