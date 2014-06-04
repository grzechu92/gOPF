<?php
    namespace gOPF;
    use \System\Filesystem;

    /**
     * gDMT - gDMT Database Migration Tool
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class gDMT {
        /**
         * Migration files path
         */
        const MIGRATION_PATH = '/migrations/';

        /**
         * Migrations database name
         */
        const MIGRATIONS_DATABASE = 'migrations';

        /**
         * @var string
         */
        private $path;

        /**
         * @var \System\Database\EngineInterface
         */
        private $database;

        public function __construct() {
            $this->path = __APPLICATION_PATH.self::MIGRATION_PATH;
            $this->database = \System\Core::instance()->database->connection();
        }

        public function getAvailableMigrations() {
            $migrations = array();

            if (Filesystem::checkDirectory($this->path)) {
                foreach (new \DirectoryIterator($this->path) as $file) {
                    if ($file->isDot()) {
                        continue;
                    }

                    $migrations[] = $file->getFilename();
                }
            }

            return $migrations;
        }

        public function initializeDatabaseStructure() {
            $this->database->query('CREATE TABLE IF NOT EXISTS `'.self::MIGRATIONS_DATABASE.'` (`migration` int(11) NOT NULL)');
        }

        public function removeDatabaseStructure() {
            $this->database->query('DROP DATABASE `'.self::MIGRATIONS_DATABASE.'`');
        }

        public function commit($number) {
            $this->database->query('INSERT INTO `'.self::MIGRATIONS_DATABASE.'` (`migration`) VALUES ('.$number.')');
        }

        public function isExecuted($number) {
            $result = $this->database->query('SELECT COUNT(*) as `amount` FROM `'.self::MIGRATIONS_DATABASE.'` WHERE `migration` = '.$number);

            return $result->amount == 1;
        }

        public function executeMigration($number) {

        }

        private function getMigrationsList() {

        }
    }
?>