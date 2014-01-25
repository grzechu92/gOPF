<?php
	namespace System\Terminal\Command;
	use \System\Terminal\Help;
	use \System\Core;
	
	/**
	 * Terminal command: welcome (displays terminal welcome message)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
			
			$message = "\n".str_pad('gOPF Terminal v'.Core::VERSION.' (build '.Core::BUILD.')', 60, ' ', STR_PAD_BOTH)."\n";
			$message .= "\n".str_pad('Yeap, that\'s it, type help if you want to know more', 60, ' ', STR_PAD_BOTH)."\n";
			
			$session->clear = true;
			$session->buffer($message);
		}
	}
?>