<?php

namespace Framework\Core\FrameworkClasses\Components;

use Framework\Core\Database\DB;
use Framework\Core\Database\IDbFactory;
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

    /** @var IDbFactory */
    protected $dbFactory = null;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * LogicComponent constructor.
     *
     * @param string $name Name of the component.
     * @param Config|null $config Config object.
     * @param IDbFactory|null $db Database factory object.
     * @param LogicComponent[] $logicComponents Logic component that this component depends on.
     * @param ISF $sf Simple Framework instance.
     */
    public function __construct($name, Config $config = null, IDbFactory $db = null, array $logicComponents = array(), ISF $sf) {
        parent::__construct($name, $config, $logicComponents, $sf);
        
        $this->dbFactory = $db;
        
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    public abstract function init();

    //</editor-fold>
}
