<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\Validator;

/**
 * Variant SKU
 */
class VariantSKU extends \XLite\Core\Validator\AValidator
{
    /**
     * Id (saved)
     *
     * @var integer
     */
    protected $id;

    /**
     * Constructor
     *
     * @param integer $id Identificator OPTIONAL
     *
     * @return void
     */
    public function __construct($id = null)
    {
        parent::__construct();

        if (isset($id)) {
            $this->id = intval($id);
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

            $data = $this->sanitize($data);

            $entity = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->findOneBySku($data);

            // DO NOT use "!==" here
            if (
                ($entity && (empty($this->id) || $entity->getId() != $this->id))
                || \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($data)
            ) {
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
        return substr($data, 0, \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->getFieldInfo('sku', 'length'));
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
