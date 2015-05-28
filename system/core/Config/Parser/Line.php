<?php

namespace System\Config\Parser;

/**
 * Line class for config parser.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Line
{
    /**
     * If line has this value it will be removed.
     *
     * @var string
     */
    const REMOVE = '__REMOVE';

    /**
     * Line name.
     *
     * @var string
     */
    public $name;

    /**
     * Line value.
     *
     * @var string
     */
    public $value;

    /**
     * Is common? (common means, it has name and value, for example not comments line or empty lines).
     *
     * @var bool
     */
    public $common = false;

    /**
     * Is array? (array means array start, for example [test] in config line).
     *
     * @var bool
     */
    public $array = false;

    /**
     * Is empty?
     *
     * @var bool
     */
    public $empty = false;

    /**
     * Raw line content.
     *
     * @var string
     */
    private $content;

    /**
     * Initiates line with content.
     *
     * @param string $line Line content
     */
    public function __construct($line = '')
    {
        $this->content = $line;
        $element = explode('=', $line, 2);

        if (!empty($this->content) && $this->content[0] == '[') {
            $this->array = true;
            $this->name = trim(str_replace(['[', ']'], ['', ''], $this->content));
        }

        $trim = trim($this->content);

        if (empty($trim)) {
            $this->content = '';
            $this->empty = true;
        }

        if (count($element) == 2) {
            $this->init(trim($element[0]), trim($element[1]));
        }
    }

    /**
     * Initiates line with specified values.
     *
     * @param string $name  Line name
     * @param string $value Line value
     */
    public function init($name, $value)
    {
        $this->common = true;
        $this->empty = false;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Creates raw line content to insert into file.
     *
     * @return string Raw line content
     */
    public function build()
    {
        if ($this->common) {
            return $this->name . ' = ' . $this->value;
        } elseif ($this->array) {
            return '[' . $this->name . ']';
        } else {
            return trim($this->content);
        }
    }
}
