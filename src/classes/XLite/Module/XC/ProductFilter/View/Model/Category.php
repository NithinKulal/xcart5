<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Model;

/**
 * Category view model
 *
 */
class Category extends \XLite\View\Model\Category implements \XLite\Base\IDecorator
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        $this->schemaDefault['useClasses'] = array(
            static::SCHEMA_CLASS      => 'XLite\Module\XC\ProductFilter\View\FormField\Select\UseClasses',
            static::SCHEMA_LABEL      => 'Classes for product filter',
            static::SCHEMA_REQUIRED   => false,
        );
        $this->schemaDefault['productClasses'] = array(
            static::SCHEMA_CLASS      => 'XLite\Module\XC\ProductFilter\View\FormField\Select\Classes',
            static::SCHEMA_FIELD_ONLY => true,
        );

        parent::__construct($params, $sections);
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $data['productClasses'] = isset($data['productClasses']) && $data['productClasses']
            ? \XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->findByIds($data['productClasses'])
            : array();

        parent::setModelProperties($data);
    }
}
