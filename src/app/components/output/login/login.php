<?php

namespace Components\Output;

use Components\Logic\Auth;
use Framework\Core\FrameworkClasses\Components\OutputComponent;

class Login extends OutputComponent
{
    /**
     * Executes component logic.
     */
    protected function execute()
    {
        $username = '';

        $formSubmitted = false;

        $loginFormIndex = 'login_form_submit';

        $arrayToUse = $this->sf->Post();

        if ($arrayToUse->hasKey($loginFormIndex))
        {
            $formSubmitted = true;
        }

        if ($formSubmitted)
        {
            $username = $arrayToUse->get('username');
            $password = $arrayToUse->get('password');

            /** @var Auth */
            $logicAuth = $this->getLogicComponent(LOGIC_AUTH);

            if ($logicAuth->authUser($username, $password) === true)
            {
                $this->sf->Url()->redirect(
                    $this->sf->Url()->getMainUrl()
                );
            }
            else
            {
                $this->tplEngine->assign('message', $this->langObj->get('bad_credentials'));
            }
        }

        $this->tplEngine->assign('username', $username);
    }
}