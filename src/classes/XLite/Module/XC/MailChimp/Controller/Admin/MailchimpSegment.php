<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail lists
 */
class MailchimpSegment extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $segment = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpSegment')->find(
            \XLite\Core\Request::getInstance()->id
        );

        if (!isset($segment)) {
            $this->setHardRedirect();

            $this->setReturnURL($this->buildURL('mailchimp_lists'));

            $this->doRedirect();
        }

        return $segment->getName();
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        $this->setReturnURL(
            $this->buildURL(
                'mailchimp_segment',
                '',
                array(
                    'id' => \XLite\Core\Request::getInstance()->id
                )
            )
        );
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return '\XLite\Module\XC\MailChimp\View\Model\MailChimpSegment';
    }
}
