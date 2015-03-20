<?php
	namespace System\Entity;
	
	/**
	 * Interface for framework Entity engine
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface EntityInterface {
		/**
		 * Initializes Entity with selected data
		 *
		 * @param mixed $data Data to initialize in entity
		 */
		public function initialize($data);

		/**
		 * Creates entity with specified data in database
		 */
		public function create();

		/**
		 * Reads entity data from database
		 */
		public function read();

		/**
		 * Updates entity data in database
		 */
		public function update();

		/**
		 * Delete entity from database
		 */
		public function delete();
	}
?>