<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/21/2016
 * Time: 4:21 PM
 */

namespace Framework\Core\FrameworkClasses\Globals;

use Framework\Core\Arrays\ResourceArray;

class Post extends ResourceArray
{
    //<editor-fold desc="Constructor">

    public function __construct(array &$data, $overwriteAllowed)
    {
        $name = '$_POST';

        parent::__construct($name, $data, $overwriteAllowed);
    }

    public function get($index, $filter = FILTER_DEFAULT, $options = array())
    {
        $this->checkIfExists($index);

        return filter_input(INPUT_POST, $index, $filter, $options);
    }

    //</editor-fold>
}