<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NextPreviousProduct\View\ItemList\Product\Customer;

use XLite\Module\XC\NextPreviousProduct\View\Product\ListItem;

/**
 * Decorated ACustomer items list
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    const NP_MODE_VIEW = 'npModeView';
    const NP_MODE_READ = 'npModeRead';

    /**
     * @var string
     */
    protected static $npMode = [];

    /**
     * @var string
     */
    protected static $npConditionCellName = [];

    /**
     * Item position on page
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * @param string $mode
     */
    public static function setNPMode($mode)
    {
        static::$npMode[get_called_class()] = $mode;
    }

    /**
     * @param string $conditionCellName
     */
    public static function setNPConditionCellName($conditionCellName)
    {
        static::$npConditionCellName[get_called_class()] = $conditionCellName;
    }

    /**
     * @return string
     */
    public static function getNPConditionCellName()
    {
        return isset(static::$npConditionCellName[get_called_class()])
            ? static::$npConditionCellName[get_called_class()]
            : '';
    }

    /**
     * @return boolean
     */
    protected static function isNPRead()
    {
        return isset(static::$npMode[get_called_class()]) && static::$npMode[get_called_class()] === self::NP_MODE_READ;
    }

    /**
     * @return boolean
     */
    protected static function isNPView()
    {
        return !isset(static::$npMode[get_called_class()]) || static::$npMode === self::NP_MODE_VIEW;
    }

    /**
     * Public wrapper for getSearchCondition()
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchConditionWrapper()
    {
        return $this->getSearchCondition();
    }

    /**
     * Get three items around $itemPosition
     *
     * @param integer $itemPosition Item position in condition
     *
     * @return array|integer
     */
    public function getNextPreviousItems($itemPosition)
    {
        $cnd = $this->getPager()->getLimitCondition($itemPosition - 1, 3, $this->getSearchCondition());

        return $this->getData($cnd);
    }

    /**
     * Public wrapper for getPager()
     *
     * @return \XLite\View\Pager\APager
     */
    public function getPagerWrapper()
    {
        return $this->getPager();
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cellName = static::getSearchSessionCellName() . '_np';

        if (static::isNPRead()) {
            $npConditionCellName = static::getNPConditionCellName() ?: $cellName;
            return \XLite\Core\Session::getInstance()->{$npConditionCellName};
        }

        $result   = parent::getSearchCondition();

        \XLite\Core\Session::getInstance()->{$cellName} = $result;

        return $result;
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        return parent::getProductWidgetParams($product) + [
            ListItem::PARAM_PAGE_ID          => $this->getPager()->getPageIdWrapper(),
            ListItem::PARAM_POSITION_ON_PAGE => $this->position++,
            ListItem::PARAM_ITEM_LIST_CLASS  => get_class($this),
        ];
    }
}
