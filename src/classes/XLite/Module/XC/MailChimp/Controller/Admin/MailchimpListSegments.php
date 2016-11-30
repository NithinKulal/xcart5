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
class MailchimpListSegments extends \XLite\Controller\Admin\AAdmin
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

        return \Xlite\Core\Translation::getInstance()->lbl(
            'MailChimp list segments',
            array(
                'list_name' => $listName->getName()
            )
        );
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
                $list->getRepository()->updateExistingListSegments($list);
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
                'Mailchimp lists',
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
            ? static::t('No segments')
            : (($name = $this->getModelName())
                ? $name
                : static::t('Manage segments')
            );
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

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->getModel()->getName();
    }

    /**
     * Preprocessor update action
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $data = \XLite\Core\Request::getInstance()->data;

        foreach ($data as $id => $value) {
            $mailChimpList = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpSegment')
                ->find($id);

            if (!is_null($mailChimpList)) {
                $mailChimpList->setEnabled($value['enabled']);

                \XLite\Core\Database::getEM()->persist($mailChimpList);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
