<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Tabs;
use XLite\Module\XC\MailChimp\Main;

/**
 * Tabs related to category section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Settings extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'mailchimp_options';
        $list[] = 'mailchimp_store_data';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $result = [
            'mailchimp_options' => [
                'weight'    => 100,
                'title'     => static::t('MailChimp settings'),
                'widget'    => 'XLite\Module\XC\MailChimp\View\Settings\MailChimpAPISettings',
            ],
        ];
        
        if (Main::isMailChimpECommerceConfigured()) {
            $result['mailchimp_store_data'] = [
                'weight'   => 200,
                'title'    => static::t('E-Commerce features setup'),
                'template' => 'modules/XC/MailChimp/store_data/body.twig',
            ];
        }
        
        return $result;
    }
}
