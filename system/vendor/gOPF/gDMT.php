<?php
	namespace gOPF;
	use \gOPF\gDMT\MigrationInterface;
	use \System\Filesystem;
	use \System\Queue\Element;

	/**
	 * gDMT - gDMT Database Migration Tool
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
		 * Migrations path
		 * @var string
		 */
		private $path;

		/**
		 * Database engine
		 * @var \System\Database\EngineInterface
		 */
		private $database;

		/**
		 * Initializes gDMT object
		 */
		public function __construct() {
			\System\Loader::registerReservedNamespace(new \System\Loader\NS(self::MIGRATION_NAMESPACE, __APPLICATION_PATH.DIRECTORY_SEPARATOR.self::MIGRATION_PATH));

			$this->path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.self::MIGRATION_PATH;
			$this->database = \System\Core::instance()->database->engine();
		}

		/**
		 * Returns list of available migrations
		 *
		 * @return \System\Queue Available migrations
		 */
		public function getAvailableMigrations() {
			$queue = new \System\Queue();

			if (Filesystem::checkDirectory($this->path)) {
				$migration = 0;

				while (true) {
					try {
						$class = '\\'.self::MIGRATION_NAMESPACE.'\\'.self::MIGRATION_CLASS.$migration;
						$object = new $class($this->database->handler());

						if ($object instanceof MigrationInterface) {
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

		/**
		 * Initializes database structure for migrations
		 */
		public function initializeDatabaseStructure() {
			$this->database->query('CREATE TABLE IF NOT EXISTS `'.self::MIGRATIONS_DATABASE.'` (`migration` int(11) NOT NULL)');
		}

		/**
		 * Removes migrations database structure
		 */
		public function removeDatabaseStructure() {
			$this->database->query('DROP DATABASE `'.self::MIGRATIONS_DATABASE.'`');
		}

		/**
		 * Sets migration as executed
		 *
		 * @param int $number Migration number
		 */
		public function markAsExecuted($number) {
			$this->database->query('INSERT INTO `'.self::MIGRATIONS_DATABASE.'` (`migration`) VALUES ('.$number.')');
		}

		/**
		 * Checks if migrations is executed
		 *
		 * @param int $number Migration number
		 * @return bool Migration exists?
		 */
		public function isExecuted($number) {
			$result = $this->database->query('SELECT COUNT(*) as `amount` FROM `'.self::MIGRATIONS_DATABASE.'` WHERE `migration` = '.$number, true);

			return $result->amount == 1;
		}

		/**
		 * Executes passed migration
		 *
		 * @param \gOPF\gDMT\MigrationInterface $migration Migration to execute
		 * @return bool|string Migration execution status, true if success, error message when false
		 */
		public function executeMigration(MigrationInterface $migration) {
			if ($this->isExecuted($migration->getMigrationNumber())) {
				return false;
			}

			$transaction = $this->database->transaction();

			try {
				$transaction->begin();
				$migration->execute();
				$transaction->commit();

				$this->markAsExecuted($migration->getMigrationNumber());
				return true;
			} catch(\Exception $e) {
				if ($transaction->status()) {
					$transaction->revert();
				}

				return $e->getMessage();
			}
		}
	}
?>