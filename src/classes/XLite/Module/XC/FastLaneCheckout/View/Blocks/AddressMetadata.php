<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 * 
 * @ListChild (list="checkout_fastlane", weight="99999", zone="customer")
 */
class AddressMetadata extends \XLite\View\ASingleView
{
    /**
     * Check view visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/address/metadata.twig';
    }

    public function buildCountryNamesObject()
    {
        $dto = \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabledDTO();

        return json_encode($dto);
    }

    public function buildStateNamesObject()
    {
        $dto = \XLite\Core\Database::getRepo('XLite\Model\State')->findAllStatesDTO();

        return json_encode($dto);
    }
}