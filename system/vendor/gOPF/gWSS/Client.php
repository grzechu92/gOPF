<?php
    namespace gOPF\gWSS;

    /**
     * gWSS Client class
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Client {
        /*
         * Unique client id
         * @var string
         */
        public $id;

        /**
         * Is client alive?
         * @var bool
         */
        public $alive = true;

        /**
         * Client socket
         * @var resource
         */
        public $socket;

        /**
         * Is client initialized?
         * @var bool
         */
        public $handshake = false;

        /**
         * Client process PID
         * @var resource
         */
        public $pid = null;

        /**
         * Client session data container
         * @var \System\Container
         */
        public $container;

        /**
         * Last data from client
         * @var \stdClass
         */
        public $data;

        /**
         * WebSocket server
         * @var \gOPF\gWSS
         */
        private $server;

        /**
         * WebSocket server configuration
         * @var \gOPF\gWSS\Config
         */
        private $config;

        /**
         * Initializes client object
         *
         * @param \gOPF\gWSS $server WebSocket server
         * @param \gOPF\gWSS\Config $config WebSocket server config
         * @param resource $socket Socket resource
         */
        public function __construct(\gOPF\gWSS $server, Config $config, $socket) {
            $this->id = uniqid();
            $this->container = new \System\Container();


            $this->socket = $socket;
            $this->server = $server;
            $this->config = $config;

            $this->server->console('Client created!', $this);
        }

        /**
         * Kill socket client
         */
        public function kill() {
            @socket_shutdown($this->socket);
            @socket_close($this->socket);

            $this->alive = false;

            $this->server->console('Client killed', $this);
        }

        /**
         * Update client data and status
         */
        public function update() {
            $this->server->console('Updating data...', $this);

            $data = $this->read();

            if (!$this->handshake) {
                $this->handshake(new Headers($data));

                if ($this->handshake) {
                    $this->loop();
                } else {
                    $this->kill();
                }
            } else {
                $this->server->console('Receiving data...', $this);

                $encoded = Encoder::decodeWebSocket($data);

                if (!empty($encoded)) {
                    $event = (object) json_decode($encoded);
                    $this->data = $event->data;

                    $this->server->client->call($event->event, $this);
                }
            }
        }

        /**
         * Send data to client
         *
         * @param \gOPF\gWSS\Response|string $data String with data for client
         */
        public function send($data) {
            $this->server->console('Sending data...', $this);

            if ($data instanceof Response) {
                $data = Encoder::encodeWebSocket($data->build());
            }

            if (@socket_write($this->socket, $data, strlen($data)) === false) {
                $this->server->console('Sending data failed', $this);
                $this->kill();
            }
        }

        /**
         * Read data from socket
         *
         * @return string Data from socket
         */
        private function read() {
            $data = null;
            while ($bytes = @socket_recv($this->socket, $reading, 2048, MSG_DONTWAIT)) {
                $data .= $reading;
            }

            if ($bytes === 0) {
                $this->kill();
            }

            return $data;
        }

        /**
         * Create handshake with client
         *
         * @param \gOPF\gWSS\Headers $headers Request headers
         */
        private function handshake(Headers $headers) {
            $this->server->console('Handshaking...', $this);

            $version = $headers->getWebSocketVersion();

            if (!$version) {
                $this->server->console('Client doesn\'t support WebSocket!');
                return;
            }

            if ($version != 13) {
                $this->server->console('WebSocket version 13 required (client version '.$version.')');
                return;
            }

            $accept = Encoder::generateAcceptToken($headers->getWebSocketKey());

            $upgrade =
                'HTTP/1.1 101 Switching Protocols'."\r\n".
                'Upgrade: websocket'."\r\n".
                'Connection: Upgrade'."\r\n".
                'Sec-WebSocket-Accept: '.$accept."\r\n".
                "\r\n";

            $this->send($upgrade);

            $this->handshake = true;
            $this->server->console('Handshaking done!', $this);
        }

        /**
         * Server events loop
         */
        private function loop() {
            $pid = pcntl_fork();

            if ($pid == -1) {
                $this->server->console('Fork create failed!', $this);
            } elseif ($pid) {
                $this->pid = $pid;
                $this->server->console('Fork created!', $this);
            } else {
                while (true) {
                    $events = $this->server->server->get();

                    if (count($events) > 0) {
                        foreach($events as $event) {
                            $result = $event->closure($this);

                            if ($result instanceof Response) {
                                $this->send($result);
                            }
                        }
                    }

                    if (!$this->alive) {
                        $this->server->console('Fork killed!', $this);
                        die();
                    } else {
                        usleep($this->config->refresh);
                    }
                }
            }
        }
    }
?>