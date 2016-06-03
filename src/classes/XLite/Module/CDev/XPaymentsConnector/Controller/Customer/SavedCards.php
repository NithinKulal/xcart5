<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
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
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Saved credit cards 
 *
 */
class SavedCards extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Saved credit cards');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Saved credit cards');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode('My account');
    }

    /**
     * Is zero-auth (card setup) allowed
     *
     * @return bool
     */
    public function allowZeroAuth()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->allowZeroAuth();
    }

    /**
     * Template for the Remove button (or something instead of it)
     *
     * @param int $cardId Card ID
     *
     * @return string
     */
    public function getRemoveTemplate($cardId)
    {
        return 'modules/CDev/XPaymentsConnector/account/saved_cards.table.remove.tpl';
    }

    /**
     * Get list of addresses
     *
     * @return array
     */
    public function getAddressList()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressList($this->getProfile());
    }

    /**
     * Get list of addresses
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     *
     * @return bool
     */
    public function isSingleAddress()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->isSingleAddress($this->getProfile());;
    }

    /**
     * Get string linne for the single address
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     *
     * @return string
     */
    public function getSingleAddress(\XLite\Model\Profile $profile)
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getSingleAddress($this->getProfile());
    }

    /**
     * Update default credit card 
     *
     * @return void
     */
    protected function doActionUpdateDefaultCard()
    {
        $profile = $this->getProfile();

        $cardId = \XLite\Core\Request::getInstance()->default_card_id;    

        $addresses = \XLite\Core\Request::getInstance()->address_id;

        if (
            $profile
            && $profile->isCardIdValid($cardId)
            && \XLite\Core\Auth::getInstance()->isLogged()
        ) {
            $this->getProfile()->setDefaultCardId($cardId);

            if ($addresses && is_array($addresses)) {

                // Get list of Address IDs associated with profile
                $profileAddressIds = array_keys(
                    \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressList($profile)
                );

                foreach ($addresses as $cardId => $addressId) {

                    // Validate Address ID and card ID
                    if (
                        in_array($addressId, $profileAddressIds)
                        && $profile->isCardIdValid($cardId)
                    ) {
                        $address = \XLite\Core\Database::getRepo('\XLite\Model\Address')->find($addressId);
                        $card = \XLite\Core\Database::getRepo('XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData')->find($cardId);

                        $card->setBillingAddress($address);
                    }
                }
            }

            \XLite\Core\Database::getEM()->flush();
        }    
    }

    /**
     * Remove credit card
     *
     * @return void
     */
    protected function doActionRemove()
    {
        $profile = $this->getProfile();

        $cardId = \XLite\Core\Request::getInstance()->card_id;

        if (
            $profile
            && $profile->isCardIdValid($cardId)
            && \XLite\Core\Auth::getInstance()->isLogged()
        ) {
            $this->getProfile()->denyRecharge($cardId);
            \XLite\Core\Database::getEM()->flush();
        }

        $this->setHardRedirect();
        $this->setReturnURL($this->buildURL('saved_cards'));
        $this->doRedirect();
    }

}
