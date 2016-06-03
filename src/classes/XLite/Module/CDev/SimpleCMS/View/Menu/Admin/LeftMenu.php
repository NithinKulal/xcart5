<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\Menu\Admin;

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
        if (!isset($this->relatedTargets['layout'])
            || empty($this->relatedTargets['layout'])
        ) {
            $this->relatedTargets['layout'] = array();
        }

        if (!in_array('logo_favicon', $this->relatedTargets['layout'])) {
            $this->relatedTargets['layout'][] = 'logo_favicon';
        }

        if (!isset($this->relatedTargets['menus'])) {
            $this->relatedTargets['menus'] = array('menu');
        }

        if (!isset($this->relatedTargets['pages'])) {
            $this->relatedTargets['pages'] = array('page');
        }

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

        if (!isset($items['content'])) {
            $items['content'] = array(
                static::ITEM_TITLE    => static::t('Content'),
                static::ITEM_TARGET   => 'menus',
                static::ITEM_WEIGHT   => 500,
                static::ITEM_ICON_SVG => 'images/contacts.svg',
                static::ITEM_CHILDREN => array(),
            );
        }

        $items['content'][static::ITEM_CHILDREN ] += array(
            'menus' => array(
                static::ITEM_TITLE      => static::t('Menus'),
                static::ITEM_TARGET     => 'menus',
                static::ITEM_PERMISSION => 'manage menus',
                static::ITEM_WEIGHT     => 100,
            ),
            'pages' => array(
                static::ITEM_TITLE      => static::t('Pages'),
                static::ITEM_TARGET     => 'pages',
                static::ITEM_PERMISSION => 'manage custom pages',
                static::ITEM_WEIGHT     => 200,
            ),
        );

        return $items;
    }
}
