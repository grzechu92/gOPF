<?php
    namespace Migrations;

    class Migration1 extends \gOPF\gDMT\Migration implements \gOPF\gDMT\MigrationInterface {
        /**
         * @var \gOPF\gODI\Handler
         */
        protected $database;

        public function getDescription() {
            return 'Wypełnienie tabeli test 10 wpisami z godziną i losowymi liczbamiads';
        }

        public function execute() {
            $res = 0;

            while ($res < 10) {
                $this->database->insert('tabela')->field('foo', rand(0, 10))->field('bar', rand(0, 10))->field('time', time())->make();
                $res++;
            }
        }
    }
?>