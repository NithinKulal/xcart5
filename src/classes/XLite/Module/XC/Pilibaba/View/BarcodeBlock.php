<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View;

/**
 * Order tracking number items list
 *
 * @ListChild (list="order.actions", weight="2000", zone="admin")
 */
class BarcodeBlock extends \XLite\View\AView
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Pilibaba/barcode_block/style.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Pilibaba/barcode_block/barcodeLazyLoader.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Pilibaba/barcode_block/body.twig';
    }

    /**
     * Get processor
     *
     * @return \XLite\Model\Payment\Base\Processor|null
     */
    protected function getProcessor()
    {
        $hasProcessor = $this->getOrder()
            && $this->getOrder()->getPaymentMethod()
            && $this->getOrder()->getPaymentMethod()->getProcessor();

        return $hasProcessor
            ? $this->getOrder()->getPaymentMethod()->getProcessor()
            : null;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getProcessor() instanceof \XLite\Module\XC\Pilibaba\Model\Payment\Processor\Pilibaba
            && $this->getBarcodeUrl();
    }

    // {{{ Template methods

    /**
     * Get image url for barcode
     *
     * @return string
     */
    public function getBarcodeUrl()
    {
        return $this->getProcessor() && $this->getOrder()
            ? $this->getProcessor()->getBarcodeUrl($this->getOrder()->getPaymentTransactionId())
            : null;
    }

    // }}}
}
