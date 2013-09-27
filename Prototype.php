<?php

/**
 * Array Storage
 */

namespace attitude\Elements;

/**
 * Array Storage class
 *
 * Non-persistent PHP in memory storage engine.
 *
 * @author Martin Adamko <@martin_adamko>
 * @version v0.1.0
 * @licence MIT
 *
 */
abstract class Array_Prototype implements Storage_Interface
{
    /**
     * Pool of data
     *
     * @var array
     */
    private $data = array();

    /**
     * Checks if document exists
     *
     * @param   string  $key    The key or array of keys to fetch.
     * @returns bool            Returns TRUE on success or FALSE on failure.
     *
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Returns document
     *
     * @param   string  $key    The key or array of keys to fetch.
     * @returns mixed           Returns the object associated with the key or an
     *                          array of found key-value pairs when key is an
     *                          array. Returns FALSE on failure, key is not
     *                          found or key is an empty array.
     *
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Add new document
     *
     * @param   string  $key    The key that will be associated with the item.
     * @param   mixed   $var    The variable to store. Strings and integers are
     *                          stored as is, other types are stored serialized.
     * @returns int|bool        Returns key on success or FALSE on failure.
     *
     */
    public function add($key, $var)
    {
        if ($this->exists($key)) {
            return false;
        }

        return $this->set($key, $var);
    }

    /**
     * Sets new or replaces document
     *
     * @param   string  $key    The key that will be associated with the item.
     * @param   mixed   $var    The variable to store. Strings and integers are
     *                          stored as is, other types are stored serialized.
     * @returns bool            Returns TRUE on success or FALSE on failure.
     */
    public function set($key, $var)
    {
        $this->data[$key] = $var;

        return true;
    }

    /**
     * Replaces existing document
     *
     * @param   string  $key    The key that will be associated with the item.
     * @param   mixed   $var    The variable to store. Strings and integers are
     *                          stored as is, other types are stored serialized.
     * @returns bool            Returns TRUE on success or FALSE on failure.
     *
     */
    public function replace($key, $var)
    {
        if (!array_key_exists($key, $this->data)) {
            return false;
        }

        return $this->set($key, $var);
    }

    /**
     * Destroys document
     *
     * @param   string  $key    The key associated with the item to delete.
     * @returns bool            Returns TRUE on success or FALSE on failure.
     *
     */
    public function delete($key)
    {
        if (!$this->exists($key)) {
            return false;
        }

        unset($this->data[$key]);

        return true;
    }

    /**
     * Returns Universally Unique IDentifier
     *
     * See https://gist.github.com/dahnielson/508447
     *
     * @param   void
     * @returns string  32 bit hexadecimal hash
     *
     */
    public function uuid()
    {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Stores data in the pool
     *
     * @param   mixed       $var    Variable to store
     * @returns string|bool         Returns UUID on success or `FALSE` on failure.
     *
     */
    public function store($var)
    {
        $key = $this->uuid();

        if ($this->add($key, $var)) {
            return $key;
        }

        return false;
    }
}
