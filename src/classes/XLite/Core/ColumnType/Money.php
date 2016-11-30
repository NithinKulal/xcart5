<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ColumnType;

/**
 * Money (value without currency)
 */
class Money extends \Doctrine\DBAL\Types\DecimalType
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'money';
    }

    /**
     * Get SQL declaration
     *
     * @param array                                     $fieldDeclaration Field declaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform         Platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $fieldDeclaration['precision'] = 14;
        $fieldDeclaration['scale'] = 4;

        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

    /**
     * Convert to PHP value
     *
     * @param string                                    $value    Value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform Platform
     *
     * @return float
     */
    public function convertToPHPValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return (null === $value) ? null : doubleval($value);
    }

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't take them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return boolean
     */
    public function requiresSQLCommentHint(\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * Define binding database type
     *
     * @return integer
     */
    public function getBindingType()
    {
        return \PDO::PARAM_INT;
    }
}
