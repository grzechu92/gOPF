<?php
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: clean (cleans various files in framework)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class cleanCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * Array with ignored filenames
		 * @var array
		 */
		private $ignored = array('.', '..', '.git', '.gitignore', '.settings', '.project', '.buildpath');
		
		/**
		 * Array with directories to clean
		 * @var unknown
		 */
		private $directories = array(
			'/application/log',
			'/application/var/cache',
			'/application/var/session',
			'/application/var/storage',
			'/system/log',
			'/system/var'
		);
		
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('Prepare to build, remove session files, logs etc.');
			
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = \System\Terminal::$session;
			
			foreach ($this->directories as $directory) {
				$session->buffer('Cleaning directory '.$directory);
				
				$iterator = new \RecursiveDirectoryIterator(__ROOT_PATH.$directory);
				
				foreach ($iterator as $file) {
					if (in_array($file->getFilename(), $this->ignored)) {
						continue;
					}
					
					\System\Filesystem::remove($file, $file->isDir());
				}

				usleep(800*1000);
			}
		}
	}
?>