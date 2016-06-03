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
 * @copyright Copyright (c) 2011-2014 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
            'lbl_showing_results_for' => $this->t('cs_showing_results_for'),
            'lbl_see_details' => $this->t('cs_see_details'),
            'lbl_see_more_results_for' => $this->t('cs_see_more_results_for'),
            'lbl_suggestions' => $this->t('cs_suggestions'),
            'lbl_products' => $this->t('cs_products'),
            'lbl_categories' => $this->t('cs_categories'),
            'lbl_pages' => $this->t('cs_pages'),
            'lbl_did_you_mean' => $this->t('cs_did_you_mean'),
        );

        $data = array(
            'cloudSearch' => array(
                'apiKey' => Config::getInstance()->QSL->CloudSearch->api_key,
                'priceTemplate' => $this->formatPrice(0),
                'selector' => 'input[name="substring"]',
                'lng' => $lng,
            )
        );

        return $data;
    }
}
