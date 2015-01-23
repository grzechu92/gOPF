<?php 
	namespace gOPF;
	use \gOPF\Upload\File;
	use \System\Request;
	
	/**
	 * POST file receiver plugin
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Upload {
		/**
		 * Uploaded files
		 * @var array
		 */
		private $files = array();
		
		/**
		 * Constructor of upload plugin
		 */
		public function __construct() {
			if (isset(Request::$files) && !empty(Request::$files)) {
				foreach (Request::$files as $name => $files) {
					if (!is_array($files['name'])) {
						if ($files['size'] > 0) {
							$this->files[$name] = new File($files['name'], $files['type'], $files['tmp_name'], $files['error'], $files['size']);
						}		
					} else {
						foreach ($files['name'] as $key => $data) {
							if ($files['size'][$key] > 0) {
								$this->files[$name][] = new File($files['name'][$key], $files['type'][$key], $files['tmp_name'][$key], $files['error'][$key], $files['size'][$key]);
							}
						}	
					}
				}
			}
		}
		
		/**
		 * Returns array of uploaded files
		 * 
		 * @return array Uploaded files
		 */
		public function getFiles() {
			return $this->files;
		}
		
		/**
		 * Returns uploaded file data
		 * 
		 * @param string $name POST file name
		 * @return \gOPF\Upload\File File data
		 */
		public function getFile($name) {
			if (isset($this->files[$name])) {
				return $this->files[$name];
			}
		}
		
		/**
		 * Checks if any file has been uploaded from client
		 * 
		 * @return bool Is uploaded
		 */
		public function isAnyUploaded() {
			return !empty($this->files);
		}
		
		/**
		 * Checks if requested file has been uploaded from client
		 * 
		 * @param string $name File name
		 * @return bool Is uploaded
		 */
		public function isUploaded($name) {
			return !empty($this->files[$name]);
		}
	}
?>