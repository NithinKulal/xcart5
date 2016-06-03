<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View;

/**
 * Order shipments widget
 */
class Settings extends \XLite\View\AView
{
    /**
     * Token TTL is 10 minutes
     */
    const TOKEN_TTL = 600;

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CanadaPost/settings/settings.twig';
    }

    // {{{ Merchant registration wizard functions
    
    /**
     * Check - is merchant registration wizard enabled or not 
     *
     * @return boolean
     */
    public function isWizardEnabled()
    {
        return \XLite\Core\Config::getInstance()->XC->CanadaPost->wizard_enabled;
    }
    
    /**
     * Get Canada Post merchant registration token
     *
     * @return string
     */
    public function getTokenId()
    {
        $token = null;

        if (!$this->isTokenValid()) {
            
            // Send request to Canada Post server to retrieve token
            $data = \XLite\Module\XC\CanadaPost\Core\Service\Platforms::getInstance()
                ->callGetMerchantRegistrationToken();

            if (isset($data->token)) {

                $token = $data->token->tokenId;
            
                \XLite\Core\Session::getInstance()->capost_token_id = $token;
                \XLite\Core\Session::getInstance()->capost_token_ts = \XLite\Core\Converter::time();
                \XLite\Core\Session::getInstance()->capost_developer_mode = \XLite\Core\Config::getInstance()->XC->CanadaPost->developer_mode;

            } else {

                // TODO: print real error message returned by the request
                \XLite\Core\TopMessage::getInstance()->addError('Failure to get token ID.');
            }

        } else {
            
            // Get token from the session
            $token = \XLite\Core\Session::getInstance()->capost_token_id;
        }

        return $token;
    }

    /**
     * Returns true if token initialized and is not expired
     *
     * @return boolean
     */
    protected function isTokenValid()
    {
        return (
            !empty(\XLite\Core\Session::getInstance()->capost_token_id)
            && static::TOKEN_TTL > \XLite\Core\Converter::time() - \XLite\Core\Session::getInstance()->capost_token_ts
            && \XLite\Core\Session::getInstance()->capost_developer_mode == \XLite\Core\Config::getInstance()->XC->CanadaPost->developer_mode
        );
    }
    
    /**
     * Get Canada Post merchant registration URL
     *
     * @return string
     */
    public function getMerchantRegUrl()
    {
        return \XLite\Module\XC\CanadaPost\Core\API::getInstance()->getMerchantRegUrl();
    }
    
    /**
     * Get X-Cart platform ID
     *
     * @return string
     */
    public function getPlatformId()
    {
        return \XLite\Module\XC\CanadaPost\Core\API::getInstance()->getPlatformId();
    }
    
    /**
     * Get merchant registration wizard return URL
     *
     * @return string
     */
    public function getWizardReturnUrl()
    {
        return \XLite\Core\Converter::buildFullURL();
    }

    // }}}
}
