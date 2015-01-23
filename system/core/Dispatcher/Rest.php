<?php
	namespace System\Dispatcher;
	use System\Request;
	
	/**
	 * REST request processing context
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Rest extends Context implements ContextInterface {
		/**
		 * @see \System\Dispatcher\ContextInterface::process()
		 */
		public function process() {
            $this->isAccessible(Request::$controller, Request::$action.'Rest');
            $data = $this->callAction(Request::$controller, Request::$action.'Rest');

			$this->toJSON($data);
		}
	}
?>