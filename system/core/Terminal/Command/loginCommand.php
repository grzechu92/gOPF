<?php 
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Terminal\Session;
	use \System\Terminal\Status;
	
	class loginCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute(Session $session) {
			if ($this->getParameter('initialize')) {
				$this->getLogin($session);
				return;
			}
			
			if ($this->getParameter('user')) {
				$session->user = $this->getParameter('user');
				$this->getPassword($session);
				return;
			}
			
			if ($this->getParameter('password')) {
				$this->validate($session);
				return;
			}
		}
		
		private function getLogin(Session $session) {
			$session->prompt = 'Login: ';
			$session->prefix = 'login -user ';
			$session->type = Status::TEXT;
		}
		
		private function getPassword(Session $session) {
			$session->prompt = 'Password: ';
			$session->prefix = 'login -password ';
			$session->type = Status::PASSWORD;
		}
		
		private function validate(Session $session) {
			sleep(1);
			
			$config = Config::factory('terminal.ini', Config::SYSTEM);
			
			$users = $config->getContent()['users'];
			
			if (!isset($users[$session->user]) || $users[$session->user] != sha1($this->getParameter('password'))) {
				$this->getLogin($session);
				$session->buffer('Access denied!');
			} else {
				$session->prefix = null;
				$session->prompt = null;
				$session->logged = true;
				$session->type = Status::TEXT;
			}
		}
	}
?>