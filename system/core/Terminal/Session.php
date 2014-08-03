<?php
	namespace System\Terminal;
    use \System\Storage;
	
	/**
	 * Terminal session object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Session {
		/**
		 * Terminal storage name
		 * @var string
		 */
		const STORAGE = 'gOPF-TERMINAL';
		
		/**
		 * Client UUID for individual terminal instance per user
		 * @var string
		 */
		private $id;
		
        /**
         * Terminal sessions storage
         * @var \System\Storage\Container
         */
        private $container;
		
		/**
		 * Initializes terminal session object
		 */
		public function __construct() {
			$this->id = \System\Core::$UUID;
			$this->container = Storage::factory(self::STORAGE, Storage::APC);
		}
		
		/**
		 * Updates terminal status object in session storage
		 *
		 * @param \System\Terminal\Status $status Current terminal status
		 */
		public function push(Status $status) {
			$this->write($status);
		}

		/**
		 * Reads terminal status object from session storage
		 *
		 * @return \System\Terminal\Status Current terminal status
		 */
		public function pull() {
			return $this->read();
		}

		/**
		 * Allows to fast pull/buffer/push action
		 *
		 * @param string $content Content to buffer
		 */
		public function buffer($content) {
			$status = $this->read();
			$status->buffer($content);
			$status->update();

			$this->write($status);
		}

		/**
		 * Allows to add command into history
		 *
		 * @param string Command to save in history
		 */
		public function history($command) {
			$status = $this->read();

			if (count($status->history) === 0 || $status->history[count($status->history) - 1] != $command) {
				$status->history[] = $command;

				$this->write($status);
			}
		}

		/**
		 * Updates time of last status edit
		 */
		public function update() {
			$status = $this->read();
			$status->update();

            $this->write($status);
		}
		
		/**
		 * Reads current terminal session
         *
         * @return \System\Terminal\Status Terminal session status
		 */
		private function read() {
			$this->container->read();
            $sessions = $this->container->get();

            if (!isset($sessions[$this->id])) {
                $status = new Status();
                $this->write($status);
            } else {
                $status = $sessions[$this->id];
            }

            return $status;
        }
		
		/**
		 * Writes current terminal session
         *
         * @param \System\Terminal\Status $status Terminal session status
		 */
		private function write(Status $status) {
            $this->container->read();
            $sessions = $this->container->get();

            $sessions[$this->id] = $status;

            $this->container->set($sessions);
            $this->container->write();
		}
	}
?>