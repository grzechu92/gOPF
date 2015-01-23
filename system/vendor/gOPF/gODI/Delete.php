<?php
	namespace gOPF\gODI;

    /**
     * gODI Delete statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	class Delete extends Statement {
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\SortTrait;
		use \gOPF\gODI\Traits\LimitTrait;

        /**
         * @see \gOPF\gODI\Statement::build()
         */
        public function build() {
			$parts = array(
				'DELETE',
				'FROM '.$this->table,
				(!empty($this->search) ? 'WHERE '.implode(' ', $this->search) : ''),
				(!empty($this->orderBy) ? 'ORDER BY '.implode(' ', array($this->orderBy, $this->orderType)) : ''),
				(($this->limit > 0) ? 'LIMIT '.$this->offset.', '.$this->limit : '')
			);
			
			return trim(implode(' ', $parts));
		}

        /**
         * Execute statement
         *
         * @return int Affected records
         */
        public function make() {
			return $this->execute(Statement::RETURN_ROWS);
		}
	}
?>