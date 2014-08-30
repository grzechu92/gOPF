<?php
	namespace Controllers;
    use \System\View;
    use \System\Session;
    use \System\Storage;

	class indexController extends \System\Controller {
		public static $DYNAMIC = true;

//		public function mainAction() {
//            $session = \System\Storage::factory('test', \System\Driver::SESSION);
//            $session->set($session->get() + 1);
//		}

//        public function mainAction() {
//            $container = Storage::factory('counter', Storage::FILESYSTEM);
//
//            $container->set($container->get() + 1);
//
//            var_dump($container->get());
//        }

        /*public function daemonAction() {
            View::instance()->setRenderStatus(false);

            $config = new \gOPF\gWSS\Config();
            $config->debug = true;

            $server = new \gOPF\gWSS($config);

            $server->events->server->on('onTimeChange', function(\gOPF\gWSS\Client $client) {
                if ($client->container->time != time()) {
                    $client->container->time = time();

                    $data = new \stdClass();
                    $data->time = time();

                    return new \gOPF\gWSS\Response('timeChanged', $data);
                }
            });

            $server->run();
        }

        public function pushAction() {
            View::instance()->setRenderStatus(false);

            $server = new \gOPF\gPAE();

            $server->events->server->on('onTimeChange', function(\gOPF\gPAE\Client $client) {
                if ($client->container->last != $client->container->data) {
                    $client->container->last = $client->container->data;

                    $output = new \stdClass();
                    $output->container = $client->container->data;

                    return new \gOPF\gPAE\Result("onChange", $output);
                }
            });

            $server->events->client->on('test', function(\gOPF\gPAE\Client $client) {
                if ($client->data->value == 'exit') {
                    $client->disconnect();
                } else {
                    $client->container->data = $client->data->value;
                }
            });

            $server->run();
        }

        public function demoAction() {
            View::instance()->setFrame(__APPLICATION_PATH.'/views/socket.php');
        }

        public function testAction() {
            View::instance()->setFrame(__APPLICATION_PATH.'/views/push.php');
        }*/
	}
?>
