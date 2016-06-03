<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ColumnType;

/**
 * Decimal 
 */
class Decimal extends \Doctrine\DBAL\Types\DecimalType
{
    /**
     * Convert DB value to PHP value 
     * 
     * @param string                                    $value    DB value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform Platform
     *  
     * @return float
     */
    public function convertToPHPValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        return isset($value) && !is_double($value) ? doubleval($value) : $value;
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
