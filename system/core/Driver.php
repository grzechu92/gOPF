<?php
	namespace System;

	/**
	 * Framework driver manager
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Driver {
		/**
		 * APC based driver
		 * @var string
		 */
		const APC = 'APC';

		/**
		 * Session based driver
		 * @var string
		 */
		const SESSION = 'Session';

		/**
		 * Filesystem based driver
		 * @var string
		 */
		const FILESYSTEM = 'Filesystem';

		/**
		 * Memcached based driver
		 * @var string
		 */
		const MEMCACHED = 'Memcached';

		/**
		 * Serialized filesystem based driver
		 * @var string
		 */
		const SERIALIZED_FILESYSTEM = 'SerializedFilesystem';

		/**
		 * Database based driver
		 * @var string
		 */
		const DATABASE = 'Database';

		/**
		 * Create specified driver instance
		 *
		 * @param string $type Driver type (Driver::APC, Driver::SESSION, Driver::FILESYSTEM, Driver::MEMCACHED, Driver::SERIALIZED_FILESYSTEM)
		 * @param string $name Driver container name
		 * @param int $lifetime Driver container lifetime
		 * @param bool $user Is driver user unique?
		 * @return \System\Driver\DriverInterface Specified driver instance
		 */
		public static function factory($type, $name, $lifetime = 0, $user = false) {
			$driver = '\\System\\Driver\\'.$type;

			return new $driver($name, $lifetime, $user);
		}
	}
?>