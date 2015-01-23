<?php
	namespace gOPF\gODI\Traits;
    use gOPF\gODI\Statement\Bind;

    /**
     * LimitTrait - allows to limit query results
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	trait LimitTrait {
        /**
         * Is statement limitable
         * @var bool
         */
        private $limitable = false;

        /**
         * Allows to limit query results
         *
         * @param int $limit Query amount limit
         * @param int $offset Query results offset
         * @return \gOPF\gODI\Statement
         */
        public function limit($limit, $offset = 0) {
            $this->limitable = true;

            $this->bind(new Bind($limit, Bind::INT, '_limit'));
            $this->bind(new Bind($offset, Bind::INT, '_offset'));

			return $this;
		}
	}
?>