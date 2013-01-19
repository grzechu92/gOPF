<?php 
	namespace gOPF;
	
	/**
	 * Paginator class
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Paginator {
		/**
		 * Results per page
		 * @var int
		 */
		private $divider = 10;
		
		/**
		 * Amount of pages in listing before and after current page
		 * @var int
		 */
		private $offset = 5;
		
		/**
		 * Fill pages listing to offset*2+1
		 * @var bool
		 */
		private $fill = false;
		
		/**
		 * Force showing backward and forward buttons
		 * @var bool
		 */
		private $force = false;
		
		/**
		 * Current page
		 * @var int
		 */
		private $current = 0;
		
		/**
		 * Calculated amount of pages
		 * @var int
		 */
		private $pages = 0;
		
		/**
		 * Total amount of results to paginate
		 * @var int
		 */
		private $total = 0;
		
		/**
		 * Default patterns
		 * @var array
		 */
		private $patterns = array(
			'backward' => '&laquo;',
			'forward' => '&raquo;',
			'list' => '{number}',
			'button' => '<a href="{url}" class="{class}">{content}</a>',
			'currentClass' => 'current',
			'forwardClass' => 'forward',
			'backwardClass' => 'backward',
			'path' => '{number}'
		);
		
		/**
		 * Calculated listing begining value
		 * @var integer
		 */
		private $begin = 0;
		
		/**
		 * Calculated listing ending value
		 * @var integer
		 */
		private $end = 0;
		
		/**
		 * Initiates Paginator class
		 * 
		 * @param int $total Total amount of results to paginate
		 * @param int $current Current page number
		 * @param int $divider Results per page
		 * @param int $offset Amount of pages in listing before and after current page
		 * @param bool $fill Fill pages listing to offset*2+1
		 * @param bool $force Force showing backward and forward buttons
		 * @param array $patterns Custom patterns
		 */
		public function __construct($total, $current, $divider = 10, $offset = 5, $fill = false, $force = false, $patterns = array()) {
			$this->total = $total;
			$this->current = $current;
			$this->divider = $divider;
			$this->offset = $offset;
			$this->fill = $fill;
			$this->force = $force;
			$this->patterns = array_merge($this->patterns, $patterns);
		}
		
		/**
		 * Generates pagination section
		 *
		 * @return string Generated pagination section
		 */
		public function generate() {
			$this->pages = ceil($this->total/$this->divider);
				
			$this->calculate();
				
			return $this->createButton('backward').$this->createList().$this->createButton('forward');
		}
		
		/**
		 * Calculates begin and end value of listing
		 */
		private function calculate() {
			$begin = 0;
			$offset = $this->offset;
				
			while ($begin <= 0) {
				$begin = $this->current-$offset;
		
				if ($begin <= 0) {
					$offset--;
						
					if ($this->fill) {
						$this->offset++;
					}
				}
			}
				
			$end = $this->current+$this->offset;
			$offset = $this->offset;
				
			while ($end > $this->pages) {
				$end--;
		
				if ($this->fill && $begin > 1) {
					$begin--;
				}
			}
				
			$this->begin = $begin;
			$this->end = $end;
		}
		
		/**
		 * Creates listing, parses buttons etc.
		 *
		 * @return string Generated listing as string
		 */
		private function createList() {
			$list = '';
				
			for ($page = $this->begin; $page <= $this->end; $page++) {
				$url = $this->parsePattern('path', array('number' => $page));
		
				$list .= $this->parsePattern('button', array(
					'url' => $url,
					'content' => $this->parsePattern('list', array('number' => $page)),
					'class' => $this->isCurrent($url) ? $this->patterns['currentClass'] : ''
				));
			}
				
			return $list;
		}
		
		/**
		 * Creates button of specified type
		 *
		 * @param string $type Type of button
		 * @return string Generated button
		 */
		private function createButton($type) {
			$current = $this->current;
			
			if (($type == 'backward' && ($current-1) >= 1) || ($type == 'forward' && ($current+1) <= $this->pages)) {
				$url = $this->parsePattern('path', array('number' => ($type == 'backward') ? $current-1 : $current+1));
			} elseif ($this->force) {
				$url = '#';
			} else {
				return '';
			}
			
			return $this->parsePattern('button', array(
					'url' => $url,
					'content' => $this->parsePattern($type),
					'class' => ($type == 'backward') ? $this->patterns['backwardClass'] : $this->patterns['forwardClass']
			));
		}
		
		/**
		 * Parses specified pattern
		 *
		 * @param string $type Type of pattenr
		 * @param array $variables Array with variables to match (optional)
		 * @return string Parsed pattern
		 */
		private function parsePattern($type, $variables = array()) {
			$pattern = $this->patterns[$type];
				
			if (!empty($variables)) {
				foreach ($variables as $name => $value) {
					$pattern = str_replace('{'.$name.'}', $value, $pattern);
				}
			}
				
			return $pattern;
		}
		
		/**
		 * Checks if specified path is current URL path
		 *
		 * @param string $path Path to check
		 * @return bool Is current?
		 */
		private function isCurrent($path) {
			if (strpos($_SERVER['REQUEST_URI'], $path) !== false) {
				return true;
			} else {
				return false;
			}
		}
	}
?>