<?php 
	namespace System;
	use \System\Queue\Element;
	
	/**
	 * Queue class, allows to create list which can be modified in order
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Queue implements \Countable, \ArrayAccess, \IteratorAggregate {
		/**
		 * Top of the list (first element)
		 * @var int
		 */
		const TOP = 0;
		
		/**
		 * Bottom of the list (last element)
		 * @var int
		 */
		const BOTTOM = 1;
		
		/**
		 * Queue elements
		 * @var \System\Queue\Element[]
		 */
		public $elements = array();
		
		/**
		 * Adds element to queue on selected position (on the bottom by default)
		 * 
		 * @param \System\Queue\Element $element Queue element to add
		 * @param int $position Element position
		 */
		public function push(Element $element, $position = self::BOTTOM) {
			switch ($position) {
				case self::BOTTOM:
					$this->elements[$element->name] = $element;
					break;
					
				case self::TOP:
					$this->elements = array_merge(array($element->name => $element), $this->elements);
					break;
			}
		}
		
		/**
		 * Returns first queue element and removes it by default
		 * 
		 * @param bool $remove Remove element (true by default)
		 * @return \System\Queue\Element If element exists, returns it
		 */
		public function top($remove = true) {
			if (empty($this->elements)) {
				return null;
			}
			
			foreach ($this->elements as $element) {
				if ($remove) {
					$this->remove($element->name);
				}
				
				return $element;
			}
		}
		
		/**
		 * Returns last queue element and removes it by default
		 *
		 * @param bool $remove Remove element (true by default)
		 * @return \System\Queue\Element If element exists, returns it
		 */
		public function bottom($remove = true) {
			$last = null;
			
			foreach ($this->elements as $element) {
				$last = $element;
			}
			
			if ($remove) {
				$this->remove($last->name);
			}
			
			return $last;
		}
		
		/**
		 * Returns requested element value
		 * 
		 * @param string $name Element name
		 * @return mixed Element value
		 */
		public function get($name) {
			if (isset($this->elements[$name]) && !empty($name)) {
				return $this->elements[$name]->value;
			}
			
			return null;
		}
		
		/**
		 * Overwrites element in queue, if not exist, adds it to bottom of the queue
		 * 
		 * @param \System\Queue\Element $element Element to overwrite
		 */
		public function set(Element $element) {
			$value = $this->get($element->name);
			
			if (empty($value)) {
				$this->push($element);
			} else {
				$this->elements[$element->name] = $element;
			}
		}
		
		/**
		 * Adds new element before selected element
		 * 
		 * @param string $name Selected element
		 * @param \System\Queue\Element $element Element to add
		 */
		public function before($name, Element $element) {
			$offset = $this->getIndex($name);
			
			$this->elements = array_merge(array_slice($this->elements, 0, $offset), array($element->name => $element), array_slice($this->elements, $offset));
		}
		
		/**
		 * Adds new element after selected element
		 *
		 * @param string $name Selected element
		 * @param \System\Queue\Element $element Element to add
		 */
		public function after($name, Element $element) {
			$offset = $this->getIndex($name)+1;
				
			$this->elements = array_merge(array_slice($this->elements, 0, $offset), array($element->name => $element), array_slice($this->elements, $offset));
		}
		
		/**
		 * Executes closure on each queue element with Element object in first parameter
		 * 
		 * @param \Closure $c Closure to execute
		 */
		public function each(\Closure $c) {
			foreach ($this->elements as $element) {
				$c($element);
			}
		}
		
		/**
		 * Removes selected element
		 * 
		 * @param string $name Element name to remove
		 */
		public function remove($name) {
			if (isset($this->elements[$name])) {
				unset($this->elements[$name]);
			}
		}
		
		/**
		 * Checks if element with specified name exists in queue
		 * 
		 * @param string $name Element name
		 * @return bool Element exist?
		 */
		public function exist($name) {
			foreach ($this->elements as $element) {
				if ($element->name == $name) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * Returns index position of element by name
		 * 
		 * @param string $name Element name
		 * @return int Element index
		 */
		public function getIndex($name) {
			$index = 0;
			
			foreach ($this->elements as $element) {
				if ($element->name == $name) {
					return $index;
				}
				
				$index++;
			}
		}
		
		/**
		 * @see \Countable::count()
		 */
		public function count() {
			return count($this->elements);
		}
		
		/**
		 * Array wrapper for set() method
		 * 
		 * @param string $name Element name
		 * @param mixed $value Element value
		 */
		public function offsetSet($name, $value) {
			$this->set(new Element($name, $value));
	    }
	    
	    /**
		 * Array wrapper for get() method
		 * 
		 * @param string $name Element name
		 * @return mixed Element value
		 */
	 	public function offsetGet($name) {
	        return $this->get($name);
	    }
	    
	    /**
	     * Checks if any element with specified name exists in queue
	     * 
	     * @param string $name Element name
	     * @return bool Element exist?
	     */
	    public function offsetExists($name) {
	        return $this->exist($name);
	    }
	    
	    /**
	     * Deletes specified element from queue
	     * 
	     * @param string $name Element name
	     */
	    public function offsetUnset($name) {
	        $this->remove($name);
	    }
	    
	    /**
	     * @see \IteratorAggregate::getIterator()
	     */
	    public function getIterator() {
	    	return new \ArrayIterator($this->elements);
	    }
	}
?>