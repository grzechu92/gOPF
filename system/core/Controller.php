<?php
	namespace System;
	
	/**
	 * Main class of controller
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Controller {
		/**
		 * Main controller action
         *
         * @gOPF-Access guest
         * @gOPF-State dynamic
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