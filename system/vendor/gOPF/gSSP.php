<?php
	namespace gOPF;
	use \gOPF\gSSP\Slot;
	
	/**
	 * gSSP - gSSP Server Status Parser
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class gSSP {
		/**
		 * Server stats page URL address
		 * @var string
		 */
		private $url;
		
		/**
		 * Parsed slots array
		 * @var array
		 */
		private $slots = array();
		
		/**
		 * Initiates gSSP object
		 * 
		 * @param string $url Server stats page URL address
		 */
		public function __construct($url) {
			$this->url = $url;
		}
		
		/**
		 * Updates data about server status
		 */
		public function update() {
			$this->parse(file_get_contents($this->url));
		}
		
		/**
		 * Returns availiable slots
		 * 
		 * @param bool $working Return only currently working slots
		 * @param int $time Returns slots which changes their status from selected timestamp
		 * @return array Array with slots
		 */
		public function getSlots($working = true, $time = 0) {
			$slots = array();
			
			foreach ($this->slots as $slot) {
				if (!$working || $slot->isWorking()) {
					if (!$time || $time <= $slot->time) {
						$slots[] = $slot;
					}
				}
			}
			
			return $slots;
		}
		
		/**
		 * Returns availiable slots grouped by host
		 *
		 * @param bool $working Return only currently working slots
		 * @param int $time Returns slots which changes their status from selected timestamp
		 * @return array Array with slots
		 */
		public function getSlotsByHost($working = true, $time = 0) {
			return $this->groupSlotsBy('host', $working, $time);
		}
		
		/**
		 * Returns all availiable slots grouped by client IP address
		 *
		 * @param bool $working Return only currently working slots
		 * @param int $time Returns slots which changes their status from selected timestamp
		 * @return array Array with slots
		 */
		public function getSlotsByClient($working = true, $time = 0) {
			return $this->groupSlotsBy('client', $working, $time);
		}
		
		/**
		 * Returns all availiable slots grouped by request
		 *
		 * @param bool $working Return only currently working slots
		 * @param int $time Returns slots which changes their status from selected timestamp
		 * @return array Array with slots
		 */
		public function getSlotsByRequest($working = true, $time = 0) {
			return $this->groupSlotsBy('request', $working, $time);
		}
		
		/**
		 * Parses data with server status
		 * 
		 * @param string $data Server status page to parse
		 */
		private function parse($data) {
			$this->slots = array();
			$first = true;
			
			$document = new \DOMDocument('1.0', 'UTF-8');
			$document->loadHTML($data);
			
			foreach ($document->getElementsByTagName('tr') as $row) {
				if ($first) {
					$first = false;
					continue;
				}
				
				$columns = $row->getElementsByTagName('td');
				
				if ($columns->length > 1) {
					$data = array();
					
					foreach ($columns as $column) {
						$data[] = $column->textContent;
					}
					
					$this->slots[] = new Slot($data);
				}
			}
		}
		
		/**
		 * Groups slots by selected field
		 *
		 * @param bool $working Return only currently working slots
		 * @param int $time Returns slots which changes their status from selected timestamp
		 * @return array Array with grouped slots
		 */
		private function groupSlotsBy($property, $working, $time) {
			$return = array();
			
			foreach ($this->slots as $slot) {
				if (!$working || $slot->isWorking()) {
					if (!$time || $time <= $slot->time) {
						if (!isset($return[$slot->{$property}])) {
							$return[$slot->{$property}] = array();
						}
						
						$return[$slot->{$property}][] = $slot;
					}
				}
			}
			
			return $return;
		}
	}
?>