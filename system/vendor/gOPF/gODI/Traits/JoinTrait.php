<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement\Join;

	/**
	 * JoinTrait - allows to connect multiple tables
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	trait JoinTrait {
		/**
		 * Join statements array
		 * @var \gOPF\gODI\Statement\Join[]
		 */
		private $join = array();

		/**
		 * Creates Join statement
		 *
		 * @param string $table Table to join
		 * @param string $type Join type
		 * @return \gOPF\gODI\Statement\Join | \gOPF\gODI\Statement
		 */
		public function join($table, $type = Join::NATURAL_JOIN) {
			$join = $this->join[] = new Join($this, $table, $type);

			if ($type == Join::NATURAL_JOIN) {
				return $this;
			} else {
				return $join;
			}
		}
	}
?>