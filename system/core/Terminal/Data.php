<?php
    namespace System\Terminal;
    use \System\Terminal\Data\Parameter;

    /**
     * Terminal command data parser
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    final class Data {
        /**
         * Raw command string
         * @var string
         */
        private $raw;

        /**
         * Command name
         * @var string
         */
        private $command;

        /**
         * Command value
         * @var string
         */
        private $value;

        /**
         * Command parameters
         * @var \System\Terminal\Data\Parameter[]
         */
        private $parameters = array();

        /**
         * Initialize parser class and parse command
         *
         * @param string $raw Raw command string
         */
        public function __construct($raw) {
            $this->raw = $raw;

            $sections = explode(' -', $raw);
            $command = explode(' ', $sections[0]);

            if (count($command) == 1) {
                $this->command = $command[0];
            } else {
                list($this->command, $this->value) = $command;
            }

            if (count($sections) > 1) {
                $this->parseParameters(array_splice($sections, 1));
            }
        }

        /**
         * Get command class name
         *
         * @return string Command class name
         */
        public function getCommand() {
            return $this->command;
        }

        /**
         * Get command value
         *
         * @return string Command value
         */
        public function getValue() {
            return $this->value;
        }

        /**
         * Get command parameters (if any)
         *
         * @return \System\Terminal\Data\Parameter[] Command parameters
         */
        public function getParameters() {
            return $this->parameters;
        }

        /**
         * Get raw command string
         *
         * @return string Raw command string
         */
        public function getRaw() {
            return $this->raw;
        }

        /**
         * Parse command parameter
         *
         * @param string[] $parameters Array with parameters
         */
        private function parseParameters(array $parameters) {
            foreach ($parameters as $parameter) {
                $exploded = explode(' ', $parameter, 2);
                $name = $exploded[0];
                $value = '';

                if (isset($exploded[1])) {
                    $value = $exploded[1];
                }

                $this->parameters[$name] = new Parameter($name, $value);
            }
        }
    }
?>