<?php

namespace Components\Output;

use Core\Components\OutputComponent;

class OutComp1 extends OutputComponent {
    
    public function execute() {
        
        $lc = $this->getLogicComponent(LOGIC_COMP_1);
        
        echo $lc->testFunction();
        
    }
    
}

