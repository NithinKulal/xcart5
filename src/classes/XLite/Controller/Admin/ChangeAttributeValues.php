<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Change attribute values from order items list
 */
class ChangeAttributeValues extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Item (cache)
     *
     * @var \XLite\Model\OrderItem
     */
    protected $item = null;

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('"{{product}} product" attributes', array('product' => $this->getItem()->getName()));
    }

    /**
     * Get item
     *
     * @return \XLite\Model\OrderItem
     */
    public function getItem()
    {
        if (!isset($this->item)) {

            // Initialize order item from request param item_id

            if (is_numeric(\XLite\Core\Request::getInstance()->item_id)) {

                $item = \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->find(\XLite\Core\Request::getInstance()->item_id);

                if (
                    $item
                    && $item->getProduct()
                    && $item->hasAttributeValues()
                ) {
                    $this->item = $item;
                }
            }
        }

        if (!isset($this->item) && \XLite\Core\Request::getInstance()->productId) {

            // Initialize order item from productId param

            $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find(\XLite\Core\Request::getInstance()->productId);

            if ($product) {
                $this->item = new \XLite\Model\OrderItem();
                $this->item->setProduct($product);
                $this->item->setAttributeValues($product->prepareAttributeValues());
            }
        }

        if (!isset($this->item)) {
            // Order item was not initialized: set to false to prevent re-initialization
            $this->item = false;
        }

        return $this->item;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    /**
     * Return selected attribute values ids
     *
     * @return array
     */
    public function getSelectedAttributeValuesIds()
    {
        return $this->getItem()->getAttributeValuesPlain();
    }

    /**
     * Get index of order item (used in field name)
     *
     * @return integer
     */
    public function getIdx()
    {
        return \XLite\Core\Request::getInstance()->idx ?: $this->getItem()->getItemId();
    }
}
