<?php
	namespace System\Entity;
	
	/**
	 * Interface for framework Entity engine
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface EntityInterface {
		/**
		 * Initializes Entity with selected data
		 * 
		 * @param \stdClass $data Data to initialize in entity
		 */
		public function initialize(\stdClass $data);
		
		/**
		 * Removes entity from database
		 */
		public function remove();
		
		/**
		 * Creates entity with specified data in database
		 */
		public function create();
		
		/**
		 * Updates entity data in database
		 */
		public function update();
	}
?>