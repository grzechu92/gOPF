<?php
	namespace System\Terminal\Command;
	use \System\Filesystem;
		
	/**
	 * Terminal command: build (creates build of framework)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class buildCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * Array with ignored filenames
		 * @var array
		 */
		private $ignored = array('.', '..', '.git', '.gitignore', '.settings', '.project', '.buildpath');
		
		private $output;
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('Work in progress');
			
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			$this->output = $this->getParameter('output');
			
			if (!$this->output) {
				$session->buffer('Wrong parameters!');
				return;
			}
			
			if (!Filesystem::checkDirectory($this->output)) {
				$session->buffer('Wrong output path!');
				return;
			}
			
			$this->output = $this->output.'/'.\System\Core::VERSION;
			$session->clear = true;
			$session->buffer('Creating output directory: '.$this->output);
			
			sleep(2);
			
			Filesystem::mkdir($this->output);
			$this->iterate($session, __ROOT_PATH);
			
			$session->buffer('Fixing chmod\'s');
			Filesystem::chmod($this->output, 0777, true);
		}	
		
		private function iterate(\System\Terminal\Session $session, $directory, $context = false) {
			if (!$context) {
				$context = $this->output;
			}
			
			$iterator = new \RecursiveDirectoryIterator($directory);
			
			foreach ($iterator as $file) {
				if (in_array($file->getFilename(), $this->ignored)) {
					continue;
				}
				
				$output = '';
				$name = str_replace(__ROOT_PATH, '', $file);
				$destination = $context.DIRECTORY_SEPARATOR.$file->getFilename();
				
				if ($file->isDir()) {
					$output .= str_pad('Creating directory: '.$name, 100, ' ', STR_PAD_RIGHT);
					Filesystem::mkdir($destination);
				} else {
					$output .= str_pad('Copying file: '.$name, 100, ' ', STR_PAD_RIGHT);
					Filesystem::copy($file, $destination);
				}

				if ($file->isDir() || Filesystem::checksum($file) == Filesystem::checksum($destination)) {
					$output .= '[<bold><green> OK </green></bold>]';
				} else {
					$output .= '[<bold><red>FAIL</red></bold>]';
				}
				
				$session->buffer($output);
				
				if ($file->isDir()) {
					$this->iterate($session, $file, $destination);
				}
					
				usleep(50000);
			}
		}
	}
?>