<?php
    namespace gOPF\gDMT;

    interface MigrationInterface {
        public function execute();
        public static function getDescription();
    }
?>