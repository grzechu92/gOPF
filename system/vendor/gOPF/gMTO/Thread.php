<?php 
	namespace gOPF\gMTO;
	
	/**
	 * gMTO Thread class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Thread {
		/**
		 * Thread id
		 * @var string
		 */
		public $id;
		
		/**
		 * Thread name
		 * @var string
		 */
		public $name;
		
		/**
		 * Processed thread data
		 * @var mixed
		 */
		public $data = null;
		
		/**
		 * Method to process in thread
		 * @var string
		 */
		private $job;
		
		/**
		 * Parameters to process method
		 * @var array
		 */
		private $parameters = array();
		
		/**
		 * Initiates thread object
		 * 
		 * @param string $name Thread name
		 * @param string $job Method to process in thread (\Namespace\Classname::staicMethod or \Namespace\Classname->dynamicMethod)
		 * @param array $parameters Array with parameters
		 */
		public function __construct($name, $job, array $parameters = array()) {
			$this->id = sha1(rand());
			$this->name = $name;
			$this->job = $job;
			$this->parameters = $parameters;
		}
		
		/**
		 * Performs the job
		 */
		public function process() {
			$job = $this->job;
			
			if (strpos($job, '->')) {
				list($class, $method) = explode('->', $job);
		
				$object = new $class();
				return call_user_func_array(array($object, $method), $this->parameters);
			} else {
				return call_user_func_array($job, $this->parameters);
			}
		}
	}
?>