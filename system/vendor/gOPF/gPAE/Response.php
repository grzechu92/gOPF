<?php 
	namespace gOPF\gPAE;
	
	/**
	 * Response class which builds response for client
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Response {
		/**
		 * Command for client
		 * @var string
		 */
		private $command;
		
		/**
		 * Values for client
		 * @var array
		 */
		private $values = array();
		
		/**
		 * Initiates response object
		 * 
		 * @param string $command Command for client
		 * @param array $values Values for client
		 */
		public function __construct($command, $values = array()) {
			$this->command = $command;
			$this->values = $values;
		}
		
		/**
		 * Builds response for client
		 * 
		 * @return array Response for client
		 */
		public function build() {
			return array_merge(array('command' => $this->command, 'time' => microtime(true)-__START_TIME), $this->values);
		}
	}
?>