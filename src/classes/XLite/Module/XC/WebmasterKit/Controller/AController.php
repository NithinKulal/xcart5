<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Controller;

use XLite\Core\Config;
use XLite\Module\XC\WebmasterKit\Logic\DebugBar;
use XLite\Module\XC\WebmasterKit\View\DebugBar as DebugBarView;


/**
 * AController
 */
abstract class AController extends \XLite\Controller\AController implements \XLite\Base\IDecorator
{
    /**
     * Handles the request.
     * Create installation timestamp if empty (at first software launch)
     *
     * @return string
     */
    public function handleRequest()
    {
        // Initialize DebugBar and start collecting data.
        if (Config::getInstance()->XC->WebmasterKit->debugBarEnabled) {
            DebugBar::getInstance();
        }

        parent::handleRequest();
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        if (Config::getInstance()->XC->WebmasterKit->debugBarEnabled) {
            ob_start();

            parent::processRequest();

            $buffer = ob_get_clean();

            echo str_replace(DebugBarView::CONTENT_PLACEHOLDER, DebugBar::getInstance()->getBody(), $buffer);
        } else {
            parent::processRequest();
        }
    }
}
