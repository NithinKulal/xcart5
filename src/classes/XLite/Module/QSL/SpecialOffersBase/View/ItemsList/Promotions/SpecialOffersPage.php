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

namespace XLite\Module\QSL\SpecialOffersBase\View\ItemsList\Promotions;

/**
 * Special offers promoted on the home page.
 * 
 * @ListChild (list="center", zone="customer")
 */
class SpecialOffersPage extends \XLite\Module\QSL\SpecialOffersBase\View\ItemsList\Promotions\APromotedOffers
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'special_offers';

        return $result;
    }
    
    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return parent::getFingerprint() . '-page';
    }

    /**
     * Dependent modules should enable this flag to get the widget displayed.
     * 
     * @return boolean
     */
    protected function isWidgetEnabled()
    {
        return true;
    }
    
    /**
     * Returns parameters to filter the list of available booking options.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();
        $cnd->{\XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_VISIBLE_OFFERS} = true;
        
        return $cnd;
    }
    
    /**
     * Returns block title.
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-promoted-offers-page';
    }

}