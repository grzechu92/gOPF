<?php
	namespace System;
	use \gOPF\gPAE;
	use \gOPF\gPAE\Event;
	use \gOPF\gPAE\Response;
	use \System\Terminal\Command;
	use \System\Terminal\Status;
	
	/**
	 * Terminal main initialization and router object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Terminal {
		/**
		 * Terminal instance handler
		 * @var \System\Terminal\Terminal
		 */
		public static $instance;
		
		/**
		 * Terminal session handler
		 * @var \System\Terminal\Session
		 */
		public static $session;
		
		/**
		 * gPAE push engine handler
		 * @var \gOPF\gPAE
		 */
		private $engine;
		
		/**
		 * Initiates gPAE engine for listening terminal events
		 * 
		 * @return \gOPF\gPAE\Response gPAE result
		 */
		public function connection() {
			self::$instance = $this;
			self::$session = new \System\Terminal\Session();
			
			Command::$terminal = self::$instance;
			Command::$session = self::$session;
			
			$this->engine = new gPAE(array(gPAE::INTERVAL => 100));
			$this->registerEvents();
			
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
				$session = Terminal::$session;
				$status = $session->pull();
				
				if ($status instanceof Status && $status->processing && $status->initialized) {
					return;
				}
				
				if (!$status instanceof Status) {
					$status = new Status();
					$status->initialize();
				}
				
				if (!$status->logged) {
					$session->push($status);
					$this->execute('login -initialize');
				}
				
				$status = $session->pull();
				$status->processing = false;
				$session->push($status);
			});
			
			$this->addServerEvent('stream', function($push) {
				$session = Terminal::$session;
				$status = $session->pull();
				
				if ($status instanceof Status && $status->checksum() != $push->container->session) {
					$value = clone($status);
					
					$status->buffer = '';
					$status->clear = false;
					
					$push->container->session = $status->checksum();
					$session->push($status);
					
					return array('value' => $value);
				}
			});
			
			$this->addClientEvent('command', function($push) {
				$session = Terminal::$session;
				
				$this->execute($push->data->command);
			});
		}
		
		/**
		 * Executes command request
		 * 
		 * @param string $command Command content
		 */
		private function execute($command) {
			$session = Terminal::$session;
			$status = $session->pull();
			
			$parsed = Command::parse($command);
			
			$session->processing = true;
			
			if (!$status->logged && $parsed->name != 'login') {
				$this->execute('login -initialize');
				return;
			}
			
			try {				
				$command = Command::factory($parsed);
				$command->execute();
			} catch (\System\Loader\Exception $e) {
				$session->buffer('Unknown command: '.$parsed->name);				
			} catch (\System\Terminal\Exception $e) {
				$session->buffer($e->getMessage());
			} catch (\System\Core\Exception $e) {
				$session->buffer('Error:'."\n".$e->getMessage());
			}
			
			$session->processing = false;
			$session->updated = microtime(true);
		}
	}
?>