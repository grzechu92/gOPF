<?php
	namespace System\Terminal;
	
	/**
	 * Terminal status data object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Status {
		/**
		 * Constant which defines password input type
		 * @var string
		 */
		const PASSWORD = 'password';
		
		/**
		 * Constant which defines normal input type
		 * @var string
		 */
		const TEXT = 'text';
		
		/**
		 * User terminal login
		 * @var string
		 */
		public $user;
		
		/**
		 * Custom prompt for terminal, if null prompt is builded from username, host and path
		 * @var string
		 */
		public $prompt;
		
		/**
		 * Terminal host
		 * @var string
		 */
		public $host;
		
		/**
		 * Terminal path, relative to __ROOT_PATH
		 * @var string
		 */
		public $path;
		
		/**
		 * Terminal output buffer (for pushing use buffer() method)
		 * @var string
		 */
		public $buffer;
		
		/**
		 * Terminal command prefix, allows execute prefilled command
		 * @var string
		 */
		public $prefix;
		
		/**
		 * Terminal input type (use constants TEXT and PASSWORD)
		 * @var string
		 */
		public $type = self::TEXT;
		
		/**
		 * Flag for terminal base initialization
		 * @var bool
		 */
		public $initialized = false;
		
		/**
		 * Flag for terminal user status
		 * @var bool
		 */
		public $logged = false;
		
		/**
		 * Flag for blocking terminal client while executing command
		 * @var bool
		 */
		public $processing = true;
		
		/**
		 * If setted, terminal client will clear printed content
		 * @var bool
		 */
		public $clear = false;
		
		/**
		 * Allows to drop long running commands, if supported
		 * @var bool
		 */
		public $abort = false;
		
		/**
		 * Microtime of last terminal status update
		 * @var float
		 */
		public $updated;
		
		/**
		 * Prefilled command
		 * @var string
		 */
		public $command = null;
		
		/**
		 * Completed command
		 * @var string
		 */
		public $complete = null;
		
		/**
		 * Terminal session storage
		 * @var array
		 */
		public $storage = array();
		
		/**
		 * Terminal session commands history
		 * @var array
		 */
		public $history = array();
		
		/**
		 * Array with uploaded files id's
		 * @var array
		 */
		public $files = array();
		
		/**
		 * Initializes and setts required status fields
		 */
		public function initialize() {
			$this->user = $_SERVER['REMOTE_ADDR'];
			$this->host = $_SERVER['HTTP_HOST'];
			$this->initialized = true;
			$this->path = '/';
			$this->logged = false;
			$this->processing = true;
			$this->clear = false;
			$this->abort = false;
			$this->prompt = null;
			$this->command = null;
			$this->complete = null;
			
			$this->storage = array();
			$this->history = array();
			$this->files = array();
		}
		
		/**
		 * Buffers content for terminal client
		 *  
		 * @param string $content Content to buffer
		 */
		public function buffer($content) {
			$this->buffer .= $content."\n";
		}
		
		/**
		 * Updates time of last status edit
		 */
		public function update() {
			$this->updated = microtime(true);
		}
	}
?>