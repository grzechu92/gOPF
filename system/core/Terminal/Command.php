<?php 
	namespace System\Terminal;
	
	class Command {
		public $command;
		public $name;
		public $value;
		public $parameters = array();
		
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
		
		public function extend(Command $command) {
			foreach ($command as $key=>$value) {
				$this->{$key} = $value;
			}
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