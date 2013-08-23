<?php 
	namespace System\Terminal\Command;
	use \System\Config;
	use \System\Terminal\Status;
	use \System\Terminal\Help;
	
	/**
	 * Terminal command: login (validates user)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class loginCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			return new Help(Help::INTERNAL);
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			$status = $session->pull();
			
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
			
			$session->push($status);
			
			if ($status->logged) {
				$command = self::parse('welcome');
				$command = self::factory($command);
				$command->execute();
			}
		}
		
		/**
		 * Asks user for username
		 * 
		 * @param \System\Terminal\Status $status Current terminal status
		 * @return \System\Terminal\Status Updated terminal status
		 */
		private function getLogin(Status $status) {
			$status->clear = true;
			$status->prompt = 'Login: ';
			$status->prefix = 'login -user ';
			$status->type = Status::TEXT;
			
			return $status;
		}
		
		/**
		 * Asks user for password
		 *
		 * @param \System\Terminal\Status $status Current terminal status
		 * @return \System\Terminal\Status Updated terminal status
		 */
		private function getPassword(Status $status) {
			$status->prompt = 'Password: ';
			$status->prefix = 'login -password ';
			$status->type = Status::PASSWORD;
			
			return $status;
		}
		
		/**
		 * Validates user
		 *
		 * @param \System\Terminal\Status $status Current terminal status
		 * @return \System\Terminal\Status Updated terminal status
		 */
		private function validate(Status $status) {
			$config = Config::factory('terminal.ini', Config::SYSTEM);
			
			$user = $config->getArrayValue('users', $status->user);
			
			if (empty($user) || $user != sha1($this->getParameter('password'))) {
				sleep(1);
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