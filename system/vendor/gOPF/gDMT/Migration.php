<?php
    namespace gOPF\gDMT;

    abstract class Migration {
        /**
         * Database handler
         *
         * @var mixed
         */
        protected $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getMigrationNumber() {
            return get_called_class()[strlen(get_called_class()) - 1];
        }

        public static function getDescription() {
            return '...';
        }
    }
?>