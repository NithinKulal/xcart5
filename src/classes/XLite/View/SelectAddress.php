<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Pick address from address book
 *
 * @ListChild (list="center")
 */
class SelectAddress extends \XLite\View\Dialog
{
    /**
     * Columns number
     *
     * @var integer
     */
    protected $columnsNumber = 2;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'select_address';

        return $result;
    }

    /**
     * Returns widget stylesheet files
     * 
     * @return array
     */
    public function getCSSFiles()
    {
        return array(
            'select_address/style.css'
        );
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'select_address/controller.js';

        return $list;
    }

    /**
     * Check - specified address is selected or not
     *
     * @param \XLite\Model\Address $address Address
     *
     * @return boolean
     */
    public function isSelectedAddress(\XLite\Model\Address $address)
    {
        $atype = \XLite\Core\Request::getInstance()->atype;

        return ($address->getIsShipping() && \XLite\Model\Address::SHIPPING == $atype)
            || ($address->getIsBilling() && \XLite\Model\Address::BILLING == $atype);
    }

    /**
     * Get addresses list
     *
     * @return array
     */
    public function getAddresses()
    {
        $list = $this->getCart()->getProfile()->getAddresses()->toArray();
        foreach ($list as $i => $address) {
            if ($address->getIsWork()) {
                unset($list[$i]);
            }
        }

        return array_values($list);
    }

    /**
     * Check - profile has addresses list or not
     *
     * @return boolean
     */
    public function hasAddresses()
    {
        return 0 < count($this->getAddresses());
    }

    /**
     * Get list item class name
     *
     * @param \XLite\Model\Address $address Address
     * @param integer              $i       Address position in addresses list
     *
     * @return string
     */
    public function getItemClassName(\XLite\Model\Address $address, $i)
    {
        $class = 'address-' . $address->getAddressId();

        if ($this->isSelectedAddress($address)) {
            $class .= ' selected';
        }

        if (0 == $i % $this->columnsNumber) {
            $class .= ' last';
        }

        return $class;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'select_address';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Check - address is shipping address or not
     * 
     * @param \XLite\Model\Address $address Address
     *  
     * @return boolean
     */
    protected function isShipping(\XLite\Model\Address $address)
    {
        return $address->getIsShipping();
    }

    /**
     * Check - address is billing address or not
     *
     * @param \XLite\Model\Address $address Address
     *
     * @return boolean
     */
    protected function isBilling(\XLite\Model\Address $address)
    {
        return $address->getIsBilling();
    }
}
