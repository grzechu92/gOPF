<?php
	namespace gOPF\gWSS;

	/**
	 * gWSS Response class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Response {
		/**
		 * Event name
		 * @var string
		 */
		private $event;

		/**
		 * Event data
		 * @var \stdClass
		 */
		private $data;

		/**
		 * Initialize response object
		 *
		 * @param string $event Event name
		 * @param \stdClass|null  $data Event data
		 */
		public function __construct($event, $data = null) {
			$this->event = $event;
			$this->data = (object) $data;
		}

		/**
		 * @see \gOPF\gWSS\Response::build()
		 */
		public function __toString() {
			return $this->build();
		}

		/**
		 * Build response string
		 *
		 * @return string JSON Response
		 */
		public function build() {
			$output = new \stdClass();
			$output->event = $this->event;

			if ($this->data != new \stdClass()) {
				$output->data = $this->data;
			}

			return json_encode($output);
		}
	}
?>