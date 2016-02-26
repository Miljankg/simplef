<?php

namespace Components\Logic;

use Framework\Core\FrameworkClasses\Components\LogicComponent;
use Framework\Core\Authentication;

class Auth extends LogicComponent
{
    /** @var Authentication\Auth */
    private $authObject = null;

    public function init()
    {
        $this->authObject = new Authentication\Auth($this->sf->Config());
    }

    /**
     * Authenticates the user.
     *
     * @param string $username Username.
     * @param string $password User password.
     * @return bool If user is auth successfully or not.
     */
    public function authUser($username, $password)
    {
        $role = $this->authObject->authUser($username, $password);

        if ($role !== false)
        {
            $this->sf->Session()->setUserData('role', $role);

            return true;
        }

        return false;
    }
}