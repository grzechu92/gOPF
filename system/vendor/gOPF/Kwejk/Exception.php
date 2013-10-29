<?php
	namespace gOPF\Kwejk;
	
	/**
	 * Kwejk exception class
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Exception extends \Exception {
		const LOGIN_SUCCESS = 'Zalogowano pomyślnie.';
		const LOGIN_ERROR = 'Nieprawidłowy login lub hasło.';
		const ACCOUNT_ACTIVATE = 'Musisz aktywować swoje konto.';
		const CONNECTION_ERROR = 'Connection error.';
		const ALREADY_LOGGED = 'Już jesteś zalogowany';
		
		/**
		 * @see \Exception::__construct()
		 */
		public function __construct($message) {
			$this->message = 'Kwejk.pl API error: '.$message;
			$this->code = $message;
		}
	}
?>