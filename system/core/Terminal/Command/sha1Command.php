<?php 
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: sha1 (generates checksum of value)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class sha1Command extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			self::$session->buffer(sha1($this->value));
		}
	}
?>