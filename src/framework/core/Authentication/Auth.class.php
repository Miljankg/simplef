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
    private $dataSource = null;

    public function __construct(IConfig $dataSource)
    {
        $this->users = $dataSource->get('users');
        $this->roles = $dataSource->get('roles');
        $this->dataSource = $dataSource;
    }

    public function authUser($username, $password)
    {
        $user = $this->dataSource->getUser($username);

        if ($user == null)
            return false;

        if ($user['user_password'] != sha1($password))
            return false;

        return $user['role_name'];
    }
}