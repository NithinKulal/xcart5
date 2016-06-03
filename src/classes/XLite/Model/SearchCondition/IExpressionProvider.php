<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition;

/**
 * IExpressionProvider
 */
interface IExpressionProvider
{
    /**
     * Expression type getter
     *
     * @return string
     */
    public function getExpressionType();

    /**
     * Get DQL expression
     *
     * @param  string $alias Root alias
     *
     * @return \Doctrine\ORM\Query\Expr|string
     */
    public function getExpression($alias);

    /**
     * Get joins list
     *
     * @param  string $alias Root alias
     *
     * @return array[\Doctrine\ORM\Query\Expr\Join]
     */
    public function getJoins($alias);

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

    /**
     * Get parameters list with names and values
     *
     * @return array Keys are names, values are values
     */
    public function getParameters();
}
