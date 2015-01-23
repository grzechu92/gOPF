<?php
	namespace System\Router;
	
	/**
	 * Interface for validable controller
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface ValidableInterface {
		/**
		 * Matches controller
		 * 
		 * @param \System\Router\Route $route Currently matching route
		 * @return bool Is matching?
		 */
		public static function validate(\System\Router\Route &$route);
	}
?>