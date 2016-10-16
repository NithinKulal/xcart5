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

namespace XLite\Module\QSL\SpecialOffersBase\View\Promo;

/**
 * Renders individual special offer in a list of offers promoted on the page.
 */
class SpecialOffer extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_OFFER  = 'offer';
    const PARAM_ROW    = 'row';
    const PARAM_COLUMN = 'column';

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Returns the path to the folder with widget templates.
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/SpecialOffersBase/promoted_offer';
    }

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_OFFER  => new \XLite\Model\WidgetParam\TypeObject(
                'Special offer',
                null,
                false,
                '\XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer'
            ),
            static::PARAM_ROW    => new \XLite\Model\WidgetParam\TypeInt('Offer row index in the list', 0),
            static::PARAM_COLUMN => new \XLite\Model\WidgetParam\TypeInt('Offer column index in the list', 0),
        );
    }

    /**
     * Returns the special offer.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer
     */
    protected function getOffer()
    {
        return $this->getParam(self::PARAM_OFFER);
    }

    /**
     * Returns the offer identifier.
     * 
     * @return integer
     */
    protected function getOfferId()
    {
        return $this->getOffer()->getOfferId();
    }
    
    /**
     * Returns the row index.
     *
     * @return integer
     */
    protected function getRow()
    {
        return $this->getParam(self::PARAM_ROW);
    }

    /**
     * Returns the column index.
     *
     * @return integer
     */
    protected function getColumn()
    {
        return $this->getParam(self::PARAM_COLUMN);
    }

    /**
     * Check if the special offer has the image.
     *
     * @return boolean
     */
    protected function hasImage()
    {
        return !is_null($this->getImage());
    }

    /**
     * Get the special offer image.
     *
     * @return \XLite\Model\Image
     */
    protected function getImage()
    {
        return $this->getOffer()->getImage();
    }
    
    /**
     * Returns the maximum image width.
     * 
     * @return integer
     */
    protected function getImageWidth()
    {
        return 0;
    }
    
    /**
     * Returns the maximum image height.
     * 
     * @return integer
     */
    protected function getImageHeight()
    {
        return 0;
    }
    
    /**
     * Returns the text for the image's alt.
     * 
     * @return string
     */
    protected function getImageAlt()
    {
        return $this->getOffer()->getTitle();
    }
    
    /**
     * Returns the text that appears for the offer in lists of offers.
     * 
     * @return string
     */
    protected function getPromoText()
    {
        $offer = $this->getOffer();
        
        return $offer->getShortPromoText() ?: $offer->getTitle();
    }
}