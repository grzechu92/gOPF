<?php
    namespace gOPF\gODI;

    /**
     * gODI transaction component
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Transaction {
        /**
         * PDO connector
         * @var \PDO
         */
        private $PDO;

        /**
         * Initializes statement
         *
         * @param \PDO $PDO PDO connector
         */
        public function __construct(\PDO $PDO) {
            $this->PDO = $PDO;
        }

        /**
         * Begin transaction
         */
        public function begin() {
            $this->PDO->beginTransaction();
        }

        /**
         * Commit transaction
         */
        public function commit() {
            $this->PDO->commit();
        }

        /**
         * Revert transaction
         */
        public function revert() {
            $this->PDO->rollBack();
        }
    }
?>