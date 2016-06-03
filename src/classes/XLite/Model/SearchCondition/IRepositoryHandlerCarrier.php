<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition;

/**
 * IRepositoryHandlerCarrier
 */
interface IRepositoryHandlerCarrier
{
    /**
     * Get repository handler function name without 'prepareCnd' part
     *
     * @return string
     */
    public function getHandlerName();

    /**
     * Expression value getter
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get search condition service name
     *
     * @return string
     */
    public function getName();
}
