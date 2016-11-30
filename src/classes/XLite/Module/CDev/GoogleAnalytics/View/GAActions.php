<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

use XLite\Module\CDev\GoogleAnalytics;
use XLite\Module\CDev\GoogleAnalytics\Logic\Action;
use XLite\Module\CDev\GoogleAnalytics\Logic\ActionsStorage;

/**
 * Actions declaration (Universal)
 *
 * @ListChild (list="layout.footer", zone="customer")
 * @ListChild (list="body", zone="admin")
 */
class GAActions extends \XLite\View\AView
{
    /**
     * Get GA options list
     *
     * @return array
     */
    public function getActions()
    {
        $actions = ActionsStorage::getInstance()->getApplicableActions();
        ActionsStorage::getInstance()->clearActions($actions);

        return array_map(
            function(Action\IAction $action) {
                return $action->getActionData();
            },
            $actions
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/universal/ga_actions.twig';
    }

    /**
     * Display widget as Standalone-specific
     *
     * @return boolean
     */
    protected function isDisplayStandalone()
    {
        return (
                !\XLite\Core\Operator::isClassExists('\XLite\Module\CDev\DrupalConnector\Handler')
                || !\XLite\Module\CDev\DrupalConnector\Handler::getInstance()->checkCurrentCMS()
            )
            && \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics
            && \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account;
    }
    /**
     * Use Universal Analytics
     *
     * @return boolean
     */
    protected function useUniversalAnalytics()
    {
        return 'U' == \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_code_version;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isDisplayStandalone()
            && GoogleAnalytics\Main::useUniversalAnalytics();
    }
}