<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Model;

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
        $isFreeShippingAdded = false;
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('shippable' == $name) {
                $schema['freeShip'] = $this->defineFreeShipping();
                $schema['freightFixedFee'] = $this->defineFreightFixedFee();
                $isFreeShippingAdded = true;
            }
        }

        if (!$isFreeShippingAdded) {
            $schema['freeShip'] = $this->defineFreeShipping();
            $schema['freightFixedFee'] = $this->defineFreightFixedFee();
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Defines the is free shipping by module field
     *
     * @return array
     */
    protected function defineFreeShipping()
    {
        return array(
            static::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            static::SCHEMA_LABEL      => static::t('Free shipping'),
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_DEPENDENCY => array(
                static::DEPENDENCY_SHOW => array(
                    'shippable' => array(\XLite\View\FormField\Select\YesNo::YES),
                ),
            ),
        );
    }

    /**
     * Defines the is free shipping by module field
     *
     * @return array
     */
    protected function defineFreightFixedFee()
    {
        return array(
            static::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
            static::SCHEMA_LABEL      => static::t('Shipping freight'),
            static::SCHEMA_HELP       => static::t('This field can be used to set a fixed shipping fee for the product. Make sure the field value is a positive number (greater than zero).'),
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_DEPENDENCY => array(
                static::DEPENDENCY_SHOW => array(
                    'shippable' => array(\XLite\View\FormField\Select\YesNo::YES),
                    'freeShip' => array(\XLite\View\FormField\Select\YesNo::NO),
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
        if (isset($data['freeShip'])) {
            $data['freeShip'] = 'Y' == $data['freeShip'];
        }

        parent::updateModelProperties($data);
    }
}
