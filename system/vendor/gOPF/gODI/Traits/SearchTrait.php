<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement\Where;

    /**
     * SearchTrait - allows to filter query results
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	trait SearchTrait {
        /**
         * Where statements array
         * @var \gOPF\gODI\Statement\Where[]
         */
        private $search = array();

        /**
         * Creates Where statement
         *
         * @param string $field Compared field name
         * @return \gOPF\gODI\Statement\Where
         */
        public function where($field) {
			return $this->search[] = new Where($this, $field);
		}

        /**
         * Creates Where statement with OR prefix
         *
         * @param string $field Compared field name
         * @return \gOPF\gODI\Statement\Where
         */
		public function orWhere($field) {
			return $this->search[] = new Where($this, $field, 'OR');
		}

        /**
         * Creates Where statement with AND prefix
         *
         * @param string $field Compared field name
         * @return \gOPF\gODI\Statement\Where
         */
		public function andWhere($field) {
			return $this->search[] = new Where($this, $field, 'AND');
		}
	}
?>