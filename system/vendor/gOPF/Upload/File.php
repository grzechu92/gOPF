<?php

namespace gOPF\Upload;

/**
 * Uploaded file class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class File
{
    /**
     * Name of file.
     *
     * @var string
     */
    public $name;

    /**
     * MIME type of file.
     *
     * @var string
     */
    public $type;

    /**
     * Temporary path of file.
     *
     * @var string
     */
    public $path;

    /**
     * Error code, if any occured while uploading.
     *
     * @var int
     */
    public $error;

    /**
     * Filesize in bytes.
     *
     * @var int
     */
    public $size;

    /**
     * Initiates file object.
     *
     * @param string $name  Name of file
     * @param string $type  MIME type of file
     * @param string $path  Temporary path of file
     * @param int    $error Error code, if any occured while uploading
     * @param int    $size  Filesize in bytes
     */
    public function __construct($name, $type, $path, $error, $size)
    {
        $this->name = $name;
        $this->type = $type;
        $this->path = $path;
        $this->error = $error;
        $this->size = $size;
    }

    /**
     * Moves uploaded file to selected destination.
     *
     * @param string $destination Full destination path (example: /path/to/directory)
     * @param string $name        Optional, change file name
     */
    public function moveTo($destination, $name = '')
    {
        if (empty($name)) {
            $name = $this->name;
        }

        move_uploaded_file($this->path, $destination . $name);
    }
}
