<?php
	namespace System;

	/**
	 * Class which allows to call object instance like array
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class ArrayContainer extends Container implements \ArrayAccess, \IteratorAggregate, \Serializable, \Countable {
		/**
		 * Array wrapper for set() method
		 * 
		 * @param string $offset Variable name
		 * @param mixed $value Variable value
		 */
		public function offsetSet($offset, $value) {
			$this->set($offset, $value);	
	    }
	    
	    /**
		 * Array wrapper for get() method
		 * 
		 * @param string $offset Variable name
		 * @return mixed Variable value
		 */
	 	public function offsetGet($offset) {
	        return $this->get($offset);
	    }
	    
	    /**
	     * Checks if any variable with specified name exists in container
	     * 
	     * @param string $offset Variable name
	     * @return bool Exist or not
	     */
	    public function offsetExists($offset) {
	        return isset($this->container[$offset]);
	    }
	    
	    /**
	     * Deletes specified variable from container
	     * 
	     * @param string $offset Variable name
	     */
	    public function offsetUnset($offset) {
	        unset($this->container[$offset]);
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
		 * Returns amount of container elements
		 * 
		 * @return integer Amount of elements in container
		 */
		public function count() {
			return count($this->container);
		}
		
		/**
		 * Returns ArrayIterator with container
		 * 
		 * @return ArrayIterator Array iterator
		 */
		public function getIterator() {
			return new ArrayIterator($this->container);
		}
	}
?>