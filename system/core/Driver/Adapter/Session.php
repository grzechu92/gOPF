<?php

namespace System\Driver\Adapter;

use System\Driver\AbstractAdapter;
use System\Driver\AdapterInterface;
use System\Driver\Session\Element;
use System\Driver\UnsupportedMethodException;

/**
 * Diver based on PHP Sessions.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Session extends AbstractAdapter implements AdapterInterface
{
    /**
     * Is session initialized?
     *
     * @var bool
     */
    private static $initialized = false;

    /**
     * @see \System\Drivers\AdapterInterface::__construct()
     */
    public function __construct($name, $lifetime = 0, $user = false)
    {
        parent::__construct($name, $lifetime, $user);

        if (!self::$initialized) {
            session_id(\System\Core::$UUID);
            session_start();

            self::$initialized = true;
        }

        $this->name = $name;

        if ($this->getLifetime() < $lifetime) {
            $this->setLifetime($lifetime);
        }

        if (!isset($_SESSION[$name])) {
            if ($lifetime != 0) {
                $lifetime += time();
            }

            $_SESSION[$name] = new Element($name, $lifetime);
        }
    }

    /**
     * @see \System\Drivers\AdapterInterface::set()
     */
    public function set($content)
    {
        $this->getElement()->set($content);
    }

    /**
     * @see \System\Drivers\AdapterInterface::get()
     */
    public function get()
    {
        $element = $this->getElement();

        if ($element->isValid()) {
            return $this->getElement()->get();
        } else {
            return null;
        }
    }

    /**
     * @see \System\Drivers\AdapterInterface::remove()
     */
    public function remove()
    {
        unset($_SESSION[$this->name]);
    }

    /**
     * @see \System\Drivers\AdapterInterface::clear()
     */
    public function clear()
    {
        throw new UnsupportedMethodException(\System\I18n::translate('UNSUPPORTED_DRIVER_METHOD', array(__CLASS__, 'clear()')));
    }

    /**
     * Get lifetime of session.
     *
     * @return int Session lifetime
     */
    private function getLifetime()
    {
        return ini_get('session.cookie_lifetime');
    }

    /**
     * Set session lifetime.
     *
     * @param int $seconds Session lifetime
     */
    private function setLifetime($seconds)
    {
        ini_set('session.cookie_lifetime', $seconds);
        ini_set('session.gc_maxlifetime', $seconds);
    }

    /**
     * Get selected session element.
     *
     * @return \System\Driver\Session\Element
     */
    private function &getElement()
    {
        return $_SESSION[$this->name];
    }
}