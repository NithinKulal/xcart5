<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Core;

/**
 * Event listener (common) 
 */
abstract class EventListener extends \XLite\Core\EventListener implements \XLite\Base\IDecorator
{
    /**
     * Get listeners
     *
     * @return array
     */
    protected function getListeners()
    {
        return parent::getListeners()
            + array(
                'migrateToS3'   => array('\XLite\Module\CDev\AmazonS3Images\Core\EventListener\MigrateToS3'),
                'migrateFromS3' => array('\XLite\Module\CDev\AmazonS3Images\Core\EventListener\MigrateFromS3'),
            );
    }
}
