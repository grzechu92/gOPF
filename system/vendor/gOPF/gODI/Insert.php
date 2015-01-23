<?php
	namespace gOPF\gODI;

	/**
	 * gODI Insert statement
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Insert extends Statement {
		use \gOPF\gODI\Traits\ValuesTrait;

		/**
		 * @see \gOPF\gODI\Statement::build()
		 */
		public function build() {
			$parts = array(
				'INSERT INTO '.$this->table,
				'SET '.implode($this->values, ', ')
			);
			
			return trim(implode(' ', $parts));
		}

		/**
		 * Execute statement
		 *
		 * @return int Last insert ID
		 */
		public function make() {
			return $this->execute(Statement::RETURN_ID);
		}
	}
?>