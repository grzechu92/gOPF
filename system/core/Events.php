<?php
    namespace System;
    use \System\Events\Event;

    class Events {
        /**
         * Array of events
         * @var array
         */
        private $list = array();

        public function on($names, \Closure $closure, $once = false) {
            $names = explode(' ', $names);

            foreach ($names as $name) {
                if (!isset($this->list->$name)) {
                    $this->list->$name = array();
                }

                $this->list->$name[] = new Event($name, $closure, $once);
            }
        }

        public function call($name, \stdClass $data = null) {
            if (isset($this->list->$name)) {
                /** @var $event \System\Events\Event */
                foreach ($this->list->$name as $id=>$event) {
                    $event->closure($data);

                    if ($event->once) {
                        unset($this->list->$name[$id]);
                    }
                }
            }
        }

        public function remove($names) {
            $names = explode(' ', $names);

            foreach ($names as $name) {
                unset($this->list->$name);
            }
        }
    }
?>