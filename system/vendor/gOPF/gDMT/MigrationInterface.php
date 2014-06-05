<?php
    namespace gOPF\gDMT;

    interface MigrationInterface {
        public function __construct($database);
        public static function getDescription();
        public function getMigrationNumber();
    }
?>