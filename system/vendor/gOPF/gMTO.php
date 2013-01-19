<?php 
	namespace gOPF;
	use \gOPF\gMTO\Thread;
	use \System\Storage;
	use \System\Request;
	
	/**
	 * gMTO - gMTO Multi Thread Operator
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class gMTO {
		/**
		 * Thread handler mode
		 * @var int
		 */
		const HANDLER = 1;
		
		/**
		 * Thread dispatcher mode
		 * @var int
		 */
		const DISPATCHER = 2;
		
		/**
		 * Thread status checking interval (in nanoseconds)
		 * @var int
		 */
		const INTERVAL = 100;
		
		/**
		 * Storage prefix
		 * @var string
		 */
		const PREFIX = '__THREAD-';
		
		/**
		 * Handler URL path
		 * @var string
		 */
		private $handler;
		
		/**
		 * Array with thread names and IDs
		 * @var array
		 */
		private $threads = array();
		
		/**
		 * cURL dispatcher
		 * @var resource
		 */
		private $dispatcher;
		
		/**
		 * Initiates different instances of object depends on mode
		 * 
		 * @param int $mode Work mode (gMTO::HANDLER or gMTO::DISPATCHER)
		 * @param string $handler URL path to handler (only in gMTO::DISPATHER mode)
		 */
		public function __construct($mode, $handler = '') {
			switch ($mode) {
				case self::DISPATCHER:
					$this->handler = $handler;
					break;
					
				case self::HANDLER;
					$this->handler();
					break;
			}
		}
		
		/**
		 * Deletes used storage containers
		 */
		public function __destruct() {
			foreach ($this->threads as $thread) {
				Storage::delete($thread);
			}
		}
		
		/**
		 * Adds new thread to do
		 * 
		 * @param Thread $thread Thread to do
		 */
		public function add(Thread $thread) {
			$id = $this->threads[$thread->name] = self::PREFIX.$thread->id;
			
			Storage::set($id, serialize($thread));
			Storage::write($id);
		}
		
		/**
		 * Processes all added threads
		 */
		public function process() {
			$this->dispatcher = curl_multi_init();
			
			foreach ($this->threads as $thread) {
				$c = curl_init();
				
				curl_setopt_array($c, array(
					CURLOPT_URL => __WWW_PATH.$this->handler,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => array('id' => $thread),
					CURLOPT_RETURNTRANSFER => 1
				));
				
				curl_multi_add_handle($this->dispatcher, $c);
			}
			
			$running = 0;
			
			do {
				usleep(self::INTERVAL);
				curl_multi_exec($this->dispatcher, $running);
			} while ($running > 0);
		}
		
		/**
		 * Receives data from processed thread
		 * 
		 * @param string $name Thread name
		 */
		public function receive($name) {
			return $this->getThread($this->threads[$name])->data;
		}
		
		/**
		 * Thread handler
		 */
		private function handler() {
			if (empty(Request::$post['id'])) {
				exit;
			}
			
			$id = Request::$post['id'];
			$thread = unserialize(Storage::get($id));
			
			$thread->data = $thread->process();
			
			Storage::set($id, serialize($thread));
			Storage::write($id);
			
			exit;
		}
		
		/**
		 * Returns synchronized data from thread
		 * @param string $id Thread ID
		 */
		private function getThread($id) {
			Storage::read($id);
			return unserialize(Storage::get($id));
		}
	}
?>