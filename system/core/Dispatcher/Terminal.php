<?php

namespace System\Dispatcher;

use System\Config;

/**
 * Page request processing context.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Terminal extends Context implements ContextInterface
{
    /**
     * @see \System\Dispatcher\ContextInterface::process()
     */
    public function process()
    {
        $config = Config::factory('terminal.ini', Config::APPLICATION);
        $ip = $config->ip;

        if (!$config->enabled) {
            \System\Request::redirect('/');
        }

        if (!empty($ip)) {
            $ips = explode(',', $ip);

            if (!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                \System\Request::redirect('/');
            }
        }

        $exploded = explode('/', \System\Request::$URL);

        if (count($exploded) == 1) {
            $this->displayTerminal();
        } else {
            switch ($exploded[1]) {
                case 'connection':
                    $this->connection();
                    break;
            }
        }
    }

    /**
     * Display terminal client template.
     */
    private function displayTerminal()
    {
        $view = \System\View::instance();

        $view->setFrame(__SYSTEM_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'terminal.php');
        $view->render();
    }

    /**
     * Creates terminal connection.
     */
    private function connection()
    {
        $terminal = \System\Terminal::instance();

        $this->toJSON($terminal->handler());
    }
}
