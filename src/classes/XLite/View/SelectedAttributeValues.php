<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Selected product attribute values widget
 *
 * @ListChild (list="cart.item.info", weight="20")
 */
class SelectedAttributeValues extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ITEM       = 'item';
    const PARAM_SOURCE     = 'source';
    const PARAM_STORAGE_ID = 'storage_id';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'selected_attribute_values/script.js';

        return $list;
    }

    /**
     * Get Change attribute_values link URL
     *
     * @return string
     */
    public function getChangeAttributeValuesLink()
    {
        return $this->buildURL(
            'change_attribute_values',
            '',
            array(
                'source'     => $this->getParam('source'),
                'storage_id' => $this->getParam('storage_id'),
                'item_id'    => $this->getItem()->getItemId(),
            )
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'selected_attribute_values/body.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ITEM       => new \XLite\Model\WidgetParam\TypeObject('Item', null, false, '\XLite\Model\OrderItem'),
            self::PARAM_SOURCE     => new \XLite\Model\WidgetParam\TypeString('Source', 'cart'),
            self::PARAM_STORAGE_ID => new \XLite\Model\WidgetParam\TypeInt('Storage id', null),
        );
    }

    /**
     * getItem
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(self::PARAM_ITEM);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getItem()->hasAttributeValues();
    }

    /**
     * Manage 'change attributes' link on cart page
     *
     * @return boolean
     */
    protected function isChangeAttributesLinkVisible()
    {
        return (bool) $this->getParam(self::PARAM_SOURCE);
    }
}
