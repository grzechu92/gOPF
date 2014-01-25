<?php
	namespace gOPF\gODI\Statement;
	use \gOPF\gODI\Statement;

    /**
     * Field object in statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	class Field {
        /**
         * @see \gOPF\gODI\Statement::INT
         */
		const INT = Statement::INT;

        /**
         * @see \gOPF\gODI\Statement::BOOL
         */
		const BOOL = Statement::BOOL;

        /**
         * @see \gOPF\gODI\Statement::STRING
         */
		const STRING = Statement::STRING;

        /**
         * Field name
         * @var string
         */
        public $name;

        /**
         * Field bind
         * @var \gOPF\gODI\Statement\Bind
         */
        public $bind;

        /**
         * Initiates field object
         *
         * @param string $name Field name
         * @param mixed $value Field value
         * @param int $type Field type
         */
        public function __construct($name, $value, $type = self::INT) {
			$this->bind = new Bind($value, $type);
			$this->name = $name;
		}

        /**
         * Creates field string
         *
         * @return string Field string
         */
        public function __toString() {
			return $this->name.' = '.$this->bind->name;
		}
	}
?>