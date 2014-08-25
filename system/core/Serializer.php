<?php
	namespace System;
	use System\Serializer\Exception;

	/**
	 * Class used for safe serializing data (checking checksum, etc.)
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Serializer {
		/**
		 * Returns safely serialized data
		 * 
		 * @param mixed $value Data to serialize
		 * @return string Safe serialized data string (checksum + serialized data)
		 */
		public static function serialize($value) {
			$serialized = serialize($value);
			
			return sha1($serialized).$serialized;
		}
		
		/**
		 * Returns safely unserialized data
		 * 
		 * @param string $string Safe serialized data string (checksum + serialzied data)
		 * @return mixed unserialized data
		 * @throws \System\Serializer\Exception
		 */
		public static function unserialize($string) {
			$checksum = substr($string, 0, 40);
			$serialized = substr($string, 40);
			
			$unserialized = unserialize($serialized);
			
			if ($checksum !== sha1(serialize($unserialized))) {
				throw new Exception(I18n::translate('SAFE_SERIALIZE_ERROR'));
			}
			
			return $unserialized;
		}
		
		/**
		 * Reads serialized data from file
		 * 
		 * @param string $path Path to file with data to read
		 * @param integer $attempt Amount of attempts when data reading fails
		 * @param integer $interval Time interval between attempts, in miliseconds
		 * @return mixed Unserialized data
		 * @throws \System\Serializer\Exception
		 */
		public static function read($path, $attempt = 100, $interval = 0) {
			while ($attempt-- > 0) {
				try {
					return self::unserialize(Filesystem::read($path, true));
				} catch (\System\Filesystem\Exception $exception) {
                    usleep(1000 * $interval);
                    continue;
				}
			}
			
			throw new Exception(I18n::translate('SAFE_SERIALIZE_ERROR'));
		}
		
		/**
		 * Writes safe serialized data info file
		 *
		 * @param string $path Path to destination file
		 * @param mixed $value Data to serialize
		 * @return mixed Unserialized data
		 * @throws \System\Filesystem\Exception
		 */
		public static function write($path, $value) {
			Filesystem::write($path, self::serialize($value), true);
		}
	}
?>