<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Export form
 */
class Export extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'export';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'export';
    }

    /**
     * Return list of additional params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        if (\XLite\Core\Request::getInstance()->exportReturnURL) {
            $params[\XLite\Controller\AController::RETURN_URL] = \XLite\Core\Request::getInstance()->exportReturnURL;
        }

        return $params;
    }
}
