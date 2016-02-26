<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/22/2016
 * Time: 5:01 PM
 */

namespace Framework\Core\Authentication;

use Framework\Core\FrameworkClasses\Configuration\IConfig;

class Auth
{
    /** @var array */
    private $users = null;
    private $roles = null;

    public function __construct(IConfig $dataSource)
    {
        $this->users = $dataSource->get('users');
        $this->roles = $dataSource->get('roles');
    }

    public function authUser($username, $password)
    {
        if (!isset($this->users[$username]))
            return false;

        $user = $this->users[$username];

        if ($user['password'] != sha1($password))
            return false;

        return $user['role'];
    }
}