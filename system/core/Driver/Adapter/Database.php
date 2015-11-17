<?php

namespace System\Driver\Adapter;

use System\Driver\AbstractAdapter;
use System\Driver\AdapterInterface;

/**
 * Database driver.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Database extends AbstractAdapter implements AdapterInterface
{
    /**
     * Database table name.
     *
     * @var string
     */
    const TABLE = '__DatabaseDriver';

    /**
     * Database instance.
     *
     * @var \System\Database\EngineInterface
     */
    private static $database;

    /**
     * @see \System\Drivers\AdapterInterface::set()
     */
    public function set($content)
    {
        $this->isInitialized();

        $content = base64_encode(serialize($content));

        if ($this->exist()) {
            $this->update($content);
        } else {
            $this->insert($content);
        }
    }

    /**
     * @see \System\Drivers\AdapterInterface::get()
     */
    public function get()
    {
        $this->isInitialized();
        $data = $this->select();

        if ($data->exist) {
            return unserialize(base64_decode($data->value));
        } else {
            return null;
        }
    }

    /**
     * @see \System\Drivers\AdapterInterface::remove()
     */
    public function remove()
    {
        $this->isInitialized();

        $this->delete();
    }

    /**
     * @see \System\Drivers\AdapterInterface::clear()
     */
    public function clear()
    {
        $this->isInitialized();

        $this->flush();
    }

    /**
     * Is driver initialized? If not, initialize it.
     */
    private function isInitialized()
    {
        if (!self::$database instanceof \System\Database\EngineInterface) {
            self::$database = \System\Database::instance()->engine();

            try {
                self::$database->query('
						SELECT 1
						FROM `' . self::TABLE . '`
						LIMIT 1
					');
            } catch (\Exception $e) {
                self::$database->query('
						CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
							`id` VARCHAR(40) NOT NULL,
							`value` TEXT NOT NULL,
							`lifetime` INT(15) NOT NULL,
							PRIMARY KEY (`id`),
							UNIQUE KEY `id` (`id`),
							KEY `id_2` (`id`)
						) DEFAULT CHARSET=utf8;
					');
            }
        }
    }

    /**
     * Update existing row.
     *
     * @param string $content Row content
     */
    private function update($content)
    {
        self::$database->query('
				UPDATE `' . self::TABLE . '`
				SET
					`value` = "' . $content . '",
					`lifetime` = ' . ($this->lifetime + time()) . '
				WHERE
					`id` = "' . $this->UID() . '"
		   ');
    }

    /**
     * Insert new row.
     *
     * @param string $content Row content
     */
    private function insert($content)
    {
        self::$database->query('
			   INSERT INTO `' . self::TABLE . '`
				   (`id`, `value`, `lifetime`)
			   VALUES
				   ("' . $this->UID() . '", "' . $content . '", ' . ($this->lifetime + time()) . ')
		   ');
    }

    /**
     * Read valid data from database.
     *
     * @return \stdClass Read data
     */
    private function select()
    {
        return self::$database->query('
				SELECT
					COUNT(*) AS `exist`,
					`value`
				FROM
					`' . self::TABLE . '`
				WHERE
					`id` = "' . $this->UID() . '"
				AND
					`lifetime` >= ' . time() . '
				LIMIT 1
			', true);
    }

    /**
     * Check if row exist.
     *
     * @return bool Exist?
     */
    private function exist()
    {
        $data = self::$database->query('
				SELECT
					COUNT(*) AS `exist`
				FROM
					`' . self::TABLE . '`
				WHERE
					`id` = "' . $this->UID() . '"
			', true);

        return $data->exist > 0;
    }

    /**
     * Delete database row.
     */
    private function delete()
    {
        self::$database->query('
				DELETE
					FROM `' . self::TABLE . '`
				WHERE
					`id` = "' . $this->UID() . '"
			');
    }

    /**
     * Delete all database rows.
     */
    private function flush()
    {
        self::$database->query('
				DELETE
					FROM `' . self::TABLE . '`
			');
    }
}
