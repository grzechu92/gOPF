<?php
	namespace gOPF\gSIP;
	use gOPF\gSIP;
	
	/**
	 * gSIP Layer class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Layer {
		/**
		 * Layer size
		 * @var \gOPF\gSIP\Size
		 */
		public $size;
		
		/**
		 * Layer position
		 * @var \gOPF\gSIP\Position
		 */
		public $position;
		
		/**
		 * Layer name
		 * @var string
		 */
		public $name;
		
		/**
		 * Layer content
		 * @var resource
		 */
		public $content;
		
		/**
		 * Initiates layer object
		 * 
		 * @param string $name Layer name
		 * @param \gOPF\gSIP\Size $size Layer size
		 * @param \gOPF\gSIP\Position $position Layer position
		 */
		public function __construct($name, Size $size = null, Position $position = null) {
			$this->name = $name;
			
			$this->size = (empty($size) ? new Size(1, 1) : $size);
			$this->position = (empty($position) ? new Position(0, 0) : $position);
			
			if (!empty($size)) {
				$this->content = imagecreatetruecolor($size->width, $size->height);
				$this->makeTransparent();
			}
		}
		
		/**
		 * Imports image into layer
		 * 
		 * @param string $path Path to image
		 * @throws \gOPF\gSIP\Exception
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function import($path) {
			$info = getimagesize($path);
			
			try {
				$this->size = new Size($info[0], $info[1]);
			} catch (Exception $e) {
				throw new Exception('Unable to open image file: '.$path);
			}
			
			switch ($info['mime']) {
				case gSIP::GIF:
					$image = imagecreatefromgif($path);
					break;
				
				case gSIP::JPEG:
					$image = imagecreatefromjpeg($path);
					break;
				
				case gSIP::PNG:
					$image = imagecreatefrompng($path);
					break;
					
				default:
					throw new Exception('Unsupported image mime type: '.$info['mime']);
					break;
			}
			
			$this->content = $image;
			
			return $this;
		}
		
		/**
		 * Checks if image on layer is valid
		 * 
		 * @return bool Is valid?
		 */
		public function isValid() {
			$status = @imagecolorat($this->content, 0, 0);
				
			return !empty($status);
		}
		
		/**
		 * Moves layer into specified position
		 * 
		 * @param \gOPF\gSIP\Position $position Target position
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function move(Position $position) {
			$this->position = $position;
			
			return $this;
		}
		
		/**
		 * Cuts layer for specifiec parameters (size and position) 
		 * 
		 * @param \gOPF\gSIP\Size $size Target size
		 * @param \gOPF\gSIP\Position $position Target position
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function crop(Size $size, Position $position = null) {
			if (empty($position)) {
				$position = new Position(0, 0);
			}
			
			imagecopy($this->content, $this->content, 0, 0, $position->x, $position->y, $position->x+$size->width, $position->y+$size->height);
			
			$this->size = $size;
			$this->position = new Position(0, 0);
			
			return $this;
		}
		
		/**
		 * Resizes layer into specified size
		 * 
		 * @param \gOPF\gSIP\Size $targetSize Target size values
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function resize(Size $targetSize) {
			$scaled = imagecreatetruecolor($targetSize->width, $targetSize->height);
			imagealphablending($scaled, false);
			imagesavealpha($scaled,true);
			imagecopyresampled($scaled, $this->content, 0, 0, 0, 0, $targetSize->width, $targetSize->height,  $this->size->width,  $this->size->height);
			
			$this->content = $scaled;
			$this->size = $targetSize;
				
			return $this;
		}
		
		/**
		 * Scales layer by ratio
		 * 
		 * @param float $ratio Scale ratio (1.0 = 100%)
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function scale($ratio) {
			$this->resize(new Size(round($this->size->width*$ratio), round($this->size->height*$ratio)));
			
			return $this;
		}
		
		/**
		 * Resizes layer to requested height
		 * 
		 * @param int $height Height size
		 * @return \gOPF\gSIP\Layer Fluid Interface
		 */
		public function resizeToHeight($height) {
			$this->scale($height/$this->size->height);
			
			return $this;
		}
		
		/**
		 * Resizes layer to requested width
		 *
		 * @param int $width Width size
		 * @return \gOPF\gSIP\Layer Fluid Interface
		 */
		public function resizeToWidth($width) {
			$this->scale($width/$this->size->width);
				
			return $this;
		}
		
		/**
		 * Makes specified color transparent
		 * 
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function makeTransparent() {
			imagesavealpha($this->content, true); 
			imagealphablending($this->content, false);
			imagefill($this->content, 0, 0, 0x7FFFFFFFFFF);
			imagealphablending($this->content,true);
			
			return $this;
		}
		
		/**
		 * Gets color values from point on layer
		 * 
		 * @param \gOPF\gSIP\Position $position Selected pixel coordinates
		 * @return \gOPF\gSIP\Color Color object with color values
		 */
		public function getPixel(Position $position) {
			$data = imagecolorsforindex($this->content, imagecolorat($this->content, $position->x, $position->y));
			
			return new Color($data['red'], $data['green'], $data['blue'], $data['alpha']);
		}
		
		/**
		 * Sets selected pixel with specified color values
		 * 
		 * @param \gOPF\gSIP\Position $position Selected pixel coordinates
		 * @param \gOPF\gSIP\Color $color Color values
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function setPixel(Position $position, Color $color) {
			imagesetpixel($this->content, $position->x, $position->y, $this->allocateColor($color));
			
			return $this;
		}
		
		/**
		 * Draws a rectangle on layer
		 * 
		 * @param \gOPF\gSIP\Position $position Left-top corner of rectangle
		 * @param \gOPF\gSIP\Size $size Size of rectangle
		 * @param \gOPF\gSIP\Color $color Color of rectangle
		 * @param bool $fill Fill rectangle
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function drawRectangle(Position $position, Size $size, Color $color, $fill = false) {
			$function = ($fill) ? 'imagefilledrectangle' : 'imagerectangle';
			
			$function($this->content, $position->x, $position->y, $position->x+$size->width, $position->y+$size->height, $this->allocateColor($color));
				
			return $this;
		}
		
		/**
		 * Draws a ellipse on layer
		 * 
		 * @param \gOPF\gSIP\Position $position Center position of ellipse
		 * @param \gOPF\gSIP\Size $size Width and height od ellipse
		 * @param \gOPF\gSIP\Color $color Color of ellipse
		 * @param bool $fill Fill elipse
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function drawEllipse(Position $position, Size $size, Color $color, $fill = false) {
			$function = ($fill) ? 'imagefilledellipse' : 'imageellipse';
				
			$function($this->content, $position->x, $position->y, $size->width, $size->height, $this->allocateColor($color));
				
			return $this;
		}
		
		/**
		 * Draws a arc on layer
		 * 
		 * @param \gOPF\gSIP\Position $position Center position of arc
		 * @param \gOPF\gSIP\Size $size Width and height of arc
		 * @param int $start Start angle
		 * @param int $end End angle
		 * @param \gOPF\gSIP\Color $color Color of arc
		 * @param bool $fill Fill arc
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function drawArc(Position $position, Size $size, $start = 0, $end = 360, Color $color, $fill = false) {
			$color = $this->allocateColor($color);
				
			if ($fill) {
				imagefilledarc($this->content, $position->x, $position->y, $size->width, $size->height, $start, $end, $color, IMG_ARC_PIE);
			} else {
				imagearc($this->content, $position->x, $position->y, $size->width, $size->height, $start, $end, $color);
			}
		
			return $this;
		}
		
		/**
		 * Draws a circle on layer
		 * 
		 * @param \gOPF\gSIP\Position $position Center position of circle
		 * @param int $radius Circle radius
		 * @param \gOPF\gSIP\Color $color Color of circle
		 * @param bool $fill Fill circle
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function drawCircle(Position $position, $radius, Color $color, $fill = false) {
			$this->drawArc($position, new Size($radius*2, $radius*2), 0, 360, $color, $fill);
				
			return $this;
		}
		
		/**
		 * Draws a line on layer
		 * 
		 * @param \gOPF\gSIP\Position $start Start position
		 * @param \gOPF\gSIP\Position $end End position
		 * @param \gOPF\gSIP\Color $color Color of line
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function drawLine(Position $start, Position $end, Color $color) {
			imageline($this->content, $start->x, $start->y, $end->x, $end->y, $this->allocateColor($color));
				
			return $this;
		}
		
		/**
		 * Puts text on layer
		 * 
		 * @param string $text Text to put on layer
		 * @param \gOPF\gSIP\Position $position Position of left-top corner
		 * @param int $size Size of text
		 * @param \gOPF\gSIP\Color $color Color of text
		 * @param string $fontFile Path to TTF font file
		 * @param int $angle Angle of text
         * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function putText($text, Position $position, $size = 5, Color $color, $fontFile = '', $angle = 0) {
			$color = $this->allocateColor($color);
				
			if (empty($fontFile)) {
				imagestring($this->content, $size, $position->x, $position->y, $text, $color);
			} else {
				imagettftext($this->content, $size, $angle, $position->x, $position->y, $color, $fontFile, $text);
			}
				
			return $this;
		}
		
		/**
		 * Negatives the layer colors
		 * 
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function negative() {
			imagefilter($this->content, IMG_FILTER_NEGATE);
			
			return $this;
		}
		
		/**
		 * Desaturates the layer
		 * 
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function desaturate() {
			imagefilter($this->content, IMG_FILTER_GRAYSCALE);
			
			return $this;
		}
		
		/**
		 * Adjusts brightness of the layer
		 * 
		 * @param int $value Brightness level
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function brightness($value) {
			imagefilter($this->content, IMG_FILTER_BRIGHTNESS, $value);
			
			return $this;
		}
		
		/**
		 * Adjusts contrast of the layer
		 * 
		 * @param int $value Contrast level
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function contrast($value) {
			imagefilter($this->content, IMG_FILTER_CONTRAST, $value);
			
			return $this;
		}
		
		/**
		 * Colorizes the layer
		 * 
		 * @param \gOPF\gSIP\Color $color Color values
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function colorize(Color $color) {
			imagefilter($this->content, IMG_FILTER_COLORIZE, $color->red, $color->green, $color->blue, $color->alpha);
			
			return $this;
		}
		
		/**
		 * Detects edges on layer
		 * 
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function detectEdges() {
			imagefilter($this->content, IMG_FILTER_EDGEDETECT);
			
			return $this;
		}
		
		/**
		 * Embosses the layer
		 * 
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function emboss() {
			imagefilter($this->content, IMG_FILTER_EMBOSS);
				
			return $this;
		}
		
		/**
		 * Blurs the layer
		 * 
		 * @param int $level Blur level
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function blur($level = 1) {
			for ($i = 1; $i <= $level; $i++) {
				imagefilter($this->content, IMG_FILTER_GAUSSIAN_BLUR);
			}
		}
		
		/**
		 * Fills entire layer with color
		 * 
		 * @param \gOPF\gSIP\Color $color Layer color
		 * @return \gOPF\gSIP\Layer Fluid interface
		 */
		public function fill(Color $color) {
			$this->drawRectangle(new Position(0), $this->size, $color, true);
			
			return $this;
		}
		
		/**
		 * Allocates color on the layer image
		 * 
		 * @param \gOPF\gSIP\Color $color Color values
		 * @return resource Color allocated ID 
		 */
		private function allocateColor(Color $color) {
			if ($color->alpha > 0) {
				return imagecolorallocatealpha($this->content, $color->red, $color->green, $color->blue, $color->alpha);
			} else {
				return imagecolorallocate($this->content, $color->red, $color->green, $color->blue);
			}
		}
	}
?>