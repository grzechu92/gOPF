<?php
	namespace System\Entity;

	/**
	 * Database field entity mapper
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	final class Field {
		/**
		 * Entity unique field annotation
		 * @var string
		 */
		const UNIQUE = 'Entity-Unique';

		/**
		 * Entity database field name annotation
		 * @var string
		 */
		const FIELD = 'Entity-Field';

		/**
		 * Entity property name
		 * @var string
		 */
		private $name;

		/**
		 * Annotations container
		 * @var \stdClass
		 */
		private $annotations;

		/**
		 * Initialize entity field object
		 *
		 * @param \ReflectionProperty $property Property reflection
		 */
		public function __construct(\ReflectionProperty $property) {
			$this->name = $property->name;
			$this->annotations = new \stdClass();

			$parsed = array();
			preg_match_all('/@(.*)/', $property->getDocComment(), $parsed);

			if (count($parsed[1]) > 0) {
				foreach ($parsed[1] as $line) {
					$exploded = explode(' ', $line, 2);

					$this->annotations->{$exploded[0]} = (isset($exploded[1]) ? $exploded[1] : true);
				}
			}
		}

		/**
		 * Return database field name
		 *
		 * @return string Database field name
		 */
		public function getDatabaseFieldName() {
			return (isset($this->annotations->{self::FIELD}) ? $this->annotations->{self::FIELD} : $this->name);
		}

		/**
		 * Return property name
		 *
		 * @return string Property name
		 */
		public function getPropertyName() {
			return $this->name;
		}

		/**
		 * Is property unique?
		 *
		 * @return bool Is unique?
		 */
		public function isUnique() {
			return isset($this->annotations->{self::UNIQUE});
		}

		/**
		 * Get custom field parameter
		 *
		 * @param string $name Field parameter name
		 * @return string|null Field parameter value
		 */
		public function getCustomParameter($name) {
			if (isset($this->annotations->{$name})) {
				return $this->annotations->{$name};
			} else {
				return null;
			}
		}
	}
?>