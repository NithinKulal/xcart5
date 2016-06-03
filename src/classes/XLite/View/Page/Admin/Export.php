<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page\Admin;

/**
 * Export page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Export extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'export';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'export/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'export/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/page.twig';
    }

    /**
     * Get inner widget class name
     * 
     * @return string
     */
    protected function getInnerWidget()
    {
        $result = 'XLite\View\Export\Begin';

        if ($this->isExportNotFinished()) {
            $result = 'XLite\View\Export\Progress';

        } elseif ($this->isExportFinished()) {
            $result = 'XLite\View\Export\Completed';

        } elseif ($this->isExportFailed()) {
            $result = 'XLite\View\Export\Failed';
        }

        return $this->isExportLocked() ? 'XLite\View\Export\Begin' : $result;
    }

    /**
     * Get export state
     *
     * @return boolean
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }

    /**
     * Check - export process is not-finished or not 
     * 
     * @return boolean
     */
    protected function isExportNotFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && in_array($state['state'], array(\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS))
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName());
    }

    /**
     * Check - export process is finished 
     * 
     * @return boolean
     */
    protected function isExportFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && \XLite\Core\EventTask::STATE_FINISHED == $state['state']
            && \XLite\Core\Request::getInstance()->completed
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName());
    }

    /**
     * Check - export process is finished
     *
     * @return boolean
     */
    protected function isExportFailed()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && \XLite\Core\EventTask::STATE_ABORTED == $state['state']
            && \XLite\Core\Request::getInstance()->failed
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName())
            && $this->getGenerator()
            && $this->getGenerator()->hasErrors();
    }

    /**
     * Get export event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Export\Generator::getEventName();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getExportCancelFlagVarName()
    {
        return \XLite\Logic\Export\Generator::getExportCancelFlagVarName();
    }
}

