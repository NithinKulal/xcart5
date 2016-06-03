<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\ItemsList;

/**
 * Items list search form
 */
class AItemsListSearch extends \XLite\View\Form\AForm
{
    /**
     * Widget parameter names
     */
    const PARAM_ITEMS_LIST = 'itemsList';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ITEMS_LIST => new \XLite\Model\WidgetParam\TypeObject(
                'ItemsList object',
                null,
                false,
                'XLite\View\ItemsList\Model\Table'
            ),
        );
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'searchItemsList';
    }

    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function designed for AJAX easy switch on/off
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        return 'true';
    }

    /**
     * getDefaultFormMethod
     *
     * @return string
     */
    protected function getDefaultFormMethod()
    {
        return 'post';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array(
            'mode'      => 'search',
        );
    }

    /**
     * getCommonFormParams
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        return parent::getCommonFormParams() + array(
            'itemsList' => get_class($this->getItemsList()),
        );
    }

    /**
     * Get itemsList
     *
     * @return \XLite\View\ItemsList\Model\Table
     */
    protected function getItemsList()
    {
        return $this->getParam(static::PARAM_ITEMS_LIST);
    }
}
