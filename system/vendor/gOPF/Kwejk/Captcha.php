<?php
	namespace gOPF\Kwejk;
	
	/**
	 * Kwejk captcha solver
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Captcha {
		/**
		 * Captcha challenge ID
		 * @var string
		 */
		public $challengeID;
		
		/**
		 * Captcha solved text
		 * @var string
		 */
		public $solved;
		
		/**
		 * Initiates captcha object
		 * 
		 * @param string $challengeID Captcha challenge ID
		 * @param string $solved Captcha solved text
		 */
		public function __construct($challengeID, $solved) {
			$this->challengeID = $challengeID;
			$this->solved = $solved;
		} 
	}
?>