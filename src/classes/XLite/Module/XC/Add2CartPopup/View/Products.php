<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

/**
 * Products list to display in 'Add to Cart Popup' widget
 */
class Products extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * Widget param names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Max count of products
     */
    const PARAM_MAX_PRODUCT_COUNT = 3;

    /**
     * Cache of found products
     *
     * @var array
     */
    protected $products;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'add2_cart_popup';

        return $result;
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (!\XLite\Core\CMSConnector::isCMSStarted()) {
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT]->setValue($this->getMaxCount());
        }
    }

    /**
     * List head title
     *
     * @return string
     */
    protected function getListHead()
    {
        return static::t('Customers also bought');
    }

    /**
     * Return CSS classes for list header
     *
     * @return string
     */
    protected function getListHeadClass()
    {
        return parent::getListHeadClass() . ' no-replace';
    }

    /**
     * Display list head
     *
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return true;
    }

    /**
     * Get max count of products
     *
     * @return integer
     */
    protected function getMaxCount()
    {
        return static::PARAM_MAX_PRODUCT_COUNT;
    }

    /**
     * Returns true if block is enabled
     *
     * @return boolean
     */
    protected function isBlockEnabled()
    {
        return true;
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
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        );

        $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_CENTER);
        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_GRID);
        $this->widgetParams[self::PARAM_GRID_COLUMNS]->setValue(3);

        $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]->setValue(false);
        $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]->setValue(false);
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Customer\Product\Category';
    }

    /**
     * This method is overridden to work around unnecessary getItemsCount call at parent
     *
     * TODO: provide a better way to express that Pager is not needed at all for this ItemsList
     *
     * @return array
     */
    protected function getPagerParams()
    {
        return array(
            \XLite\View\Pager\APager::PARAM_ITEMS_COUNT => 0,
            \XLite\View\Pager\APager::PARAM_LIST        => $this,
        );
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (!isset($this->products)) {

            $productIds = $this->getExcludedProductIds();

            $methods = \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::getInstance()->getActiveSources();

            foreach ($methods as $method) {
                /** @var \XLite\Model\Product[] $products */
                $products = \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::getInstance()->$method(
                    $this->getProductId(),
                    $productIds,
                    static::PARAM_MAX_PRODUCT_COUNT
                );

                if ($products) {
                    foreach ($products as $product) {
                        $this->products[] = $product;
                        $productIds[] = $product->getProductId();
                        if (static::PARAM_MAX_PRODUCT_COUNT <= count($this->products)) {
                            break;
                        }
                    }
                }

                if (static::PARAM_MAX_PRODUCT_COUNT <= count($this->products)) {
                    break;
                }
            }
        }

        return $countOnly ? count($this->products) : $this->products;
    }

    /**
     * Get list of product IDs which should be excluded from the search results
     *
     * @return array
     */
    protected function getExcludedProductIds()
    {
        $result = array();

        $result[$this->getProductId()] = 1;

        $items = $this->getCart()->getItems();

        foreach ($items as $item) {
            if ($item->getProduct() && 0 < $item->getProduct()->getProductId()) {
                $result[$item->getProduct()->getProductId()] = 1;
            }
        }

        return array_keys($result);
    }

    /**
     * Get currently viewed product ID
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getParam(static::PARAM_PRODUCT) ? $this->getParam(static::PARAM_PRODUCT)->getProductId() : 0;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' add2-cart-products-block';
    }

    /**
     * Add CSS style to items list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' add-to-cart-popup';
    }

    /**
     * Get product list item widget class.
     *
     * @return string
     */
    protected function getProductWidgetClass()
    {
        return 'XLite\Module\XC\Add2CartPopup\View\Product\CartPopupListItem';
    }
}
