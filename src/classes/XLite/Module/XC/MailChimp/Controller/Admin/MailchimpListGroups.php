<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail list groups
 */
class MailchimpListGroups extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $listName = $this->getModel();

        if (!isset($listName)) {
            $this->setHardRedirect();

            $this->setReturnURL($this->buildURL('mailchimp_lists'));

            $this->doRedirect();
        }

        return static::t('MailChimp list groups', [ 'list_name' => $listName->getName() ]);
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        try {
            $list = $this->getModel();

            if ($list) {
                $list->getRepository()->updateExistingListGroups($list);
            }

        } catch (Core\MailChimpException $e) {
            \XLite\Core\TopMessage::addError($e->getMessage());
        }
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getModel()) {
            $this->addLocationNode(
                static::t('MailChimp lists'),
                $this->buildURL('mailchimp_lists')
            );
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return !$this->isVisible()
            ? static::t('No groups defined')
            : (($categoryName = $this->getModelName())
                ? $categoryName
                : static::t('Manage groups')
            );
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->getModel()->getName();
    }
    /**
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList
     */
    public function getModel()
    {
        return \Xlite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')->find(
            \XLite\Core\Request::getInstance()->id
        );
    }
}
