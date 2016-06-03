<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step\Base;

/**
 * I18n-based abstract step
 */
abstract class I18n extends \XLite\Logic\Export\Step\AStep
{
    /**
     * Assign i18n columns 
     * 
     * @param array $columns Base columns
     *  
     * @return array
     */
    protected function assignI18nColumns(array $columns)
    {
        $result = array();

        foreach ($this->getRepository()->getTranslationRepository()->getUsedLanguageCodes() as $code) {
            foreach ($columns as $name => $column) {
                if (!isset($column[static::COLUMN_GETTER])) {
                    $column[static::COLUMN_GETTER] = 'getTranslationColumnValue';
                }
                $result[$name . '_' . $code] = $column;
            }
        }

        return $result;
    }

    /**
     * Get translation column value 
     * 
     * @param array   $dataset Dataset
     * @param string  $name    Name
     * @param integer $i       Subrowindex
     *  
     * @return string
     */
    protected function getTranslationColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getTranslation(substr($name, -2))->getterProperty(substr($name, 0, -3));
    }

}

