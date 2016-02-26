<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/19/2016
 * Time: 4:49 PM
 */

namespace Framework\Core\FrameworkClasses\URLUtils;

interface IUrl
{
    /**
     * Returns current language.
     *
     * @return string Current language.
     */
    public function getCurrentLanguage();

    /**
     * Return current page name.
     *
     * @return string Current page name.
     */
    public function getCurrentPageName();

    /**
     * Returns current protocol from URL.
     *
     * @return string Protocol https or http
     */
    public function getProtocol();

    /**
     * Returns main url.
     *
     * @return string Main URL
     */
    public function getMainUrl();

    /**
     * Returns main url without lang.
     *
     * @return string Main URL with no lang
     */
    public function getMainUrlNoLang();

    /**
     * Retrieves URL Parts.
     *
     * @return array URL Parts.
     */
    public function getUrlParts();

    /**
     * Redirects to a given location.
     *
     * @param string $location
     */
    public function redirect($location);

    /**
     * Process URL string in order to escape it.
     *
     * @param string $url Url to parse
     * @return string Parsed url
     */
    public function getProperUrlFromString($url);

    /**
     * Determines http or https.
     *
     * @param string $sslPort
     * @return string HTTP or HTTPS
     */
    public function determineProtocol($sslPort);
}