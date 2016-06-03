<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

/**
 * Minimum quantity for product
 */
class MinQuantity extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Minimum order quantity
     *
     * @var   integer
     */
    protected $minQuantity = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Wholesale/min_quantity/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Wholesale/min_quantity/body.twig';
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
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                $this->getProduct(),
                false,
                '\XLite\Model\Product'
            ),
        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->hasMinimumOrderQuantity();
    }

    /**
     * Return minimum quantity for ordering
     *
     * @return boolean
     */
    protected function hasMinimumOrderQuantity()
    {
        return $this->getMinimumOrderQuantity() > 1;
    }

    /**
     * Return minimum quantity for ordering
     *
     * @return integer
     */
    protected function getMinimumOrderQuantity()
    {
        if (is_null($this->minQuantity)) {
            $this->minQuantity = $this->getParam(self::PARAM_PRODUCT)->getMinQuantity(
                $this->getCart()->getProfile() ? $this->getCart()->getProfile()->getMembership() : null
            );
        }

        return $this->minQuantity;
    }
}
