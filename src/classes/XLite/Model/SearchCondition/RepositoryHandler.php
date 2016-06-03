<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition;

/**
 * RepositoryHandler
 */
class RepositoryHandler extends \XLite\Model\SearchCondition\ASearchCondition implements \XLite\Model\SearchCondition\IRepositoryHandlerCarrier
{
    /**
     * Repository handler function name without 'prepareCnd' part
     *
     * @var string
     */
    protected $repoHandlerName;

    /**
     * @param  $repoHandlerName Repository handler name
     */
    public function __construct($repoHandlerName)
    {
        $this->repoHandlerName = $repoHandlerName;
    }

    /**
     * Get repository handler function name without 'prepareCnd' part
     *
     * @return string
     */
    public function getHandlerName()
    {
        return $this->repoHandlerName;
    }
    /**
     * Get search condition service name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getHandlerName();
    }
}
