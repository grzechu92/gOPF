<?php 
	namespace gOPF\gPAE;
	
	/**
	 * Response class which build JSON response for client
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
		 * @var \gOPF\gPAE\Result
		 */
		private $result;

        /**
         * Event to call on client side
         * @var string
         */
        private $event;
		
		/**
		 * Initiates response object
		 * 
		 * @param string $command Command for client
		 * @param \gOPF\gPAE\Result $result Data result
         * @param string|null $event Event to call on client side
		 */
		public function __construct($command, Result $result = null, $event = null) {
			$this->command = $command;
			$this->result = $result;
            $this->event = $event;
		}

        /**
         * @see \gOPF\gPAE\Response::build()
         */
        public function __toString() {
            return $this->build();
        }
		
		/**
		 * Builds response for client
         *
         * @return string JSON response object
		 */
		public function build() {
            $output = new \stdClass();
            $output->command = $this->command;
            $output->time = microtime(true) - __START_TIME;

            if ($this->result instanceof Result) {
                $output->result = $this->result;
            }

            if (!empty($this->event)) {
                $output->event = $this->event;
            }

			return json_encode($output);
		}
	}
?>