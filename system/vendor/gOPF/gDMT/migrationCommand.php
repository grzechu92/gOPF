<?php
    namespace gOPF\gDMT;
    use \System\Filesystem;
    use \System\Terminal\Help\Line;
    use \gOPF\gDMT;

    class migrationCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
        /**
         * Path to migrations
         * @var string
         */
        private $path;

        /**
         * Migration Tool
         * @var \gOPF\gDMT
         */
        private $migrations;

        public function __construct() {
            $this->path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.gDMT::MIGRATION_PATH;
            $this->migrations = new \gOPF\gDMT();
        }

        /**
         * @see \System\Terminal\CommandInterface::help()
         */
        public function help() {
            $lines = array();

            $help = new \System\Terminal\Help('Database migration tool');
            $lines[] = new Line('migration', 'start migration process');
            $lines[] = new Line('migration -status', 'display all migrations status');

            $help->addLines($lines);

            return $help;
        }

        /**
         * @see \System\Terminal\CommandInterface::execute()
         */
        public function execute() {
            $session = self::$session;

            if ($this->getParameter('status')) {
                $session->buffer($this->getMigrationsStatus());
            }
        }

        /**
         * @see \System\Terminal\CommandInterface::onInstall()
         */
        public function onInstall() {
            Filesystem::mkdir($this->path);
            Filesystem::chmod($this->path, 0777);

            $this->migrations->initializeDatabaseStructure();
        }

        /**
         * @see \System\Terminal\CommandInterface::onUninstall()
         */
        public function onUninstall() {
            Filesystem::remove($this->path);

            $this->migrations->removeDatabaseStructure();
        }

        private function getMigrationsStatus() {
            $output = '';
            $migrations = $this->migrations->getAvailableMigrations();

            if (count($migrations) == 0) {
                $output = 'No migrations available.';
            } else {
                foreach ($migrations as $element) {
                    $output .= 'Migration '.$element->name.' - '.($this->migrations->isExecuted($element->name) ? 'DONE' : 'TODO')."\n";
                }
            }

            return $output;
        }
    }
?>