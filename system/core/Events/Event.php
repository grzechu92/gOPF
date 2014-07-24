<?php
    namespace System\Events;

    class Event {
        public $name;
        public $closure;
        public $once;

        public function __construct($name, \Closure $closure, $once = false) {
            $this->name = $name;
            $this->closure = $closure;
            $this->once = $once;
        }

        /**
         * Magical method to call closure by calling $this->closure()
         *
         * @param string $method Method name
         * @param array $args Array with arguments
         * @return mixed Function result
         */
        public function __call($method, $args) {
            return call_user_func_array($this->{$method}, $args);
        }
    }
?>