<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Delete category popup button
 */
class DeleteCategory extends \XLite\View\Button\APopupButton
{
    /**
     * Widget param names
     */
    const PARAM_CATEGORY_ID          = 'categoryId';
    const PARAM_REMOVE_SUBCATEGORIES = 'removeSubcategories';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/delete_category.js';

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'      => 'categories',
            'pre_action'  => 'delete',
            'widget'      => '\XLite\View\DeleteCategory',
            'category_id' => $this->getParam(self::PARAM_CATEGORY_ID),
            'subcats'     => (bool) $this->getParam(self::PARAM_REMOVE_SUBCATEGORIES),
        );
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Delete';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_CATEGORY_ID          => new \XLite\Model\WidgetParam\TypeInt('Category ID', 1),
            self::PARAM_REMOVE_SUBCATEGORIES => new \XLite\Model\WidgetParam\TypeBool('Remove subcategories', false),
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' delete-category-button';
    }

    /**
     * Return template path
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/delete_category.twig';
    }
}
