<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/22/2016
 * Time: 4:50 PM
 */

namespace Framework\Core\FrameworkClasses\Session;

class Session implements ISession
{
    private $data = null;

    public function __construct()
    {
        session_start();

        $this->data = &$_SESSION;
    }

    public function sessionCreate()
    {
        $this->data['last_access_time'] = time();
        $this->data['ip_address'] = $this->getClientIp();
        $this->data['role'] = '';
    }

    public function sessionExists()
    {
        return !empty($this->data);
    }

    public function sessionUpdate()
    {
        $this->data['last_access_time'] = time();
    }

    public function sessionDestroy()
    {
        session_destroy();
    }

    public function getUserData($index = null)
    {
        if ($index == null)
            return $this->data;

        if (!isset($this->data[$index]))
            throw new \Exception("No \"{$index}\" key in session data.");

        return $this->data[$index];
    }

    public function setUserData($key, $value)
    {
        $this->data[$key] = $value;
    }

    private function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipAddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipAddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipAddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipAddress = getenv('REMOTE_ADDR');
        else
            $ipAddress = 'UNKNOWN';
        return $ipAddress;
    }
}