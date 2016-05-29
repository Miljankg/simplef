<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/24/2016
 * Time: 2:02 AM
 */

namespace Console\Core\Config;


interface IConfig
{
    public function getParsed($index);
    public function setParsed($key, $value);
    public function get($index);
    public function set($key, $value);
    public function saveChanges();
    public function queueConstantForRemoval($constantName);
    public function setUser($userData, $update = false);
    public function removeUser($username);
    public function getUser($username);
    public function setRole($roleName);
    public function getUserCountWithSpecifiedRole($roleName);
    public function changeUserRole($oldRole, $newRole);
    public function roleExists($roleName);
    public function removeRole($roleName);
}