<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/24/2016
 * Time: 2:07 AM
 */

namespace Console\Core\Config;

use Framework\Core\Database\DB;

class ConfigFromFile implements IConfig
{
    //<editor-fold desc="Members">

    private $configFileMapping = array();
    private $loadedConfig = array();
    private $loadedConfigParsed = array();
    private $rootDir;
    private $constantsFile;
    private $constantsToRemove = array();
    /** @var DB */
    private $dbObj = null;

    //</editor-fold>

    public function __construct($rootDir, $constantsFile, $config, $configParsed, $configFileMapping, $dbObj = null)
    {
        $this->rootDir = $rootDir;
        $this->constantsFile = $constantsFile;
        $this->loadedConfig = $config;
        $this->loadedConfigParsed = $configParsed;
        $this->configFileMapping = $configFileMapping;
        $this->dbObj = $dbObj;
    }

    public function queueConstantForRemoval($constantName)
    {
        array_push($this->constantsToRemove, $constantName);
    }

    //<editor-fold desc="IConfig functions">

    /**
     * Retrieves value by given index.
     *
     * @param string $index Index to search for.
     * @return mixed Founded value.
     * @throws \Exception If index does not exists.
     */
    public function getParsed($index)
    {
        if (!isset($this->loadedConfigParsed[$index]))
            throw new \Exception('No config index: ' . $index);

        return $this->loadedConfigParsed[$index];
    }

    /**
     * Set value in config.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParsed($key, $value)
    {
        $this->loadedConfigParsed[$key] = $value;
    }

    /**
     * Retrieves value by given index.
     *
     * @param string $index Index to search for.
     * @return mixed Founded value.
     * @throws \Exception If index does not exists.
     */
    public function get($index)
    {
        if (!isset($this->loadedConfig[$index]))
            throw new \Exception('No config index: ' . $index);

        return $this->loadedConfig[$index];
    }

    /**
     * Set value in config.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->loadedConfig[$key] = $value;
    }

    public function removeRole($roleName)
    {
        if ($roleName == 'unknown_role')
            throw new \Exception('Unknown role cannot be removed.');

        if ($this->dbObj == null)
        {
            $roles = $this->loadedConfig['roles'];

            if(($key = array_search($roleName, $roles)) !== false)
            {
                unset($roles[$key]);
            }

            $this->set('roles', $roles);
        }
        else
        {
            $sql = "DELETE FROM roles WHERE role_name='{$roleName}'";

            $this->dbObj->ExecuteNonQuery($sql);
        }
    }

    public function roleExists($roleName)
    {
        $role = null;

        if ($this->dbObj == null)
        {
            $roles = $this->loadedConfig['roles'];

            if (in_array($roleName, $roles))
                return array_search($roleName, $roles);
        }
        else
        {
            $sql = "SELECT * FROM roles WHERE role_name='{$roleName}'";

            $results = $this->dbObj->ExecuteTableQuery($sql);

            if (!empty($results))
                return $results[0]['role_id'];
        }

        return false;
    }

    public function setRole($roleName)
    {
        $roleExists = $this->roleExists($roleName);

        if ($roleExists !== false)
            throw new \Exception("Role \"$roleName\" already exists.");

        if ($this->dbObj == null)
        {
            $roles = $this->loadedConfig['roles'];

            array_push($roles, $roleName);

            $this->set('roles', $roles);
        }
        else
        {
            $sql = "INSERT INTO roles (role_name) VALUES ('{$roleName}')";

            $this->dbObj->ExecuteNonQuery($sql);
        }
    }

    public function changeUserRole($oldRole, $newRole)
    {
        if ($this->dbObj == null)
        {
            $users =  $this->loadedConfig['users'];

            foreach ($users as $userName => $userConfig)
            {
                if (isset($userConfig['role_name']) && $userConfig['role_name'] == $oldRole)
                    $users[$userName]['role_name'] = 'unknown_role';
            }

            $this->set('users', $users);
        }
        else
        {
            $newRoleId = $this->roleExists($newRole);
            $oldRoleId = $this->roleExists($oldRole);

            $sql = "UPDATE users SET user_role=$newRoleId WHERE user_role = $oldRoleId";

            $this->dbObj->ExecuteNonQuery($sql);
        }
    }

    public function getUserCountWithSpecifiedRole($roleName)
    {
        $usersCount = 0;

        if ($this->dbObj == null)
        {
            $users =  $this->loadedConfig['users'];

            foreach ($users as $user => $userConfig)
            {
                if (isset($userConfig['role_name']) && $userConfig['role_name'] == $roleName)
                    $usersCount++;
            }
        }
        else
        {
            $sql = "SELECT count(*) FROM users u LEFT JOIN roles r ON u.user_role = r.role_id WHERE r.role_name='{$roleName}'";

            $usersCount = $this->dbObj->ExecuteScalarQuery($sql);
        }

        return $usersCount;
    }

    public function removeUser($username)
    {
        if ($this->dbObj == null)
        {
            $users = $this->loadedConfig['users'];

            if (isset($users[$username]))
                unset($users[$username]);

            $this->set('users', $users);
        }
        else
        {
            $sql = "DELETE FROM users WHERE user_name='{$username}'";

            $this->dbObj->ExecuteNonQuery($sql);
        }
    }

    public function getUser($username)
    {
        $userData = null;

        if ($this->dbObj == null)
        {
            $users = $this->loadedConfig['users'];

            if (isset($users[$username]))
            {
                $userData = $users[$username];
                $userData['user_name'] = $username;
            }
        }
        else
        {
            $sql = "SELECT * FROM users WHERE user_name='$username'";

            $results = $this->dbObj->ExecuteTableQuery($sql);

            if (!empty($results))
                $userData = $results[0];
        }

        return $userData;
    }

    public function setUser($userData, $update = false)
    {
        if ($this->dbObj == null)
        {
            $users = $this->get('users');

            $users[$userData['user_name']]['user_password'] = $userData['user_password'];
            $users[$userData['user_name']]['role_name'] = $userData['role_name'];

            $this->set('users', $users);
        }
        else
        {
            $sql = "";

            if (!$update)
            {
                $sql = "INSERT INTO users (user_name, user_password, user_role) VALUES ";

                $sql .= "('{$userData['user_name']}'";
                $sql .= ", '{$userData['user_password']}'";
                $sql .= ", '{$userData['user_role']}')";
            }
            else
            {
                $sql = "UPDATE users SET ";

                $sql .= "user_password='{$userData['user_password']}'";
                $sql .= ", user_role='{$userData['user_role']}'";
                $sql .= " WHERE user_name='{$userData['user_name']}'";
            }

            $this->dbObj->ExecuteNonQuery($sql);
        }
    }

    /**
     * Save changes to the data source.
     */
    public function saveChanges()
    {
        $currFile = '';

        foreach ($this->configFileMapping as $config => $file)
        {
            if ($currFile != $file)
            {
                file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL);
                $currFile = $file;
            }

            $value = var_export($this->loadedConfig[$config], true);

            $string = str_pad("\$config['{$config}']", 40) . " = {$value};" . PHP_EOL;

            file_put_contents($file, $string, FILE_APPEND | LOCK_EX);
        }

        $definedConstants = get_defined_constants('true');

        $constantFile = $this->constantsFile;

        file_put_contents($constantFile, '<?php' . PHP_EOL . PHP_EOL);

        foreach ($definedConstants['user'] as $constantName => $constantValue)
        {
            if (in_array($constantName, $this->constantsToRemove))
                continue;

            $varStr = var_export($constantValue, true);

            $string = "define('$constantName', $varStr);" . PHP_EOL;
            file_put_contents($constantFile, $string, FILE_APPEND | LOCK_EX);
        }
    }

    //</editor-fold>
}