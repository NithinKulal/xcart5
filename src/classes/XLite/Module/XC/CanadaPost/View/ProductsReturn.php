<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View;

/**
 * Products return page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ProductsReturn extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('capost_return'));
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CanadaPost/products_return/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CanadaPost/products_return/body.twig';
    }

    // {{{ Helper methods

    /**
     * Check - has profile separate modification page or not
     *
     * @return boolean
     */
    protected function hasProfilePage()
    {
        return $this->getOrder()->getOrigProfile()
            && $this->getOrder()->getOrigProfile()->getProfileId() != $this->getOrder()->getProfile()->getProfileId();
    }

    /**
     * Get profile name
     *
     * @return string
     */
    protected function getProfileName()
    {
        $profile = $this->getOrder()->getProfile();

        $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        if (!$address) {
            $profile->getAddresses()->first();
        }

        return $address ? $address->getName() : $profile->getLogin();
    }

    /**
     * Get profile URL
     *
     * @return string
     */
    protected function getProfileURL()
    {
        return \XLite\Core\Converter::buildURL(
            'profile',
            '',
            array('profile_id' => $this->getOrder()->getOrigProfile()->getProfileId())
        );
    }

    /**
     * Get products return formatted creation date
     *
     * @return string
     */
    protected function getCreateDate()
    {
        return $this->formatTime($this->getProductsReturn()->getDate());
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership
     */
    protected function getMembership()
    {
        return $this->getOrder()->getOrigProfile()
            ? $this->getOrder()->getOrigProfile()->getMembership()
            : null;
    }

    /**
     * Get cart items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getOrder()
            ? $this->getOrder()->getItems()
            : [];
    }

    /**
     * Check - customer notes block is visible or not
     *
     * @return boolean
     */
    protected function isCustomerNotesVisible()
    {
        return (bool) $this->getProductsReturn()->getNotes();
    }

    // }}}
}
