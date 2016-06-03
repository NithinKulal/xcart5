<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\View;

/**
 * Sidebar categories list
 */
class TopCategoriesSlidebar extends \XLite\View\TopCategoriesSlidebar implements \XLite\Base\IDecorator
{
    /**
     * Preprocess DTO
     *
     * @param  array    $categoryDTO
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {
        $categoryDTO = parent::preprocessDTO($categoryDTO);

        if ($this->isShowCatIcon()) {
            $categoryDTO['image'] = $categoryDTO['image_id']
                ? \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Image')->find($categoryDTO['image_id'])
                : null;
        }

        return $categoryDTO;
    }

    /**
     * Check if word wrap disabled
     *
     * @return boolean
     */
    protected function isShowCatIcon()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_icons;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'categories/tree';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'categories/tree/body.twig';
    }
}
