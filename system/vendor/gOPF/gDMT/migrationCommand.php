<?php

namespace gOPF\gDMT;

use System\Filesystem;
use System\Terminal\Help\Line;
use gOPF\gDMT;

/**
 * Terminal command: migration (migrate product database).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class migrationCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface
{
    /**
     * Path to migrations.
     *
     * @var string
     */
    private $path;

    /**
     * Migration Tool.
     *
     * @var \gOPF\gDMT
     */
    private $migrations;

    /**
     * Initialize migration command object.
     */
    public function __construct()
    {
        $this->path = __APPLICATION_PATH . DIRECTORY_SEPARATOR . gDMT::MIGRATION_PATH;
        $this->migrations = new \gOPF\gDMT();
    }

    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        $lines = array();

        $help = new \System\Terminal\Help('Database migration tool');

        $lines[] = new Line('migration', 'start migration process');
        $lines[] = new Line('migration -status', 'display all migrations status');
        $lines[] = new Line('migration -initialize', 'initialize migration structure');

        $help->addLines($lines);

        return $help;
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $session = self::$session;

        if ($this->getParameter('status')) {
            $session->buffer($this->getMigrationsStatus());
        } else {
            if ($this->getParameter('initialize')) {
                $this->migrations->initializeDatabaseStructure();
            } else {
                $this->runMigration();
            }
        }
    }

    /**
     * @see \System\Terminal\CommandInterface::onInstall()
     */
    public function onInstall()
    {
        Filesystem::mkdir($this->path);
        Filesystem::chmod($this->path, 0777);

        $this->migrations->initializeDatabaseStructure();
    }

    /**
     * @see \System\Terminal\CommandInterface::onUninstall()
     */
    public function onUninstall()
    {
        Filesystem::remove($this->path);

        $this->migrations->removeDatabaseStructure();
    }

    /**
     * Generate output for migrations status.
     *
     * @return string Migrations status
     */
    private function getMigrationsStatus()
    {
        $output = '';
        $migrations = $this->migrations->getAvailableMigrations();

        if (count($migrations) == 0) {
            $output = 'No migrations available.';
        } else {
            foreach ($migrations as $element) {
                /* @var $migration \gOPF\gDMT\MigrationInterface */
                $migration = $element->value;

                $output .= $element->name . ' - ' . ($this->migrations->isExecuted($element->name) ? '<bold><green>DONE</green></bold>' : '<bold><red>TODO</red></bold>') . ' - ' . $migration->getDescription() . "\n";
            }
        }

        return $output;
    }

    public function runMigration()
    {
        $session = self::$session;
        $migrations = $this->migrations->getAvailableMigrations();

        if (count($migrations) == 0) {
            $session->buffer('No migrations available.');
        } else {
            foreach ($migrations as $element) {
                /* @var $migration \gOPF\gDMT\MigrationInterface */
                $migration = $element->value;
                $session->buffer('Checking migration <bold>' . $element->name . '</bold> status...');
                usleep(200000);

                if ($this->migrations->isExecuted($element->name)) {
                    $session->buffer('<bold><green>DONE</green></bold>');
                    usleep(200000);
                } else {
                    $session->buffer('<bold><red>MIGRATING</red></bold>');
                    sleep(1);

                    $status = $this->migrations->executeMigration($migration);

                    if ($status === true) {
                        $session->buffer('<bold><green>DONE</green></bold>');
                        sleep(1);
                    } else {
                        $session->buffer('<bold><red>ERROR</red></bold>');
                        $session->buffer($status);
                        break;
                    }
                }
            }
        }
    }
}
