<?php
	namespace Controllers;
	use \gOPF\gSIP;
	use \gOPF\gSIP\Size;
	use \gOPF\gSIP\Position;
	use \gOPF\gSIP\ColorHex;
	
	
	class indexController extends \System\Controller {
		public static $DYNAMIC = true;
		
		public function mainAction() {
			$g = new \gOPF\gSIP();
			
			$background = $g->createLayer('background', new Size(500));
			
			
			$red = $g->createLayer('red', new Size(200), new Position(100));
			$red->fill(new ColorHex('F00'));
			
			
			$g->createImage(gSIP::PNG);
		}
	}
?>