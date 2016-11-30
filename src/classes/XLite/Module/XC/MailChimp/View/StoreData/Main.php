<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\StoreData;

/**
 * Class Main
 */
class Main extends \XLite\View\AView
{
    /**
     * @inheritDoc
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'mailchimp_store_data'
            ]
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/MailChimp/store_data/main/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/MailChimp/store_data/main/body.twig';
    }

    /**
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     */
    public function getLists()
    {
        /** @var \XLite\Module\XC\MailChimp\Model\Repo\MailChimpList $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList');
        
        return $repo->getActiveMailChimpLists();
    }

    /**
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list
     */
    public function isChecked(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return $list->getStore()
            && $list->getStore()->isMain();   
    }

    /**
     * @return string
     */
    public function getGettingStartedURL()
    {
        return 'http://kb.mailchimp.com/integrations/e-commerce/how-to-use-mailchimp-for-e-commerce';
    }
}