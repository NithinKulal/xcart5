<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Enum;

/**
 * AEnum 
 */
abstract class AEnum extends \XLite\Core\Validator\Scalar
{
    /**
     * Items list 
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $list = array();

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    public function validate($data)
    {
        if (!in_array($data, $this->list)) {
            throw $this->throwError('Unallowed value');
        }
    }
}
