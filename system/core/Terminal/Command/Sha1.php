<?php 
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: sha1 (generates checksum of value)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Sha1 extends \System\Terminal\Command {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('Generate SHA1 checksum');
			$help->add(new \System\Terminal\Help\Line('sha1 [text]', 'generate SHA1 checksum from source'));
		
			return $help;
		}
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$this->buffer(sha1($this->getValue()));
		}
	}
?>