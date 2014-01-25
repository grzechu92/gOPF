<?php
	namespace gOPF\gODI;
	use \PDO;
	
	/**
	 * gODI Statement abstract class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Statement {
		/**
		 * Integer variable type
		 * @var int
		 */
		const INT = PDO::PARAM_INT;
		
		/**
		 * String variable type
		 * @var int
		 */
		const STRING = PDO::PARAM_STR;
		
		/**
		 * Boolean variable type
		 * @var int
		 */
		const BOOL = PDO::PARAM_BOOL;
		
		/**
		 * Ascending order
		 * @var string
		 */
		const ASC = 'ASC';
		
		/**
		 * Descending order
		 * @var string
		 */
		const DESC = 'DESC';
		
		/**
		 * PDO connector
		 * @var \PDO
		 */
		private $PDO;
		
		/**
		 * Bind values
		 * @var \gOPF\gODI\Statement\Bind[]
		 */
		private $bind = array();

        /**
         * Table name
         * @var string
         */
        protected $table;

        /**
         * Initializes statement
         *
         * @param \PDO $PDO PDO connector
         * @param string $table Table name
         */
        final public function __construct(PDO $PDO, $table) {
			$this->PDO = $PDO;
			$this->table = $table;
		}

        /**
         * Builds statement
         *
         * @return string Statement
         */
        final public function __toString() {
			return $this->build();
		}

        /**
         * Adds parameter to bind statement
         *
         * @param \gOPF\gODI\Statement\Bind $bind Filled bind object
         */
        final public function bind(\gOPF\gODI\Statement\Bind $bind) {
			$this->bind[] = $bind;
		}

        /**
         * Executes statement
         *
         * @param bool $values Return value or number of affected rows
         * @return array|int|\stdClass Array of results, affected rows or data object
         */
        final protected function execute($values) {
			$query = $this->PDO->prepare($this->build());

			foreach ($this->bind as $bind) {
				$query->bindValue($bind->name, $bind->value, $bind->type);
			}

			$query->execute();

			if ($values) {
				$result = $query->fetchAll(PDO::FETCH_OBJ);

				if (count($result) == 1) {
					return $result[0];
				} else {
					return $result;
				}
			} else {
				return $query->rowCount();
			}
		}

        /**
         * Build query body
         *
         * @return string Query body
         */
        abstract public function build();
    }
?>