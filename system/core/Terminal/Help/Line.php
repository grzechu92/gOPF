<?php
	namespace System\Terminal\Help;
	
	/**
	 * Help line object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Line {
		/**
		 * Metastring for empty line
		 * @var string
		 */
		const SEPARATOR = '__SEPARATOR';
		
		/**
		 * Is command common? (common, means it has command and description)
		 * @var bool
		 */
		public $common = false;
		
		/**
		 * Content of command
		 * @var string
		 */
		public $content;
		
		/**
		 * Command name, if common
		 * @var string
		 */
		public $command;
		
		/**
		 * Command description, if common
		 * @var unknown
		 */
		public $description;
		
		/**
		 * Initiates Line object
		 * 
		 * @param string $command Command or content of line
		 * @param string $description Description of command, if common
		 */
		public function __construct($command, $description = '') {
			if ($command == self::SEPARATOR) {
				$this->content = "\n";
			} elseif (empty($description)) {
				$this->content = $command;
			} else {
				$this->command = $command;
				$this->description = $description;
				$this->common = true;
			}
		}
	}
?>