<?php
    namespace gOPF\gODI\Statement;

    /**
     * gODI cache statement
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Cache {
        /**
         * gODI cache name prefix
         * @var string
         */
        const PREFIX = 'gODI-';

        /**
         * Query expire time
         * @var int
         */
        private $expires;

        /**
         * Cache type
         * @var int
         */
        private $type;

        /**
         * Query checksum
         * @var string
         */
        private $checksum;

        /**
         * Initializes cache statement
         *
         * @param int $expires Cache expire time (in seconds)
         * @param int $type Cache type (Statement::COMMON, Statement::USER, Statement::RUNTIME)
         */
        public function __construct($expires, $type) {
            $this->expires = $expires;
            $this->type = $type;
        }

        /**
         * Set query checksum
         *
         * @param string $checksum Query checksum
         */
        public function checksum($checksum) {
            $this->checksum = $checksum;
        }

        /**
         * Is query cache valid?
         *
         * @return bool Is valid?
         */
        public function isValid() {
            return \System\Cache::isValid(self::PREFIX.$this->checksum,  $this->type);
        }

        /**
         * Get value of query cache
         *
         * @return mixed Query value from cache
         */
        public function get() {
            return \System\Cache::get(self::PREFIX.$this->checksum,  $this->type);
        }

        /**
         * Set value to query cache
         *
         * @param mixed $value Value to cache
         */
        public function set($value) {
            \System\Cache::set(self::PREFIX.$this->checksum, $value, $this->expires, $this->type);
        }
    }
?>