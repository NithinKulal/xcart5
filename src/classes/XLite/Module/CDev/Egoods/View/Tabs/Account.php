<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Tabs;

/**
 * Tabs related to user profile section
 */
abstract class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'ordered_files';
        
        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        if ($this->getOrdersWithFiles()) {
            $list['ordered_files'] = [
                'weight'   => 400,
                'title'    => static::t('Ordered files'),
                'template' => 'modules/CDev/Egoods/files.twig',
            ];
        }

        return $list;
    }

    /**
     * Get orders with files
     *
     * @return \XLite\Model\Order[]
     */
    public function getOrdersWithFiles()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order')->findAllOrdersWithEgoods($this->getProfile());
    }
}
