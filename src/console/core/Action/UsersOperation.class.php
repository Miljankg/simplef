<?php

namespace Console\Core\Action;


class UsersOperation extends Operation
{

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected function previewValue()
    {
        if ($this->previewValueValue != $this->value)
            return false;

        $question = "Enter username to filter output, or just press enter: ";

        $answer = $this->scriptParams->askForUserInput($question, array(), 'username');

        $users = $this->config->get('users');

        $outputArray = array();

        if (!empty($answer))
        {
            if (!isset($users[$answer]))
            {
                throw new \Exception("User \"$answer\" does not exists.");
            }

            $outputArray[$answer] = $users[$answer];
        }
        else
        {
            $outputArray = $users;
        }



        $str = "";

        foreach ($outputArray as $username => $userConfig) {
            $str .= "Username: $username\nRole: {$userConfig['role']}\n\n";
        }

        return $str;
    }

    private function addUser($userName)
    {
        $user = $this->config->getUser($userName);

        if ($user !== null)
            throw new \Exception("User \"$userName\" already exists.");

        $role = $this->scriptParams->askForUserInput("Enter role: ", array(), "role");

        if (empty($role))
            throw new \Exception("Role cannot be empty.");

        $userRole = $this->config->roleExists($role);

        if ($userRole === false)
            throw new \Exception("Role \"$role\" does not exists.");

        $users = array('role_name' => $role);

        $password = $this->scriptParams->askForUserInput("Enter password: ", array(), "password");

        if (empty($password))
            throw new \Exception("Password cannot be empty.");

        $users['user_password'] = sha1($password);

        $users['user_name'] = $userName;
        $users['user_role'] = $userRole;

        $this->config->setUser($users);

        return "User \"$userName\" successfully added.";
    }

    private function removeUser($userName)
    {
        $user = $this->config->getUser($userName);

        if ($user === null)
            throw new \Exception("User \"$userName\" does not exists.");

        $question = "Are you sure that you want to remove user \"$userName\"? (yes|no)";

        $answer = $this->scriptParams->askForUserInput($question, array('yes', 'no'));

        if ($answer == 'no')
            return "Giving up on removing user.";

        $this->removeUser($userName);

        return "User \"$userName\" removed successfully.";
    }

    private function changePassword($userName)
    {
        $user = $this->config->getUser($userName);

        if ($user === null)
            throw new \Exception("User \"$userName\" does not exists.");

        $password = $this->scriptParams->askForUserInput("Enter password: ", array(), 'password');

        if (empty($password))
            throw new \Exception("Password cannot be empty.");

        $user['user_password'] = sha1($password);

        $this->config->setUser($user, true);

        return "Password changed for user \"$userName\"";
    }

    private function changeRole($userName)
    {
        $user = $this->config->getUser($userName);

        if ($user === null)
            throw new \Exception("User \"$userName\" does not exists.");

        $role = $this->scriptParams->askForUserInput("Enter role: ", array(), 'role');

        if (empty($role))
            throw new \Exception("Role cannot be empty.");

        $userRole = $this->config->roleExists($role);

        if ($userRole === false)
            throw new \Exception("Role \"$role\" does not exists.");

        $user['user_role'] = $userRole;
        $user['role_name'] = $role;

        $this->config->setUser($user, true);

        return "Role changed for user \"$userName\"";
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $output = "";

        $userName = $this->scriptParams->askForUserInput("Enter username: ", array(), 'username');

        if (empty($userName))
            throw new \Exception("Username cannot be empty");

        switch ($this->value)
        {
            case 'add':
                $output = $this->addUser($userName);
                break;
            case 'remove':
                $output = $this->removeUser($userName);
                break;
            case 'change_password':
                $output = $this->changePassword($userName);
                break;
            case 'change_role':
                $output = $this->changeRole($userName);
                break;
        }

        return $output;
    }
}