<?php
	namespace gOPF\gODI\Traits;

    /**
     * LimitTrait - allows to limit query results
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	trait LimitTrait {
        /**
         * Query results limit
         * @var int
         */
        private $limit = 0;

        /**
         * Query results offset
         * @var int
         */
        private $offset = 0;

        /**
         * Allows to limit query results
         *
         * @param int $limit Query amount limit
         * @param int $offset Query results offset
         * @return \gOPF\gODI\Statement
         */
        public function limit($limit, $offset = 0) {
			$this->limit = $limit;
			$this->offset = $offset;
				
			return $this;
		}
	}
?>