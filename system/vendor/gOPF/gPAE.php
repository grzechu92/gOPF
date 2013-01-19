<?php 
	namespace gOPF;
	use \System\Request;
	use \gOPF\gPAE\Container;
	use \gOPF\gPAE\Event;
	use \gOPF\gPAE\Response;
	
	/**
	 * gPAE - gPAE Push AJAX Engine
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class gPAE {
		/**
		 * Config constant for server events check interval (in miliseconds)
		 * @var int
		 */
		const INTERVAL = 1;
		
		/**
		 * Config constant for connection timeout value (in miliseconds) 
		 * @var int
		 */
		const TIMEOUT = 2;
		
		/**
		 * Config constant for connection reconnect interval (in miliseconds)
		 * @var int
		 */
		const RECONNECT = 3;
		
		/**
		 * Config constant for connection encryption (boolean)
		 * @var int
		 */
		const ENCRYPTED = 4;
		
		/**
		 * Server side event
		 * @var int
		 */
		const SERVER = 1;
		
		/**
		 * Client side event
		 * @var int
		 */
		const CLIENT = 2;
		
		/**
		 * Push session container
		 * @var \gPAE\Container
		 */
		public $container;
		
		/**
		 * Event data from client
		 * @var \System\Container
		 */
		public $data;
		
		/**
		 * Client side event name
		 * @var string
		 */
		public $event;
		
		/**
		 * Ping between client and server via HTTP (in miliseconds)
		 * @var int
		 */
		public $ping = 0;
		
		/**
		 * Requested command
		 * @var string
		 */
		private $command;
		
		/**
		 * Push session key
		 * @var string
		 */
		private $key;
		
		/**
		 * Array with server and client side events
		 * @var array
		 */
		private $events = array(
			self::SERVER => array(),
			self::CLIENT => array()
		);
		
		/**
		 * Array with Push module configuration
		 * @var array
		 */
		private $config = array(
			self::INTERVAL => 1000,
			self::TIMEOUT => 600000,
			self::RECONNECT => 5000,
			self::ENCRYPTED => false	
		);
		
		/**
		 * Initiates Push module, loads config
		 * 
		 * @param array $config Array with config values to swap
		 */
		public function __construct($config = array()) {
			foreach ($config as $key=>$value) {
				$this->config[$key] = $value;
			}
			
			if (isset(Request::$post['encrypted'])) {
				foreach(self::decrypt(Request::$post['encrypted']) as $variable => $value) {
					Request::$post[$variable] = $value;
				}
			}
			
			set_time_limit(0);
			
			$this->command = isset(Request::$post['command']) ? Request::$post['command'] : '';
			$this->key = isset(Request::$post['key']) ? Request::$post['key'] : self::generateUniqeKey();
			$this->data = isset(Request::$post['data']) ? new \System\Container((array) Request::$post['data']) : '';
			$this->event = isset(Request::$post['event']) ? Request::$post['event'] : '';
			$this->ping = (empty(Request::$post['ping'])) ? 0 : Request::$post['ping'];
			
			$this->container = new Container($this->key, $this->config[self::TIMEOUT]/1000+1);
		}
		
		/**
		 * Adds server side event
		 * 
		 * @param Event $event Event to add
		 */
		public function addServerEvent(Event $event) {
			if (!is_array($this->events[self::SERVER])) {
				$this->events[self::SERVER] = array();
			}
			
			$this->events[self::SERVER][$event->name] = $event;
		}
		
		/**
		 * Adds event from client
		 * @param Event $event Event to add
		 */
		public function addClientEvent(Event $event) {
			if (!is_array($this->events[self::CLIENT])) {
				$this->events[self::CLIENT] = array();
			}
			
			$this->events[self::CLIENT][$event->name] = $event;
		}
		
		/**
		 * Runns main functionality
		 * 
		 * @return \gPAE\Response Result of request processing
		 */
		public function run() {
			$result = null;
			
			switch ($this->command) {
				case 'CONNECT':
					$result = $this->connect();
					break;
					
				case 'HOLD':
					$result = $this->hold();
					break;
					
				case 'DISCONNECT':
					$this->disconnect();
					break;
					
				case 'CATCH':
					$this->callEvent($this->event);
					break;
			}
			
			if ($result instanceof Response) {
				$result = $result->build();
				
				if ($this->config[self::ENCRYPTED]) {
					$result = self::encrypt($result);
				}
			}
			
			return $result;
		}
		
		/**
		 * Disconnects client from server
		 */
		public function disconnect() {
			$this->container->__DISCONNECTED = true;
		}
		
		/**
		 * Initiates new connection with server
		 * 
		 * @return \gPAE\Response Connection parameters
		 */
		private function connect() {
			$this->callEvent('onConnect');
			
			return new Response('CONNECTED', array(
				'key' => $this->key,
				'timeout' => $this->config[self::TIMEOUT],
				'reconnect' => $this->config[self::RECONNECT]	
			));
		}
		
		/**
		 * Holds HTTP request on server side, check that any server event has been called
		 * 
		 * @return \gPAE\Response Data from event for client
		 */
		private function hold() {
			ignore_user_abort(true);
				
			while (((__START_TIME+$this->config[self::TIMEOUT]/1000) >= microtime(true))) {
				if ($this->container->__DISCONNECTED || connection_aborted()) {
					if (connection_aborted()) {
						$this->callEvent('onConnectionLost');
					}
					$this->callEvent('onDisconnect');
					
					$this->container->expired();
					
					return new Response('DISCONNECTED');
				}
				
				foreach ($this->events[self::SERVER] as $event) {
					$result = $event->closure($this);
					
					if (is_array($result)) {
						return new Response('CATCH', $result + array('event' => $event->name));
					}
				}
				
				echo ' ';
				ob_flush();
				flush();
				
				usleep($this->config[self::INTERVAL]*1000);
			}
			
			return new Response('RENEW');
		}
		
		/**
		 * Calls event from client
		 * 
		 * @param string $name Event name
		 */
		private function callEvent($name) {
			if (isset($this->events[self::CLIENT][$name])) {
				$this->events[self::CLIENT][$name]->closure($this);
			}
		}
		
		/**
		 * Generates unique Push session key
		 * 
		 * @return string Session key
		 */
		private static function generateUniqeKey() {
			return 'gPAE-'.substr(sha1(\System\Core::$UUID.rand(1, 1000000)), 0, 10);
		}
		
		/**
		 * Encrypts data for client
		 * 
		 * @param array $data Encrypted data string
		 * @return array Response for client
		 */
		private static function encrypt(array $data) {
			$encoded = base64_encode(json_encode($data));
			$encrypted = '';
				
			foreach (str_split($encoded, 2) as $chunk) {
				$encrypted .= substr(base64_encode($chunk), 0, 1).$chunk;
			}
				
			return array('encrypted' => $encrypted);
		}
		
		/**
		 * Decrypts data from client
		 * 
		 * @param string $encrypted Encrypted data string
		 * @return array Decrypted data from client
		 */
		private static function decrypt($encrypted) {
			$encoded = '';
			$offset = 0;
				
			foreach (str_split($encrypted, 3) as $chunk) {
				$encoded .= substr($encrypted, $offset+1, 2);
				$offset += 3;
			}
				
			return json_decode(base64_decode($encoded), true);
		}
	}
?>