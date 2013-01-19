<?php 
	namespace gOPF\gSIP;	

	/**
	 * gSIP Size class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Size {
		/**
		 * Width value
		 * @var int
		 */
		public $width = 1;
		
		/**
		 * Height value
		 * @var height
		 */
		public $height = 1;
		
		/**
		 * Initializes size object
		 * 
		 * @param int $width Width value
		 * @param int $height Height value
		 * @throws Exception
		 */
		public function __construct($width, $height = false) {
			if ($height === false) {
				$height = $width;
			}
			
			if ($width <= 0 || $height <= 0) {
				throw new Exception((($width <= 0) ? 'Width' : 'Height').' can not be less or equal to 0');
			}
			
			$this->width = $width;
			$this->height = $height;
		}
		
		/**
		 * Returns array with width and height
		 * 
		 * @return arrat Array with values
		 */
		public function getSize() {
			return array($this->width, $this->height);
		}
	}
?>