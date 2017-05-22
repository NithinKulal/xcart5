<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\View\Product;

/**
 * Customer attachments input on product page
 *
 * @ListChild (list="product.details.page.info", weight="45")
 * @ListChild (list="product.details.quicklook.info", weight="65")
 */
class CustomerAttachmentsInput extends \XLite\View\AView
{
    /**
     * Product model
     *
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/product/details/customer-attachment-input.twig';
    }

    /**
     * Get product model
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if (!isset($this->product)) {
            $productId = \XLite\Core\Request::getInstance()->product_id;
            $this->product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($productId);
        }

        return $this->product;
    }

    /**
     * Check if attachments available for this product
     *
     * @return boolean
     */
    protected function isAttachmentsAvailable()
    {
        return $this->getProduct()->getIsCustomerAttachmentsAvailable();
    }

    /**
     * Get class for input tag
     *
     * @return string
     */
    protected function getInputClassString()
    {
        $classString = '';

        if ($this->getProduct()->isCustomerAttachmentsMandatory()) {
            $classString = 'validate[required]';
        }

        return $classString;
    }

    /**
     * Get accept attribute string
     *
     * @return string
     */
    protected function getAcceptString()
    {
        $extensions = \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::getAllowedExtensions();

        $acceptString = '';
        foreach ($extensions as $ext) {
            $acceptString .= '.' . $ext . ',';
        }
        $acceptString = trim($acceptString, ',');

        return $acceptString;
    }

    /**
     * Get warning message if not all files have been attached
     *
     * @return string
     */
    protected function getAttachmentWarningMessage()
    {
        return static::t('Some files haven`t been attached');
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->isAttachmentsAvailable()) {
            $list[] = 'modules/XC/CustomerAttachments/product/details/style.css';
        }

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isAttachmentsAvailable()) {
            $list[] = 'modules/XC/CustomerAttachments/product/details/customer-attachments.js';
        }

        return $list;
    }
}
