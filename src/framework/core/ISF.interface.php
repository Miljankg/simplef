<?php

namespace Framework\Core;
use Framework\Core\FrameworkClasses\Configuration\IConfig;
use Framework\Core\FrameworkClasses\Logging\ILogger;
use Framework\Core\FrameworkClasses\Session\ISession;
use Framework\Core\FrameworkClasses\URLUtils\IUrl;
use Framework\Core\FrameworkClasses\Globals\Get;
use Framework\Core\FrameworkClasses\Globals\Post;
use Framework\Core\Lang\Language;

/**
 * Interface ISF
 */
interface ISF
{
    /**
     * Executes SF.
     */
    public function execute();

    /**
     * @return ISession
     */
    public function Session();

    /**
     * @return Get
     */
    public function Get();

    /**
     * @return Post
     */
    public function Post();

    /**
     * @return IConfig
     */
    public function Config();

    /**
     * @return Language
     */
    public function Lang();

    /**
     * @return ILogger
     */
    public function Logger();

    /**
     * @return IUrl
     */
    public function Url();
}