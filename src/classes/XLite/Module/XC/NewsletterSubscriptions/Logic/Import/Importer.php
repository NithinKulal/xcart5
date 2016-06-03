<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Logic\Import;

/**
 * Importer
 */
class Importer extends \XLite\Logic\Import\Importer implements \XLite\Base\IDecorator
{
    /**
     * Get processor list
     *
     * @return array
     */
    public static function getProcessorList()
    {
        return array_merge(
            parent::getProcessorList(),
            array(
                'XLite\Module\XC\NewsletterSubscriptions\Logic\Import\Processor\NewsletterSubscribers',
            )
        );
    }
}
