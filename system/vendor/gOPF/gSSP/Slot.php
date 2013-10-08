<?php
	namespace gOPF\gSSP;
	
	/**
	 * gSSP Slot class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Slot {
		/**
		 * Status for opened slots - wait for request
		 * @var string
		 */
		const OPENED = '_';
		
		/**
		 * Status for closed slot - hibernated
		 * @var string
		 */
		const CLOSED = '.';
		
		/**
		 * Status for reading slot - reading request from client
		 * @var string
		 */
		const READING = 'R';
		
		/**
		 * Status for writing slot - writing data for client
		 * @var string
		 */
		const SENDING = 'W';
		
		/**
		 * Status for keepalive slot - waiting for another client request
		 * @var string
		 */
		const ALIVE = 'K';
		
		/**
		 * Unknown statusstrong
		 * @var string
		 */
		const UNKNOWN = '';
		
		/**
		 * Slot ID
		 * @var int
		 */
		public $id = 0;
		
		/**
		 * Slot status code
		 * @var string
		 */
		public $status;
		
		/**
		 * Slot status name
		 * @var string
		 */
		public $state;
		
		/**
		 * Slot process ID
		 * @var string
		 */
		public $PID = 0;
		
		/**
		 * Client IP addres
		 * @var string
		 */
		public $client;
		
		/**
		 * Slot host
		 * @var string
		 */
		public $host;
		
		/**
		 * Slot request data
		 * @var string
		 */
		public $request;
		
		/**
		 * Timestamp of last status change
		 * @var int
		 */
		public $time;
		
		/**
		 * Flag of internal request
		 * @var bool
		 */
		public $internal = false;
		
		/**
		 * Initiates Slot object
		 * 
		 * @param string $values Server stats row with selected slot
		 */
		public function __construct($values) {
			list($id,$pid,,$status,,$time,,,,,$client,$host,$request) = $values;
			
			list($this->id,) = explode('-', $id);
			
			$this->pid = $pid;
			$this->status = self::getStatus($status);
			$this->state = self::getState($this->status);
			$this->client = $client;
			$this->host = $host;
			$this->request = $request;
			$this->time = time()-$time;
			
			if (strpos($this->request, \gOPF\gSSP::SERVER_STATUS) > 0) {
				$this->internal = true;
			}
		}
		
		/**
		 * Checks if slot is currently working
		 * 
		 * @return boolean
		 */
		public function isWorking() {
			return (bool) in_array($this->status, array(self::READING, self::SENDING, self::ALIVE));
		}
		
		/**
		 * Returns status code for slot
		 * 
		 * @param string $slot Slot status
		 * @return string Internal slot status
		 */
		private static function getStatus($slot) {
			$slot = trim($slot);
			$statuses = array(self::OPENED, self::CLOSED, self::READING, self::SENDING, self::ALIVE);
			
			foreach ($statuses as $status) {
				if ($slot == $status) {
					return $status;
				}
			}
			
			return self::UNKNOWN;
		}
		
		/**
		 * Returns state name of status
		 * 
		 * @param string $status Status code
		 * @return string State name
		 */
		private static function getState($status) {
			$list = array(
				self::OPENED => 'OPENED',
				self::CLOSED => 'CLOSED',
				self::READING => 'READING',
				self::SENDING => 'SENDING',
				self::ALIVE => 'ALIVE'
			);
			
			if (isset($list[$status])) {
				return $list[$status];
			} else {
				return 'UNKNOWN';
			}
		}
	}
?>