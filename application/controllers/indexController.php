<?php
	namespace Controllers;
    use \gOPF\gDMT;
	
	class indexController extends \System\Controller {
		public static $DYNAMIC = true;
		
		public function mainAction() {
			$lib = new gDMT();

            var_dump($lib->getAvailableMigrations());
		}
	}
?>