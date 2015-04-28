<?php
	namespace System\Terminal\Command;
	use \System\Terminal\Help\Line;
	use \gOPF\gSSP;
	use \gOPF\gSSP\Slot;
	
	/**
	 * Terminal command: server (read information about server)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Server extends \System\Terminal\Command {
		/**
		 * Data refresh interval in nanoseconds (1s = 1 000 000ns)
		 * @var int
		 */
		const INTERVAL = 500000;
		
		/**
		 * Default server-status page URL address
		 * @var string
		 */
		const URL = 'http://localhost/server-status';

		/**
		 * Column margin
		 * @var int
		 */
		const MARGIN = 2;

		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$lines = array();
			$help = new \System\Terminal\Help('Display server trafic and main server information');
			
			$lines[] = new Line('server', 'display main server information');
			$lines[] = new Line('server -slots', 'displays current slots info');
			$lines[] = new Line('server -interval [miliseconds]', 'set custom server status refreshing interval');
				
			$help->addLines($lines);			
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$running = true;
			$interval = $this->getParameter('interval') ? $this->getParameter('interval') * 1000 : self::INTERVAL;
			
			while ($running) {
				usleep($interval);
				
				$session = $this->getSession();
				$status = $session->pull();
				
				$status->buffer($this->executeTask());
				$status->clear = true;
				$status->update();
				
				if ($status->abort) {
					$running = false;
				}
				
				$session->push($status);
			}
		}
		
		/**
		 * Executes specified task depends on parameters
		 * 
		 * @return string
		 */
		private function executeTask() {
			if (!$this->getParameter('slots')) {
				return $this->displayMainInfo();
			} else {
				return $this->displaySlotsInfo();
			}
		}
		
		/**
		 * Returns generated main info page 
		 * 
		 * @return string Generated page
		 */
		private function displayMainInfo() {
			$parser = new gSSP(self::URL);
			$return = '';
				
			foreach ($parser->getServer() as $line) {
				$return .= $line."\n";
			}

			$return .= "\n";
			$stats = $parser->getSlotStats();

			$return .= '<bold>[';
			$return .= '<red>'.str_repeat('-', $stats->get(Slot::READING)).'</red>';
			$return .= '<red>'.str_repeat('-', $stats->get(Slot::SENDING)).'</red>';
			$return .= '<yellow>'.str_repeat('-', $stats->get(Slot::CLOSING)).'</yellow>';
			$return .= '<yellow>'.str_repeat('-', $stats->get(Slot::ALIVE)).'</yellow>';
			$return .= '<green>'.str_repeat('-', $stats->get(Slot::OPENED)).'</green>';
			$return .= str_repeat('-', $stats->get(Slot::CLOSED));
			$return .= ']</bold>';

			return $return;
		}
		
		/**
		 * Returns generated slots info page
		 * 
		 * @return string Generated page
		 */
		private function displaySlotsInfo() {
			$parser = new gSSP(self::URL);
			$slots = $parser->getSlots(false, time() - 60);
			
			$head = array(
				'pid' => 'Process ID',
				'state' => 'State',
				'client' => 'Client IP',
				'time' => 'Request time',
				'host' => 'Request host',
				'request' => 'Request content'
			);
			
			$rows = array();
			$rows[] = $head;

			$length = array();
			$align = array(
				STR_PAD_BOTH,
				STR_PAD_BOTH,
				STR_PAD_BOTH,
				STR_PAD_BOTH,
				STR_PAD_RIGHT,
				STR_PAD_RIGHT	
			);
			
			foreach ($slots as $slot) {
				if ($slot->internal) {
					continue;
				}
				
				$col = array();
				
				foreach ($head as $name => $display) {
					$col[] = $slot->{$name};
				}
				
				$rows[] = $col;
			}
			
			foreach ($rows as $row) {
				$position = 0;
				
				foreach ($row as $col) {
					if ($length[$position] < strlen($col) +(self::MARGIN*2)) {
						$length[$position] = strlen($col) + (self::MARGIN*2);
					}
					
					$position++;
				}
			}
			
			$output = '';
			
			foreach ($rows as $row) {
				$position = 0;
				
				foreach ($row as $col) {
					$output .= str_pad($col, $length[$position], ' ', $align[$position]);
					
					$position++;
				}
				
				$output .= "\n";
			}
			
			return $output;
		}
	}
?>