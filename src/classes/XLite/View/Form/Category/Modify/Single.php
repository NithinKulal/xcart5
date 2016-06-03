<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Category\Modify;

/**
 * Single
 */
class Single extends \XLite\View\Form\Category\Modify\AModify
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'category';
    }

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
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['category_id'] = $this->getCategoryId();
        $list['parent_id']   = $this->getParentCategoryId();

        return $list;
    }

    /**
     * Get validator
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();
        $this->setDataValidators($validator->addPair('postedData', new \XLite\Core\Validator\HashArray()));

        return $validator;
    }

    /**
     * Set validators pairs for products data
     *
     * @param mixed $data Data
     *
     * @return null
     */
    protected function setDataValidators($data)
    {
        $data->addPair('name', new \XLite\Core\Validator\TypeString(true), null, 'Category name');
        $data->addPair('show_title', new \XLite\Core\Validator\Enum\Boolean(), null, 'Category title');
        $data->addPair('description', new \XLite\Core\Validator\TypeString(), null, 'Description');
        $data->addPair('enabled', new \XLite\Core\Validator\Enum\Boolean(), null, 'Availability');
        $data->addPair('metaTitle', new \XLite\Core\Validator\TypeString(), null, 'Meta title');
        $data->addPair('metTags', new \XLite\Core\Validator\TypeString(), null, 'Meta keywords');
        $data->addPair('metaDesc', new \XLite\Core\Validator\TypeString(), null, 'Meta description');

        $data->addPair(
            'membership_ids',
            new \XLite\Core\Validator\PlainArray(),
            \XLite\Core\Validator\Pair\APair::SOFT,
            'Membership'
        )->setValidator(new \XLite\Core\Validator\TypeInteger());

        $data->addPair(
            'cleanURL',
            new \XLite\Core\Validator\String\CleanURL(false, null, 'XLite\Model\Category', $this->getCategoryId()),
            null,
            'Clean URL'
        );
    }
}
