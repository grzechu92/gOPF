<?php
	namespace System\Dispatcher;
	use System\Config;

/**
	 * CRON request processing context
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Cron extends Context implements ContextInterface {
		/**
		 * Cron config filename
		 * @var string
		 */
		const FILENAME = 'cron.ini';

		/**
		 * @see System\Dispatcher\ContextInterface::process()
		 */
		public function process() {
			$this->execute(date('H:i'));
		}
		
		/**
		 * Process cron request
		 * 
		 * @param string $time Time index to execute (HH:MM)
		 */
		public function execute($time) {
			$jobs = $this->getJobs($time);
				
			if (count($jobs) > 0) {
				foreach ($jobs as $job) {
					if (!empty($job)) {
						$this->doJob($job);
					}
				}
			}
		}
		
		/**
		 * Splits jobs separated by comma, and returns it
		 * 
		 * @param string $jobs String with separated jobs
		 * @return array Separated jobs array
		 */
		private function splitJobs($jobs) {
			return explode(',', str_replace(' ', '', $jobs));
		}
		
		/**
		 * Returns unique jobs list, which should be done at this time
		 * 
		 * @param string $time Time index to execute
		 * @return array Jobs to do
		 */
		private function getJobs($time) {
			$application = Config::factory(self::FILENAME, Config::APPLICATION);
			$system = Config::factory(self::FILENAME, Config::SYSTEM);
				
			$jobs = array_merge_recursive($application->getContent(), $system->getContent());
			$toDo = array();
			
			$indexes = $this->getCurrentIndexes($time);
			
			foreach ($indexes as $key) {
				if (!empty($jobs[$key])) {
					foreach ( (array) $jobs[$key] as $job) {
						$toDo = array_merge($toDo, $this->splitJobs($job));
					}
				}
			}
				
			return array_unique($toDo);
		}
		
		/**
		 * Does the requested job
		 * 
		 * @param string $job Job to do
		 */
		private function doJob($job) {
			if (strpos($job, '->')) {
				list($class, $method) = explode('->', $job);
		
				$object = new $class();
				call_user_func_array(array($object, $method), array());
			} else {
				call_user_func_array($job, array());
			}
		}
		
		/**
		 * Generates current time indexes to call by cron engine
		 * 
		 * @param string $time Time index to find
		 * @return array Array with time values
		 */
		private function getCurrentIndexes($time) {
			$return = array($time);
			$parts = array();
			
			foreach (explode(':', $time) as $index=>$double) {
				$parts[$index] = array();
				
				foreach (array(0, 1, 2, 3) as $key) {
					$part = $double;
					
					switch ($key) {
						case 3:
							break;
						
						case 2:
							$part = '**';
							break;
							
						case 1:
						case 0:
							$part[$key] = '*';
					}
					
					$parts[$index][] = $part;
				}
			}
			
			foreach ($parts[0] as $h) {
				foreach ($parts[1] as $m) {
					$t = $h.':'.$m;
					
					if ($t != $time) {
						$return[] = $h.':'.$m;
					}
				}
			}			
			
			$special = array(5, 10, 15, 20, 30);
			$minutes = date('i');
			
			foreach ($special as $value) {
				if ($minutes%$value == 0) {
					$return[] = ':'.$value;
				}
			}
			
			return $return;
		}
	}
?>
