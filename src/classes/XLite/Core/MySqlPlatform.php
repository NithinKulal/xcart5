<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * MySql DBAL platform
 */
class MySqlPlatform extends \Doctrine\DBAL\Platforms\MySqlPlatform
{
    /**
     * Get binary type declaration SQL
     * 
     * @param array $field Field declaration
     *  
     * @return string
     */
    public function getBinaryTypeDeclarationSQL(array $field)
    {
        if (!isset($field['length'])) {
            $field['length'] = $this->getVarcharDefaultLength();
        }

        $fixed = (isset($field['fixed'])) ? $field['fixed'] : false;

        return $field['length'] > $this->getVarcharMaxLength()
            ? $this->getClobTypeDeclarationSQL($field)
            : $this->getBinaryTypeDeclarationSQLSnippet($field['length'], $fixed);
    }

    /**
     * Get binary type declaration SQL snippet 
     * 
     * @param integer $length Field length
     * @param boolean $fixed  Fixed type flag
     *  
     * @return string
     */
    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
    {
        return ($fixed ? '' : 'VAR') . 'BINARY(' . ($length ?: 255) . ')';
    }

    /**
     * {@inheritDoc}
     *
     * TODO: remove once https://github.com/doctrine/dbal/pull/881 is merged
     */
    public function getColumnCharsetDeclarationSQL($charset)
    {
        return 'CHARACTER SET ' . $charset;
    }
}
