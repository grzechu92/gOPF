<?php
    namespace gOPF\gDMT;

    /**
     * Interface for user migrations
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    interface MigrationInterface {
        /**
         * Initializes Migration object with database
         *
         * @param mixed $database Database handler
         */
        public function __construct($database);

        /**
         * Returns user migration description
         *
         * @return string Migration description
         */
        public function getDescription();

        /**
         * Returns migration number
         *
         * @return int Migration number
         */
        public function getMigrationNumber();

        /**
         * Executes migration content
         */
        public function execute();
    }
?>