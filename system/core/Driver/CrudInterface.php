<?php

namespace System\Driver;

interface CrudInterface {
    /**
     * Saves data under selected ID by driver.
     *
     * @param mixed $content Data to save
     */
    public function set($content);

    /**
     * Reads data from selected ID by driver.
     *
     * @return mixed Data from selected ID
     */
    public function get();

    /**
     * Removes selected ID from driver database.
     */
    public function remove();

    /**
     * Clears all ID's from selected driver.
     */
    public function clear();
}