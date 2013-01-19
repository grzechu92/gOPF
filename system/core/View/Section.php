<?php 
	namespace System\View;
	use System\Cache;
	
	/**
	 * View section class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Section extends \System\Container {
		/**
		 * Cache name prefix
		 * @var string
		 */
		const PREFIX = 'SECTION-';
		
		/**
		 * Application section (in /application/views/)
		 * @var int
		 */
		const APPLICATION = 0;
		
		/**
		 * System section (in /system/views/)
		 * @var int
		 */
		const SYSTEM = 1;
		
		/**
		 * Custom path to section (full pathname required)
		 * @var int
		 */
		const CUSTOM = 2;
		
		/**
		 * Section filename
		 * @var string
		 */
		private $file;
		
		/**
		 * Section name
		 * @var string
		 */
		private $name;
		
		/**
		 * Section expire time
		 * @var int
		 */
		private $expires = 0;
		
		/**
		 * Section caching status
		 * @var bool
		 */
		private $cache = false;
		
		/**
		 * Section cache element name
		 * @var string
		 */
		private $cacheName;
		
		/**
		 * Setion cache type
		 * @var int
		 */
		private $cacheType;
		
		/**
		 * Rendered section content
		 * @var string
		 */
		private $content;
		
		/**
		 * Cache element validity
		 * @var bool
		 */
		private $valid;
		
		/**
		 * Section type (Section::APPLICATION (default) or Section::SYSTEM or Section::CUSTOM)
		 * @var int
		 */
		private $type = self::APPLICATION;
		
		/**
		 * Initiates section object
		 * 
		 * @param string $name Section name
		 * @param string $file Section filename (/application/views/)
		 * @param int $expires Cache expire time (if more than 0, cache is automaticly enabled)
		 * @param int $type Cache type (Cache::GLOBAL_CACHE or Cache::USER_CACHE)
		 */
		public function __construct($name, $file, $expires = 0, $type = Cache::GLOBAL_CACHE) {
			$this->container = new \System\ArrayContainer();
			
			$this->name = $name;
			$this->file = $file;
			
			if ($expires > 0) {
				$this->cache = true;
				$this->cacheType = $type;
				$this->expires = $expires;
				$this->cacheName = self::PREFIX.$this->name;
			}
		}
		
		/**
		 * If cache is enabled, and cache element is not valid, saves section content into cache
		 */
		public function __destruct() {
			if ($this->cache && !$this->valid) {
				Cache::set($this->cacheName, $this->content, $this->expires, $this->cacheType);
			}
		}
		
		/**
		 * Checks if section cache element is valid
		 * Allows to change cache element name in parameter $name
		 * 
		 * @param string $name New cache name
		 * @return bool Is valid?
		 */
		public function isValid($name = '') {
			if (!empty($name)) {
				$this->cacheName = self::PREFIX.$name;
			}
			
			return $this->valid = Cache::isValid($this->cacheName, $this->cacheType);
		}
		
		
		
		/**
		 * Sets content of section
		 * 
		 * @param string $content Section content
		 */
		public function setContent($content = '') {
			$this->content = $content;
		}
		
		/**
		 * Changes type of section, it means, path to file template
		 * 
		 * @param int $type (Section::APPLICATION or Section::SYSTEM)
		 */
		public function setType($type) {
			$this->type = $type;
		}
		
		/**
		 * Returns section content
		 * If is generated, or cached, returns it, if not, renders it
		 * 
		 * @return string Section content
		 */
		public function getContent() {
			if (!empty($this->content)) {
				return $this->content;
			}
			
			if ($this->cache && $this->valid) {
				return $this->content = Cache::get($this->cacheName, $this->cacheType);
			}
			
			return $this->content = $this->render();
		}
		
		/**
		 * Renders section and returns content
		 * 
		 * @return string Rendered section content
		 */
		private function render() {
			if (\System\Core::STAGE == __DEVELOPMENT) {
				if (!\System\Filesystem::checkFile($this->prepareFilename())) {
					throw new \System\View\Exception(\System\I18n::translate('TEMPLATE_NOT_FOUND', array($this->prepareFilename())));
				}
			}
			
			ob_start();
			
			$section = $this->container;
			require $this->prepareFilename();
			
			$content = ob_get_clean();
			
			if ($this->cache) {
				Cache::set($this->cacheName, $content, $this->expires, $this->cacheType);
			}
			
			return $content;
		}
		
		/**
		 * Returns full path of template file
		 * 
		 * @return string Path to template file
		 */
		private function prepareFilename() {
			if ($this->type != self::CUSTOM) {
				return (($this->type == self::APPLICATION) ? __APPLICATION_PATH : __SYSTEM_PATH).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$this->file;
			} else {
				return $this->file;
			} 
		}
	}
?>