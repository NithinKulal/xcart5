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
 * -----------------------------------------------------------------------------
 * CHANGES
 * 
 * 5.3.0
 * - adapted the 5.2.4 version of the module for X-Cart 5.3.x
 * 
 * 5.3.1
 * - fixed the issue that arise when creating special offers with empty dates
 * 
 * 5.3.2
 * - fixed the issue with the cart page crashing on Crisp White theme (#0046804)
 * 
 * 5.3.3
 * - fixed styles for the Special Offers backend page
 * - fixed the issue with offers of disabled modules being displyed on Special
 *   Offers backend page (#0047540)
 * 
 * -----------------------------------------------------------------------------
 * 
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'Qualiteam Software';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Special Offers (base)';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'This is the base module that allows other modules to add different types of special offers.';
    }

    /**
     * Get module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '3';
    }

    /**
     * Get minor core version which is required for the module activation
     *
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        // The minimum required version of X-Cart 5 is 5.2.11
        return '0';
    }

    /**
     * Determines if we need to show settings form link
     *
     * @return boolean
     */
    public static function showSettingsForm()
    {
        return true;
    }

    /**
     * Actions that take place after redeploying the store.
     *
     * @return void
     */
    public static function runBuildCacheHandler()
    {
        parent::runBuildCacheHandler();

        \XLite\Core\Layout::getInstance()->removeTemplateFromList('shopping_cart/parts/item.subtotal.twig', 'cart.item');

        static::updateOfferTypes();
        
        if (class_exists('\XLite\Module\CDev\SimpleCMS\Model\Menu')) {
            static::addSimpleCMSMenuLink();            
        }
        
        \XLite\Core\Database::getEM()->flush();
    }
    
    /**
     * Updates offer types and disable those that have no enabled modules.
     * 
     * @return void
     */
    protected static function updateOfferTypes()
    {
        foreach (\XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\OfferType')->findAll() as $type) {
            $enabled = $type->hasAllRequiredClasses();
            $type->setEnabled($enabled);
            if (!$enabled) {
                foreach ($type->getSpecialOffers() as $offer) {
                    $offer->setEnabled(false);
                }
            }
        }
        
    }
    
    /**
     * Adds the Special Offers entry to the primary storefront menu if it is not there yet.
     * 
     * @return void
     */
    protected static function addSimpleCMSMenuLink()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu');
        $repoLang = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\MenuTranslation');
        
        $link = \XLite\Module\QSL\SpecialOffersBase\Model\Menu::DEFAULT_OFFERS_PAGE;
        
        $item = $repo->findOneByLink('?target=special_offers');
        if ($item) {
            $item->setLink($link);
        } else {
            
            $item = $repo->findOneByLink($link);
            if (!$item) {
                
                $item = new \XLite\Module\CDev\SimpleCMS\Model\Menu(
                    array(
                        'enabled'  => false,
                        'link'     => $link,
                        'type'     => \XLite\Module\CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY,
                        'position' => 150,
                    )
                );
                $repo->insert($item);
                
                $en = new \XLite\Module\CDev\SimpleCMS\Model\MenuTranslation(
                    array(
                        'code' => 'en',
                        'name' => 'Special offers',
                    )
                );
                $en->setOwner($item);
                $item->addTranslations($en);
                $repoLang->insert($en);
                
                $ru = new \XLite\Module\CDev\SimpleCMS\Model\MenuTranslation(
                    array(
                        'code' => 'ru',
                        'name' => 'Акции',
                    )
                );
                $ru->setOwner($item);
                $item->addTranslations($ru);
                $repoLang->insert($ru);
            }
        }
    }
}
