<?php
    namespace gOPF;
    use \System\Filesystem;
    use \System\Queue\Element;

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
         * @var string
         */
        const MIGRATION_PATH = 'migrations';

        /**
         * Migrations database name
         * @var string
         */
        const MIGRATIONS_DATABASE = 'migrations';

        /**
         * Migration class name
         * @var string
         */
        const MIGRATION_CLASS = 'Migration';

        /**
         * Migrations namespace
         * @var string
         */
        const MIGRATION_NAMESPACE = 'Migrations';

        /**
         * @var string
         */
        private $path;

        /**
         * @var \System\Database\EngineInterface
         */
        private $database;

        public function __construct() {
            \System\Loader::registerReservedNamespace(new \System\Loader\NS(self::MIGRATION_NAMESPACE, __APPLICATION_PATH.DIRECTORY_SEPARATOR.self::MIGRATION_PATH));

            $this->path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.self::MIGRATION_PATH;
            $this->database = \System\Core::instance()->database->engine();
        }

        public function getAvailableMigrations() {
            $queue = new \System\Queue();

            if (Filesystem::checkDirectory($this->path)) {
                $migration = 0;

                while (true) {
                    try {
                        $class = '\\'.self::MIGRATION_NAMESPACE.'\\'.self::MIGRATION_CLASS.$migration;
                        $object = new $class($this->database->handler());

                        if ($object instanceof \gOPF\gDMT\MigrationInterface) {
                            $queue->push(new Element($object->getMigrationNumber(), $object));
                        }

                        $migration++;
                    } catch (\Exception $e) {
                        break;
                    }
                }
            }

            return $queue;
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
    }
?>