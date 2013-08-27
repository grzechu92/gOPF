<?php 
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Terminal\Help;
	use \System\Terminal\Help\Line;
	
	class helpCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function help() {
			return new Help('Guess what it does...');
		}
		
		public function execute() {
			$help = new Help('Availiable commands');
			$config = Config::factory('terminal.ini', Config::SYSTEM);
			$commands = $config->get('commands');
			
			foreach ($commands as $name=>$class) {
				$class = new $class();				
				$line = new Line($name, $class->help()->description);
				$help->add($line);
			}
			
			\System\Terminal::$session->buffer($help->build());
		}
	}
?>