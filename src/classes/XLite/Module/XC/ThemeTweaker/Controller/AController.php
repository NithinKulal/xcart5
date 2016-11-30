<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller;

/**
 * Payment method
 */
abstract class AController extends \XLite\Controller\AController implements \XLite\Base\IDecorator
{
    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        parent::processRequest();

        if (!$this->suppressOutput
            && !$this->isAJAX()
        ) {
            $viewer = $this->getViewer();

            echo $viewer::getHtmlTree();
        }
    }
}
