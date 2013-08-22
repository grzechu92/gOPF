<?php
	namespace System;
	use System\Filesystem\File;
	use System\Filesystem\Exception;
	
	/**
	 * Smart filesystem managing class
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Filesystem {
		/**
		 * File identifier
		 * @var int
		 */
		const FILE = 0;
		
		/**
		 * Directory identifier
		 * @var int
		 */
		const DIRECTORY = 1;
		
		/**
		 * Writes data into file
		 *
		 * @param string $path Path to file
		 * @param string $content File content
		 * @param bool $lock Lock file while writing
		 */
		public static function write($path, $content, $lock = false) {
			$file = new File(self::preparePath($path), File::WRITE);
			
			if ($lock) {
				$file->lock(File::LOCK_WRITING);
			}
			
			$file->setContent($content);
		}
	
		/**
		 * Appends data into file
		 *
		 * @param string $path Path to file
		 * @param string $content File content
		 * @param bool $lock Lock file while writing
		 */
		public static function append($path, $content, $lock = false) {
			$file = new File(self::preparePath($path), File::APPEND);
			
			if ($lock) {
				$file->lock(File::LOCK_WRITING);
			}
			
			$file->setContent($content);
		}
	
		/**
		 * Reads content of file
		 *
		 * @param string $path Path to file
		 * @param bool $lock Lock while reading
		 * @return string Content of file
		 */
		public static function read($path, $lock = false) {
			$file = new File(self::preparePath($path), File::READ);
			
			if ($lock) {
				$file->lock(File::LOCK_READING);
			}
			
			return $file->getContent();
		}
	
		/**
		 * Makes a new directory
		 *
		 * @param string $path Directory name
		 * @throws \System\Filesystem\Exception
		 */
		public static function mkdir($path) {
			$path = self::preparePath($path);
			
			if (@!mkdir($path)) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_DIRECTORY', array($path)), $path);
			}
		}
	
		/**
		 * Changes the file system modes of files and directories
		 *
		 * @param string $path Path to file
		 * @param int $mode Mode of file
		 * @param bool $recursive Recursive
		 * @throws \System\Filesystem\Exception
		 */
		public static function chmod($path, $mode, $recursive = false) {
			$path = self::preparePath($path);
				
			if (@!chmod($path, $mode)) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_CHMOD', array($path)), $path);
			}
				
			if (self::getType($path) == 'dir' && $recursive) {
				$browser = new \DirectoryIterator($path);
				
				foreach ($browser as $file) {
					if ($file != '.' && $file != '..') {
						self::chmod($file->getRealPath(), $mode, true);
					}
				}
			}
		}
	
		/**
		 * Removes file or directory
		 *
		 * @param string $path Path to file or directory
		 * @param bool $recursive Recursive
		 * @throws \System\Filesystem\Exception
		 */
		public static function remove($path, $recursive = false) {
			$path = self::preparePath($path);
				
			switch (self::getType($path)) {
				case 'file':
					if (!unlink($path)) {
						throw new Exception(I18n::translate('FILESYSTEM_UNABLE_REMOVE_FILE', array($path)), $path);
					}
					break;
	
				case 'dir':
					if (@!rmdir($path)) {
						if ($recursive) {
							$browser = new \DirectoryIterator($path);
							
							foreach ($browser as $file) {
								if ($file != '.' && $file != '..') {
									self::remove($file->getRealPath(), true);
								}
							}
							
							self::remove($path);
						} else {
							throw new Exception(I18n::translate('FILESYSTEM_UNABLE_REMOVE_DIRECTORY', array($path)), $path);
						}
					}
					break;
			}
		}
	
		/**
		 * Checks if file or directory is empty
		 *
		 * @param string $path Path to file or directory
		 * @return bool Emptiness
		 * @throws \System\Filesystem\Exception
		 */
		public static function isEmpty($path) {
			$path = self::preparePath($path);
			$return = false;
				
			switch (self::getType($path)) {
				case 'file':
					$contents = self::read($path);
						
					if (empty($contents)) {
						$return = true;
					}
					break;
	
				case 'dir':
					$browser = new \DirectoryIterator($path);
					$count = 0;
										
					foreach ($browser as $file) {
						if ($file != '.' && $file != '..') {
							$count++;
						}
					}
					
					$return = ($count > 0) ? false : true;
					break;
			}
				
			return $return;
		}
	
		/**
		 * Copies file from source into destination
		 *
		 * @param string $source Path to source file
		 * @param string $destination Destination path
		 * @throws \System\Filesystem\Exception
		 */
		public static function copy($source, $destination) {
			$source = self::preparePath($source);
			$destination = self::preparePath($destination);
				
			if (!@copy($source, $destination)) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_COPY', array($source)), $source);
			}
		}
	
		/**
		 * Moves file from source into destination
		 *
		 * @param string $source Path to source file
		 * @param string $destination Destination path
		 * @throws \System\Filesystem\Exception
		 */
		public static function move($source, $destination) {
			$source = self::preparePath($source);
			$destination = self::preparePath($destination);
				
			try {
				self::rename($source, $destination);
			} catch (Exception $error) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_MOVE', array($source)), $source);
			}
		}
	
		/**
		 * Renames file
		 *
		 * @param string $old Path to file with old name
		 * @param string $new Path to file with new name
		 * @throws \System\Filesystem\Exception
		 */
		public static function rename($old, $new) {
			$old = self::preparePath($old);
			$new = self::preparePath($new);
				
			if (@!rename($old, $new)) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_RENAME', array($old)), $old);
			}
		}
	
		/**
		 * Returns type of selected element (file or directory)
		 *
		 * @param string $path Path to element
		 * @throws \System\Filesystem\Exception
		 * @return string Type
		 */
		public static function getType($path) {
			$path = self::preparePath($path);
				
			if (!@$filetype = filetype($path)) {
				throw new Exception(I18n::translate('FILESYSTEM_WRONG_PATH', array($path)), $path);
			} else {
				return $filetype;
			}
		}
	
		/**
		 * Returns base name of file
		 *
		 * @param string $path Path to file
		 * @return string Filename
		 */
		public static function getName($path) {
			return basename($path);
		}
	
		/**
		 * Returns size of selected file
		 *
		 * @param string $path Path to file
		 * @return int Amount of bytes
		 * @throws \System\Filesystem\Exception
		 */
		public static function getSize($path) {
			$path = self::preparePath($path);
			@$filesize = filesize($path);
			
			if ($filesize === false) {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_SIZE', array($path)), $path);
			} else {
				return $filesize;
			}
		}
	
		/**
		 * Checks if selected element is a directory
		 *
		 * @param string $path Path to element
		 * @return bool Status
		 */
		public static function checkDirectory($path) {
			return is_dir(self::preparePath($path));
		}
	
		/**
		 * Checks if selected element is a file
		 *
		 * @param string $path Path to element
		 * @return bool Status
		 */
		public static function checkFile($path) {
			return is_file(self::preparePath($path));
		}
	
		/**
		 * Returns checksum of element
		 *
		 * @param string $path Path to element
		 * @throws \System\Filesystem\Exception
		 * @return string Checksum
		 */
		public static function checksum($path) {
			$path = self::preparePath($path);
				
			if (self::checkFile($path)) {
				return sha1_file($path);
			} else {
				throw new Exception(I18n::translate('FILESYSTEM_UNABLE_CHECKSUM', array($path)), $path);
			}
		}
		
		/**
		 * Prepares path, removes doubled //
		 *
		 * @param string $path Path to prepare
		 * @return string Prepared path
		 */
		private static function preparePath($path) {
			return str_replace('//', '/', $path);
		}
	}
?>