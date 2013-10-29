<?php
	namespace System\Terminal\Command;
	use \System\Filesystem;
	use \System\Core;
	use \System\Terminal\Session;
	use \System\Terminal\Help\Line;
		
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
		
		/**
		 * Path to output directory
		 * @var string
		 */
		private $output;
		
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$lines = array();
			
			$help = new \System\Terminal\Help('Build framework version');
			$lines[] = new Line('build', 'upgrades build value in Core class');
			$lines[] = new Line('build -output [path]', 'creates clean instance of framework in specified directory');
			$lines[] = new Line('build -version [version]', 'upgrades build value and version in Core class');
			
			$help->addLines($lines);
			
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			$this->output = $this->getParameter('output');
			$build = date('ymdhis');
			$version = $this->getParameter('version');
			
			if (!$version) {
				$version = Core::VERSION;
			}
			
			if ($this->output) {
				$this->createInstance($session, $version, $build);
			} else {
				$this->upgradeCore($session, $version, $build);
			}
		}
		
		/**
		 * Upgrades core to specified version and build
		 * 
		 * @param \System\Terminal\Session $session Terminal session
		 * @param string $version Version to upgrade
		 * @param string $build Build to upgrade
		 */
		private function upgradeCore(Session $session, $version, $build) {
			$session->buffer('Upgrading core to version: '.$version.' (build '.$build.')');
			
			$path = __ROOT_PATH.'/system/core/Core.php';
			
			$content = Filesystem::read($path);
			$content = str_replace([Core::VERSION, Core::BUILD], [$version, $build], $content);
			
			Filesystem::write($path, $content);
			sleep(3);
		}
		
		/**
		 * Creates framework clean clone in specified directory
		 * 
		 * @param \System\Terminal\Session $session Terminal session
		 * @param string $version Framework version
		 * @param string $build Framework build
		 */
		private function createInstance(Session $session, $version, $build) {
			if (!Filesystem::checkDirectory($this->output)) {
				$session->buffer('Wrong output path!');
				return;
			}
				
			$this->output = $this->output.'/'.$version.' (build '.$build.')';
			$session->clear = true;
			$session->buffer('Creating output directory: '.$this->output);
				
			sleep(2);
				
			Filesystem::mkdir($this->output);
			$this->iterate($session, __ROOT_PATH);
				
			$session->buffer('Fixing access rights...');
			Filesystem::chmod($this->output, 0777, true);
		}
		
		/**
		 * Iterates through directory
		 * 
		 * @param \System\Terminal\Session $session terminal session
		 * @param string $directory Directory to iterate
		 * @param string $context Target directory context
		 */
		private function iterate(Session $session, $directory, $context = false) {
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