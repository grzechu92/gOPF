<?php
    namespace Migrations;

    class Migration0 extends \gOPF\gDMT\Migration implements \gOPF\gDMT\MigrationInterface {
        /**
         * @var \gOPF\gODI\Handler
         */
        protected $database;

        public static function getDescription() {
            return 'Utworzenie w bazie danych tabeli test';
        }

        public function execute() {
            $this->database->raw('');
        }
    }
?>