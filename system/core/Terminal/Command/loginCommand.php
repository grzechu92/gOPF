<?php 
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Terminal\Status;
	
	class loginCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute() {
			$session = self::$session;
			$status = $session->get();
			
			if ($this->getParameter('initialize')) {
				$status = $this->getLogin($status);
			}
			
			if ($this->getParameter('user')) {
				$status->user = $this->getParameter('user');
				$status = $this->getPassword($status);
			}
			
			if ($this->getParameter('password')) {
				$status = $this->validate($status);
			}
			
			$session->set($status);
		}
		
		private function getLogin(Status $status) {
			$status->prompt = 'Login: ';
			$status->prefix = 'login -user ';
			$status->type = Status::TEXT;
			
			return $status;
		}
		
		private function getPassword(Status $status) {
			$status->prompt = 'Password: ';
			$status->prefix = 'login -password ';
			$status->type = Status::PASSWORD;
			
			return $status;
		}
		
		private function validate(Status $status) {
			sleep(1);
			
			$config = Config::factory('terminal.ini', Config::SYSTEM);
			
			$users = $config->getContent()['users'];
			
			if (!isset($users[$status->user]) || $users[$status->user] != sha1($this->getParameter('password'))) {
				$this->getLogin($status);
				$status->buffer('Access denied!');
			} else {
				$status->prefix = null;
				$status->prompt = null;
				$status->logged = true;
				$status->type = Status::TEXT;
			}
			
			return $status;
		}
	}
?>