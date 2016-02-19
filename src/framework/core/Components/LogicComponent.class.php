<?php

namespace Core\Components;

use Core\Database\DB;
use Core\Configuration\Config;

/**
 * Logic Components
 *
 * @author Miljan Pantic
 */
abstract class LogicComponent extends SFComponent
{
    //<editor-fold desc="Members">

    protected $db = null;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * LogicComponent constructor.
     *
     * @param string $name Name of the component.
     * @param Config|null $config Config object.
     * @param DB|null $db Database object.
     * @param LogicComponent[] $logicComponents Logic component that this component depends on.
     */
    public function __construct($name, Config $config = null, DB $db = null, array $logicComponents = array()) {
        parent::__construct($name, $config, $logicComponents);
        
        $this->db = $db;
        
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    public abstract function init();

    //</editor-fold>
}
