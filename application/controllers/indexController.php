<?php
	namespace Controllers;
	
	class indexController extends \System\Controller {
		public static $DYNAMIC = true;
		
		public function mainAction() {
			$model = \System\Model::factory('squares');
			$model instanceof \Models\squaresModel;
			
			$entity = $model->getEntity(1);
			
			var_dump($entity);
		}
	}
?>