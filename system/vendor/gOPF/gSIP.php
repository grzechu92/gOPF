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
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class gSIP {
		const VERSION = '2.1.1';
		
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
		 * @var \System\Queue
		 */
		private $layers;
		
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
		 * @param \gOPF\gSIP\Size $size Layer size
		 * @param \gOPF\gSIP\Position $position Layer position on first layer
		 * @return \gOPF\gSIP\Layer New initialized layer
		 */
		public function create($name, Size $size = null, Position $position = null) {
			$layer = new Layer($name, $size, $position);
			
			$this->layers->push(new Element($name, $layer), Queue::TOP);
			
			return $layer;
		}
		
		/**
		 * Returns requested layer from registry
		 * 
		 * @param string $name Requested layer name
		 * @return \gOPF\gSIP\Layer Requested layer, if exists
		 * @throws \gOPF\gSIP\Exception
		 */
		public function get($name) {
			if (!$this->layers->exist($name)) {
				throw new Exception('Selected layer does not exists: '.$name);
			}
			
			return $this->layers->get($name);
		}
		
		/**
		 * Removes layer from registry
		 * 
		 * @param string $name Layer to remove
		 * @return \gOPF\gSIP Fluid interface
		 */
		public function remove($name) {
			$this->layers->remove($name);
			
			return $this;
		}
		
		/**
		 * Sets layer into registry
		 * 
		 * @param string $name Layer name
		 * @param \gOPF\gSIP\Layer $layer Layer object instance
		 * @return \gOPF\gSIP Fluid interface
		 */
		public function set($name, Layer $layer) {
			$this->layers->set(new Element($name, $layer));
			
			return $this;
		}
		
		/**
		 * Insterts layer after selected layer
		 * 
		 * @param string $name Selected layer name
		 * @param \gOPF\gSIP\Layer $layer Layer to insert
		 * @return \gOPF\gSIP Fluid interface
		 */
		public function after($name, Layer $layer) {
			$this->remove($layer->name);
			$this->layers->before($name, new Element($layer->name, $layer));
			
			return $this;
		}
		
		/**
		 * Insterts layer before selected layer
		 *
		 * @param string $name Selected layer name
		 * @param \gOPF\gSIP\Layer $layer Layer to insert
		 * @return \gOPF\gSIP Fluid interface
		 */
		public function before($name, Layer $layer) {
			$this->remove($layer->name);
			$this->layers->after($name, new Element($layer->name, $layer));
			
			return $this;
		}
		
		/**
		 * Creates an new image by merging all layers together
		 * 
		 * @param string $type Image type (gSIP::GIF, gSIP::PNG, gSIP::JPEG)
		 * @param string $filename Output path with filename
		 * @param int $quality Image quality in percents
		 * @return \gOPF\gSIP Fluid interface
		 * @throws \gOPF\gSIP\Exception
		 */
		public function export($type, $filename = null, $quality = 100) {
			$merged = $this->merge();
			
			if (empty($filename)) {
				header('Content-Type: '.$type);
			}
				
			switch ($type) {
				case self::GIF:
					imagegif($merged, $filename);
					break;
			
				case self::JPEG:
					imagejpeg($merged, $filename, $quality);
					break;
			
				case self::PNG:
					imagepng($merged, $filename, round($quality/10)-1);
					break;
					
				default:
					throw new Exception('Unsupported image export format');
					break;
			}
				
			return $this;
		}
		
		/**
		 * Merges all layers to another layer
		 * 
		 * @param string $name New layer name
		 * @return \gOPF\gSIP\Layer Created layer
		 */
		public function mergeToLayer($name) {
			$merged = $this->merge();
			
			$layer = new Layer($name, new Size(imagesx($merged), imagesy($merged)));
			$layer->content = $merged;
			
			return $layer;
		}
		
		/**
		 * Merges all layers
		 * 
		 * @return resource Merged image resource
		 * @throws \gOPF\gSIP\Exception
		 */
		private function merge() {
			if (count($this->layers) > 0) {
				$merged = $this->generateBlankCanvas();
				
				foreach (array_reverse($this->layers->elements) as $layer) {
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
						
			$image = new Layer('', new Size($width, $height));
			$image->makeTransparent();
			
			return $image->content;
		}
	}
?>