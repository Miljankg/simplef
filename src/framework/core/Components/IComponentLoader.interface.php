<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/19/2016
 * Time: 11:03 PM
 */

namespace Core\Components;

use \Exception;

interface IComponentLoader
{
    /**
     * Loads passed list of output components.
     *
     * @param array $outputComponentsToLoad
     * @return array Array of component contents
     * @throws Exception If some component is not an instance of the OutputComponent class
     */
    public function loadOutputComponents(array $outputComponentsToLoad);

    /**
     * Load multiple logic components.
     *
     * @param string[] $logicComponentsToLoad Array of logic component names to load.
     * @return LogicComponent[] Loaded logic components.
     * @throws Exception If logic component is not configured properly.
     */
    public function loadLogicComponents($logicComponentsToLoad);

    /**
     * Loads logic component, by passed component name.
     *
     * @param string $logicToLoad Logic component name to load.
     * @param LogicComponent[] $logicComponentDependencies Logic components that current logic component depends on.
     * @return LogicComponent Loaded logic component object.
     * @throws Exception If loaded component does not extends the LogicComponent class.
     */
    public function loadLogicComponent($logicToLoad, array $logicComponentDependencies);
}