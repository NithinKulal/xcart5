<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Prepare;

/**
 * StatusMessages
 *
 * @ListChild (list="admin.center", weight="0", zone="admin")
 */
class StatusMessages extends \XLite\View\Upgrade\Step\Prepare\APrepare
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/status_messages';
    }

    /**
     * Return list of diagnostic messages
     *
     * @return array
     */
    protected function getMessages()
    {
        return \XLite\Upgrade\Cell::getInstance()->getErrorMessages();
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool) $this->getMessages();
    }
}
