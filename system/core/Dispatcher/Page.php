<?php
	namespace System\Dispatcher;
	use \System\Request;
	
	/**
	 * Page request processing context
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Page extends Context implements ContextInterface {
		/**
		 * @see \System\Dispatcher\ContextInterface::process()
		 */
		public function process() {
            $view = \System\View::instance();

            $view->setFrame(__APPLICATION_PATH.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'default.php');
            $this->callController(Request::$controller, Request::$action, true);
            $view->render();
        }
	}
?>