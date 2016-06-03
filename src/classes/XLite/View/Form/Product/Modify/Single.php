<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Modify;

/**
 * Details
 */
class Single extends \XLite\View\Form\Product\Modify\Base\Single
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify';
    }

    /**
     * Ability to add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }

    /**
     * Get validator
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();

        $data = $validator->addPair('postedData', new \XLite\Core\Validator\HashArray());
        $this->setDataValidators($data);

        return $validator;
    }

    /**
     * Get product identificator from request
     *
     * @return integer Product identificator
     */
    protected function getProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id ?: \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Set validators pairs for products data
     *
     * @param mixed $data Data
     *
     * @return null
     */
    protected function setDataValidators(&$data)
    {
        $data->addPair('sku', new \XLite\Core\Validator\SKU($this->getProductId()), null, 'SKU');
        $data->addPair('name', new \XLite\Core\Validator\TypeString(true), null, 'Product Name');
        $data->addPair('category_ids', new \XLite\Core\Validator\PlainArray(), \XLite\Core\Validator\Pair\APair::SOFT, 'Category')
            ->setValidator(new \XLite\Core\Validator\TypeInteger());
        $data->addPair('price', new \XLite\Core\Validator\TypeFloat(), null, 'Price')->setRange(0);
        $data->addPair('weight', new \XLite\Core\Validator\TypeFloat(), null, 'Weight')->setRange(0);
        $data->addPair('shippable', new \XLite\Core\Validator\Enum\Boolean(), null, 'Shippable');
        $data->addPair('enabled', new \XLite\Core\Validator\Enum\Boolean(), null, 'Available for sale');
        $data->addPair('metaTitle', new \XLite\Core\Validator\TypeString(), null, 'Product page title');
        $data->addPair('briefDescription', new \XLite\Core\Validator\TypeString(), null, 'Brief description');
        $data->addPair('description', new \XLite\Core\Validator\TypeString(), null, 'Full description');
        $data->addPair('metaTags', new \XLite\Core\Validator\TypeString(), null, 'Meta keywords');
        $data->addPair('metaDesc', new \XLite\Core\Validator\TypeString(), null, 'Meta description');

        $data->addPair(
            'cleanURL',
            new \XLite\Core\Validator\String\CleanURL(false, null, 'XLite\Model\Product', $this->getProductId()),
            null,
            'Clean URL'
        );

        $data->addPair(
            'memberships',
            new \XLite\Core\Validator\PlainArray(),
            \XLite\Core\Validator\Pair\APair::SOFT,
            'Membership'
        )->setValidator(new \XLite\Core\Validator\TypeInteger());

        $data->addPair(
            'category_ids',
            new \XLite\Core\Validator\PlainArray(),
            \XLite\Core\Validator\Pair\APair::SOFT,
            'Category'
        )->setValidator(new \XLite\Core\Validator\TypeInteger());
    }
}
