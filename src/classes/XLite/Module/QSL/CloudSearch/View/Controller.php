<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View;

use XLite\Core\Config;

/**
 * Controller widget extension
 */
class Controller extends \XLite\View\Controller implements \XLite\Base\IDecorator
{
    /**
     * Return common data to send to JS
     *
     * @return array
     */
    protected function getCommonJSData()
    {
        $data = parent::getCommonJSData();

        if (!\XLite::isAdminZone()) {
            $data += $this->getCloudSearchInitData();
        }

        return $data;
    }

    /**
     * Get CloudSearch initialization data to pass to the JS code
     *
     * @return array
     */
    protected function getCloudSearchInitData()
    {
        $lng = array(
            'lbl_showing_results_for'   => static::t('cs_showing_results_for'),
            'lbl_see_details'           => static::t('cs_see_details'),
            'lbl_see_more_results_for'  => static::t('cs_see_more_results_for'),
            'lbl_suggestions'           => static::t('cs_suggestions'),
            'lbl_products'              => static::t('cs_products'),
            'lbl_categories'            => static::t('cs_categories'),
            'lbl_pages'                 => static::t('cs_pages'),
            'lbl_did_you_mean'          => static::t('cs_did_you_mean'),
        );

        $data = array(
            'cloudSearch'       => array(
                'apiKey'        => Config::getInstance()->QSL->CloudSearch->api_key,
                'priceTemplate' => static::formatPrice(0),
                'selector'      => 'input[name="substring"]',
                'lng'           => $lng,
            )
        );

        return $data;
    }
}
