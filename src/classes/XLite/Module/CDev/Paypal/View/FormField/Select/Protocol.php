<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Select;

/**
 * Protocol (http|https) selector
 */
class Protocol extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/form_field/';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'select_protocol.twig';
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'http'  => 'http',
            'https' => 'https',
        );
    }

    /**
     * Get sign in return URL
     *
     * @param boolean $withProto If true - return URL with protocol (http|https), else - without protocol
     *
     * @return string
     */
    protected function getSignInReturnURL($withProto = true)
    {
        $api = new \XLite\Module\CDev\Paypal\Core\Login();

        $flag = $withProto
            ? null   // Get current value of Return URL
            : false; // Get Return URL with protocol 'http'

        $returnURL = $api->getSignInReturnURL($flag);

        if (!$withProto) {
            $returnURL = preg_replace('/^http/', '', $returnURL);
        }

        return $returnURL;
    }

    /**
     * Return true if it is allowed to change protocol of return URL
     *
     * @return boolean
     */
    protected function isEditableReturnURLProtocol()
    {
        return true;
    }
}
