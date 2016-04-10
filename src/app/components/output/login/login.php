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

		$pageBeforeLogin = $this->sf->Session()->getUserData('PAGE_BEFORE_LOGIN');
		
		$refererPage = (isset($pageBeforeLogin) && !empty($pageBeforeLogin)) ? $pageBeforeLogin : $this->sf->Url->getMainUrl();

        if ($arrayToUse->hasKey($loginFormIndex))
        {
            $formSubmitted = true;
        }

        if ($formSubmitted)
        {
            $username = $arrayToUse->get('username');
            $password = $arrayToUse->get('password');
			
			$refererPage = $arrayToUse->get('referer_page');

            /** @var Auth */
            $logicAuth = $this->getLogicComponent(LOGIC_AUTH);

            if ($logicAuth->authUser($username, $password) === true)
            {							
                $this->sf->Url()->redirect(
                    $refererPage
                );
            }
            else
            {
                $this->tplEngine->assign('message', $this->langObj->get('bad_credentials'));
            }
        }

		$this->tplEngine->assign('refererPage', $refererPage);
        $this->tplEngine->assign('username', $username);
    }
}