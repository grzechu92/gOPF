<?php
	namespace System;
	
	/**
	 * Checks request context, and starts specified request processing procedure
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Dispatcher {		
		/**
		 * Starts specific request processing type
		 */
		public function dispatch() {
			switch (Request::$context) {
				case 'rest':
					$mode = 'Rest';
					break;
						
				case 'cron':
					$mode = 'Cron';
					break;
			
				case 'terminal':
					$mode = 'Terminal';
					break;
			
				default:
					$mode = 'Page';
					break;
			}


            $class = '\\System\\Dispatcher\\'.$mode;

            /** @var $context \System\Dispatcher\ContextInterface */
            $context = new $class();

			Core::instance()->context = $context;
			$context->process();
		}
	}
?>