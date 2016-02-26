<?php

namespace Framework\Core\FrameworkClasses\Routing;

use Framework\Core\FrameworkClasses\Logging\ILogger;
use Framework\Core\FrameworkClasses\URLUtils\IUrl;
use Framework\Core\FrameworkClasses\Components\SFComponentLoader;

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
    protected $loadSpecificComponent = false;

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
     * @param mixed|bool|string $loadSpecificComponent Load specific component (false or name of the component)
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
        $pagesUrl,
        $loadSpecificComponent
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
            $pagesUrl,
            $loadSpecificComponent
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

        $excludePageHead = ($this->loadSpecificComponent !== false) ? true : false;

        $header = $this->genHeaderString($headerArr, $page, $this->pagesUrl, $this->componentsUrl, $excludePageHead);

        return $content;
    }

    /**
     * Generates HTML header for a page.
     *
     * @param array $outputComponentsConfig Output components config.
     * @param Page $page Page object.
     * @param string $pagesUrl Pages URL.
     * @param string $componentsUrl Components URL.
     * @param bool $excludePageHead Should page header be excluded or not.
     * @return string Generated header.
     */
    public function genHeaderString(array $outputComponentsConfig, Page $page, $pagesUrl, $componentsUrl, $excludePageHead)
    {
        $header = "";

        if (!$excludePageHead)
        {
            if ($page->cssLoad)
            {
                $pageCss = $pagesUrl . $page->tplName . "/css/$page->tplName.css";
                $header = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$pageCss}\">" . PHP_EOL;
            }

            if ($page->jsLoad)
            {
                $pageJs = $pagesUrl . $page->tplName . "/js/$page->tplName.js";
                $header .= "<script src=\"{$pageJs}\"></script>" . PHP_EOL;
            }
        }

        foreach ($outputComponentsConfig as $outCompName => $outCompValues)
        {
            $componentUrl = $componentsUrl . $outCompName . '/';
            $css = $componentUrl . "css/$outCompName.css";
            $js = $componentUrl . "js/$outCompName.js";

            if (isset($outCompValues['js']) && $outCompValues['js'])
                $header .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$css}\">" . PHP_EOL;

            if (isset($outCompValues['css']) && $outCompValues['css'])
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
     * @param mixed|int|string $loadSpecificComponent Load specific component.
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
        $pagesUrl,
        $loadSpecificComponent)
    {
        $this->emptyPageIndex = $emptyPageIndex;
        $this->tplEngine = $tplEngine;
        $this->pagesTplDir = $pagesTplDir;
        $this->maintenanceMode = $maintenanceMode;
        $this->logger = $logger;
        $this->url = $url;
        $this->pagesUrl = $pagesUrl;
        $this->componentsUrl = $componentsUrl;
        $this->loadSpecificComponent = $loadSpecificComponent;

        foreach ($configuredPages as $pageName => $pageConfig)
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

            $cssLoad = false;
            $jsLoad = false;

            if (isset($pageConfig['css']) && $pageConfig['css'] === true)
                $cssLoad = true;

            if (isset($pageConfig['js']) && $pageConfig['js'] === true)
                $jsLoad = true;

            // Populate pages array
            $this->pages[$pageName] = new Page(
                $pageName,
                $outCompsToLoad,
                $tplToLoad,
                $tplName,
                $componentLoader,
                $this->logger,
                $this->loadSpecificComponent,
                $jsLoad,
                $cssLoad
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
