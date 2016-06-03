<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Module;

/**
 * Activate key
 */
class ActivateKey extends \XLite\View\Form\Module\AModule
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return $this->isPopupTarget()
            ? 'activate_key_popup'
            : 'activate_key';
    }

    /**
     * The 'trial_notice' and 'activate_key' targets are used
     * when form goes in the popup window
     *
     * @return boolean
     */
    protected function isPopupTarget()
    {
        return in_array(
            \XLite\Core\Request::getInstance()->target,
            array(
                'trial_notice',
                'activate_key',
            )
        );
    }

    /**
     * Check and (if needed) set the return URL parameter
     *
     * @param array &$params Form params
     *
     * @return void
     */
    protected function setReturnURLParam(array &$params)
    {
        parent::setReturnURLParam($params);

        $params[\XLite\Controller\AController::RETURN_URL] = \XLite\Core\Request::getInstance()->returnUrl;
    }
}
