<?php
	namespace System\Terminal\Command;

	/**
	 * Terminal command: clean (cleans various files in framework)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Clean extends \System\Terminal\Command {
		/**
		 * Array with ignored filenames
		 * @var array
		 */
		private $ignored = array('.', '..', '.git', '.gitignore', '.settings', '.project', '.buildpath', '.keep');
		
		/**
		 * Array with directories to clean
		 * @var array
		 */
		private $directories = array(
			'/application/log',
			'/system/log',
			'/system/var'
		);
		
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('Prepare to build, remove session files, logs etc.');
			$help->add(new \System\Terminal\Help\Line('clean -force', 'clean without asking'));
			
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$force = $this->getParameter('force');
			
			foreach ($this->directories as $directory) {
				$this->buffer('Cleaning directory '.$directory);
				
				foreach (new \RecursiveDirectoryIterator(__ROOT_PATH . $directory) as $file) {
					/** @var $file \RecursiveDirectoryIterator */
					if (in_array($file->getFilename(), $this->ignored)) {
						continue;
					}

					if ($force || $this->ask('Remove '.$file.'?', ['y', 'n']) == 'y') {
						\System\Filesystem::remove($file, $file->isDir());
						$this->buffer(' - '.$file);
					}
				}

				usleep(100000);
			}
		}
	}
?>