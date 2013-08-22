<?php 
	namespace System;
	use \System\View\Section;
	
	/**
	 * View module of framework
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class View {
		/**
		 * View object instance
		 * @var \System\View
		 */
		private static $instance;
		
		/**
		 * Holds all view sections
		 * @var array
		 */
		private $sections = array();
		
		/**
		 * Path to page frame
		 * @var string
		 */
		private $frame;
		
		/**
		 * Rendering staus
		 * @var bool
		 */
		private $render = true;
		
		/**
		 * Creates object instance for static access
		 */
		public function __construct() {
			self::$instance = $this;
		}
		
		/**
		 * Creates section slot in frame
		 * If section not exists, calls mainAction in controller which is selected by section name and optional action name
		 * 
		 * @param string $name Section name
		 * @param string $action Action in controller to call
		 */
		public static function sectionSlot($name, $action = 'main') {
			if (empty(self::$instance->sections[$name])) {
				try {
					Core::instance()->context->callController($name, $action);
					
					if (empty(self::$instance->sections[$name])) {
						return false;
					}
				} catch (\System\Dispatcher\Exception $e) {					
					return false;
				}
			}
			
			if (count(self::$instance->sections[$name]) > 0) {
				foreach (self::$instance->sections[$name] as $key=>$section) {
					if (empty(self::$instance->buffer[$name])) {
						echo $section->getContent();
					}
				}
			}
		}
		
		/**
		 * Creates new section
		 * 
		 * @param string $name Section name
		 * @param string $file Section filename (/application/views/)
		 * @param int $expires Cache expire time (if more than 0, cache is automaticly enabled)
		 * @param int $type Cache type (Cache::GLOBAL_CACHE or Cache::USER_CACHE)
		 * @return \System\View\Section
		 */
		public static function factorySection($name, $file, $expires = 0, $type = \System\Cache::GLOBAL_CACHE) {
			return self::$instance->sections[$name][] = new Section($name, $file, $expires, $type);
		}
		
		/**
		 * Removes section
		 * 
		 * @param string $name Section name to remove
		 */
		public static function removeSection($name) {
			if (isset(self::$instance->sections[$name])) {
				unset(self::$instance->sections[$name]);
			}
		}
		
		/**
		 * Sets document frame to render
		 * 
		 * @param string $name Path to frame
		 */
		public static function setFrame($name) {
			self::$instance->frame = $name;
		}
		
		/**
		 * Sets rendering status
		 * 
		 * @param bool $status Rendering status
		 */
		public static function setRenderStatus($status) {
			self::$instance->render = $status;	
		}
		
		/**
		 * Renders view, prints out the output
		 */
		public function render() {
			if ($this->render) {
				ob_start();
				
				require $this->frame;
				
				ob_end_flush();
			}
		}
	}
?>