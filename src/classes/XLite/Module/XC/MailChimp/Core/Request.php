<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

/**
 * Request
 */
abstract class Request extends \XLite\Core\Request implements \XLite\Base\IDecorator
{
    const MAILCHIMP_CAMPAIGN_ID     = 'mc_cid';
    const MAILCHIMP_USER_ID         = 'mc_eid';
    const MAILCHIMP_TRACKING_CODE   = 'mc_tc';

    /**
     * Map request data
     *
     * @param array $data Custom data OPTIONAL
     *
     * @return void
     */
    public function mapRequest(array $data = array())
    {
        if (
            isset(\XLite\Core\Config::getInstance()->XC)
            && isset(\XLite\Core\Config::getInstance()->XC->MailChimp)
            && isset(\XLite\Core\Config::getInstance()->XC->MailChimp->analytics360enabled)
            && \XLite\Core\Config::getInstance()->XC->MailChimp->analytics360enabled
        ) {
            $this->processECommerce360Data();
        }

        parent::mapRequest($data);
    }

    /**
     * Process ECommerce360 input data
     *
     * @return void
     */
    protected function processECommerce360Data()
    {
        $data = $this->getGetData(true);

        $this->tryToMapParameter(self::MAILCHIMP_CAMPAIGN_ID, $data);
        $this->tryToMapParameter(self::MAILCHIMP_USER_ID, $data);
        $this->tryToMapParameter(self::MAILCHIMP_CAMPAIGN_ID, $data);
    }

    /**
     * @param $name
     * @param $data
     */
    protected function tryToMapParameter($name, $data)
    {
        if (
            isset($data[$name])
            && !empty($data[$name])
        ) {
            setcookie(
                $name,
                $data[$name],
                \XLite\Core\Converter::getInstance()->time() + 2592000
            );
        }
    }
}
