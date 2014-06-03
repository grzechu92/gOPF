<?php
	namespace System\Filesystem;
	use System\Filesystem;
	use System\I18n;
	use System\Filesystem\Exception;
	
	/**
	 * File class for Filesystem managing class
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class File {
		/**
		 * Open file for reading
		 * @var string
		 */
		const WRITE = 'w+';
		
		/**
		 * Open file for appending data
		 * @var string
		 */
		const APPEND = 'a';
		
		/**
		 * Open file for reading
		 * @var string
		 */
		const READ = 'a+';
		
		/**
		 * Lock file for reading purpose
		 * @var int
		 */
		const LOCK_READING = 1;
		
		/**
		 * Lock file for writing  purpose
		 * @var int
		 */
		const LOCK_WRITING = 2;
		
		/**
		 * File handle, returned by fopen() function
		 * @var mixed
		 */
		private $handle;
		
		/**
		 * Path to file
		 * @var string
		 */
		private $path;
		
		/**
		 * Creates file handler and sets required data into object
		 * 
		 * @param string $path Path to file
		 * @param int $mode File open mode
		 * @param bool $lock Lock file while processing
		 */
		public function __construct($path, $mode, $lock = false) {
			$this->path = $path;
			$this->handle = $this->getFileHandle($mode);
		}
		
		/**
		 * Removes file handler from memory
		 * 
		 * @throws \System\Filesystem\Exception
		 */
		public function __destruct() {
			if (!fclose($this->handle)) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_CLOSE', array($this->path)), $this->path);
			}
		}
		
		/**
		 * Locks file to selected mode
		 * 
		 * @param int $mode File lock mode (File::LOCK_READING, File::LOCK_WRITING)
		 * @throws \System\Filesystem\Exception
		 */
		public function lock($mode) {
			if (!flock($this->handle, $mode)) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_LOCK', array($this->path)), $this->path);
			}
		}
		
		/**
		 * Returns content of file
		 * 
		 * @return string File content
		 * @throws \System\Filesystem\Exception
		 */
		public function getContent() {
			$size = Filesystem::getSize($this->path);
			
			try {
				$content = @fread($this->handle, $size);
			} catch (Exception $exception) {
				$content = null;
			}
			
			if ($content === false && $size > 0) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_READ', array($this->path)), $this->path);
			}
			
			return $content;
		}
		
		/**
		 * Sets file content
		 * 
		 * @param string $content Content to set in file
		 * @throws \System\Filesystem\Exception
		 */
		public function setContent($content) {
			if (fwrite($this->handle, $content) === false) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_WRITE', array($this->path)), $this->path);
			}
		}
		
		/**
		 * Returns file handle, by fopen() function
		 * 
		 * @param int $mode File open mode (File::READ, File::WRITE, File::APPEND)
		 * @return mixed File handle
		 * @throws \System\Filesystem\Exception
		 */
		private function getFileHandle($mode) {
			$handle = @fopen($this->path, $mode);
			
			if (!$handle) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_OPEN', array($this->path)), $this->path);
			}
			
			return $handle;
		}
	}
?>