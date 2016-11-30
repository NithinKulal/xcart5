<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail list groups names
 */
class MailchimpListInterests extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $group = $this->getModel();

        if (!isset($group)) {
            $this->setHardRedirect();

            $this->setReturnURL($this->buildURL('mailchimp_list_groups'));

            $this->doRedirect();
        }

        return static::t('MailChimp list group names', [ 'group_name' => $group->getTitle() ]);
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        try {
            $list = $this->getListModel();
            
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
        if ($this->isVisible()) {
            if ($this->getListModel()) {
                $this->addLocationNode(
                    static::t('MailChimp lists'),
                    $this->buildURL('mailchimp_lists')
                );
            }

            if ($this->getModel()) {
                $this->addLocationNode(
                    (($name = $this->getListModelName())
                        ? $name
                        : static::t('Manage groups')
                    ),
                    $this->buildURL('mailchimp_list_groups', '', [ 'id' => \XLite\Core\Request::getInstance()->id ])
                );
            }
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
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpGroup
     */
    public function getModel()
    {
        return \Xlite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpGroup')->find(
            \XLite\Core\Request::getInstance()->group_id
        );
    }

    /**
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList
     */
    public function getListModel()
    {
        return \Xlite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')->find(
            \XLite\Core\Request::getInstance()->id
        );
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->getModel()->getTitle();
    }

    /**
     * @return string
     */
    public function getListModelName()
    {
        return $this->getListModel()->getName();
    }
}
