<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Product SKU
 */
class SKU extends \XLite\Core\Validator\AValidator
{
    /**
     * Product Id (saved)
     *
     * @var integer
     */
    protected $productId;

    /**
     * Constructor
     *
     * @param integer $productId Product identificator OPTIONAL
     *
     * @return void
     */
    public function __construct($productId = null)
    {
        parent::__construct();

        if (isset($productId)) {
            $this->productId = intval($productId);
        }
    }

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function validate($data)
    {
        if (!\XLite\Core\Converter::isEmptyString($data)) {
            $entity = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($this->sanitize($data));

            // DO NOT use "!==" here
            if ($entity && (empty($this->productId) || $entity->getProductId() != $this->productId)) {
                $this->throwSKUError();
            }
        }
    }

    /**
     * Sanitize
     *
     * @param mixed $data Data
     *
     * @return string
     */
    public function sanitize($data)
    {
        return substr($data, 0, \XLite\Core\Database::getRepo('XLite\Model\Product')->getFieldInfo('sku', 'length'));
    }

    /**
     * Wrapper
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    protected function throwSKUError()
    {
        throw $this->throwError('SKU must be unique');
    }
}
