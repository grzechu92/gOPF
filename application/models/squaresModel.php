<?php
	namespace Models;
	use \PDO;
	
	class squaresModel extends \System\Model {
		/**
		 * 
		 * @return \Entities\Square
		 */
		public function getEntity($id) {
			$query = $this->database->prepare('SELECT * FROM `squares` WHERE `id` = :id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			
			$entity = \System\Entity::factory('Square');
			$entity->initialize($query->fetch(PDO::FETCH_OBJ));
			
			return $entity;
		}
		
		public function create($x, $y, $width, $height) {
			$query = $this->database->prepare('INSERT INTO `squares` (`x`, `y`, `width`, `height`) VALUES (:x, :y, :width, :height)');
			$query->bindValue(':x', $x, PDO::PARAM_INT);
			$query->bindValue(':y', $y, PDO::PARAM_INT);
			$query->bindValue(':width', $width, PDO::PARAM_INT);
			$query->bindValue(':height', $height, PDO::PARAM_INT);
			$query->execute();
		}
		
		public function remove($id) {
			$query = $this->database->prepare('DELETE FROM `squares` WHERE `id` = :id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
		}
		
		public function update($id, $x, $y, $width, $height) {
			$query = $this->database->prepare('UPDATE `squares` SET `x` = :x, `y` = :y, `width` = :width, `height` = :height WHERE `id` = :id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->bindValue(':x', $x, PDO::PARAM_INT);
			$query->bindValue(':y', $y, PDO::PARAM_INT);
			$query->bindValue(':width', $width, PDO::PARAM_INT);
			$query->bindValue(':height', $height, PDO::PARAM_INT);
			$query->execute();
		}
	}
?>