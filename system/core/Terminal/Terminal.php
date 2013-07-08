<?php
	namespace System\Terminal;
	use \gOPF\gPAE;
	use \gOPF\gPAE\Event;
	use \gOPF\gPAE\Response;
	use \System\Storage;
			
	class Terminal {
		private $engine;
		private $session;
		
		public function connection() {
			$this->engine = new gPAE(array(gPAE::INTERVAL => 100));
			$this->session = new Session();
			$this->registerEvents();
			
			$this->engine->terminal = $this;
			
			return $this->engine->run();
		}
		
		private function addClientEvent($name, \Closure $closure) {
			$this->engine->addClientEvent(new Event($name, $closure));
		}
		
		private function addServerEvent($name, \Closure $closure) {
			$this->engine->addServerEvent(new Event($name, $closure));
		}
		
		private function registerEvents() {
			$this->addServerEvent('initialize', function($push) {
				if (!$push->terminal->session->get() instanceof Status) {
					$status = new Status();
					$status->initialize();
					$push->terminal->session->set($status);
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
				
				$session->processing = true;
				
				try {
					$this->execute($push->data->command, $session);
				} catch (Exception $e) {
					$session->buffer($e->getMessage());
				}
				
				$session->processing = false;
			});
		}
		
		private function execute($command, $session) {
			$parsed = Command::parse($command);
			
			try {
				$class = '\\System\\Terminal\\Command\\'.$parsed->name.'Command';
				$command = new $class();
				
				if ($command instanceof CommandInterface && $command instanceof Command) {
					$command->extend($parsed);
					$command->execute($session);
				}
			} catch (\System\Core\Exception $e) {
				throw new Exception('Unknown command');
			}
		}
	}
?>