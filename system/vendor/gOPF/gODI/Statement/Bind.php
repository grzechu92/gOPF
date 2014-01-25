<?php
	namespace gOPF\gODI\Statement;
	use \gOPF\gODI\Statement;

    /**
     * Bind object in statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
	class Bind {
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
         * Bind name
         * @var string
         */
        public $name;

        /**
         * Bind type
         * @var int
         */
        public $type;

        /**
         * Bind value
         * @var mixed
         */
        public $value;

        /**
         * Initiates bind object
         *
         * @param mixed $value Bind value
         * @param int $type Bind type
         */
        public function __construct($value, $type = self::INT) {
			$this->name = ':'.substr(sha1(rand()), 0, 9);
			$this->value = $value;
			$this->type = $type;
		}
	}
?>