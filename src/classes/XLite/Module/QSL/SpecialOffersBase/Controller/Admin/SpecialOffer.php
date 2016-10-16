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

namespace XLite\Module\QSL\SpecialOffersBase\Controller\Admin;

/**
 * Special offer controller
 */
class SpecialOffer extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'offer_id', 'type_id');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getSpecialOffer();
        
         return ($model && $model->getOfferId())
            ? $model->getName()
            : \XLite\Core\Translation::getInstance()->lbl('Special Offer');
    }

    /**
     * Returns the name of the view class that renders the page.
     * 
     * @return string
     */
    public function getPageWidgetClass()
    {
        return $this->getModelFormClass();
    }
    
    /**
     * Check if the view model class is available.
     * 
     * @return boolean
     */
    public function isOfferTypeEnabled()
    {
        return \XLite\Core\Operator::isClassExists($this->getPageWidgetClass());
    }
    
    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('special_offers'));
        }
    }

    /**
     * Get model form class name.
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        $type = $this->getOfferType();
        
        return $type ? $type->getViewModelClass() : '';
    }

    /**
     * Returns the model for the special offer being edited.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer
     */
    protected function getSpecialOffer()
    {
        $id = intval(\XLite\Core\Request::getInstance()->offer_id);
        
        return $id
            ? \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer')->find($id)
            : null;
   }
   
   /**
    * Returns the offer type model for the special offer being edited.
    * 
    * @return \XLite\Module\QSL\SpecialOffersBase\Model\OfferType
    */
   protected function getOfferType()
   {
       $model = $this->getSpecialOffer();
       
       return $model
           ? $model->getOfferType()
           : \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\OfferType')->find(
               intval(\XLite\Core\Request::getInstance()->type_id)
           );
   }
   

}