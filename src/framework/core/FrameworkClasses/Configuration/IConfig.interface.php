<?php

namespace Framework\Core\FrameworkClasses\Configuration;


interface IConfig
{
    /**
     * Merges new config with already loaded one.
     *
     * @param array $config Config to add
     * @throws \Exception When config is closed for addition
     */
    public function addMultipleConfigValues(array &$config) ;

    /**
     * Adds value to the config array.
     *
     * @param string $index Index to add to
     * @param mixed $value Value to add
     * @throws \Exception if Index is already present.
     */
    public function set($index, $value);

    /**
     * Returns value from the config, by the passed index.
     *
     * @param string $index Index to search for
     * @return mixed Value from config
     * @throws \Exception If index does not exists
     */
    public function get($index);

    public function getUser($username);

    /**
     * Returns all config fields.
     *
     * @return array Loaded config
     */
    public function getAllFields();

    /**
     * Close config for adding new values
     */
    public function closeConfig();
}