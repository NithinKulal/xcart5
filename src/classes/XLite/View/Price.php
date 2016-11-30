<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Product price
 */
class Price extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Widget parameters
     */
    const PARAM_DISPLAY_ONLY_PRICE = 'displayOnlyPrice';
    const PARAM_ALLOW_RANGE = 'allowRange';

    /**
     * @var array $labels List labels runtime cache
     */
    protected static $labels = array();

    /**
     * @var array $listPrices List prices runtime cache
     */
    protected static $listPrices = array();

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/price_plain_body.twig';
    }

    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if ($this->getProduct()) {
            // Warmup cache
            $id = $this->getProduct()->getProductId();
            if (!isset(static::$labels[$id])) {
                static::$labels[$id] = $this->getLabels();
            }
        }
    }

    /**
     * Check if price as range allowed
     *
     * @return mixed
     */
    public function isAllowRange()
    {
        return $this->getParam(static::PARAM_ALLOW_RANGE);
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
            static::PARAM_DISPLAY_ONLY_PRICE => new \XLite\Model\WidgetParam\TypeBool('Display only price', false),
            static::PARAM_ALLOW_RANGE => new \XLite\Model\WidgetParam\TypeBool('Allow to display as range', false)
        );
    }

    /**
     * Return list price of product
     *
     * @return float
     */
    protected function getListPrice($value = null)
    {
        $id = $this->getProduct()->getProductId();

        if (!isset(static::$listPrices[$id])) {
            static::$listPrices[$id] = $this->getNetPrice($value);
        }

        return static::$listPrices[$id];
    }

    /**
     * Return net price of product
     *
     * @return float
     */
    protected function getNetPrice($value = null)
    {
        return $this->getProduct()->getDisplayPrice();
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-price';
    }

    /**
     * Return list of product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $id = -1;

        if ($this->getProduct()) {
            $id = $this->getProduct()->getProductId();
        }

        return isset(static::$labels[$id])
            ? static::$labels[$id]
            : array();
    }

    /**
     * Return the specific label info
     *
     * @param string $labelName
     *
     * @return array
     */
    protected function getLabel($labelName)
    {
        return array();
    }
}
