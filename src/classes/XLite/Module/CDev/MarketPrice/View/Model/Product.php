<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\MarketPrice\View\Model;

/**
 * Product model widget extention
 */
class Product extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * We add market price widget into the default section
     *
     * @param array $params
     * @param array $sections
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        $schema = array();
        $marketPriceAdded = false;

        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('price' == $name) {
                $schema['marketPrice'] = $this->defineMarketPriceField();
                $marketPriceAdded = true;
            }
        }

        if (!$marketPriceAdded) {
            $schema['marketPrice'] = $this->defineMarketPriceField();
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Defines the sale price field information
     *
     * @return array
     */
    protected function defineMarketPriceField()
    {
        return array(
            static::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Price',
            static::SCHEMA_LABEL    => 'Market price',
            static::SCHEMA_REQUIRED => false,
        );
    }
}
