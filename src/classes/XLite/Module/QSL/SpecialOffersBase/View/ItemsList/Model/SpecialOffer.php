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
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\View\ItemsList\Model;

/**
 * Special offers items list
 */
class SpecialOffer extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'main/style.css';
        $list[] = 'modules/QSL/SpecialOffersBase/special_offers/style.css';

        return $list;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return \XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::ORDER_BY_POSITION;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Special offer'),
                static::COLUMN_LINK     => 'special_offer',
                static::COLUMN_ORDERBY  => 100,
            ),
            'activeFrom' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Active from'),
                static::COLUMN_TEMPLATE => 'modules/QSL/SpecialOffersBase/special_offers/parts/cell.date.twig',
                static::COLUMN_ORDERBY  => 200,
            ),
            'activeTill' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Active till'),
                static::COLUMN_TEMPLATE => 'modules/QSL/SpecialOffersBase/special_offers/parts/cell.date.twig',
                static::COLUMN_ORDERBY  => 300,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer';
    }


    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }
    
    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('special_offer');
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        return array($this->getSortBy(), $this->getSortOrder());
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New special offer';
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }
    
        // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as switchyabvle (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' special_offers';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\QSL\SpecialOffersBase\View\StickyPanel\ItemsList\SpecialOffer';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase->{\XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_TYPE_ENABLED} = true;

        return $searchCase;
    }
}