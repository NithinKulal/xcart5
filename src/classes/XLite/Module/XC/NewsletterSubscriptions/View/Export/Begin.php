<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\View\Export;

/**
 * Begin section
 */
class Begin extends \XLite\View\Export\Begin implements \XLite\Base\IDecorator
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getSections()
    {
        return parent::getSections()
             + array(
                'XLite\Module\XC\NewsletterSubscriptions\Logic\Export\Step\NewsletterSubscribers'   => 'Subscribers',
             );
    }
}
