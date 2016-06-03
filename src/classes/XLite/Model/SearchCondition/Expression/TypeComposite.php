<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition\Expression;

/**
 * TypeComposite
 */
class TypeComposite extends \XLite\Model\SearchCondition\ASearchCondition implements \XLite\Model\SearchCondition\IExpressionProvider
{
    protected $innerConditions;
    protected $strategy;
    protected $expressionType;

    public function __construct(array $innerConditions, $strategy = 'OR', $expressionType = 'where')
    {
        $this->innerConditions  = $innerConditions;
        $this->strategy         = $strategy;
        $this->expressionType   = $expressionType;

        foreach ($this->innerConditions as $condition) {
            $suffix = str_replace('.', '_', $this->getName()) . '_' . $condition->getParameterNameSuffix();
            $condition->setParameterNameSuffix($suffix);
        }
    }

    public function getExpressionType()
    {
        return $this->expressionType;
    }

    public function getInitialExpression()
    {
        $expr = null;
        switch (strtolower($this->strategy)) {
            case 'and':
                $expr = new \Doctrine\ORM\Query\Expr\Andx();
                break;

            case 'or':
                $expr = new \Doctrine\ORM\Query\Expr\Orx();
                break;

            default:
                $expr = new \Doctrine\ORM\Query\Expr\Andx();
                break;
        }

        return $expr;
    }

    public function getExpression($alias)
    {
        $expr = $this->getInitialExpression();

        foreach ($this->innerConditions as $condition) {
            $expr->add($condition->getExpression($alias));
        }

        return $expr;
    }

    public function getName()
    {
        $innerNames = array_reduce(
            $this->innerConditions,
            function($carry, $condition){
                $carry[] = $condition->getName();
                return $carry;
            },
            array()
        );

        return 'CompositeExpression_' . join('_', $innerNames);
    }

    public function getParameters()
    {
        $parameters = array();

        foreach ($this->innerConditions as $condition) {
            $parameters = array_merge(
                $parameters,
                $condition->getParameters()
            );
        }

        return $parameters;
    }

    public function setValue($value)
    {
        parent::setValue($value);

        if (!is_array($value)) {
            $value = array_fill(0, count($this->innerConditions), $value);
        }

        for ($i=0; $i < count($this->innerConditions) ; $i++) {
            $this->innerConditions[$i]->setValue($value[$i]);
        }
    }

    public function getJoins($alias)
    {
        $joins = array();

        foreach ($this->innerConditions as $condition) {
            $joins = array_merge(
                $joins,
                $condition->getJoins($alias)
            );
        }

        return $joins;
    }
}
