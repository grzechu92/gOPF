<?php
	namespace System;
	use \System\Entity\EntityInterface;
	use \System\Entity\Exception;
	use \stdClass;
	use \System\Entity\Field;

	/**
	 * Database Entity framework engine 
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Entity implements EntityInterface {
		/**
		 * Entity initialization flag
		 * @var bool
		 */
		private $initialized = false;
		
		/**
		 * Entity initialized data checksum
		 * @var string
		 */
		private $checksum;

		/**
		 * Entity fields cache
		 * @var \System\Entity\Field[]
		 */
		private $fields = array();

		/**
		 * Creates new entity of specified type
		 * 
		 * @param string $entity Entity class without namespace
		 * @param mixed $data Entity initialization data
		 * @throws \System\Entity\Exception
		 * @return \System\Entity Initialized Entity object
		 */
		final public static function factory($entity, $data = null) {
			$class = '\\Entities\\'.$entity;
		
			try {
				$entity = new $class();
				
				if ($entity instanceof EntityInterface) {
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
		 * @see \System\Entity\EntityInterface::initialize()
		 */
		final public function initialize($data = null) {
			if (empty($data)) {
				throw new Exception('NOT FOUND');
			}

			if ($data instanceof stdClass) {
				foreach ($data as $name=>$value) {
					if (!property_exists($this, $name)) {
						$found = false;

						foreach ($this->getFields() as $field) {
							if ($field->getDatabaseFieldName() == $name) {
								$found = true;

								$name = $field->getPropertyName();
								break;
							}
						}

						if (!$found) {
							throw new Exception(I18n::translate('UNKNOWN_ENTITY_FIELD', array($name, __CLASS__)));
						}
					}

					$this->{$name} = $value;
				}
			}

			$this->initialized = true;
			$this->checksum = $this->generateChecksum();
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
				
				if (is_scalar($value)) {
					$string .= $field.$value;
				}
			}
			
			return sha1($string);
		}

		/**
		 * Return database property fields
		 *
		 * @return \System\Entity\Field[]
		 */
		final protected function getFields() {
			if (empty($this->fields)) {
				$reflection = new \ReflectionClass($this);

				foreach ($reflection->getProperties() as $property) {
					if ($property->isPublic()) {
						$this->fields[] = new Field($property);
					}
				}
			}

			return $this->fields;
		}

		/**
		 * Return database unique fields
		 *
		 * @return \System\Entity\Field[]
		 */
		final protected function getUniqueFields() {
			$unique = array();

			foreach ($this->getFields() as $field) {
				if ($field->isUnique()) {
					$unique[] = $field;
				}
			}

			return $unique;
		}
	}
?>