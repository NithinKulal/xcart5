<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Logic;

use DebugBar\Bridge\DoctrineCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use XLite\Core\Database;
use XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;
use XLite\Module\XC\WebmasterKit\DebugBar\Doctrine\DBAL\Logging\ObservableDebugStack;
use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

/**
 * DebugBar logic
 */
class DebugBar extends \XLite\Base
{
    /** @var \DebugBar\StandardDebugBar */
    protected $debugbar;

    protected $renderer;

    protected function __construct()
    {
        $settingsMgr = new DebugBarSettingsManager();

        $this->debugbar = new \DebugBar\DebugBar();

        $this->debugbar->addCollector(new PhpInfoCollector());
        $this->debugbar->addCollector(new MessagesCollector());
        $this->debugbar->addCollector(new RequestDataCollector());
        $this->debugbar->addCollector(new MemoryCollector());
        $this->debugbar->addCollector(new DataCollector\DoctrineUOWDataCollector(
            \XLite\Core\Database::getEM()->getUnitOfWork()
        ));
        $this->debugbar->addCollector(new DataCollector\MemoryPointsDataCollector());
        $this->debugbar->addCollector(new ExceptionsCollector());

        $configuration = Database::getEM()->getConnection()->getConfiguration();

        $debugStack = $configuration->getSQLLogger()
            ? new ObservableDebugStack($configuration->getSQLLogger())
            : new ObservableDebugStack();

        $configuration->setSQLLogger($debugStack);


        if ($settingsMgr->areDatabaseDetailedModeEnabled()) {
            $this->debugbar->addCollector(new DoctrineCollector($debugStack));
        } else {
            $this->debugbar->addCollector(new DataCollector\DoctrineCollectorSimple($debugStack));
        }

        if ($settingsMgr->areWidgetsTabEnabled()) {
            $this->debugbar->addCollector(new DataCollector\WidgetTimeDataCollector($debugStack));
        }

        $this->debugbar->addCollector(new DataCollector\SettingsDataCollector());

        $this->renderer = $this->debugbar->getJavascriptRenderer();
    }

    /**
     * Widget timeline data collector
     *
     * @return WidgetTimeDataCollector
     */
    public function getWidgetTimeDataCollector()
    {
        return $this->debugbar['widget_times'];
    }

    public function getBody()
    {
        return $this->renderer->render();
    }

    public function getJsFiles()
    {
        return array_map(function ($js) {
            return 'modules/XC/WebmasterKit/' . substr($js, strlen(LC_DIR_MODULES . 'XC/WebmasterKit/lib/vendor/maximebf/debugbar/src/'));
        }, $this->renderer->getAssets('js'));
    }

    public function getCssFiles()
    {
        return array_map(function ($css) {
            return 'modules/XC/WebmasterKit/' . substr($css, strlen(LC_DIR_MODULES . 'XC/WebmasterKit/lib/vendor/maximebf/debugbar/src/'));
        }, $this->renderer->getAssets('css'));
    }

    public function addMessage($message, $type = 'messages')
    {
        $this->debugbar[$type]->addMessage($message);
    }
}
