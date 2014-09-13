<?php 
	namespace System;
	use \System\View\Section;
	
	/**
	 * View module of framework
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class View extends Singleton {
		/**
		 * Holds all view sections
		 * @var \System\View\Section[]
		 */
		private $sections = array();
		
		/**
		 * Path to page frame
		 * @var string
		 */
		public $frame;
		
		/**
		 * Rendering status
		 * @var bool
		 */
		public $render = true;
		
		/**
		 * Creates section slot in frame
		 * If section not exists, calls mainAction in controller which is selected by section name and optional action name
		 * 
		 * @param string $name Section name
		 * @param string $action Action in controller to call
         * @return bool Section rendering status
		 */
		public static function sectionSlot($name, $action = 'main') {
			if (empty(self::instance()->sections[$name])) {
				try {
					Core::instance()->context->callController($name, $action);
					
					if (empty(self::instance()->sections[$name])) {
						return false;
					}
				} catch (\System\Dispatcher\Exception $e) {					
					return false;
				}
			}
			
			if (count(self::instance()->sections[$name]) > 0) {
                /** @var $section \System\View\Section */
                foreach (self::instance()->sections[$name] as $section) {
                    echo $section->getContent();
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
			return self::instance()->sections[$name][] = new Section($name, $file, $expires, $type);
		}
		
		/**
		 * Removes section
		 * 
		 * @param string $name Section name to remove
		 */
		public static function removeSection($name) {
			if (isset(self::instance()->sections[$name])) {
				unset(self::instance()->sections[$name]);
			}
		}
		
		/**
		 * Sets document frame to render
		 * 
		 * @param string $name Absolute path to frame
		 */
		public function setFrame($name) {
			$this->frame = $name;
		}
		
		/**
		 * Sets rendering status
		 * 
		 * @param bool $status Rendering status
		 */
		public function setRenderStatus($status) {
			$this->render = $status;
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