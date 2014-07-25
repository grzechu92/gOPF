<?php
    namespace gOPF\gWSS;

    /**
     * gWSS Response class
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Response {
        /**
         * Event name
         * @var string
         */
        private $event;

        /**
         * Event data
         * @var \stdClass
         */
        private $data;

        /**
         * Initialize Resposne object
         *
         * @param string $event Event name
         * @param \stdClass $data Event data
         */
        public function __construct($event, $data) {
            $this->event = $event;
            $this->data = (object) $data;
        }

        /**
         * Build response string
         *
         * @return string JSON Response
         */
        public function build() {
            $output = new \stdClass();
            $output->event = $this->event;
            $output->data = $this->data;

            return json_encode($output);
        }
    }
?>