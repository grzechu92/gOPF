<?php
	namespace System\Terminal\Command;
	use \System\Filesystem;
		
	/**
	 * Terminal command: cat (displays file content)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class catCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('Display file content');
			$help->add(new \System\Terminal\Help\Line('cat [filename]', 'show content of pointed file'));
			
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			
			$path = $this->buildPath($this->value, $session);
			
			if (!$this->checkFile($path)) {
				$session->buffer('Requested file doesn\'t exists!');
			} else {
				$session->buffer(htmlspecialchars(Filesystem::read($path)));
			}
		}
		
		/**
		 * Checks if file exists in path
		 * 
		 * @param string $path Path to check
		 * @return bool Is path valid?
		 */
		private function checkFile($path) {
			return Filesystem::checkFile($path);
		}
		
		/**
		 * Builds file path
		 * 
		 * @param string $file Filename
		 * @param \System\Terminal\Session $session Current terminal session
         * @return string Built path
		 */
		private function buildPath($file, \System\Terminal\Session $session) {
			return str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, __ROOT_PATH . $session->pull()->path . $file);
		}
	}
?>