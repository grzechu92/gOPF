<?php

namespace gOPF\Kwejk;

/**
 * Kwejk exception class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Exception extends \Exception
{
    /**
     * Logged user is banned.
     *
     * @var string
     */
    const BAN = 'Konto zostało zbanowane';

    /**
     * Login success.
     *
     * @var string
     */
    const LOGIN_SUCCESS = 'Zalogowano pomyślnie.';

    /**
     * Login error.
     *
     * @var string
     */
    const LOGIN_ERROR = 'Nieprawidłowy login lub hasło.';

    /**
     * User requres activation.
     *
     * @var string
     */
    const ACCOUNT_ACTIVATE = 'Musisz aktywować swoje konto.';

    /**
     * Connection error.
     *
     * @var string
     */
    const CONNECTION_ERROR = 'Błąd połączenia';

    /**
     * User is alerady logged.
     *
     * @var string
     */
    const ALREADY_LOGGED = 'Już jesteś zalogowany';

    /**
     * File upload success.
     *
     * @var string
     */
    const UPLOAD_SUCCESS = '';

    /**
     * Captcha is wrong.
     *
     * @var string
     */
    const CAPTCHA_ERROR = '';

    /**
     * User is not authorized.
     *
     * @var string
     */
    const NOT_AUTHORIZED = 'By kontynuować musisz się zalogować lub zarejestrować.';

    /**
     * File to upload not exists.
     *
     * @var string
     */
    const FILE_NOT_EXIST = 'Plik nie istnieje';

    /**
     * @see \Exception::__construct()
     */
    public function __construct($message)
    {
        $this->message = 'Kwejk.pl API: ' . $message;
        $this->code = $message;
    }
}
