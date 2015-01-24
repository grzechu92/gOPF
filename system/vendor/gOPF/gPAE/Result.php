<?php
	namespace gOPF\gPAE;

	/**
	 * Result of event processing
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Result {
		/**
		 * Event name to call on client side
		 * @var string
		 */
		public $event;

		/**
		 * Event data
		 * @var \stdClass
		 */
		public $data;

		/**
		 * Initialize result object
		 *
		 * @param string $event Event name
		 * @param array|null|\stdClass $data Event data
		 */
		public function __construct($event, $data = null) {
			$this->event = $event;
			$this->data = (object) $data;
		}
	}
?>