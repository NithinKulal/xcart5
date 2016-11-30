<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

use XLite\Model\Cart;
use XLite\Core\Database;
use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeSet;
use XLite\Model\WidgetParam\TypeString;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\CacheableTrait;
use XLite\View\ItemsList\Product\Customer\ACustomer;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\AView implements DynamicWidgetInterface
{
    use CacheableTrait;

    /**
     * Widget parameters
     */
    const PARAM_PRODUCT_ID                        = 'productId';
    const PARAM_VIEW_LIST_NAME                    = 'productViewListName';
    const PARAM_DISPLAY_MODE                      = 'displayMode';
    const PARAM_ITEM_LIST_WIDGET_TARGET           = 'itemListWidgetTarget';
    const PARAM_ICON_MAX_WIDTH                    = 'iconWidth';
    const PARAM_ICON_MAX_HEIGHT                   = 'iconHeight';
    const PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY = 'productStockAvailabilityPolicy';
    const PARAM_PRODUCT_ENTITY_VERSION_FETCHER    = 'productEntityVersionFetcher';

    /**
     * An associated product instance
     *
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Runtime cache of item hover text params, see method defineItemHoverParams()
     *
     * @var array
     */
    protected $itemHoverParams = null;

    /**
     * Return class attribute for the product cell
     *
     * Note:
     *  If you decorate this method you must use $this->getSafeValue() on return value
     * 
     * @return object
     */
    public function getProductCellClass()
    {
        $product = $this->getProduct();

        $classes = 'product productid-'
               . $product->getProductId()
               . ($product->isOutOfStock() ? ' out-of-stock' : '')
               . ($product->isAvailable() ? '' : ' not-available')
               . ($this->isDraggable() ? ' draggable' : '')
               . ($this->isGotoProduct() ? ' need-choose-options' : '')
               . ' ' . $this->getDynamicProductCellClasses();

        return $this->getSafeValue($classes);
    }

    /**
     * Is draggable
     *
     * @return boolean
     */
    public function isDraggable()
    {
        return true;
    }

    /**
     * Get aggregated content of dynamic widgets rendering product css classes.
     *
     * Dynamic widgets are required to personalize a common cached product list content. Customers with different cart contents can see product list differently.
     *
     * @return string
     */
    public function getDynamicProductCellClasses()
    {
        $classes = [
            $this->getProductAddedToCartCellClass(),
        ];

        return trim(implode(' ', $classes));
    }

    /**
     * Get content of the dynamic widget that renders 'product-added' css class if product was added to cart.
     *
     * @return string
     */
    public function getProductAddedToCartCellClass()
    {
        $widget = $this->getChildWidget('XLite\View\Product\ProductAddedToCartCellClass', [
            ProductAddedToCartCellClass::PARAM_PRODUCT_ID => $this->getProduct()->getProductId(),
        ]);

        return $widget->getContent();
    }

    /**
     * Check - go to product page instead of adding to cart
     *
     * @return boolean
     */
    protected function isGotoProduct()
    {
        return \XLite\Core\Config::getInstance()->General->force_choose_product_options !== ''
               && $this->getProduct()->hasEditableAttributes();
    }

    /**
     * Should we show add2cart block
     *
     * @return boolean
     */
    protected function isShowAdd2CartBlock()
    {
        return true;
    }

    /**
     * Get add2cart block widget
     *
     * @return \XLite\View\AView
     */
    protected function getAdd2CartBlockWidget()
    {
        $widget = null;

        if ($this->getProduct()->isOutOfStock()) {
            $widget = $this->getWidget(
                array(
                    'style'     => 'out-of-stock',
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

        return $widget;
    }


    /**
     * Get product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return array();
    }

    /**
     * Return true if quick-look is enabled on the items list
     *
     * @return boolean
     */
    protected function isQuickLookEnabled()
    {
        return true;
    }

    /**
     * Return true if 'Add to cart' buttons shoud be displayed on the list items
     *
     * @return boolean
     */
    protected function isDisplayAdd2CartButton()
    {
        return (
            $this->getDisplayMode() != ACustomer::DISPLAY_MODE_GRID
            || \XLite\Core\Config::getInstance()->General->enable_add2cart_button_grid
        ) && $this->isDisplayGridAdd2CartButton();
    }

    /**
     * Return true if 'Add to cart' buttons shoud be displayed on the grid list items
     *
     * @return boolean
     */
    protected function isDisplayGridAdd2CartButton()
    {
        return !($this->getDisplayMode() == ACustomer::DISPLAY_MODE_GRID && $this->getProduct()->isOutOfStock());
    }

    /**
     * Get product URL
     *
     * @param integer $categoryId Category ID
     *
     * @return string
     */
    protected function getProductURL($categoryId = null)
    {
        $product              = $this->getProduct();
        $params               = array();
        $params['product_id'] = $product->getProductId();

        // TODO: Optimize/rewrite. Very heavy logic. It seems that there's no need to fetch product categories in this method since the "current" category has already been taken into account when category listing was rendered
        if ($categoryId && $categoryId != $this->getRootCategoryId()) {
            $found             = false;
            $firstId           = null;
            $productCategories = $product->getCategories();
            if (
                $productCategories
                && (
                    1 < count($productCategories)
                    || (
                        LC_USE_CLEAN_URLS
                        && !(bool)\Includes\Utils\ConfigParser::getOptions(array('clean_urls', 'use_canonical_urls_only'))
                    )
                )
            ) {
                foreach ($productCategories as $category) {
                    if (!isset($firstId)) {
                        $firstId = $category->getCategoryId();
                    }
                    if ($category->getCategoryId() == $categoryId) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $categoryId = $firstId;
                }

            } else {
                $categoryId = null;
            }

            if ($categoryId) {
                $params['category_id'] = $categoryId;
            }
        }

        return \XLite\Core\Converter::buildURL('product', '', $params);
    }

    /**
     * Return the maximal icon width
     *
     * @return integer
     */
    protected function getIconWidth()
    {
        return $this->getParam(static::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * Return the maximal icon height
     *
     * @return integer
     */
    protected function getIconHeight()
    {
        return $this->getParam(static::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * Return the icon 'alt' value
     *
     * @return string
     */
    protected function getIconAlt()
    {
        $product = $this->getProduct();

        return $product->getImage() && $product->getImage()->getAlt()
            ? $product->getImage()->getAlt()
            : $product->getName();
    }

    /**
     * getDisplayMode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->getParam(self::PARAM_DISPLAY_MODE);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
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
            self::PARAM_PRODUCT_ID                        => new TypeInt('ProductId'),
            self::PARAM_ICON_MAX_WIDTH                    => new TypeInt('Max icon width', 0, true),
            self::PARAM_ICON_MAX_HEIGHT                   => new TypeInt('Max icon height', 0, true),
            self::PARAM_VIEW_LIST_NAME                    => new TypeString('View list name'),
            self::PARAM_DISPLAY_MODE                      => new TypeSet('Display mode'),
            self::PARAM_ITEM_LIST_WIDGET_TARGET           => new TypeString('Item list widget target'),
            self::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY => new TypeObject('Product stock availability policy'),
            self::PARAM_PRODUCT_ENTITY_VERSION_FETCHER    => new TypeObject('Product entity version fetcher'),
        );
    }

    /**
     * Get associated product's id.
     *
     * @return int
     */
    protected function getProductId()
    {
        return $this->getParam(self::PARAM_PRODUCT_ID);
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if (!isset($this->product)) {
            $this->product = Database::getRepo('XLite\Model\Product')->find($this->getProductId());
        }

        return $this->product;
    }

    /**
     * Get product view list name used to render this product list item.
     *
     * @return string
     */
    protected function getListName()
    {
        return $this->getParam(self::PARAM_VIEW_LIST_NAME);
    }

    /**
     * Get widget target of the item list
     *
     * @return string
     */
    protected function getItemListWidgetTarget()
    {
        return $this->getParam(self::PARAM_ITEM_LIST_WIDGET_TARGET);
    }

    /**
     * Get item hover parameters
     *
     * @return array
     */
    protected function getItemHoverParams()
    {
        if (!isset($this->itemHoverParams)) {
            $this->itemHoverParams = $this->defineItemHoverParams();
        }

        return $this->itemHoverParams ?: array();
    }

    /**
     * Get item hover parameters
     *
     * @return array
     */
    protected function defineItemHoverParams()
    {
        $result = array();

        $product = $this->getProduct();

        if ($product->isOutOfStock()) {
            $result['out_of_stock']     = $this->defineItemHoverParamOutOfStock();

        } elseif ($this->isGotoProduct()) {
            $result['choose_options']   = $this->defineItemHoverParamChooseOptions();

        } elseif ($this->isDraggable()) {
            $result['draggable']        = $this->defineItemHoverParamDraggable();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllItemHoverParams(){
        return [
            'draggable'         => $this->defineItemHoverParamDraggable(),
            'out_of_stock'      => $this->defineItemHoverParamOutOfStock(),
            'choose_options'    => $this->defineItemHoverParamChooseOptions(),
        ];
    }

    /**
     * Get item hover data for draggable item
     *
     * @return array
     */
    protected function defineItemHoverParamDraggable()
    {
        return array(
            'text'          => static::t('Drag and drop me to the bag'),
            'style'         => 'drag-message',
            'showCondClass' => 'draggable',
            'hideCondClass' => 'ui-draggable-disabled',
        );
    }

    /**
     * Get item hover data for out-of-stock item
     *
     * @return array
     */
    protected function defineItemHoverParamOutOfStock()
    {
        return array(
            'text'      => static::t('Product is out of stock'),
            'style'     => 'out-message',
            'showCondClass' => 'out-of-stock',
        );
    }

    /**
     * Get item hover data for item which cannot be added to cart without options selected
     *
     * @return array
     */
    protected function defineItemHoverParamChooseOptions()
    {
        return array(
            'text'      => static::t('Choose the product options first'),
            'style'     => 'choose-product-option',
            'showCondClass' => 'need-choose-options',
        );
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = $this->getCacheKeyPartsGenerator()->getMembershipPart();
        $params[] = $this->getCacheKeyPartsGenerator()->getShippingZonesPart();

        $params[] = $this->getProductId();
        $params[] = $this->getParam(self::PARAM_PRODUCT_ENTITY_VERSION_FETCHER)->fetch($this->getProductId());

        $params[] = $this->getParam(self::PARAM_DISPLAY_MODE);

        $policy = $this->getParam(self::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY);
        $cart   = Cart::getInstance();

        $params[] = $policy->isOutOfStock($cart);
        $params[] = $this->getItemListWidgetTarget();

        return $params;
    }
}
