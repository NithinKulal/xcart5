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
class MailchimpLists extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return \Xlite\Core\Translation::getInstance()->lbl('MailChimp Lists');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        try {
            Core\MailChimp::getInstance()->updateMailChimpLists();
        } catch (Core\MailChimpException $e) {
            \XLite\Core\TopMessage::addError($e->getMessage());
        }
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
            /** @var \XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpList */
            $mailChimpList = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList')
                ->find($id);

            if (!is_null($mailChimpList)) {
                $mailChimpList->setEnabled(intval($value['enabled']));
                $mailChimpList->setSubscribeByDefault(intval($value['subscribe_by_default']));

                $default = 0;
                if (
                    Core\MailChimp::isSelectBoxElement()
                    && $mailChimpList->getId() == \XLite\Core\Request::getInstance()->default_list
                ) {
                    $default = 1;
                } elseif (!Core\MailChimp::isSelectBoxElement()) {
                    $default = (1 == $value['subscribe_by_default']) ? 1 : 0;
                }

                $mailChimpList->setSubscribeByDefault($default);

                \XLite\Core\Database::getEM()->persist($mailChimpList);
            }
        }

        $delete = \XLite\Core\Request::getInstance()->delete;

        if (
            isset($delete)
            && !empty($delete)
        ) {
            foreach ($delete as $id => $value) {
                $mailChimpList = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList')
                    ->find($id);

                if (
                    $value
                    && $mailChimpList->getIsRemoved()
                ) {
                    $mailChimpList->delete();
                }
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
