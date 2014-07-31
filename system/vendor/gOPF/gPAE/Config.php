<?php
    namespace gOPF\gPAE;

    /**
     * Config for gPAE
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Config {
        /**
         * Server events check interval (in miliseconds)
         * @var int
         */
        public $interval = 100;

        /**
         * Connection timeout (in miliseconds)
         * @var int
         */
        public $timeout = 600000;

        /**
         * Reconnect interval(in miliseconds)
         * @var int
         */
        public $reconnect = 5000;

        /**
         * Encrypted connection
         * @var bool
         */
        public $encrypted = false;
    }
?>