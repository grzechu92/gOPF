<?php
	namespace Entities;
	
	class Square extends \System\Entity implements \System\Entity\EntityInterface {
		protected $identifiers = array('id');
		
		public $x;
		public $y;
		public $width;
		public $height;
		
		/**
		 * @var \Models\squaresModel
		 */
		private $model;
		
		public function __construct() {
			$this->model = \System\Model::factory('squares');
		}
		
		public function remove() {
			$this->model->remove($this->identifiers->id);
		}
		
		public function create() {
			$this->model->create($this->x, $this->y, $this->width, $this->height);
		}
		
		public function update() {
			$this->model->update($this->identifiers[0], $this->x, $this->y, $this->width, $this->height);
		}
	}
?>