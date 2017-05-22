<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View\Model;

/**
 * Decorate product settings page
 */
class Product extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Constructor
     *
     * @param array $params   Params   OPTIONAL
     * @param array $sections Sections OPTIONAL
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        $schema = array();
        $isAttachableAdded = false;
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('description' == $name) {
                $schema['isCustomerAttachmentsAvailable'] = $this->defineIsAttachable();
                $schema['isCustomerAttachmentsRequired'] = $this->defineIsRequired();
                $isAttachableAdded = true;
            }
        }

        if (!$isAttachableAdded) {
            $schema['isCustomerAttachmentsAvailable'] = $this->defineIsAttachable();
            $schema['isCustomerAttachmentsRequired'] = $this->defineIsRequired();
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Defines the is attachable field
     *
     * @return array
     */
    protected function defineIsAttachable()
    {
        return array(
            static::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            static::SCHEMA_LABEL      => static::t('Allow buyers to attach files to this product'),
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_FIELD_ONLY => false,
        );
    }

    protected function defineIsRequired()
    {
        return array(
            static::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            static::SCHEMA_LABEL      => static::t('File Attaching is mandatory for this product'),
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_FIELD_ONLY => false,
            static::SCHEMA_DEPENDENCY => array(
                static::DEPENDENCY_SHOW => array(
                    'isCustomerAttachmentsAvailable' => array(\XLite\View\FormField\Select\YesNo::YES),
                ),
            ),
        );
    }

    /**
     * Populate model object properties by the passed data.
     * Specific wrapper for setModelProperties method.
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function updateModelProperties(array $data)
    {
        foreach (array('isCustomerAttachmentsRequired', 'isCustomerAttachmentsAvailable') as $field) {
            if (isset($data[$field])) {
                $data[$field] = 'Y' == $data[$field];
            }
        }

        parent::updateModelProperties($data);
    }
}
