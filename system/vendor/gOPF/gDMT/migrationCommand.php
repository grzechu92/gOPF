<?php
    namespace gOPF\gDMT;
    use \System\Filesystem;
    use \gOPF\gDMT;

    class migrationCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
        private $path;

        /**
         * @var \gOPF\gDMT
         */
        private $migrations;

        public function __construct() {
            $this->path = __APPLICATION_PATH.gDMT::MIGRATION_PATH;
            $this->migrations = new \gOPF\gDMT();
        }

        /**
         * @see \System\Terminal\CommandInterface::help()
         */
        public function help() {
            $lines = array();

            $help = new \System\Terminal\Help('Database migration tool');
            $lines[] = new Line('migration', 'start migration process');
            $lines[] = new Line('migration -list', 'display all migrations');
            $lines[] = new Line('migration -executed', 'display all executed migrations');

            $help->addLines($lines);

            return $help;
        }

        public function execute() {

        }

        public function onInstall() {
            Filesystem::mkdir($this->path);
            Filesystem::chmod($this->path, 0777);

            $this->migrations->initializeDatabaseStructure();
        }

        public function onUninstall() {
            Filesystem::remove($this->path);

            $this->migrations->removeDatabaseStructure();
        }
    }
?>