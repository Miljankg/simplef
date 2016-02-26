<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/22/2016
 * Time: 4:50 PM
 */

namespace Framework\Core\FrameworkClasses\Session;


interface ISession
{
    public function sessionCreate();

    public function sessionUpdate();

    public function sessionDestroy();

    public function getUserData($index = null);

    public function setUserData($key, $value);


}