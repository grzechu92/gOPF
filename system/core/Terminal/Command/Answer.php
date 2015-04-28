<?php
	namespace System\Terminal\Command;
	use \System\Terminal\Help;
	use \System\Core;

	/**
	 * Terminal command: answer (internal command for user asking)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Answer extends \System\Terminal\Command {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			return new Help(Help::INTERNAL);
		}

		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			\System\Storage::set(self::ANSWER_CONTAINER_NAME, $this->getValue());
		}
	}
?>