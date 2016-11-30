<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

class EventListener extends \XLite\Core\EventListener implements \XLite\Base\IDecorator
{
    /**
     * Get listeners
     *
     * @return array
     */
    protected function getListeners()
    {
        $listeners = parent::getListeners();

        $listeners += array(
            'MailChimpUploadingData' => ['XLite\Module\XC\MailChimp\Core\EventListener\UploadingData'],
        );

        return $listeners;
    }
}