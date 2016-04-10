<?php

namespace Framework\Core\FrameworkClasses\URLUtils;

/**
 * URL Utilities
 *
 * @author Miljan Pantic
 */
class Url implements IUrl
{
    //<editor-fold desc="Members">

	protected $currentUrl = "";	
    protected $pageName = "";
    protected $currLang = "";
    protected $protocol = "";
    protected $mainUrl = "";
    protected $mainUrlNoLang = "";
    protected $urlParts = array();

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * Parse URL and populates internal URL class values.
     *
     * @param bool $multilingual Is site multilingual or not
     * @param string $defaultLanguage Default language from config
     * @param string $defaultPage Default page from config
     * @param string $sslPort SSL Port from config
     * @param string $siteName Site name from config
     */
    public function __construct(
        $multilingual,
        $defaultLanguage,
        $defaultPage,
        $sslPort,
        $siteName
    )
    {
        $this->processURL(
            $multilingual,
            $defaultLanguage,
            $defaultPage,
            $sslPort,
            $siteName
        );
    }

    //</editor-fold>

    //<editor-fold desc="IUrl functions">

    /**
     * Returns current language.
     *
     * @return string Current language.
     */
    public function getCurrentLanguage()
    {
        return $this->currLang;
    }

    /**
     * Return current page name.
     *
     * @return string Current page name.
     */
    public function getCurrentPageName()
    {
        return $this->pageName;
    }

    /**
     * Returns current protocol from URL.
     *
     * @return string Protocol https or http
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Returns main url.
     *
     * @return string Main URL
     */
    public function getMainUrl()
    {
        return $this->mainUrl;
    }

    /**
     * Returns main url without lang.
     *
     * @return string Main URL with no lang
     */
    public function getMainUrlNoLang()
    {
        return $this->mainUrlNoLang;
    }

    /**
     * Retrieves URL Parts.
     *
     * @return array URL Parts.
     */
    public function getUrlParts()
    {
        return $this->urlParts;
    }
	
	/**
     * Retrieves current URL.
     *
     * @return array current URL.
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * Redirects to a given location.
     *
     * @param string $location
     */
    public function redirect($location)
    {
        header("Location: $location");
        exit;
    }

    /**
     * Process URL string in order to escape it.
     *
     * @param string $url Url to parse
     * @return string Parsed url
     */
    public function getProperUrlFromString($url)
    {
        return str_replace('\\', '/', $url);
    }

    /**
     * Determines http or https.
     *
     * @param string $sslPort
     * @return string HTTP or HTTPS
     */
    public function determineProtocol($sslPort)
    {
        return
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || $_SERVER['SERVER_PORT'] == $sslPort)
                ? "https://" : "http://";
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Parse URL and populates internal URL class values.
     *
     * @param bool $multilingual Is site multilingual or not
     * @param string $defaultLanguage Default language from config
     * @param string $defaultPage Default page from config
     * @param string $sslPort SSL Port from config
     * @param string $siteName Site name from config
     */
    protected function processURL(
        $multilingual,
        $defaultLanguage,
        $defaultPage,
        $sslPort,
        $siteName
    )
    {
        $this->parseUrl($multilingual, $defaultLanguage, $defaultPage);
        $this->protocol = $this->determineProtocol($sslPort);
        $this->generateMainUrlValues($this->protocol, $siteName, $this->currLang);
		$this->generateCurrentUrl($this->mainUrlNoLang);
    }
		
	protected function getUrlStr()
	{
		$pageName = (isset($_GET['pageName'])) ? $_GET['pageName'] : '';
		
		return htmlentities(addslashes($pageName), ENT_NOQUOTES, 'UTF-8');	
	}
	
	protected function generateCurrentUrl($mainUrlNoLang)
	{
		$urlStr = $this->getUrlStr();
		
		$this->currentUrl = $mainUrlNoLang . $urlStr;
	}

    /**
     * Parses URL and populates URL class internal fields.
     *
     * @param bool $multilingual Is site multilingual.
     * @param string $defaultLanguage
     * @param string $defaultPage
     */
    protected function parseUrl(
        $multilingual,
        $defaultLanguage,
        $defaultPage
    )
    {
        $urlStr = '';
        $urlPartsStartIndex = 1;
        $pageName = '';

        if (isset($_GET['pageName']))
            $urlStr = $this->getUrlStr();
		
        $urlArr = explode("/", $urlStr);

        if ($multilingual)
        {
            // if lang is present in the url
            if (isset($urlArr[0]) && $urlArr[0] != "" && strlen($urlArr[0]) == 2)
            {
                $urlPartsStartIndex = 2;

                $language = $urlArr[0];

                if (isset($urlArr[1]) && $urlArr[1] != "")
                    $pageName = $urlArr[1];

            }
            else
            {
                $pageName = $urlArr[0];
                $language = $defaultLanguage;
            }

        }
        else
        {
            $pageName = $urlStr;
            $language = $defaultLanguage;
        }

        if (strlen($pageName) == 0)
            $pageName = $defaultPage;

        for ($i = $urlPartsStartIndex; $i < count($urlArr); $i++)
            $this->urlParts[] = $urlArr[$i];

        $this->currLang = $language;
        $this->pageName = $pageName;		
    }

    /**
     * Generates main url and main url with no language.
     *
     * @param string $protocol http or https
     * @param string $siteName Site name from main config
     * @param string $currLang Current language
     */
    protected function generateMainUrlValues($protocol, $siteName, $currLang)
    {
        $this->mainUrlNoLang =
            $protocol .
            $_SERVER['HTTP_HOST'] .
            '/' .
            str_replace('\\', '/', $siteName) .
            '/';

        $this->mainUrl = $this->mainUrlNoLang . $currLang . "/";		
    }

    //</editor-fold>
}
