<?php
	namespace Controllers;

	class indexController extends \System\Controller {
		public static $DYNAMIC = true;
		
		public function mainAction() {
            $session = \System\Storage::factory('test', \System\Driver::SESSION);
            $session->set($session->get() + 1);
		}
	}
?>