<?php
	namespace gOPF\gSSP;
	
	/**
	 * gSSP Server data object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Server {
		/**
		 * Version of apache server
		 * @var string
		 */
		public $version;
		
		/**
		 * Current server time
		 * @var string
		 */
		public $time;
		
		/**
		 * Time when server has been started
		 * @var string
		 */
		public $start;
		
		/**
		 * Server uptime
		 * @var string
		 */
		public $uptime;
		
		/**
		 * Server load
		 * @var string
		 */
		public $load;
		
		/**
		 * Traffic information
		 * @var string
		 */
		public $traffic;
		
		/**
		 * CPU usage information
		 * @var string
		 */
		public $cpu;
		
		/**
		 * Hits stats
		 * @var string
		 */
		public $stats;
		
		/**
		 * Request stats
		 * @var string
		 */
		public $requests;
	}
?>