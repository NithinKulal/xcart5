<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition\Expression;

/**
 * Base common search condition
 */
abstract class Base extends \XLite\Model\SearchCondition\ASearchCondition implements \XLite\Model\SearchCondition\IExpressionProvider
{
    protected $propertyName;
    protected $expressionType;
    protected $parameterNameSuffix;

    /**
     * @param string    $propertyName      Property name
     * @param string    $expressionType    Expression type    OPTIONAL
     */
    public function __construct($propertyName, $expressionType = 'where')
    {
        $this->propertyName         = $propertyName;
        $this->expressionType       = $expressionType;
        $this->parameterNameSuffix  = $this->getDefaultParameterNameSuffix();
    }

    public static function create($propertyName, $value)
    {
        $condition = new static($propertyName);
        $condition->setValue($value);
        return $condition;
    }

    /**
     * Expression type getter
     *
     * @return string
     */
    public function getExpressionType()
    {
        return $this->expressionType;
    }

    /**
     * Get parameters list with names and values
     *
     * @return array Keys are parameters names, values are parameters values
     */
    public function getParameters()
    {
        return array(
            $this->getParameterName() => $this->preprocessValue($this->getValue()),
        );
    }

    /**
     * Parse property name and find there associations
     *
     * @return array
     */
    protected function getParsedPropertyName($alias)
    {
        $result = array();
        $parts = explode('.', $this->propertyName);
        $rootAlias = $alias;

        do {
            $nextJoinAlias = $parts[0];
            $joinAlias = $rootAlias . '_' . $nextJoinAlias;
            $result[] = [$rootAlias, $nextJoinAlias];
            $parts[0] = $joinAlias;
            $rootAlias = array_shift($parts);
        } while (count($parts) > 0);


        return $result;
    }

    protected function getFinalAliasName($alias)
    {
        $parsed = $this->getParsedPropertyName($alias);
        list($rootAlias, $name) = array_pop($parsed);

        if ($rootAlias === $alias . '_translations') {
            $rootAlias = 'translations';
        }

        return $rootAlias . '.' . $name;
    }

    /**
     * Get DQL expression
     *
     * @param  string $alias Root alias
     *
     * @return \Doctrine\ORM\Query\Expr|string
     */
    public function getExpression($alias)
    {
        $nameWithAlias = $this->getFinalAliasName($alias);

        return sprintf('%s %s :%s', $nameWithAlias, $this->getOperator(), $this->getParameterName());
    }

    /**
     * Get search condition service name
     *
     * @return strig
     */
    public function getName()
    {
        return $this->propertyName;
    }

    /**
     * Get joins list
     *
     * @param  string $alias Root alias
     *
     * @return array[\Doctrine\ORM\Query\Expr\Join]
     */
    public function getJoins($alias)
    {
        $joins = array();

        $parsed = $this->getParsedPropertyName($alias);
        array_pop($parsed);

        foreach ($parsed as $value) {
            list($parentAlias, $joinProperty) = $value;
            $join = $parentAlias . '.' . $joinProperty;
            $joinAlias = $parentAlias . '_' . $joinProperty;

            $skipJoin = $alias . '.translations' === $join;
            if (!$skipJoin) {
                $joins[] = new \Doctrine\ORM\Query\Expr\Join(
                    \Doctrine\ORM\Query\Expr\Join::LEFT_JOIN,
                    $join,
                    $joinAlias
                );
            }
        }

        return $joins;
    }

    /**
     * Get DQL parameter name
     *
     * @return string
     */
    protected function getParameterName()
    {
        $parts = explode('.', $this->propertyName);
        $name = join('_', $parts);

        return $name . $this->getParameterNameSuffix();
    }

    /**
     * Preprosessing DQL parameter value
     *
     * @param  mixed    $value  Value
     *
     * @return mixed
     */
    protected function preprocessValue($value)
    {
        return $value;
    }

    /**
     * Get parameter name custom suffix, to avoid collision
     *
     * @return string
     */
    public function getParameterNameSuffix() {
        return $this->parameterNameSuffix;
    }

    /**
     * Set parameter name custom suffix, to avoid collision
     *
     * @param   string  $suffix Suffix value
     *
     * @return  string
     */
    public function setParameterNameSuffix($suffix) {
        $this->parameterNameSuffix = $suffix;
    }

    /**
     * Get parameter name custom suffix, to avoid collision
     *
     * @return string
     */
    abstract protected function getDefaultParameterNameSuffix();

    /**
     * Get DQL expr operator
     *
     * @return string
     */
    abstract protected function getOperator();
}
