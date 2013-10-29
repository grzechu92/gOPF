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
		const BAN = 'Konto zostało zbanowane';
		const LOGIN_SUCCESS = 'Zalogowano pomyślnie.';
		const LOGIN_ERROR = 'Nieprawidłowy login lub hasło.';
		const ACCOUNT_ACTIVATE = 'Musisz aktywować swoje konto.';
		const CONNECTION_ERROR = 'Błąd połączenia';
		const ALREADY_LOGGED = 'Już jesteś zalogowany';
		const UPLOAD_SUCCESS = '';
		
		/**
		 * @see \Exception::__construct()
		 */
		public function __construct($message) {
			$this->message = 'Kwejk.pl API error: '.$message;
			$this->code = $message;
		}
	}
?>