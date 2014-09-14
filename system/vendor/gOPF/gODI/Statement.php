<?php
	namespace gOPF\gODI;
	use \PDO;
    use \System\Cache;
	
	/**
	 * gODI Statement abstract class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
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
         * Inner join
         * @var string
         */
        const INNER_JOIN = 'INNER JOIN';

        /**
         * Left join
         * @var string
         */
        const LEFT_JOIN = 'LEFT JOIN';

        /**
         * Right join
         * @var string
         */
        const RIGHT_JOIN = 'RIGHT JOIN';

        /**
         * Natural join
         * @var string
         */
        const NATURAL_JOIN = 'NATURAL JOIN';

        /**
         * Return number of modified rows
         * @var int
         */
        const RETURN_ROWS = 0;

        /**
         * Return insert ID
         * @var int
         */
        const RETURN_ID = 1;

        /**
         * Return row data
         * @var int
         */
        const RETURN_DATA = 3;

        /**
         * @see \System\Cache::USER
         */
        const USER = Cache::USER;

        /**
         * @see \System\Cache::COMMON
         */
        const COMMON = Cache::COMMON;

        /**
         * @see \System\Cache::RUNTIME
         */
        const RUNTIME = Cache::RUNTIME;

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
         * Generates query checksum depends on query body and query bindable values
         *
         * @return string Query checksum
         */
        final protected function checksum() {
            return sha1($this->build().implode($this->bind));
        }

        /**
         * Executes statement
         *
         * @param int|bool $mode Return mode (Statement::RETURN_ROWS, Statement::RETURN_ID, Statement::RETURN_DATA, false)
         * @return array|int|\stdClass|null Array of results, affected rows, ID or data object
         */
        final protected function execute($mode = false) {
			$query = $this->PDO->prepare($this->build());

			foreach ($this->bind as $bind) {
				$query->bindValue($bind->name, $bind->value, $bind->type);
			}

			$query->execute();

            switch ($mode) {
                case self::RETURN_DATA:
                    return $query->fetchAll(PDO::FETCH_OBJ);

                case self::RETURN_ID:
                    return $this->PDO->lastInsertId();

                case self::RETURN_ROWS:
                    return $query->rowCount();

                default:
                    return null;
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