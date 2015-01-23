<?php
    namespace gOPF\gPAE;

    /**
     * Client class
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Client {
        /**
         * Event data from client
         * @var \stdClass
         */
        public $data;

        /**
         * Ping between client and server via HTTP (in miliseconds)
         * @var int
         */
        public $ping = 0;

        /**
         * Push session container
         * @var \gOPF\gPAE\Container
         */
        public $container;

        /**
         * System and client events
         * @var \gOPF\gPAE\Events
         */
        public $events;

        /**
         * Push session key
         * @var string
         */
        private $key;

        /**
         * Push server configuration
         * @var \gOPF\gPAE\Config
         */
        private $config;

        /**
         * Initializes Client object
         *
         * @param string $key Client session key
         * @param \gOPF\gPAE\Config $config
         * @param \gOPF\gPAE\Events $events
         */
        public function __construct($key, Config $config, Events $events) {
            $this->key = $key;
            $this->config = $config;
            $this->events = $events;

            $this->container = new Container($key, $this->config->timeout / 1000 + 1);
        }

        /**
         * Create connection with client
         *
         * @return \gOPF\gPAE\Response Connection parameters
         */
        public function connect() {
            $this->events->client->call('onConnect', $this);

            $data = new \stdClass();
            $data->key = $this->key;
            $data->config = $this->config;

            return new Response(Command::CONNECTED, new Result('', $data));
        }

        /**
         * Disconnects client from server
         */
        public function disconnect() {
            $this->container->__DISCONNECTED = true;
        }

        /**
         * Holds HTTP request on server side, check that any server event has been called
         *
         * @return \gOPF\gPAE\Response Data from event for client
         */
        public function loop() {
            ignore_user_abort(true);

            while (((__START_TIME + $this->config->timeout / 1000) >= microtime(true))) {
                if ($this->container->__DISCONNECTED || connection_aborted()) {
                    if (connection_aborted()) {
                        $this->events->client->call('onConnectionLost', $this);
                    }

                    $this->events->client->call('onDisconnect', $this);

                    $this->container->expired();
                    return new Response(Command::DISCONNECTED);
                }

                $events = $this->events->server->get();

                if (count($events) > 0) {
                    foreach ($events as $event) {
                        $result = $event->closure($this);

                        if ($result instanceof Result) {
                            return new Response(Command::ACTION, $result);
                        }
                    }
                }

                echo ' ';
                ob_flush();
                flush();

                usleep($this->config->interval * 1000);
            }

            return new Response(Command::RENEW);
        }

        /**
         * Execute event on client
         *
         * @param string $event Event name
         * @param \stdClass|null $data Event data
         */
        public function action($event, \stdClass $data = null) {
            $this->data = $data;

            $this->events->client->call($event, $this);
        }
    }
?>