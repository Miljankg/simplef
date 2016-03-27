<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/21/2016
 * Time: 4:12 PM
 */

namespace Framework\Core\Arrays;

use \Exception;

class ResourceArray
{
    //<editor-fold desc="Members">

    /** @var array */
    private $array = array();

    /** @var string */
    private $name = "";

    /** @var bool */
    private $overwriteAllowed = true;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    public function __construct($name, array &$data = array(), $overwriteAllowed)
    {
        $this->name = $name;
        $this->array = $data;
        $this->overwriteAllowed = $overwriteAllowed;
    }

    //</editor-fold>

    protected function checkIfExists($index)
    {
        if (!isset($this->array[$index]))
            throw new Exception("There is no index \"$index\" in array {$this->name}");
    }

    //<editor-fold desc="Public functions">

    public function hasKey($index)
    {
        return isset($this->array[$index]);
    }

    /**
     * Returns value by given index.
     *
     * @param string $index Index to find.
     * @return mixed Founded value.
     * @throws Exception If there is no index.
     */
    public function get($index)
    {
        $this->checkIfExists($index);

        return $this->array[$index];
    }

    /**
     * Sets index and value.
     *
     * @param string $index Index to set.
     * @param mixed $value Value to set.
     * @throws Exception If index already exists.
     */
    public function set($index, $value)
    {
        if ($this->overwriteAllowed && isset($this->array[$index]))
            throw new Exception("Index \"$index\" already exists in array {$this->name}");

        $this->array[$index] = $value;
    }

    //</editor-fold>
}