<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\DataSource;

/**
 * Abstract data source model widget
 */
class Ecwid extends ADataSource
{

    /**
     * Form fields definition
     * 
     * @var array
     */
    protected $schemaDefault = array(
        'parameter_storeid' => array(
            self::SCHEMA_CLASS      => '\XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL      => 'Store ID',
            self::SCHEMA_REQUIRED   => true,
            \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 1000,
        ),
    );

    /**
     * This object will be used if another one is not pased
     *
     * @return \XLite\Model\DataSource
     */
    protected function getDefaultModelObject()
    {
        // Always new
        return new \XLite\Model\DataSource();
    }
}
