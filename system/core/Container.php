<?php
	namespace System;
	
	/**
	 * Class which allows to use object as container (magic setter/getter and methods set()/get())
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Container implements \Countable, \IteratorAggregate, \Serializable {
		/**
		 * Holds content of container
		 * @var array
		 */
		protected $container = array();
		
		/**
		 * Allows to define container value
		 * 
		 * @param array $value Value of container
		 */
		public function __construct(array $value = array()) {
			$this->container = $value;
		}
		
		/**
		 * Magic wrapper for set() method
		 * 
		 * @param string $offset Variable name
		 * @param mixed $value Variable value
		 */
		public function __set($offset, $value) {
			$this->set($offset, $value);
		}
		
		/**
		 * Magic wrapper for get() method
		 * 
		 * @param string $offset Variable name
		 * @return mixed Variable value
		 */
		public function __get($offset) {
			return $this->get($offset);
		}
			
		/**
		 * Sets variable value in container
		 * 
		 * @param string $offset Variable name
		 * @param mixed $value Variable value
		 */
		public function set($offset, $value) {
			$this->container[$offset] = $value;
		}
		
		/**
		 * Gets variable value from container
		 * 
		 * @param string $offset Variable name
		 * @return mixed Variable value
		 */
		public function get($offset) {
			return isset($this->container[$offset]) ? $this->container[$offset] : null;
		}

		/**
		 * @see Countable::count()
		 */
		public function count() {
			return count($this->elements);
		}
		
		/**
		 * Serializes container content
		 *
		 * @return string Serialized content
		 */
		public function serialize() {
			return serialize($this->container);
		}
		
		/**
		 * Unserializes and writes content into container
		 *
		 * @param string $data Serialized content
		 */
		public function unserialize($data) {
			$this->contaniner = unserialize($data);
		}
		 
		/**
		 * @see IteratorAggregate::getIterator()
		 */
		public function getIterator() {
			return new \ArrayIterator($this->container);
		}
	} 
?>