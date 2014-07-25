<?php
    namespace System\Events;

    /**
     * Framework events manager event class
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Event {
        /**
         * Event name
         * @var string
         */
        public $name;

        /**
         * Event action
         * @var \Closure
         */
        public $closure;

        /**
         * Call event once?
         * @var bool
         */
        public $once;

        /**
         * Initialize event object
         *
         * @param string $name Event name
         * @param \Closure $closure Event action
         * @param bool $once Call event once?
         */
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