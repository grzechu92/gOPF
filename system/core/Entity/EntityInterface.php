<?php
	namespace System\Entity;
	
	interface EntityInterface {
		public function initialize(\stdClass $data);
		public function remove();
		public function create();
		public function update();
	}
?>