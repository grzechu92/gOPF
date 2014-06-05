<?php
    namespace Migrations;

    class Migration0 extends \gOPF\gDMT\Migration implements \gOPF\gDMT\MigrationInterface {
        /**
         * @var \gOPF\gODI\Handler
         */
        protected $database;

        public function getDescription() {
            return 'Utworzenie w bazie danych tabeli o nazwie tabela';
        }

        public function execute() {
            $this->database->raw('
                CREATE TABLE IF NOT EXISTS `tabela` (
                    `foo` int(11) NOT NULL,
                    `bar` int(11) NOT NULL,
                    `time` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ');
        }
    }
?>