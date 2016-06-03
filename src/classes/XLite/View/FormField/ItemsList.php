<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

/**
 * Items list
 */
class ItemsList extends \XLite\View\FormField\AFormField
{
    /**
     * Widget parameters
     */
    const PARAM_LIST_CLASS = 'listClass';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_ITEMS_LIST;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'items_list.twig';
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
            self::PARAM_LIST_CLASS => new \XLite\Model\WidgetParam\TypeString('List class', ''),
        );
    }

    /**
     * Get list class
     *
     * @return string
     */
    protected function getListClass()
    {
        return $this->getParam(self::PARAM_LIST_CLASS);
    }

}
