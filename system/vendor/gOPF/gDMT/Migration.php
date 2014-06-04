<?php
    namespace gOPF\gDMT;

    abstract class Migration {
        /**
         * @var \System\Database\EngineInterface
         */
        protected $connection;

        public function __construct(\System\Database\EngineInterface $connection) {
            $this->connection = $connection;
        }
    }
?>