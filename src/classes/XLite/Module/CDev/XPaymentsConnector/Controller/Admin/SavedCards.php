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
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Admin;

/**
 * Saved credit cards
 */
class SavedCards extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Edit profile');
    }

    /**
     * Get saved cards
     *
     * @return array
     */
    public function getSavedCards()
    {
        return $this->getCustomerProfile()
            ? $this->getCustomerProfile()->getSavedCards()
            : null;
    }

    /**
     * Get customer profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(
            intval(\XLite\Core\Request::getInstance()->profile_id)
        );
    }

    /**
     * Get customer profile Id
     *
     * @return integer
     */
    public function getCustomerProfileId()
    {
        return intval(\XLite\Core\Request::getInstance()->profile_id);
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
     * Update default credit card, card addresses and remove cards
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $profile = $this->getCustomerProfile();

        $cardId = \XLite\Core\Request::getInstance()->default_card_id;
        $delete = \XLite\Core\Request::getInstance()->delete;

        $addresses = \XLite\Core\Request::getInstance()->address_id;

        if ($profile) {
            // Mark card as default
            if ($profile->isCardIdValid($cardId)) {
                $profile->setDefaultCardId($cardId);
            }

            // Remove credit card
            // I.e deny recharges for it
            if ($delete && is_array($delete)) {
                foreach ($delete as $cardId => $v) {
                    if ($profile->isCardIdValid($cardId)) {
                        $profile->denyRecharge($cardId);
                    }
                }
            }

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
}
