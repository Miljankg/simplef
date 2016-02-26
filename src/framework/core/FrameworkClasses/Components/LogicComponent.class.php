<?php

namespace Framework\Core\FrameworkClasses\Components;

use Framework\Core\Database\DB;
use Framework\Core\FrameworkClasses\Configuration\Config;
use Framework\Core\ISF;

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
     * @param ISF $sf Simple Framework instance.
     */
    public function __construct($name, Config $config = null, DB $db = null, array $logicComponents = array(), ISF $sf) {
        parent::__construct($name, $config, $logicComponents, $sf);
        
        $this->db = $db;
        
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    public abstract function init();

    //</editor-fold>
}
