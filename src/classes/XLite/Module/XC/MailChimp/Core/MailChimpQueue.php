<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

use XLite\Module\XC\MailChimp\Core\Action\IMailChimpAction;

class MailChimpQueue extends \XLite\Base\Singleton
{
    /**
     * @var IMailChimpAction[]
     */
    protected $actions = [];

    /**
     * @param                  $key
     * @param IMailChimpAction $action
     */
    public function addAction($key, IMailChimpAction $action)
    {
        $this->actions[$key] = $action;
    }

    /**
     * @return Action\IMailChimpAction[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return static
     */
    public function clearActions()
    {
        $this->actions = [];

        return $this;
    }
}