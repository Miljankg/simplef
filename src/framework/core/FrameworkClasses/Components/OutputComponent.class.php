<?php

namespace Framework\Core\FrameworkClasses\Components;

use Framework\Core\FrameworkClasses\Configuration\Config;
use Framework\Core\ISF;
use Framework\Core\Lang\Language;

/**
 * Represents OutputComponent in SF
 *
 * @author Miljan Pantic
 */
abstract class OutputComponent extends SFComponent
{
    //<editor-fold desc="Members">

    protected $tplEnabled = false;

    /** @noinspection PhpUndefinedClassInspection */

    /** @var \Smarty */
    protected $tplEngine = null;
    protected $tpl = "";   

    /** @var Language */
    protected $langObj = null;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * OutputComponent constructor.
     *
     * @param string $name Component name.
     * @param Config|null $config Config object.
     * @param Language|null $langObj Language object.
     * @param LogicComponent[] $logicComponents Logic component on which this component depends on.
     * @param ISF $sf Simple Framework instasnce.
     */
    public function __construct($name, Config $config = null, Language $langObj = null, array $logicComponents, ISF $sf) {
        parent::__construct($name, $config, $logicComponents, $sf);
        
        $this->langObj = $langObj;
        
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    /**
     * Runs component logic and returns results.
     *
     * @return string Component parsed template.
     */
    public function runComponentLogic()
    {
        $this->execute();

        $return = null;
        
        if ($this->tplEnabled)
            $return = $this->getTemplateContent();
        
        return $return;
    }

    /** @noinspection PhpUndefinedClassInspection */

    /**
     * Enabled tpl loading.
     *
     * @param \Smarty $tplEngine
     * @param $tpl
     */
    public function enableTplLoading(
        /** @noinspection PhpUndefinedClassInspection */
        \Smarty
        $tplEngine,
        $tpl)
    {
        $this->tplEnabled = true;
        $this->tplEngine = $tplEngine;
        $this->tpl = $tpl;
    }

    /**
     * Disables tpl loading.
     */
    public function disableTplLoading()
    {
        $this->tplEnabled = false;
        $this->tplEngine = null;
        $this->tpl = "";
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Executes component logic.
     */
    protected abstract function execute();

    /**
     * Retrieves template content.
     * 
     * @return string Fetched template
     */
    protected function getTemplateContent()
    {
        return $this->tplEngine->fetch($this->tpl);
    }
    
    /**
     * Outputs passed data.
     * 
     * @param string $data Data to output.
     */
    protected function output($data)
    {
        die($data);
    }

    //</editor-fold>
}
