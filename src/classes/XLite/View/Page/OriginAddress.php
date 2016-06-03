<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page;

/**
 * Origin address page view
 */
class OriginAddress extends \XLite\View\Model\Settings
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'origin_address';

        return $result;
    }

    /**
     * Perform some operations when creating fields list by schema
     *
     * @param string $name Node name
     * @param array  $data Field description
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        $result = parent::getFieldSchemaArgs($name, $data);

        if ($name !== 'origin_use_company') {
            $result[static::SCHEMA_DEPENDENCY] = array(
                static::DEPENDENCY_SHOW => array(
                    'origin_use_company' => array(false),
                ),
            );
        }

        return $result;
    }

    /**
     * Get array of country/states selector fields which should be synchronized
     *
     * @return array
     */
    protected function getCountryStateSelectorFields()
    {
        return array(
            'origin_country' => array(
                'origin_state',
                'origin_custom_state',
            ),
        );
    }
}
