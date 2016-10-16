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
 * Form field to choose the special offer type.
 */
class OfferType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Set value.
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $options = $this->getDefaultOptions();
        if (!isset($options[$value])) {
            $value = key($options);
        }

        parent::setValue($value);
    }


    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = array();
        foreach ($this->getRepo()->findActiveOfferTypes() as $type) {
            $options[$type->getTypeId()] = $type->getName();
        }
        
        return $options;
    }
    
    /**
     * Returns the repository class used to retrieve offer types.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\Repo\OfferType
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\OfferType');
    }

}