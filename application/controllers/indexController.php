<?php
	namespace Controllers;
    use \gOPF\gWSS;
    use \gOPF\gWSS\Config;
	
	class indexController extends \System\Controller {
		public static $DYNAMIC = true;
		
		public function daemonAction() {
            \System\View::setRenderStatus(false);

            $config = new Config();
            $config->debug = true;

            $server = new gWSS($config);
            $server->run();
		}

        public function mainAction() {

        }
	}
?>