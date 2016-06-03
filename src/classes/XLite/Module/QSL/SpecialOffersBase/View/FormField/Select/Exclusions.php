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

namespace XLite\Module\QSL\SpecialOffersBase\View\FormField\Select;

/**
 * Form field to choose offers that may not apply together.
 */
class Exclusions extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Widget param names
     */
    const PARAM_CURRENT_SPECIAL_OFFER = 'offer';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CURRENT_SPECIAL_OFFER => new \XLite\Model\WidgetParam\Object(
                'Special offer',
                null,
                false,
                '\XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer'
            ),
        );
    }
    
    /**
     * Get current special offer.
     *
     * @return integer
     */
    protected function getSpecialOffer()
    {
        return $this->getParam(self::PARAM_CURRENT_SPECIAL_OFFER);
    }
    
    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = array();
        
        foreach ($this->getSpecialOfferRepo()->findAll() as $offer) {
            $options[$offer->getOfferId()] = $offer->getName();
        }
        
        return $options;
    }
    
    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();
        
        $currentId = $this->getSpecialOffer() ? $this->getSpecialOffer()->getOfferId() : 0;
        if ($currentId) {
            unset($options[$currentId]);
        }
        
        return $options;
    }

    /**
     * Returns the repository object for SpecialOffer model.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer
     */
    protected function getSpecialOfferRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer');
    }

}