<?php
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Terminal;
	use \System\Terminal\Status;
	use \System\Terminal\Help\Line;
	
	/**
	 * Terminal command: password (allows to change user password)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class passwordCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$lines = array();
			$help = new \System\Terminal\Help('Change password');
		
			$lines[] = new Line('password', 'begins procedure for current logged user');
			$lines[] = new Line('password [user]', 'begins procedure for selected user');
				
			$help->addLines($lines);
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$initialized = false;
			
			$session = self::$session;
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
		 * Changes user password
		 * 
		 * @param \System\Terminal\Status $status Terminal status
		 * @param string $user User to change password
		 * @return \System\Terminal\Status Modified terminal status
		 */
		private function changeUserPassword(Status $status, $user) {
			$error = false;
			$config = Config::factory('terminal.ini', Config::APPLICATION, true);

            $users = $config->getArrayValue('users', $user);

			if (empty($users)) {
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