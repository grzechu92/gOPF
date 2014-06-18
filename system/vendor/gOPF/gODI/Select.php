<?php
	namespace gOPF\gODI;

    /**
     * gODI Select statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	class Select extends Statement {
		use \gOPF\gODI\Traits\FieldsTrait;
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\LimitTrait;
		use \gOPF\gODI\Traits\SortTrait;
        use \gOPF\gODI\Traits\JoinTrait;

        /**
         * @see \gOPF\gODI\Statement::build()
         */
        public function build() {
			$parts = array(
				'SELECT '.implode(', ', $this->fields),
				'FROM '.$this->table,
                (!empty($this->join) ? implode(' ', $this->join) : ''),
				(!empty($this->search) ? 'WHERE '.implode(' ', $this->search) : ''),
				(!empty($this->orderBy) ? 'ORDER BY '.implode(' ', array($this->orderBy, $this->orderType)) : ''),
				(($this->limit > 0) ? 'LIMIT '.$this->offset.', '.$this->limit : '')
			);
			
			return trim(implode(' ', $parts));
		}

        /**
         * Get all results
         *
         * @return array Query results
         */
        public function all() {
			return $this->execute(true);
		}

        /**
         * Get specified number of results
         *
         * @param int $limit Number of results
         * @param int $offset Offset from get results
         * @return array|mixed Result of array of results
         */
        public function get($limit = 1, $offset = 0) {
			$this->limit($limit, $offset);
				
			return $this->execute(true);
		}
	}
?>