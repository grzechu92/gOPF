<?php
	namespace gOPF\gDMT;

	/**
	 * Abstract class for migrations with required stuff
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Migration {
		/**
		 * Database handler
		 * @var mixed
		 */
		protected $database;

		/**
		 * @see \gOPF\gDMT\MigrationInterface::__construct()
		 */
		public function __construct($database) {
			$this->database = $database;
		}

		/**
		 * @see \gOPF\gDMT\MigrationInterface::getMigrationNumber()
		 */
		public function getMigrationNumber() {
			return get_called_class()[strlen(get_called_class()) - 1] * 1;
		}
	}
?>