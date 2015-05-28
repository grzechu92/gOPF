<?php

namespace System;

/**
 * Abstract class of repository.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
abstract class Repository
{
    /**
     * Framework database handler.
     *
     * @var mixed
     */
    protected $database;

    /**
     * Creates link to framework database in class.
     */
    final public function __construct()
    {
        $this->database = Core::instance()->database->connection();
    }

    /**
     * If repository method not exists, exception is thrown.
     *
     * @param string $index Method name
     * @param array  $args  Method call arguments
     *
     * @throws \System\Core\Exception
     */
    final public function __call($index, $args)
    {
        throw new \System\Core\Exception(I18n::translate('REPOSITORY_METHOD_NOT_EXISTS', array(__CLASS__, $index)));
    }

    /**
     * Returns repository object instance.
     *
     * @param string $name Repository name
     *
     * @return \System\Repository Repository object instance
     */
    final public static function factory($name)
    {
        return Core::instance()->context->getRepository($name);
    }
}
