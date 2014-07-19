<?php
    namespace gOPF\gODI\Statement;
    use \gOPF\gODI\Statement;

    /**
     * Condition statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Condition {
        /**
         * Statement prefix (AND, OR)
         * @var string
         */
        private $prefix;

        /**
         * Field name
         * @var string
         */
        private $field;

        /**
         * Compare operator
         * @var string
         */
        private $operator;

        /**
         * Bind value object
         * @var \gOPF\gODI\Statement\Bind
         */
        private $bind;

        /**
         * Parent statement object
         * @var \gOPF\gODI\Statement
         */
        private $statement;

        /**
         * Initiates compare statement
         *
         * @param \gOPF\gODI\Statement $statement Parent statement
         * @param string $field Field name
         * @param string $prefix Statement prefix (AND, OR)
         */
        public function __construct(Statement $statement, $field, $prefix = null) {
            $this->statement = $statement;
            $this->field = $field;
            $this->prefix = $prefix;
        }

        /**
         * Creates compare string
         * @return string Where string
         */
        public function __toString() {
            return trim(implode(' ', array($this->prefix, $this->field, $this->operator, $this->bind->name)));
        }

        /**
         * =
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function eq($value, $type = Statement::INT) {
            return $this->set('=', $value, $type);
        }

        /**
         * <>
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function not($value, $type = Statement::INT) {
            return $this->set('<>', $value, $type);
        }

        /**
         * >
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function gt($value, $type = Statement::INT) {
            return $this->set('>', $value, $type);
        }

        /**
         * <
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function lt($value, $type = Statement::INT) {
            return $this->set('<', $value, $type);
        }

        /**
         * >=
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function gte($value, $type = Statement::INT) {
            return $this->set('>=', $value, $type);
        }

        /**
         * <=
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function lte($value, $type = Statement::INT) {
            return $this->set('<=', $value, $type);
        }

        /**
         * LIKE
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function like($value, $type = Statement::STRING) {
            return $this->set('LIKE', $value, $type);
        }

        /**
         * IS
         *
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        public function is($value, $type = Statement::STRING) {
            return $this->set('IS', $value, $type);
        }

        /**
         * Set statement parameters
         *
         * @param string $operator Compare type
         * @param mixed $value Compare to value
         * @param int $type Compare value type
         * @return \gOPF\gODI\Statement Fluid interface
         */
        private function set($operator, $value, $type) {
            $this->operator = $operator;

            $this->bind = new Bind($value, $type);
            $this->statement->bind($this->bind);

            return $this->statement;
        }
    }
    ?>