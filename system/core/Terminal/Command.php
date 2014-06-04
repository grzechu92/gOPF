<?php 
	namespace System\Terminal;
	use \System\Config;
	
	/**
	 * Base terminal command object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
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
		
		/**
		 * Raw command
		 * @var string
		 */
		public $command;
		
		/**
		 * Parsed command name
		 * @var string
		 */
		public $name;
		
		/**
		 * Parsed command value
		 * @var string
		 */
		public $value;
		
		/**
		 * Parsed command parameters
		 * @var array
		 */
		public $parameters = array();
		
		/**
		 * Creates Command object from parsed data, and returns it
		 * 
		 * @param \System\Terminal\Command $parsed Parsed command data
		 * @return \System\Terminal\CommandInterface Command ready to execute
		 * @throws \System\Terminal\Exception
		 */
		public static function factory(Command $parsed) {
			$config = Config::factory('terminal.ini', Config::SYSTEM);
			$commands = $config->get('commands');
			
			if ($parsed->name[0] != '\\') {
				if (array_key_exists($parsed->name, $commands)) {
					$class = $commands[$parsed->name];
				} else {
					$class = '\\System\\Terminal\\Command\\'.$parsed->name.'Command';
				}
			} else {
				$class = $parsed->name;
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
		
		/**
		 * Parses command string and returns Command object filled with parsed data
		 * 
		 * @param string $command Raw comand
		 * @return \System\Terminal\Command Parsed command data
		 */
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
					
					$parameter = explode(' ', $section, 2);
					
					if (count($parameter) == 1) {
						$parsed->parameters[$parameter[0]] = null;
					} else {
						$parsed->parameters[$parameter[0]] = $parameter[1];
					}
				}
			}
			
			return $parsed;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::onInstall()
		 */
		public function onInstall() {}
		
		/**
		 * @see \System\Terminal\CommandInterface::onUninstall()
		 */
		public function onUninstall() {}

        /**
         * @see \System\Terminal\CommandInterface::getName();
         */
        public function getName() {
            $clean = str_replace(\System\Terminal::COMMAND_SUFFIX, '', get_called_class());
            $exploded = explode('\\', $clean);

            return $exploded[count($exploded) - 1];
        }
		
		/**
		 * Checks if parameter has been passed with command
		 * If is passed, but empty, returns true
		 * If is passed and has value, returns value
		 * If isn't passed, returns false
		 * 
		 * @param string $name Parameter name 
		 * @return mixed Parameter data
		 */
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