<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement\Condition;

    /**
     * SearchTrait - allows to filter query results
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	trait SearchTrait {
        /**
         * Condition statements array
         * @var \gOPF\gODI\Statement\Condition[]
         */
        private $search = array();

        /**
         * Creates condition statement
         *
         * @param string $field Compared field name
         * @return \gOPF\gODI\Statement\Condition
         */
        public function where($field) {
			return $this->search[] = new Condition($this, $field);
		}

        /**
         * Creates condition statement with OR prefix
         *
         * @param string $field Compared field name
         * @return \gOPF\gODI\Statement\Condition
         */
		public function orWhere($field) {
			return $this->search[] = new Condition($this, $field, 'OR');
		}

        /**
         * Creates condition statement with AND prefix
         *
         * @param string $field Compared field name
         * @return \gOPF\gODI\Statement\Condition
         */
		public function andWhere($field) {
			return $this->search[] = new Condition($this, $field, 'AND');
		}
	}
?>