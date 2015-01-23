<?php
	namespace System\Database;

	/**
	 * PDO database engine
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class PDO extends Engine implements EngineInterface {
		/**
		 * @see \System\Database\Engine::$handler
		 * @var \PDO
		 */
		protected $handler;

		/**
		 * @see \System\Database\EngineInterface::__construct()
		 */
		public function connect() {
			$dsn = $this->config['system'].':host='.$this->config['host'].';dbname='.$this->config['database'];
			
			$charset = array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES `'.$this->config['names'].'` COLLATE `'.$this->config['collate'].'`');
			
			$handler = new \PDO($dsn, $this->config['user'], $this->config['pass'], $charset);
			$handler->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
			$this->handler = $handler;
		}

		/**
		 * @see \System\Database\EngineInterface::query()
		 */
		public function query($query, $result = false) {
			$return = $this->handler->query($query);

			return $result ? $return->fetch(\PDO::FETCH_OBJ) : null;
		}

		/**
		 * @see \System\Database\EngineInterface::transaction()
		 */
		public function transaction() {
			return new \System\Database\PDO\Transaction($this->handler);
		}
	}
?>