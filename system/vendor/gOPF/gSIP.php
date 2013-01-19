<?php
	namespace gOPF;
	use \gOPF\gSIP\Size;
	use \gOPF\gSIP\Layer;
	use \gOPF\gSIP\Position;
	use \gOPF\gSIP\Exception;
	use \System\Queue;
	use \System\Queue\Element;

	/**
	 * gSIP - gSIP Sophisticated Image Parser
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class gSIP {
		/**
		 * Move layer to top of the image
		 * @var int
		 */
		const TOP = 0;
		
		/**
		 * Move layer to bottom of the image 
		 * @var int
		 */
		const BOTTOM = 1;
		
		/**
		 * Move layer before specified layer
		 * @var int
		 */
		const BEFORE = 2;
		
		/**
		 * Move layer after specified layer
		 * @var int
		 */
		const AFTER = 3;
		
		/**
		 * Mime type for PNG image
		 * @var string
		 */
		const PNG = 'image/png';
	
		/**
		 * Mime type for JPEG image
		 * @var string
		 */
		const JPEG = 'image/jpeg';
	
		/**
		 * Mime type for GIF image
		 * @var string
		 */
		const GIF = 'image/gif';
		
		/**
		 * Array with layers
		 * @var Queue
		 */
		private $layers;
		
		/**
		 * Image resource for merged image
		 * @var resource
		 */
		private $merged = null;
		
		/**
		 * Initiates gSIP object
		 */
		public function __construct() {
			$this->layers = new Queue();
		}
		
		/**
		 * Creates new layer
		 * 
		 * @param string $name Layer name
		 * @param Size $size Layer size
		 * @param Position $position Layer position on first layer
		 * @return Layer New initialized layer
		 */
		public function createLayer($name, Size $size = null, Position $position = null) {
			$layer = new Layer($name, $size, $position);
			
			$this->layers->push(new Element($name, $layer), Queue::TOP);
			
			return $layer;
		}
		
		/**
		 * Returns requested layer from registry
		 * 
		 * @param string $name Requested layer name
		 * @return Layer Requested layer, if exists
		 * @throws Exception
		 */
		public function getLayer($name) {
			if (!$this->layer->exist($name)) {
				throw new Exception('Selected layer does not exists: '.$name);
			}
			
			return $this->layers->get($name);
		}
		
		/**
		 * Removes layer from registry
		 * 
		 * @param string $name Layer to remove
		 * @return gSIP Fluid interface
		 */
		public function removeLayer($name) {
			$this->layers->remove($name);
			
			return $this;
		}
		
		/**
		 * Sets layer into registry
		 * 
		 * @param string $name Layer name
		 * @param Layer $layer Layer object instance
		 * @return gSIP Fluid interface
		 */
		public function setLayer($name, Layer $layer) {
			$this->layers->set(new Element($name, $layer));
			
			return $this;
		}
		
		/**
		 * Creates an new image by merging all layers together
		 * 
		 * @param string $type Image type (gSIP::GIF, gSIP::PNG, gSIP::JPEG)
		 * @param string $filename Output path with filename
		 * @param int $quality Image quality in percents
		 * @return gSIP Fluid interface
		 * @throws Exception
		 */
		public function createImage($type, $filename = null, $quality = 100) {
			$this->mergeLayers();
			
			if (empty($filename)) {
				header('Content-Type: '.$type);
			}
				
			switch ($type) {
				case self::GIF:
					imagegif($this->merged, $filename);
					break;
			
				case self::JPEG:
					imagejpeg($this->merged, $filename, $quality);
					break;
			
				case self::PNG:
					imagepng($this->merged, $filename, round($quality/10)-1);
					break;
					
				default:
					throw new Exception('Unsupported image export format');
					break;
			}
				
			return $this;
		}
		
		/**
		 * Merges all layers together
		 */
		public function mergeLayers() {
			$this->merged = $this->merge();
		}
		
		/**
		 * Merges all layers together and saves it into another layer
		 * 
		 * @param string $name Layer name
		 */
		public function mergeLayersTo($name) {
			$layer = $this->getLayer($name);
			$layer->content = $this->merge();
			$this->setLayer($name, $layer);
		}
		
		/**
		 * Merges all layers and returns it
		 * 
		 * @throws Exception
		 */
		private function merge() {
			$merged = null;
			
			if (count($this->layers) > 0) {
				if (!empty($name)) {
					$merged = $this->layers->get($name)->content;
				} else {
					$merged = $this->generateBlankCanvas();
				}
				
				foreach ($this->layers as $layer) {
					$layer = $layer->value;
					
					if (!empty($layer->content)) {
						imagecopy($merged, $layer->content, $layer->position->x, $layer->position->y, 0, 0, $layer->size->width, $layer->size->height);
					}
				}
				
				return $merged;
			} else {
				throw new Exception('Ooooops! There is nothing to merge!');
			}
		}
		
		/**
		 * Creates image layer which has size of all layers
		 * 
		 * @return resource Created image
		 */
		private function generateBlankCanvas() {
			$width = 0;
			$height = 0;
			
			foreach ($this->layers as $layer) {
				$layer = $layer->value;
				
				$w = $layer->size->width+$layer->position->x;
				$h = $layer->size->height+$layer->position->y;
				
				if ($width < $w) {
					$width = $w;
				}
				
				if ($height < $h) {
					$height = $h;
				}
			}
			
			$image = imagecreatetruecolor($width, $height); 
			$color = imagecolorallocate($image, 0, 0, 0);
			
			imagefill($image, 0, 0, $color);
			imagecolortransparent($image);
			
			return $image;
		}
	}
?>