<?php
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Terminal;
	use \System\Terminal\Status;
	
	class passwordCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		private $users;
		
		public function execute() {
			$initialized = false;
			
			$session = Terminal::$session;
			$status = $session->pull();
			
			if (!isset($status->storage['user'])) {
				$user = (empty($this->value) ? $status->user : $this->value);
				$status->storage['user'] = $user;
			} else {
				$user = $status->storage['user'];
			}
			
			if ($this->getParameter('current') || !empty($this->value)) {
				$initialized = true;
				$status->prefix = 'password -new ';
				$status->prompt = 'Type new password: ';
				
				$status->storage['current'] = sha1($this->getParameter('current'));
			}
			
			if ($this->getParameter('new')) {
				$initialized = true;
				$status->prefix = 'password -repeat ';
				$status->prompt = 'Repeat new password: ';
				
				$status->storage['new'] = sha1($this->getParameter('new'));
			}
			
			if ($this->getParameter('repeat')) {
				$status->storage['repeat'] = sha1($this->getParameter('repeat'));
				
				$status = $this->changeUserPassword($status, $user);
				
				$status->prefix = null;
				$status->prompt = null;
				$status->type = Status::TEXT;
				
				$session->push($status);
				return;
			}
			
			if (!$initialized) {
				$status->prefix = 'password -current ';
				$status->prompt = 'Current password: ';
			}
			
			$status->type = Status::PASSWORD;
			$session->push($status);
		}
		
		/**
		 * 
		 * @param \System\Terminal\Status $status
		 * @param string $user
		 * @return \System\Terminal\Status
		 */
		private function changeUserPassword(Status $status, $user) {
			$error = false;
			$config = Config::factory('terminal.ini', Config::SYSTEM, true);
			
			if (empty($config->getArrayValue('users', $user))) {
				$error = true;
				$status->buffer('User do not exists');
			}
			
			if (!$error && $status->storage['new'] != $status->storage['repeat']) {
				$error = true;
				$status->buffer('Passwords do not match');
			}
			
			if (!$error && isset($status->storage['current']) && $status->storage['current'] != $config->getArrayValue('users', $user)) {
				sleep(2);
				
				$error = true;
				$status->buffer('Wrong password');
			}
			
			if (!$error) {
				$config->setArrayValue('users', $user, $status->storage['new']);
				$status->buffer('Password changed!');
			}
			
			$status->storage = array();
			return $status;
		}
	}
?>