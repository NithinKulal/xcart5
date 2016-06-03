<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core;
use XLite\Logic\Sales\Generator;

/**
 * Sales calculation page controller
 */
class SalesCalculation extends ACL\Catalog
{
    /**
     * Resize
     *
     * @var \XLite\Logic\Sales\Generator
     */
    protected $salesGenerator;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Sales calculation');
    }

    /**
     * Get resize
     *
     * @return \XLite\Logic\Sales\Generator
     */
    public function getSalesGenerator()
    {
        if (null === $this->salesGenerator) {
            $eventName = Generator::getEventName();
            $state = Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);
            $this->salesGenerator = ($state && isset($state['options']))
                ? new Generator($state['options'])
                : false;
        }

        return $this->salesGenerator;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isSalesNotFinished()
    {
        $eventName = Generator::getEventName();
        $state = Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                array(Core\EventTask::STATE_STANDBY, Core\EventTask::STATE_IN_PROGRESS),
                true
            )
            && !Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getSalesCancelFlagVarName());
    }

    /**
     * Sales action
     *
     * @return void
     */
    protected function doActionSales()
    {
        Generator::run($this->assembleSalesOptions());
    }

    /**
     * Sales action
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (Core\Request::getInstance()->failed) {
            Core\TopMessage::addInfo('The calculation failed');

        } elseif (Core\Request::getInstance()->completed) {
            Core\TopMessage::addInfo('The calculation completed');
        }

        $moduleId = Core\Database::getRepo('XLite\Model\Module')->findOneBy(
            array(
                'author'          => 'CDev',
                'name'            => 'Bestsellers',
                'fromMarketplace' => false,
            )
        )->getModuleID();

        $this->setReturnURL(
            $this->buildURL(
                'module',
                '',
                array(
                    'moduleId'     => $moduleId,
                    'returnTarget' => 'addons_list_installed',
                )
            )
        );
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleSalesOptions()
    {
        $request = Core\Request::getInstance();

        return array(
            'include' => $request->section,
        );
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionSalesCancel()
    {
        Generator::cancel();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getSalesCancelFlagVarName()
    {
        return Generator::getSalesCancelFlagVarName();
    }
}
