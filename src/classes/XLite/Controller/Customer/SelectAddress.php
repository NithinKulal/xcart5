<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Select address from address book
 */
class SelectAddress extends \XLite\Controller\Customer\Cart
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'atype');


    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Pick address from address book');
    }

    /**
     * Get current aAddress id
     *
     * @return integer|void
     */
    public function getCurrentAddressId()
    {
        $address = null;

        if ($this->getCart()->getProfile()) {
            $address = \XLite\Model\Address::SHIPPING == \XLite\Core\Request::getInstance()->atype
                ? $this->getCart()->getProfile()->getShippingAddress()
                : $this->getCart()->getProfile()->getBillingAddress();
        }

        return $address ? $address->getAddressId() : null;
    }


    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Select address
     *
     * @return void
     */
    protected function doActionSelect()
    {
        $atype = \XLite\Core\Request::getInstance()->atype;
        $addressId = \XLite\Core\Request::getInstance()->addressId;
        $hasEmptyFields = \XLite\Core\Request::getInstance()->hasEmptyFields === 'true'
            ? true
            : false;

        if (\XLite\Model\Address::SHIPPING != $atype && \XLite\Model\Address::BILLING != $atype) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Address type has wrong value');

        } elseif (!$addressId) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Address is not selected');

        } else {
            $address = \XLite\Core\Database::getRepo('XLite\Model\Address')->find($addressId);

            if (!$address) {
                // Address not found
                $this->valid = false;
                \XLite\Core\TopMessage::addError('Address not found');

            } elseif (
                \XLite\Model\Address::SHIPPING == $atype
                && $this->getCart()->getProfile()->getShippingAddress()
                && $address->getAddressId() == $this->getCart()->getProfile()->getShippingAddress()->getAddressId()
            ) {
                if ($hasEmptyFields) {
                    \XLite\Core\Event::updateCart(
                        array(
                            'shippingAddressId' => $address->getAddressId(),
                        )
                    );
                }
                // This shipping address is already selected
                $this->silenceClose = true;

            } elseif (
                \XLite\Model\Address::BILLING == $atype
                && $this->getCart()->getProfile()->getBillingAddress()
                && $address->getAddressId() == $this->getCart()->getProfile()->getBillingAddress()->getAddressId()
            ) {

                if ($hasEmptyFields) {
                    \XLite\Core\Event::updateCart(
                        array(
                            'billingAddressId' => $address->getAddressId(),
                        )
                    );
                }
                // This billing address is already selected
                $this->silenceClose = true;

            } else {
                if (\XLite\Model\Address::SHIPPING == $atype) {
                    $old = $this->getCart()->getProfile()->getShippingAddress();
                    $andAsBilling = false;
                    if ($old) {
                        $old->setIsShipping(false);
                        $andAsBilling = $old->getIsBilling();
                        if ($old->getIsWork()) {
                            $this->getCart()->getProfile()->getAddresses()->removeElement($old);
                            \XLite\Core\Database::getEM()->remove($old);

                        } elseif ($andAsBilling) {
                            $old->setIsBilling(false);
                        }

                    } elseif (!$this->getCart()->getProfile()->getBillingAddress()) {
                        $andAsBilling = true;
                    }

                    $address->setIsShipping(true);
                    if ($andAsBilling) {
                        $address->setIsBilling($andAsBilling);
                    }

                } else {
                    $old = $this->getCart()->getProfile()->getBillingAddress();
                    $andAsShipping = false;
                    if ($old) {
                        $old->setIsBilling(false);
                        $andAsShipping = $old->getIsShipping();
                        if ($old->getIsWork()) {
                            $this->getCart()->getProfile()->getAddresses()->removeElement($old);
                            \XLite\Core\Database::getEM()->remove($old);

                        } elseif ($andAsShipping) {
                            $old->setIsShipping(false);
                        }

                    } elseif (!$this->getCart()->getProfile()->getShippingAddress()) {
                        $andAsShipping = true;
                    }

                    $address->setIsBilling(true);
                    if ($andAsShipping) {
                        $address->setIsShipping($andAsShipping);
                    }
                }

                \XLite\Core\Session::getInstance()->same_address = $this->getCart()->getProfile()->isEqualAddress();

                \XLite\Core\Event::selectCartAddress(
                    array(
                        'type'      => $atype,
                        'addressId' => $address->getAddressId(),
                        'same'      => $this->getCart()->getProfile()->isSameAddress(),
                    )
                );

                \XLite\Core\Database::getEM()->flush();

                $this->updateCart();

                $this->silenceClose = true;
            }
        }
    }

    /**
     * Controller marks the cart calculation.
     * On the checkout page we need cart recalculation
     *
     * @return boolean
     */
    protected function markCartCalculate()
    {
        return true;
    }

    /**
     * Get cart fingerprint exclude keys
     *
     * @return array
     */
    protected function getCartFingerprintExclude()
    {
        return array();
    }
}
