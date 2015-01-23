<?php
	namespace System\Database;

	/**
	 * Database transaction interface
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface TransactionInterface {
		/**
		 * Revert transaction
		 */
		public function revert();

		/**
		 * Begin transaction
		 */
		public function begin();

		/**
		 * Commit transaction
		 */
		public function commit();

		/**
		 * Checks that if database is in transaction
		 *
		 *  @return bool Transaction status
		 */
		public function status();
	}
?>