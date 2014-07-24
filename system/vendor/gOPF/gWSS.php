<?php
    namespace gOPF;
    use \gOPF\gWSS\Client;
    use \gOPF\gWSS\Config;

    /**
     * gWSS - grzechu WebSocket Service
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class gWSS  {
        /**
         * Events manager
         * @var \System\Event
         */
        public $events;

        /**
         * Server configuration object
         * @var \gOPF\gWSS\Config
         */
        private $config;

        /**
         * Main socket
         * @var resource
         */
        private $main;

        /**
         * The array of connected clients
         * @var \gOPF\gWSS\Client[]
         */
        private $clients = array();

        public function __construct(Config $config = null) {
            set_time_limit(0);

            $this->config = ($config instanceof Config) ? $config : new Config();

            $this->console('Server starting...');

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

            if (!is_resource($socket)) {
                $this->terminate('socket_create() failed: '.socket_strerror(socket_last_error()));
            }

            if (!socket_bind($socket, $this->config->host, $this->config->port)) {
                $this->terminate('socket_bind() failed: '.socket_strerror(socket_last_error()));
            }

            if (!socket_listen($socket, 20)) {
                $this->terminate('socket_listen() failed: '.socket_strerror(socket_last_error()));
            }

            $this->main = $socket;
            $this->console('Server started on '.$this->config->host.':'.$this->config->port);
        }

        public function console($message, Client $client = null) {
            $output = date('[Y-m-d H:i:s]');
            $output .= ($client instanceof Client) ? '['.$client->id.']' : str_repeat(' ', 15);
            $output .= ' '.$message."\r\n";

            if ($this->config->debug) {
                echo $output;
            }
        }

        public function run() {
            $this->console('Daemon started!');

            while (true) {
                $sockets = array();
                $sockets[] = $this->main;

                foreach ($this->clients as $client) {
                    if ($client->alive) {
                        $sockets[] = $client->socket;
                    } else {
                        unset($this->clients[$client->id]);
                    }
                }

                @socket_select($sockets, $write, $except, 1);

                foreach($sockets as $socket) {
                    if ($socket == $this->main) {
                        if (($accepted = socket_accept($this->main)) < 0) {
                            $this->console('Socket error: '.socket_strerror(socket_last_error($accepted)));
                        } else {
                            $client = new Client($this, $this->config, $accepted);
                            $this->clients[$client->id] = $client;
                        }
                    } else {
                        $client = $this->getClientBySocket($socket);

                        if ($client instanceof Client) {
                            $client->update();
                        }
                    }
                }

                $this->console('Users: '.count($this->clients));
            }
        }

        private function getClientBySocket($socket) {
            foreach ($this->clients as $client) {
                if ($client->socket == $socket) {
                    $this->console('Client loaded!', $client);

                    return $client;
                }
            }

            $this->console('Client not found!');
            return false;
        }

        private function action(Client $client, $action) {
            $action = $this->unmask($action);

            $this->console('Performing action: '.$action);

            if($action == 'CLOSE' || $action == '') {
                $client->kill($client);
            }
        }


        private function startProcess(Client $client) {
            $pid = pcntl_fork();

            if ($pid == -1) {
                $this->console('Fork create failed!', true);
            } elseif ($pid) {
                $client->pid = $pid;
                $this->console('Fork created!');
            } else {
                while (true) {
                    if ($client->container->counter == null) {
                        $client->container->counter = 0;
                    }

                    $this->send($client, $client->container->counter++);

                    if (!$client->alive) {
                        $this->console('Process '.$client->pid.' killed!');
                        die();
                    } else {
                        usleep(500000);
                    }
                }
            }
        }

        private function terminate($message = '') {
            if (!empty($message)) {
                $this->console($message);
            }

            die();
        }
    }
?>