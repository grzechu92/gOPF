<?php
	namespace System\Terminal;
	use \gOPF\gPAE;
	use \gOPF\gPAE\Event;
	use \gOPF\gPAE\Response;
	use \System\Storage;
	
	/**
	 * Terminal main initialization and router object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Terminal {
		/**
		 * gPAE push engine handler
		 * @var \gOPF\gPAE
		 */
		private $engine;
		
		/**
		 * Terminal session handler
		 * @var \System\Terminal\Session
		 */
		private $session;
		
		/**
		 * Initiates gPAE engine for listening terminal events
		 * 
		 * @return \gOPF\gPAE\Response gPAE result
		 */
		public function connection() {
			$this->engine = new gPAE(array(gPAE::INTERVAL => 100));
			$this->session = new Session();
			$this->registerEvents();
			
			$this->engine->terminal = $this;
			
			return $this->engine->run();
		}
		
		/**
		 * Creates event for client
		 * 
		 * @param string $name Event name
		 * @param \Closure $closure Event body
		 */
		private function addClientEvent($name, \Closure $closure) {
			$this->engine->addClientEvent(new Event($name, $closure));
		}
		
		/**
		 * Creates event for server 
		 * 
		 * @param string $name Event name
		 * @param \Closure $closure Event body
		 */
		private function addServerEvent($name, \Closure $closure) {
			$this->engine->addServerEvent(new Event($name, $closure));
		}
		
		/**
		 * Registers terminal events
		 */
		private function registerEvents() {
			$this->addClientEvent('initialize', function($push) {
				$session = $push->terminal->session;
				
				if ($session->processing) {
					return;
				}
				
				if (!$session->get() instanceof Status) {
					$status = new Status();
					$status->initialize();
					$session->set($status);
				}
				
				if (!$session->logged) {
					$this->execute('login -initialize', $session);
				} else {
					$session->processing = false;
				}
			});
			
			$this->addServerEvent('stream', function($push) {
				$session = $push->terminal->session;
				$status = $session->get();
				
				if ($session->checksum() != $push->container->session) {
					$value = clone($status);
					
					$status->buffer = '';
					$push->container->session = $session->checksum();
					
					$session->set($status);
					
					return array('value' => $value);
				}
			});
			
			$this->addClientEvent('command', function($push) {
				$session = $push->terminal->session;
				
				$this->execute($push->data->command, $session);
			});
		}
		
		/**
		 * Executes command request
		 * 
		 * @param string $command Command content
		 * @param \System\Terminal\Session $session Terminal session
		 */
		private function execute($command, Session $session) {
			$parsed = Command::parse($command);
			$session->processing = true;
			
			if (!$session->logged && $parsed->name != 'login') {
				$this->execute('login -initialize', $session);
				return;
			}
			
			try {
				if ($parsed->name[0] == '/') {
					$class = str_replace('/', '\\', $parsed->name).'Command';
				} else {
					$class = '\\System\\Terminal\\Command\\'.$parsed->name.'Command';
				}
				
				$command = new $class();
				
				if ($command instanceof CommandInterface && $command instanceof Command) {
					$command->extend($parsed);
					$command->execute($session);
				}
			} catch (Exception $e) {
				$session->buffer($e->getMessage());
			} catch (\System\Core\Exception $e) {
				$session->buffer('Unknown command: '.$parsed->name);
			}
			
			$session->processing = false;
			$session->updated = microtime(true);
		}
	}
?>