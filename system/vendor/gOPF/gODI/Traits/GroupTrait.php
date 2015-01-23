<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement\Condition;

	/**
	 * GroupTrait - allows to select field or fields in query
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	trait GroupTrait {
		/**
		 * Group by column name
		 * @var string
		 */
		private $group;

		/**
		 * Condition statements array
		 * @var \gOPF\gODI\Statement\Condition[]
		 */
		private $having = array();

		/**
		 * Groups by column
		 *
		 * @param string $column Column name
		 * @return \gOPF\gODI\Statement
		 */
		public function group($column) {
			$this->group = $column;

			return $this;
		}

		/**
		 * Creates condition statement
		 *
		 * @param string $field Compared field name
		 * @return \gOPF\gODI\Statement\Condition
		 */
		public function having($field) {
			return $this->having[] = new Condition($this, $field);
		}
	}
?>