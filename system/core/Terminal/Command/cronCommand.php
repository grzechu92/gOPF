<?php
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Dispatcher\Cron;
	use \System\Terminal\Help\Line;

	/**
	 * Terminal command: cron (execute cron job)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
			$lines[] = new Line('cron -list', 'shows all CRON entries');
				
			$help->addLines($lines);
		
			return $help;
		}

		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$cron = new Cron();

			if (!$this->getParameter('list')) {
				$time = $this->getParameter('time');

				if (empty($time)) {
					$time = date('H:i');
				}

				$cron->execute($time);
			} else {
				$application = Config::factory(Cron::FILENAME, Config::APPLICATION);
				$system = Config::factory(Cron::FILENAME, Config::SYSTEM);
				$buffer = '';

				foreach (array_merge_recursive($application->getContent(), $system->getContent()) as $hour => $jobs) {
					$list = array();

					foreach ($jobs as $job) {
						foreach (explode(',', $job) as $exploded) {
							$exploded = trim($exploded);

							if (!empty($exploded)) {
								$list[] = $exploded;
							}
						}
					}

					$buffer .= $hour.' = '.implode("\n        ", $list)."\n\n";
				}

				self::$session->buffer($buffer);
			}
		}
	}
?>