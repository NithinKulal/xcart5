<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions\View\Menu\Admin;

/**
 * Left menu widget
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        if (!isset($this->relatedTargets['roles'])) {
            $this->relatedTargets['roles'] = array();
        }

        $this->relatedTargets['roles'][] = 'role';

        parent::__construct();
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['users'][self::ITEM_CHILDREN]['roles'] = array(
            self::ITEM_TITLE  => static::t('Roles'),
            self::ITEM_TARGET => 'roles',
            self::ITEM_WEIGHT => 300,
        );

        return $items;
    }
}
