<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/24/2016
 * Time: 1:03 AM
 */

namespace Framework\Core\Loaders;


class ClassLoader
{
    private $rootDir = '';

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function loadClass($oopElement)
    {
        $ds = DIRECTORY_SEPARATOR;
		$oopElementOriginal = $oopElement;
        $oopElement = str_replace(array('\\', '/'), array($ds, $ds), $oopElement);
        $tmp = explode($ds, $oopElement);
        $oopElementName = end($tmp);
        $suffix = ".class";

        if(strpos($oopElementName, 'I') === 0 && ctype_upper(substr($oopElementName, 0, 2)))
            $suffix = ".interface";

        $suffix .= ".php";

        if (isset($tmp[0]))
            $tmp[0] = lcfirst($tmp[0]);

        if (isset($tmp[1]))
            $tmp[1] = lcfirst($tmp[1]);

        $oopElement = implode($ds, $tmp);

        $file =
            $this->rootDir .
            str_replace('\\', $ds, $oopElement) .
            $suffix;

        if( is_file($file) && !class_exists($oopElementOriginal) )
            /** @noinspection PhpIncludeInspection */
            require $file;
    }
}