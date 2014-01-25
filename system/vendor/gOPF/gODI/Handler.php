<?php
	namespace gOPF\gODI;
	
	/**
	 * gODI Statement handler
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Handler {
		/**
		 * PDO connector
		 * @var \PDO
		 */
		private $PDO;
		
		/**
		 * Creates PDO connection
		 * @param \PDO $PDO PDO object
		 */
		public function __construct(\PDO $PDO) {
			$this->PDO = $PDO;
		}
		
		/**
		 * Returns select statement
		 * 
		 * @param string $table Table in database
		 * @return \gOPF\gODI\Select Select statement
		 */
		public function select($table) {
			return new Select($this->PDO, $table);
		}
		
		/**
		 * Returns delete statement
		 *
		 * @param string $table Table in database
		 * @return \gOPF\gODI\Delete Delete statement
		 */
		public function delete($table) {
			return new Delete($this->PDO, $table);
		}
		
		/**
		 * Returns insert statement
		 *
		 * @param string $table Table in database
		 * @return \gOPF\gODI\Insert Insert statement
		 */
		public function insert($table) {
			return new Insert($this->PDO, $table);
		}
		
		/**
		 * Returns update statement
		 *
		 * @param string $table Table in database
		 * @return \gOPF\gODI\Update Update statement
		 */
		public function update($table) {
			return new Update($this->PDO, $table);
		}
		
		/**
		 * Returns raw PDO connection
		 *
		 * @return \PDO Raw PDO object
		 */
		public function raw() {
			return $this->PDO;
		}
	}
?>