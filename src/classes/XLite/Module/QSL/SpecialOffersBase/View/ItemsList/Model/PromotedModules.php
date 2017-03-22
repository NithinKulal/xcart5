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
 * @copyright Copyright (c) 2017 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\View\ItemsList\Model;

/**
 * Promoted special offer modules.
 */
class PromotedModules extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/SpecialOffersBase/special_offers/special_offer_modules.twig';
    }

    /**
     * Returns an array of special offer related modules.
     * 
     * @return array
     */
    protected function getPromotedModules()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Module');

        $result = [
            [
                'author' => 'QSL',
                'code' => 'SpecialOffersBuyXGetY',
                'cssClass' => 'special-offer-mod--buy-x-get-y',
                'promo' => $this->t('This module adds variations of the "Buy X Get Y" special offer type. For example, you can give the 50% discount on each third product from Toys category.'),                
            ],
            [
                'author' => 'QSL',
                'code' => 'SpecialOffersSpendXGetY',
                'cssClass' => 'special-offer-mod--spend-x-get-y',
                'promo' => $this->t('This module adds variations of the "Spend X Get Y" special offer type. For example, in every $100 spent by customer you can give the cheapest product away as a gift.'),                
            ],
        ];
        
        foreach ($result as $k=>$m) {
            $module = $repo->findOneBy(['author' => $m['author'], 'name' => $m['code']], ['fromMarketplace' => 'ASC']);
            if ($module) {
                $result[$k]['name'] = $module->getName();
                $result[$k]['url'] = $module->getMarketplaceURL();                
            } else {
                unset($result[$k]);
            }
        }

        return $result;
    }
}