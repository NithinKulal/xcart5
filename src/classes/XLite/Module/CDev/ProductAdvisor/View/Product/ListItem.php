<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\CDev\ProductAdvisor\View\Product;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Add 'coming-soon' class attribute for the product cell
     *
     * @return string
     */
    public function getProductCellClass()
    {
        $result = parent::getProductCellClass();

        if ($this->getProduct()->isUpcomingProduct()) {
            $result = preg_replace('/out-of-stock/', '', $result) . ' coming-soon';
            if (!preg_match('/cancel-ui-state-disabled/', $result)) {
                $result .= ' cancel-ui-state-disabled';
            }
        }

        return $this->getSafeValue($result);
    }

    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $labels = parent::getLabels();

        $targets = array(
            \XLite\Module\CDev\ProductAdvisor\View\ANewArrivals::WIDGET_TARGET_NEW_ARRIVALS,
            \XLite\Module\CDev\ProductAdvisor\View\AComingSoon::WIDGET_TARGET_COMING_SOON,
        );

        if (!in_array($this->getItemListWidgetTarget(), $targets)) {
            // Add ProductAdvisor's labels into the begin of labels list
            $labels = array_reverse($labels);
            $labels += \XLite\Module\CDev\ProductAdvisor\Main::getLabels($this->getProduct());
            $labels = array_reverse($labels);
        }

        return $labels;
    }

    /**
     * Return true if 'Add to cart' buttons shoud be displayed on the list items
     *
     * @return boolean
     */
    protected function isDisplayAdd2CartButton()
    {
        $product = $this->getProduct();

        $result = parent::isDisplayAdd2CartButton();

        if ($result && $product && $product->isUpcomingProduct()) {
            // Disable 'Add to cart' button for upcoming products
            $result = false;
        }

        return $result;
    }

    /**
     * Get item hover parameters
     *
     * @return array
     */
    protected function defineItemHoverParams()
    {
        $result = parent::defineItemHoverParams();
        $result[] = $this->defineItemHoverParamComingSoon();

        return $result;
    }

    /**
     * Get item hover data for coming soon item
     *
     * @return array
     */
    protected function defineItemHoverParamComingSoon()
    {
        return array(
            'text'  => static::t('Coming soon...'),
            'style' => 'coming-soon-message',
        );
    }

    /**
     * Get add2cart block widget
     *
     * @return \XLite\View\AView
     */
    protected function getAdd2CartBlockWidget()
    {
        $widget = null;

        if ($this->getProduct()->isUpcomingProduct()) {
            $widget = $this->getWidget(
                array(
                    'style'     => 'product-add2cart',
                    'label'     => 'Coming soon',
                ),
                'XLite\View\Button\Simple'
            );
        } else {
            if ($this->getProduct()->isOutOfStock()) {
                $widget = $this->getWidget(
                    array(
                        'label'     => 'Out of stock',
                    ),
                    'XLite\View\Button\Simple'
                );
            } else {
                $widget = $this->getWidget(
                    array(
                        'style'     => 'add-to-cart product-add2cart productid-' . $this->getProduct()->getProductId(),
                        'label'     => 'Add to cart',
                    ),
                    'XLite\View\Button\Simple'
                );
            }
        }

        return $widget;
    }
}
