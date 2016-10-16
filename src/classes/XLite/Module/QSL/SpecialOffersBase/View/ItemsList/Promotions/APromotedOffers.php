<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\View\ItemsList\Promotions;

/**
 * Widget displaying an abstract list of special offers promoted on the page.
 */
class APromotedOffers extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Rows of data.
     *
     * @var array
     */
    protected $rows;
    
    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-promoted-offers';
    }

    /**
     * Register CSS files required by the widget.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/styles.css';

        return $list;
    }

    /**
     * Returns class name for the list pager.
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * Returns path to the directory where the page body template resides in
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'promoted_offers';
    }

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/SpecialOffersBase/items_list';
    }

    /**
     * Returns the sorted list of available booking options.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Whether to return items, or just the number of them OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if ($this->isWidgetEnabled()) {
            return $this->getRepo()->search($cnd, $countOnly);
        } else {
            return $countOnly ? 0 : array();
        }
    }
    
    /**
     * Dependent modules should enable this flag to get the widget displayed.
     * 
     * @return boolean
     */
    protected function isWidgetEnabled()
    {
        return false;
    }
    
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->isWidgetEnabled() && parent::isVisible();
    }
    
    /**
     * Returns parameters to filter the list of available booking options.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        return $this->getRepo()->getActiveOffersConditions();
    }
    
    /**
     * Returns repository object for special offers model.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('\XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer');
    }

    /**
     * Returns block title.
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Special offers';
    }
    
    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return false;
    }

    /**
     * Returns the widget class name.
     * 
     * @return string
     */
    protected function getWidgetClassname()
    {
        return '\XLite\Module\QSL\SpecialOffersBase\View\Promo\SpecialOffer';
    }
 
    /**
     * Returns the number of columns in the list.
     * 
     * @return integer
     */
    protected function getColumnsCount()
    {
        return 3;
    }
    
    /**
     * Get rows of data.
     *
     * @return array
     */
    public function getRows()
    {
        if (!isset($this->rows)) {
            $this->rows = array_chunk($this->getPageData(), $this->getColumnsCount());
        }

        return $this->rows;
    }

    /**
     * Count the total number of rows.
     *
     * @return integer
     */
    public function countRows()
    {
        return isset($this->rows) ? count($this->rows) : count($this->getRows());
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param integer $row Row index.
     *
     * @return string
     */
    public function getRowCSSClass($row)
    {
        if (!$row) {
            $class = 'first';
        } elseif ($row == $this->countRows() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param integer $row    Row index.
     * @param integer $column Column index.
     *
     * @return string
     */
    public function getColumnCSSClass($row, $column)
    {
        if (!$column) {
            $class = 'first';
        } elseif ($column == $this->getColumnsCount() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }
    
    /**
     * Returns the inline CSS for an item in the grid.
     *
     * @return string
     */
    public function getItemInlineStyle()
    {
        $items = array();

        $min = $this->getMinItemWidth();
        if ($min) {
            $items[] = "min-width: {$min}";
        }

        $max = $this->getMaxItemWidth();
        if ($max) {
            $items[] = "max-width: {$max}";
        }

        return implode('; ', $items);
    }
    
    /**
     * Brand logo width.
     *
     * @return integer
     */
    public function getImageWidth()
    {
        return 160;
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMinItemWidth()
    {
        return ($this->getImageWidth() + 70) . 'px';
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMaxItemWidth()
    {
        return (($this->getImageWidth() + 70) * 2) . 'px';
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-promoted-offers';
    }
}