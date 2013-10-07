<?php
	namespace System;
	use \System\Entity\EntityInterface;
	use \System\Entity\Exception;
	
	abstract class Entity implements EntityInterface {
		protected $identifiers = array();
		protected $initialized = false;
		protected $checksum;
		
		final public static function factory($entity) {
			$class = '\\Entities\\'.$entity;
		
			try {				
				$entity = new $class();
				
				if ($entity instanceof Entity) {
					return $entity;
				}
			} catch (\System\Loader\Exception $e) {
				throw new Exception(I18n::translate('ENTITY_DOESNT_EXISTS', array($entity)));
			}
		}
		
		final public function __destruct() {
			if ($this->initialized && $this->checksum != $this->generateChecksum()) {
				$this->update();
			}
		}
		
		final public function initialize(\stdClass $data) {
			if ($this->initialized) {
				return;
			}
			
			foreach ($data as $name=>$value) {
				if (!in_array($name, $this->identifiers)) {
					if (!property_exists($this, $name)) {
						throw new Exception(I18n::translate('UNKNOWN_ENTITY_FIELD', array($name, __CLASS__)));
					}
					
					$this->{$name} = $value;
				}
			}
			
			$this->initialized = true;
			$this->checksum = $this->generateChecksum();
		}
		
		final private function generateChecksum() {
			$string = '';
			
			foreach ($this as $field=>$value) {
				if (!is_array($value)) {
					$string .= $field.$value;
				}
			}
			
			return sha1($string);
		}
	}
?>