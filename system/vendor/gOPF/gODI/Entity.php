<?php
	namespace gOPF\gODI;
	use \System\Database\Exception;
    use \System\I18n;
    use \gOPF\gODI\Statement;

	/**
	 * gODI fast entity wrapper
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Entity extends \System\Entity {
		/**
		 * Entity field type name (must be valid Statement type)
		 * @var string
		 */
		const TYPE = 'Entity-Type';

		/**
		 * @var \gOPF\gODI\Handler
		 */
		private $database;

		/**
		 * @var string
		 */
		protected $table;

		/**
		 * Initialize gODI fast entity wrapper
		 *
		 * @throws \System\Database\Exception
		 */
		public function __construct() {
			$this->database = \System\Database::instance()->connection();

			if (!($this->database instanceof Handler)) {
                throw new Exception(I18n::translate('WRONG_DATABASE_ENGINE'));
			}
		}

		/**
		 * @see \System\Entity\EntityInterface::create()
		 */
		public function create() {
			$query = $this->database->insert($this->getTable());
			$this->bindNonUniqueFields($query, $this->getFields());

			$query->make();
		}

		/**
		 * @see \System\Entity\EntityInterface::read()
		 */
		public function read() {
			$query = $this->database->select($this->getTable())->fields('*');
			$this->bindUniqueFields($query, $this->getUniqueFields());

			$this->initialize($query->get(1));
		}

		/**
		 * @see \System\Entity\EntityInterface::update()
		 */
		public function update() {
			$query = $this->database->update($this->getTable());
			$this->bindUniqueFields($query, $this->getUniqueFields());
			$this->bindNonUniqueFields($query, $this->getFields());

			$query->make();
		}

		/**
		 * @see \System\Entity\EntityInterface::delete()
		 */
		public function delete() {
			$query = $this->database->delete($this->getTable());
			$this->bindUniqueFields($query, $this->getUniqueFields());

			$query->make();
		}

		/**
		 * Return entity table name
		 *
		 * @return string Table name
		 * @throws \System\Database\Exception
		 */
		private function getTable() {
			if (!isset($this->table)) {
                throw new Exception(I18n::translate('DATABASE_TABLE_IS_NOT_SET'));
			}

			return $this->table;
		}

		/**
		 * Get entity field type
		 *
		 * @param \System\Entity\Field $field Entity field instance
		 * @return int Entity type
		 * @throws \System\Database\Exception
		 */
		private function getFieldType(\System\Entity\Field $field) {
			$type = @constant('\gOPF\gODI\Statement::'.$field->getCustomParameter(self::TYPE));

			if (empty($type)) {
                throw new Exception(I18n::translate('UNKNOWN_ENTITY_TYPE', array($field->getPropertyName(), $field->getCustomParameter(self::TYPE))));
			}

			return $type;
		}

		/**
		 * Bind unique fields to query
		 *
		 * @param \gOPF\gODI\Statement $query Query reference
		 * @param \System\Entity\Field[] $fields Unique fields
		 * @throws \System\Database\Exception
		 */
		private function bindUniqueFields(Statement &$query, $fields) {
			$first = true;

			foreach ($fields as $field) {
				if ($field->isUnique()) {
					if (empty($this->{$field->getPropertyName()})) {
                        throw new Exception(I18n::translate('ENTITY_UNIQUE_FIELD_EMPTY', array($field->getPropertyName())));
					}

					if ($first) {
						$first = false;
						$query->where($field->getDatabaseFieldName())->eq($this->{$field->getPropertyName()}, $this->getFieldType($field));
					} else {
						$query->andWhere($field->getDatabaseFieldName())->eq($this->{$field->getPropertyName()}, $this->getFieldType($field));
					}
				}
			}
		}

		/**
		 * Bind non-unique fields to query
		 *
		 * @param \gOPF\gODI\Statement $query Query reference
		 * @param \System\Entity\Field[] $fields Unique fields
		 * @throws \System\Database\Exception
		 */
		private function bindNonUniqueFields(Statement &$query, $fields) {
			foreach ($fields as $field) {
				if (!$field->isUnique()) {
					$query->field($field->getDatabaseFieldName(), $this->{$field->getPropertyName()}, $this->getFieldType($field));
				}
			}
		}
	}
?>