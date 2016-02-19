<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/19/2016
 * Time: 9:27 PM
 */

namespace Core\Routing;


interface IPageLoader
{
    /**
     * Retrieve current page content.
     *
     * @param string $currentPageName
     * @param string &$header Generated header will be stored here
     * @return string Page content
     * @throws \Exception if page 404 is not configured (prevents redirection loop).
     */
    public function getCurrentPageContent($currentPageName, &$header);

    /**
     * Generates HTML header for a page.
     *
     * @param array $outputComponentsConfig Output components config.
     * @param string $tplName Page template name.
     * @param string $pagesUrl Pages URL.
     * @param string $componentsUrl Components URL.
     * @return string Generated header.
     */
    public function genHeaderString(array $outputComponentsConfig, $tplName, $pagesUrl, $componentsUrl);
}