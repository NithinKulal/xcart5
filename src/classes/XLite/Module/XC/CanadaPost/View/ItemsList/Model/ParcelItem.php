<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\ItemsList\Model;

/**
 * Parcel's items list
 */
class ParcelItem extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget params
     */
    const PARAM_PARCEL_ID = 'parcelId';

    /**
     * Hide panel
     *
     * @return null
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Items are non-removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'sku'                            => array(
                static::COLUMN_ORDERBY       => 100,
                static::COLUMN_NAME          => static::t('SKU'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_METHOD_SUFFIX => 'Sku',
            ),
            'name'                           => array(
                static::COLUMN_ORDERBY       => 200,
                static::COLUMN_NAME          => static::t('Product name'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_MAIN          => true,
            ),
            'amount'                         => array(
                static::COLUMN_ORDERBY       => 300,
                static::COLUMN_NAME          => static::t('Qty'),
                static::COLUMN_NO_WRAP       => true,
           ),
            'weight'                         => array(
                static::COLUMN_ORDERBY       => 400,
                static::COLUMN_NAME          => static::t('Weight'),
                static::COLUMN_NO_WRAP       => true,
            ),
            'total_weight'                   => array(
                static::COLUMN_ORDERBY       => 500,
                static::COLUMN_NAME          => static::t('Total weight'),
                static::COLUMN_NO_WRAP       => true,
            ),
            'move_item'                      => array(
                static::COLUMN_ORDERBY       => 600,
                static::COLUMN_NAME          => static::t('Move item'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_TEMPLATE      => 'modules/XC/CanadaPost/shipments/parcel.products.move_item.twig',
            ),
        );
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
            static::PARAM_PARCEL_ID => new \XLite\Model\WidgetParam\TypeInt('Parcel ID', 0),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return true if widget can be displayed
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && $this->getParcel();
    }

    /**
     * Get parcel ID
     *
     * @return integer
     */
    protected function getParcelId()
    {
        return $this->getParam(static::PARAM_PARCEL_ID);
    }

    /**
     * Get products return
     *
     * @return \XLite\Module\XC\CanadaPost\Model\ProductsReturn|null
     */
    protected function getParcel()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\CanadaPost\Model\Order\Parcel')
            ->find($this->getParcelId());
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' capost-parcel-items-list';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item';
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getParcelId();
    }
    
    // {{{ Search

    /**
     * Return search parameters
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Module\XC\CanadaPost\Model\Repo\Order\Parcel\Item::P_PARCEL_ID => static::PARAM_PARCEL_ID
        );
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->{$modelParam} = $this->getParam($requestParam);
        }

        return $result;
    }

    // }}}

    // {{{ Columns value getters

    /**
     * Get value of the "sku" column
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return string
     */
    protected function getSkuColumnValue(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getOrderItem()->getSku();
    }

    /**
     * Get value of the "name" column
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return string
     */
    protected function getNameColumnValue(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getOrderItem()->getName();
    }

    /**
     * Get value of the "amount" column
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return integer
     */
    protected function getAmountColumnValue(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getAmount();
    }

    /**
     * Get value of the "weight" column
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return float
     */
    protected function getWeightColumnValue(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getWeightInKg(true);
    }

    /**
     * Get value of the "total_weight" column
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return float
     */
    protected function getTotalWeightColumnValue(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getTotalWeightInKg(true);
    }

    // }}}

    // {{{ Preprocessors

    /**
     * Pre-process "name" field
     *
     * @param string                                              $name   Product name
     * @param array                                               $column Column data
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item   Parcel item
     *
     * @return string
     */
    protected function preprocessName($name, array $column, \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        if (!$item->getOrderItem()->getObject()->isDeleted()) {
            $name = '<a href="' . $item->getOrderItem()->getObject()->getURL() . '">' . $name . '</a>';
        }

        return $name;
    }

    /**
     * Pre-process "weight" field
     *
     * @param string                                              $weight Item's weight
     * @param array                                               $column Column data
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item   Parcel item
     *
     * @return string
     */
    protected function preprocessWeight($weight, array $column, \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $weight . ' ' . static::t('kg');
    }

    /**
     * Pre-process "total_weight" field
     *
     * @param string                                              $weight Item's weight
     * @param array                                               $column Column data
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item   Parcel item
     *
     * @return string
     */
    protected function preprocessTotalWeight($weight, array $column, \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $weight . ' ' . static::t('kg');
    }

    // }}}

    // {{{ Helpers

    /**
     * Get allowed parcels to move
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return array
     */
    public function getAllowedToMoveParcels(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $allowedParcels = array();

        foreach ($parcel->getOrder()->getCapostParcels() as $p) {

            if (
                $p->getId() !== $parcel->getId()
                && $p->isEditable()
            ) {
                $allowedParcels[$p->getId()] = static::t('Parcel') . ' #' . $p->getNumber();
            }
        }

        $allowedParcels['NEW'] = static::t('New parcel');

        return $allowedParcels;
    }

    // }}}
}
