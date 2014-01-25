<?php
	namespace System;
	use \System\Entity\EntityInterface;
	use \System\Entity\Exception;
	use \System\Entity\Identifiers;
	use \stdClass;
	
	/**
	 * Database Entity framework engine 
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Entity implements EntityInterface {
		/**
		 * Unique entity specified index keys
		 * @var array
		 */
		protected $keys = array();
		
		/**
		 * Unique entity index keys with values
		 * @var \System\Entity\Identifiers
		 */
		protected $identifiers;
		
		/**
		 * Entity initialization flag
		 * @var bool
		 */
		protected $initialized = false;
		
		/**
		 * Entity initialized data checksum
		 * @var string
		 */
		protected $checksum;
		
		/**
		 * Creates new entity of specified type
		 * 
		 * @param string $entity Entity class without namespace
		 * @param \stdClass $data Entity initialization data
		 * @throws \System\Entity\Exception
		 * @return \System\Entity Initialized Entity object
		 */
		final public static function factory($entity, stdClass $data = null) {
			$class = '\\Entities\\'.$entity;
		
			try {
				$entity = new $class();
				
				if ($entity instanceof Entity) {
					if ($data instanceof stdClass) {
						$entity->initialize($data);
					}
					
					return $entity;
				}
			} catch (\System\Loader\Exception $e) {
				throw new Exception(I18n::translate('ENTITY_DOESNT_EXISTS', array($entity)));
			}
		}
		
		/**
		 * If object is initialized and modified, updates it
		 */
		final public function __destruct() {
			if ($this->initialized && $this->checksum != $this->generateChecksum()) {
				$this->update();
			}
		}
		
		/**
		 * @see \System\Entity\EntityInterface::initialize()
		 */
		final public function initialize(stdClass $data) {
			if ($this->initialized) {
				return;
			}
			
			foreach ($data as $name=>$value) {
				if (!in_array($name, $this->keys)) {
					if (!property_exists($this, $name)) {
						throw new Exception(I18n::translate('UNKNOWN_ENTITY_FIELD', array($name, __CLASS__)));
					}
					
					$this->{$name} = $value;
				} else {
					if (!$this->identifiers instanceof Identifiers) {
						$this->identifiers = new Identifiers();
					}
					
					$this->identifiers->{$name} = $value;
				}
			}
			
			$this->initialized = true;
			$this->checksum = $this->generateChecksum();
		}
		
		/**
		 * Generates checksum of Entity data
		 * 
		 * @return string Data checksum
		 */
		final private function generateChecksum() {
			$reservedFields = array('keys', 'identifiers', 'checksum');
			$string = '';
			
			foreach ($this as $field=>$value) {
				if (in_array($field, $reservedFields)) {
					continue;
				}
				
				if (!is_array($value)) {
					$string .= $field.$value;
				}
			}
			
			return sha1($string);
		}
	}
?>