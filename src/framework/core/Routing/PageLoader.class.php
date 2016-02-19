<?php

namespace Core\Routing;

use Core\Logging\ILogger;
use Core\URLUtils\IUrl;
use Core\Components\SFComponentLoader;

/**
 * Handles multiple pages operations.
 *
 * @author Miljan Pantic
 */
class PageLoader implements IPageLoader
{
    //<editor-fold desc="Members">

    /** @var Page[] */
    protected $pages = array();

    protected $tplEngine = null;
    protected $emptyPageIndex = "";
    protected $maintenanceMode = "";
    protected $pagesTplDir = "";

    protected $pagesUrl;
    protected $componentsUrl;

    /** @var IUrl */
    protected $url = null;

    /** @var ILogger */
    protected $logger = null;

    public $pageNotFoundPage = "404";
    public $pageMaintenance = "maintenance";

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /** @noinspection PhpUndefinedClassInspection */

    /**
     * Constructor.
     *
     * @param array $configuredPages
     * @param array $configuredOutComponents
     * @param array $configuredTemplates
     * @param string $emptyPageIndex
     * @param bool $maintenanceMode
     * @param \Smarty $tplEngine
     * @param string $pagesTplDir Pages template dir
     * @param SFComponentLoader $componentLoader
     * @param ILogger $logger
     * @param IUrl $url
     * @param string $componentsUrl Components URL.
     * @param string $pagesUrl Pages URL.
     */
    public function __construct(
        array $configuredPages,
        array $configuredOutComponents,
        array $configuredTemplates,
        $emptyPageIndex,
        $maintenanceMode,
        /** @noinspection PhpUndefinedClassInspection */
        \Smarty $tplEngine,
        $pagesTplDir,
        SFComponentLoader $componentLoader,
        ILogger $logger,
        IUrl $url,
        $componentsUrl,
        $pagesUrl
    )
    {
        $this->init(
            $configuredPages,
            $configuredOutComponents,
            $configuredTemplates,
            $emptyPageIndex,
            $maintenanceMode,
            $tplEngine,
            $pagesTplDir,
            $componentLoader,
            $logger,
            $url,
            $componentsUrl,
            $pagesUrl
        );
    }

    //</editor-fold>

    //<editor-fold desc="IPageLoader functions">

    /**
     * Retrieve current page content.
     *
     * @param string $currentPageName
     * @param string &$header Generated header will be stored here
     * @return string Page content
     * @throws \Exception if page 404 is not configured (prevents redirection loop).
     */
    public function getCurrentPageContent($currentPageName, &$header)
    {
        $this->handleEmptyPage($currentPageName);
        if ($this->maintenanceMode)
            $currentPageName = $this->pageMaintenance;

        if (!isset($this->pages[$currentPageName]))
        {
            // Prevent 404 redirection loop if 404 page is not configured
            if ($currentPageName == $this->pageNotFoundPage)
                throw new \Exception("Page {$this->pageNotFoundPage} is not configured.");

            $this->redirectTo404();
        }

        $this->logger->logDebug("Loading page \"{$currentPageName}\"");

        /** @var Page */
        $page = $this->pages[$currentPageName];

        $headerArr = array();

        $content = $page->getContent($this->tplEngine, $headerArr);

        $header = $this->genHeaderString($headerArr, $page->tplName, $this->pagesUrl, $this->componentsUrl);

        return $content;
    }

    /**
     * Generates HTML header for a page.
     *
     * @param array $outputComponentsConfig Output components config.
     * @param string $tplName Page template name.
     * @param string $pagesUrl Pages URL.
     * @param string $componentsUrl Components URL.
     * @return string Generated header.
     */
    public function genHeaderString(array $outputComponentsConfig, $tplName, $pagesUrl, $componentsUrl)
    {
        $pageCss = $pagesUrl . $tplName . "/css/$tplName.css";
        $pageJs = $pagesUrl . $tplName . "/js/$tplName.js";

        $header = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$pageCss}\">" . PHP_EOL;
        $header .= "<script src=\"{$pageJs}\"></script>" . PHP_EOL;

        foreach ($outputComponentsConfig as $outCompName => $outCompValues)
        {
            $componentUrl = $componentsUrl . $outCompName . '/';
            $css = $componentUrl . "css/$outCompName.css";
            $js = $componentUrl . "js/$outCompName.js";

            if (isset($outCompVals['js']) && $outCompVals['js'])
                $header .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$css}\">" . PHP_EOL;

            if (isset($outCompVals['js']) && $outCompVals['js'])
                $header .= "<script src=\"{$js}\"></script>" . PHP_EOL;
        }

        return $header;

    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /** @noinspection PhpUndefinedClassInspection */

    /**
     * Initializes pages.
     *
     * @param array $configuredPages
     * @param array $configuredOutComponents
     * @param array $configuredTemplates
     * @param string $emptyPageIndex
     * @param bool $maintenanceMode
     * @param \Smarty $tplEngine
     * @param string $pagesTplDir Pages template dir
     * @param SFComponentLoader $componentLoader
     * @param ILogger $logger
     * @param IUrl $url Url object
     * @param string $componentsUrl Components URL.
     * @param string $pagesUrl Pages URL.
     */
    protected function init(
        array $configuredPages,
        array $configuredOutComponents,
        array $configuredTemplates,
        $emptyPageIndex,
        $maintenanceMode,
        /** @noinspection PhpUndefinedClassInspection */
        \Smarty $tplEngine,
        $pagesTplDir,
        SFComponentLoader $componentLoader,
        ILogger $logger,
        IUrl $url,
        $componentsUrl,
        $pagesUrl)
    {
        $this->emptyPageIndex = $emptyPageIndex;
        $this->tplEngine = $tplEngine;
        $this->pagesTplDir = $pagesTplDir;
        $this->maintenanceMode = $maintenanceMode;
        $this->logger = $logger;
        $this->url = $url;
        $this->pagesUrl = $pagesUrl;
        $this->componentsUrl = $componentsUrl;

        foreach ($configuredPages as $pageName)
        {
            $outCompsToLoad = array();
            $tplToLoad = "";

            // Handle out components
            if (isset($configuredOutComponents[$pageName]))
                $outCompsToLoad = $configuredOutComponents[$pageName];

            $tplName = $pageName;

            // Handle template
            if (isset($configuredTemplates[$pageName]))
            {
                $tplName = $configuredTemplates[$pageName];
                $tplToLoad = $configuredTemplates[$pageName] . '/' . $configuredTemplates[$pageName] . ".tpl";
            }

            if (empty($tplToLoad))
                $tplToLoad = $pageName . '/' . $pageName . ".tpl";

            $tplToLoad = $this->pagesTplDir . $tplToLoad;

            // Populate pages array
            $this->pages[$pageName] = new Page(
                $pageName,
                $outCompsToLoad,
                $tplToLoad,
                $tplName,
                $componentLoader,
                $this->logger
            );
        }
    }

    /**
     * Redirect to 404.
     */
    protected function redirectTo404()
    {
        $location = $this->url->getMainUrl() . $this->pageNotFoundPage;

        $this->url->redirect($location);
    }

    /**
     * Sets empty page index if current page is empty string.
     *
     * @param string $currPage Current page
     */
    protected function handleEmptyPage(&$currPage)
    {
        if (empty($currPage))
            $currPage = $this->emptyPageIndex;
    }
    //</editor-fold>
}
