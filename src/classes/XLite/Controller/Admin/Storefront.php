<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Close storefront action controller
 */
class Storefront extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Close storefront
     *
     * @return void
     */
    protected function doActionClose()
    {
        \XLite\Core\Auth::getInstance()->closeStorefront();
        $this->fireEvent();
    }

    /**
     * Open storefront
     *
     * @return void
     */
    protected function doActionOpen()
    {
        \XLite\Core\Auth::getInstance()->openStorefront();
        $this->fireEvent();
    }

    /**
     * Fire event 
     * 
     * @return void
     */
    protected function fireEvent()
    {
        \XLite\Core\Event::switchStorefront(
            array(
                'opened' => !\XLite\Core\Auth::getInstance()->isClosedStorefront(),
                'link'   => $this->buildURL(
                    'storefront',
                    '',
                    array(
                        'action'    => (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'open' : 'close'),
                    )
                ),
                'privatelink' => $this->getAccessibleShopURL(false),
            )
        );

        if ($this->isAJAX()) {
            $this->silent = true;
            $this->setSuppressOutput(true);
        }
    }
}
