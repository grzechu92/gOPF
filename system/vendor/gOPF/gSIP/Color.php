<?php 
	namespace gOPF\gSIP;
	
	/**
	 * gSIP Color class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Color {
		/**
		 * Red color value
		 * @var int
		 */
		public $red = 0;
		
		/**
		 * Green color value 
		 * @var int
		 */
		public $green = 0;
		
		/**
		 * Blue color value
		 * @var int
		 */
		public $blue = 0;
		
		/**
		 * Alpha transparency value
		 * @var int
		 */
		public $alhpa = 0;
		
		/**
		 * Initiates color object
		 * 
		 * @param int $red Red color value
		 * @param int $green Green color value
		 * @param int $blue Blue color value
		 * @param int $alpha Alpha transparency value
		 */
		public function __construct($red = 0, $green = 0, $blue = 0, $alpha = 0) {
			$this->red = $red;
			$this->green = $green;
			$this->blue = $blue;
			$this->alpha = $alpha;
		}
		
		/**
		 * Returns array with colours
		 * 
		 * @return array Array with colours (RGBA)
		 */
		public function getColor() {
			return array($this->red, $this->green, $this->blue, $this->alpha);
		}
	}
?>