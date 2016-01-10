<?php

namespace Components\Logic;

use Core\Components\LogicComponent;

class Lc1 extends LogicComponent {
    
    public function init() {
        
    }


    public function testFunction() {
        
        return $this->config->get('test');
        
    }
    
}

