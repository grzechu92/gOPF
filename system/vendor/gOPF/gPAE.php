<?php 
	namespace gOPF;
	use \System\Request;
	use \gOPF\gPAE\Events;
	use \gOPF\gPAE\Response;
    use \gOPF\gPAE\Config;
    use \gOPF\gPAE\Client;
    use \gOPF\gPAE\Command;
	
	/**
	 * gPAE - gPAE Push AJAX Engine
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class gPAE {
        /**
         * System and client events
         * @var \gOPF\gPAE\Events
         */
        public $events;
		
		/**
		 * Push server configuration
		 * @var \gOPF\gPAE\Config
		 */
		private $config;

        /**
         * Client session object
         * @var \gOPF\gPAE\Client
         */
        private $client;
		
		/**
		 * Initiates Push module, loads config
		 * 
		 * @param \gOPF\gPAE\Config $config PUSH server config
		 */
		public function __construct(Config $config = null) {
            $this->config = (!($config instanceof Config)) ? new Config() : $config;
            $this->events = new Events();

			if (isset(Request::$post['encrypted'])) {
				foreach(self::decrypt(Request::$post['encrypted']) as $variable => $value) {
					Request::$post[$variable] = $value;
				}
			}
			
			set_time_limit(0);

            $key = isset(Request::$post['key']) ? Request::$post['key'] : self::generateUniqueKey();

            $client = new Client($key, $this->config, $this->events);
			$client->ping = (empty(Request::$post['ping'])) ? 0 : Request::$post['ping'];

            $this->client = $client;
		}
		
		/**
		 * Start push server
		 * 
		 * @return string JSON response
		 */
		public function run() {
            $result = null;

			switch (isset(Request::$post['command']) ? Request::$post['command'] : Command::DISCONNECT) {
                case Command::CONNECT:
					$result = $this->client->connect();
					break;
					
                case Command::HOLD:
					$result = $this->client->loop();
					break;
					
                case Command::ACTION:
                    $event = isset(Request::$post['event']) ? Request::$post['event'] : '';
                    $data = isset(Request::$post['data']) ? (object) Request::$post['data'] : new \stdClass();

					$this->client->action($event, $data);
					break;

                case Command::DISCONNECT:
                    $this->client->disconnect();
                    break;
			}

			if ($result instanceof Response) {
				$result = $result->build();
				
				if ($this->config->encrypted) {
					$result = self::encrypt($result);
				}

                die($result);
			}

            die();
		}

		/**
		 * Generates unique Push session key
		 * 
		 * @return string Session key
		 */
		private static function generateUniqueKey() {
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
				$encoded .= substr($encrypted, $offset + 1, 2);
				$offset += 3;
			}
				
			return json_decode(base64_decode($encoded), true);
		}
	}
?>