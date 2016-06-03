<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\View\Menu\Admin;

/**
 * Left menu widget
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        if (!isset($this->relatedTargets['news_messages'])) {
            $this->relatedTargets['news_messages'] = array('news_message');
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
        $list = parent::defineItems();

        if (!isset($list['content'])) {
            $list['content'] = array(
                static::ITEM_TITLE    => static::t('Content'),
                static::ITEM_TARGET   => 'menus',
                static::ITEM_WEIGHT   => 500,
                static::ITEM_ICON_SVG => 'images/contacts.svg',
                static::ITEM_CHILDREN => array(),
            );
        }

        $list['content'][static::ITEM_CHILDREN]['news_messages'] = array(
            static::ITEM_TITLE  => static::t('News messages'),
            static::ITEM_TARGET => 'news_messages',
        );

        return $list;
    }
}
