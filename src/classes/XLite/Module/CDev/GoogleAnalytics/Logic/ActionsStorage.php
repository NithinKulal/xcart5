<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic;

use XLite\Module\CDev\GoogleAnalytics\Logic\Action\IAction;

/**
 * Class ActionsStorage
 */
class ActionsStorage extends \XLite\Base\Singleton
{
    /**
     * @var Action\IAction[]
     */
    protected $actions = [];

    public function __construct()
    {
        $this->actions = \XLite\Core\Session::getInstance()->gaActions;
    }

    /**
     * Add action that may not be executed in current process,
     * and will not be postponed for next requests
     * @param                $key
     * @param Action\IAction $action
     */
    public function addAction($key, Action\IAction $action)
    {
        $this->actions[$key] = $action;
    }

    /**
     * Add action that may not be executed in current process,
     * but will be executed as soon as possible if applicable
     *
     * @param                $key
     * @param Action\IAction $action
     */
    public function addPostponedAction($key, Action\IAction $action)
    {
        $this->actions[$key] = $action;

        $actions = \XLite\Core\Session::getInstance()->gaActions;
        if (!$actions || !is_array($actions)) {
            $actions = [];
        }

        $actions[$key] = $action;

        \XLite\Core\Session::getInstance()->gaActions = $actions;
    }

    /**
     * @param $key
     *
     * @return Action\IAction
     */
    public function getAction($key)
    {
        return $this->actions[$key];
    }

    /**
     * @return Action\IAction[]
     */
    public function getApplicableActions()
    {
        return array_filter(
            $this->getActions(),
            function (Action\IAction $action) {
                return $action->isApplicable();
            }
        );
    }

    /**
     * @return Action\IAction[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     *
     */
    public function clearActions(array $actions)
    {
        foreach ($actions as $key => $action) {
            if (isset($this->actions[$key])) {
                unset($this->actions[$key]);
            }
        }

        \XLite\Core\Session::getInstance()->gaActions = $this->actions;
    }
}
