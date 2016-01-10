<?php

namespace Core\Components;

use Core\Database\DB;
use Core\Configuration\Config;

/**
 * Logic Components
 *
 * @author Miljan Pantic
 */
abstract class LogicComponent extends SFComponent {
    
    protected $db = null;
    
    public function __construct($name, Config $config = null, DB $db = null, array $logicComponents = array()) {
        parent::__construct($name, $config, $logicComponents);
        
        $this->db = $db;
        
    }        
    
     public abstract function init();
    
}
