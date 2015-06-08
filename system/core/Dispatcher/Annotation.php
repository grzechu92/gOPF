<?php

namespace System\Dispatcher;

/**
 * DocBlock annotation parser.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Annotation
{
    /**
     * Annotation for ACL (user levels separated by space).
     *
     * @var string
     */
    const ACL = 'gOPF-Access';

    /**
     * Annotation for defining state of controller (static or dynamic).
     *
     * @var string
     */
    const STATE = 'gOPF-State';

    /**
     * Annotation for defining REST aware method in controller.
     *
     * @var string
     */
    const REST = 'gOPF-REST';

    /**
     * Parsed values.
     *
     * @var array
     */
    private $parsed = array();

    /**
     * Initialize annotation class.
     *
     * @param string $block DocBlock
     */
    public function __construct($block)
    {
        $parsed = array();
        preg_match_all('/@(.*)/', $block, $parsed);

        if (count($parsed[1]) > 0) {
            foreach ($parsed[1] as $line) {
                $exploded = explode(' ', preg_replace('/\s+/', ' ', $line), 2);

                $this->parsed[$exploded[0]] = (isset($exploded[1]) && !empty($exploded[1])) ? $exploded[1] : true;
            }
        }
    }

    /**
     * Get requested annotation value.
     *
     * @param string $name Requested annotation name
     *
     * @return string|bool Annotation value
     */
    public function get($name)
    {
        if (!isset($this->parsed[$name])) {
            return false;
        } else {
            return $this->parsed[$name];
        }
    }
}
