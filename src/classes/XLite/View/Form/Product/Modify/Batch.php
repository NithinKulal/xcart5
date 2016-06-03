<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Modify;

/**
 * List
 */
class Batch extends \XLite\View\Form\Product\Modify\AModify
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product_list';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Get validator
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();

        $validator->addPair(
            'postedData',
            new \XLite\Core\Validator\PlainArray()
        )->setValidator($this->getDataValidator());

        return $validator;
    }

    /**
     * Get validator products data
     *
     * @return null
     */
    protected function getDataValidator()
    {
        $data = new \XLite\Core\Validator\HashArray();
        $data->addPair('price', new \XLite\Core\Validator\TypeFloat(), null, 'Price')->setRange(0);

        return $data;
    }
}
