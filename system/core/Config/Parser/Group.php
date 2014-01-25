<?php
	namespace System\Config\Parser;
	
	/**
	 * Lines group class for config parser
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Group extends \System\Queue {
		/**
		 * Group name
		 * @var string
		 */
		public $name;
		
		/**
		 * Initiates group object
		 * @param string $name Group name
		 */
		public function __construct($name) {
			$this->name = $name;
		}
	}