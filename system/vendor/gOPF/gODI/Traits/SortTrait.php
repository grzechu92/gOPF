<?php
	namespace gOPF\gODI\Traits;

	/**
	 * SortTrait - allows to sort query results
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	trait SortTrait {
		/**
		 * Sort by field
		 * @var string
		 */
		private $orderBy;

		/**
		 * Sort type
		 * @var string
		 */
		private $orderType;

		/**
		 * Allows to sort query results
		 *
		 * @param string $field Field to sort with
		 * @param string $type Sort type (ASC, DESC)
		 * @return \gOPF\gODI\Statement Fluid interface
		 */
		public function by($field, $type = 'ASC') {
			$this->orderBy = $field;
			$this->orderType = $type;
			
			return $this;
		}
	}
?>