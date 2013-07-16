<?php 
	namespace System\Terminal;
	
	class Command {
		/**
		 * Terminal instance
		 * @var \System\Terminal
		 */
		public static $terminal;
		
		/**
		 * Terminal session instance
		 * @var \System\Terminal\Session
		 */
		public static $session;
		
		public $command;
		public $name;
		public $value;
		public $parameters = array();
		
		public static function factory(Command $parsed) {
			if ($parsed->name[0] == '/') {
				$class = str_replace('/', '\\', $parsed->name).'Command';
			} else {
				$class = '\\System\\Terminal\\Command\\'.$parsed->name.'Command';
			}
			
			$command = new $class();
			
			if ($command instanceof CommandInterface && $command instanceof Command) {
				$command->command = $parsed->command;
				$command->name = $parsed->name;
				$command->value = $parsed->value;
				$command->parameters = $parsed->parameters;
				
				return $command;
			} else {
				throw new Exception();
			}
		}
		
		public static function parse($command) {
			$parsed = new Command();
			
			$parsed->command = $command;
			$sections = explode(' -', $command);
			$base = explode(' ', $sections[0]);
			
			if (count($base) == 1) {
				$parsed->name = $base[0];
			} else {
				list($parsed->name, $parsed->value) = $base;
			}
			
			if (count($sections) > 1) {
				$first = true;
				
				foreach ($sections as $section) {
					if ($first) {
						$first = false;
						continue;
					}
					
					$parameter = explode(' ', $section);
					
					if (count($parameter) == 1) {
						$parsed->parameters[$parameter[0]] = null;
					} else {
						$parsed->parameters[$parameter[0]] = $parameter[1];
					}
				}
			}
			
			return $parsed;
		}
		
		protected function getParameter($name) {
			if (array_key_exists($name, $this->parameters)) {
				if (empty($this->parameters[$name])) {
					return true;
				} else {
					return $this->parameters[$name];
				}
			} else {
				return false;
			}
		}
	}
?>