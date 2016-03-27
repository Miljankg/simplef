<?php

namespace Console\Core\Action;


class RolesOperation extends Operation
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

        $roles = $this->config->get('roles');

        $users = $this->config->get('users');

        $rolesData = array('undefined_role' => 0);

        foreach ($roles as $role) {
            foreach ($users as $user => $userConfig) {
                if (isset($userConfig['role']) && $userConfig['role'] == $role) {
                    if (!isset($rolesData[$role]))
                        $rolesData[$role] = 0;

                    $rolesData[$role]++;
                } else {
                    $rolesData['undefined_role']++;
                }
            }
        }

        $str = "";

        foreach ($rolesData as $role => $userCount) {
            $str .= "Role: $role, Users: $userCount\n";
        }

        return $str;
    }

    private function addRole($roleName)
    {
        $roles = $this->config->get('roles');

        if (in_array($roleName, $roles))
            throw new \Exception("Role \"$roleName\" already exists.");

        array_push($roles, $roleName);

        $this->config->set('roles', $roles);

        return "Role \"$roleName\" successfully added.";
    }

    private function removeRole($roleName)
    {
        $roles = $this->config->get('roles');

        if (!in_array($roleName, $roles))
            throw new \Exception("Role \"$roleName\" does not exists.");

        $usersCount = 0;

        $users = $this->config->get('users');

        foreach ($users as $user => $userConfig)
        {
            if (isset($userConfig['role']) && $userConfig['role'] == $roleName)
                $usersCount++;
        }

        $question = "Are you sure that you want to remove role \"$roleName\"? ";

        $questionAppendix = "";

        if ($usersCount > 0)
            $questionAppendix .= "$usersCount user/s belong to this role (will be moved to undefined_role)!";

        $pagesMappedToRole = array();

        $pagesAccessConfig = $this->config->get('pages_access');

        foreach ($pagesAccessConfig as $pageName => $rolesForPage)
        {
            if (in_array($roleName, $rolesForPage))
                array_push($pagesMappedToRole, $pageName);
        }

        if (!empty($pagesMappedToRole))
            $questionAppendix .= "\nPages mapped to this role: " . $this->arrToStr($pagesMappedToRole);

        if (!empty($questionAppendix))
            $question .= "\n\n$questionAppendix\n\nAnswer ";

        $question .= "(yes|no):";

        $answer = $this->scriptParams->askForUserInput($question, array("yes", "no"));

        if ($answer == 'no')
            return "Giving up on removing role.";

        if(($key = array_search($roleName, $roles)) !== false)
        {
            unset($roles[$key]);
        }

        $pageAffectedCount = 0;

        foreach ($pagesMappedToRole as $pageName)
        {
            if(($key = array_search($roleName, $pagesAccessConfig[$pageName])) !== false)
            {
                unset($pagesAccessConfig[$pageName][$key]);
                $pageAffectedCount++;
            }
        }

        foreach ($users as $userName => $userConfig)
        {
            if (isset($userConfig['role']) && $userConfig['role'] == $roleName)
                $users[$userName]['role'] = 'unknown_role';
        }

        $this->config->set('users', $users);
        $this->config->set('pages_access', $pagesAccessConfig);
        $this->config->set('roles', $roles);

        return "Role \"$roleName\" removed successfully. Users affected: $usersCount. Pages affected: $pageAffectedCount.";
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

        $roleName = $this->scriptParams->askForUserInput("Enter role name: ");

        if (empty($roleName))
            throw new \Exception("Role name cannot be empty");

        switch ($this->value)
        {
            case 'add':
                $output = $this->addRole($roleName);
                break;
            case 'remove':
                $output = $this->removeRole($roleName);
                break;
        }

        return $output;
    }
}