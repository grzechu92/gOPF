<?php
	namespace System\Terminal\Command;
	use \System\Terminal\Help\Line;
	
	/**
	 * Terminal command: cron (execute cron job)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class cronCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$lines = array();
			$help = new \System\Terminal\Help('Execute CRON job');
		
			$lines[] = new Line('cron', 'execute CRON job for current time');
			$lines[] = new Line('cron -time [HH:MM]', 'execute CRON job for specified time');
				
			$help->addLines($lines);
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$time = $this->getParameter('time');
			
			if (empty($time)) {
				$time = date('H:i');
			}
			
			$cron = new \System\Dispatcher\Cron();
			$cron->execute($time);
		}
	}
?>