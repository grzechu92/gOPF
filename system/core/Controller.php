<?php
	namespace System;
	
	/**
	 * Main class of controller
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Controller {
		/**
		 * Defines users class which have access to controller
		 * 
		 * scheme: array('userClass', array('actionName' => 'userClass'))
		 * example: array('guest', array('logout' => 'userClass'))
		 * 
		 * @var array
		 */
		public static $ACL = array('guest');
		
		/**
		 * Controller can be called directly by calling it from URL?
		 * @var bool
		 */
		public static $DYNAMIC = false;

		/**
		 * Main controller action
		 */
		public function mainAction() {}
		
		/**
		 * Returns object of requested controller
		 * 
		 * @param string $name Controller name
		 * @return \System\Controller Requested controller object
		 */
		public static function factory($name) {
			return Core::instance()->context->getController($name);
		}

        /**
         * Returns request processing context
         *
         * @return \System\Dispatcher\ContextInterface
         */
        protected static function context() {
            return Core::instance()->context;
        }
	}
?>