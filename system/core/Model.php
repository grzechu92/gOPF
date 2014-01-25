<?php 
	namespace System;
	
	/**
	 * Abstract class of model
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Model {
		/**
		 * Framework database handler
		 * @var mixed
		 */
		protected $database;
		
		/**
		 * Creates link to framework database in class
		 */
		public function __construct() {
			$this->database = Core::instance()->database->connection();
		}
		
		/**
		 * If model method not exists, exception is thrown
		 * 
		 * @param string $index Method name
		 * @param array $args Method call arguments
		 * @throws \System\Core\Exception
		 */
		public function __call($index, $args) {			
			throw new \System\Core\Exception(I18n::translate('MODEL_METHOD_NOT_EXISTS', array(__CLASS__, $index)));
		}
		
		/**
		 * Returns model object instance
		 * 
		 * @param string $name Model name
		 * @return \System\Model Model object instance
		 */
		public static function factory($name) {
			return Core::instance()->context->getModel($name);
		}
	}
?>