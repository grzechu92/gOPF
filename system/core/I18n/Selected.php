<?php
    namespace System\I18n;

    /**
     * Selected language class
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Selected {
        /**
         * Selected system language
         * @var string
         */
        public $system;

        /**
         * Selected application language
         * @var string
         */
        public $application;

        /**
         * Initialize selected language class
         */
        public function __construct($system, $application) {
            $this->system = $system;
            $this->application = $application;
        }
    }
?>