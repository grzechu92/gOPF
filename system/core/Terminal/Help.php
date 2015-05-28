<?php

namespace System\Terminal;

use System\Terminal\Help\Line;

/**
 * Simple terminal help generator.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Help
{
    /**
     * Margin between command and description.
     *
     * @var int
     */
    const MARGIN = 5;

    /**
     * Message when command is internal.
     *
     * @var string
     */
    const INTERNAL = 'Internal command, using may cause serious damage!';

    /**
     * General description of command.
     *
     * @var string
     */
    public $description;

    /**
     * Array with command options.
     *
     * @var array
     */
    private $elements = array();

    /**
     * Initiates Help object.
     *
     * @param string $description General description of command
     */
    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * Adds new line.
     *
     * @param Line $line Line to add
     *
     * @return \System\Terminal\Help Fluid interface
     */
    public function add(Line $line)
    {
        $this->elements[] = $line;

        return $this;
    }

    /**
     * Adds multiple lines.
     *
     * @param array $lines Array with Lines
     */
    public function addLines(array $lines = array())
    {
        if (!empty($lines)) {
            foreach ($lines as $line) {
                if ($line instanceof Line) {
                    $this->add($line);
                }
            }
        }
    }

    /**
     * Generates Help content.
     *
     * @return string Help content
     */
    public function build()
    {
        if (count($this->elements) == 0) {
            return $this->description;
        }

        $output = array();
        $length = $this->findMaxLength();

        foreach ($this->elements as $line) {
            if (!$line->common) {
                $output[] = $line->content;
            } else {
                $output[] = str_pad($line->command, $length, ' ', STR_PAD_RIGHT) . str_repeat(' ',
                        self::MARGIN) . $line->description;
            }
        }

        return $this->description . "\n\n" . implode("\n", $output) . "\n";
    }

    /**
     * Finds command max length.
     *
     * @return int Command max length
     */
    private function findMaxLength()
    {
        $length = 0;

        foreach ($this->elements as $line) {
            if ($line->common) {
                $l = strlen($line->command);

                if ($l > $length) {
                    $length = $l;
                }
            }
        }

        return $length;
    }
}
