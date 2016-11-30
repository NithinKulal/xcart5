<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Menu\Admin;

/**
 * Top menu widget
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->relatedTargets['profile_list'][] = 'mailchimp_subscriptions';

        $this->relatedTargets['mailchimp_lists'][] = 'mailchimp_list_segments';
        $this->relatedTargets['mailchimp_lists'][] = 'mailchimp_segment';
        $this->relatedTargets['mailchimp_lists'][] = 'mailchimp_list_groups';
        $this->relatedTargets['mailchimp_lists'][] = 'mailchimp_list_interests';
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $return = parent::defineItems();

        $return['promotions'][self::ITEM_CHILDREN]['mailchimp_lists'] = array(
            self::ITEM_TITLE      => 'MailChimp Lists',
            self::ITEM_TARGET     => 'mailchimp_lists',
            self::ITEM_CLASS      => 'mailchimp-lists',
            self::ITEM_PERMISSION => 'manage users',
            self::ITEM_WEIGHT     => 1000,
        );

        return $return;
    }
}
