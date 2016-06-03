<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Model;

/**
 * Product model widget extention
 */
class Product extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * We add sale price widget into the default section
     *
     * @param array $params
     * @param array $sections
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        $schema = array();
        $salePriceAdded = false;

        // We insert the sale fields after market price input if the MarketPrice module is on or after the price input.
        $schemaIdToSeek = $this->getSchemaIdToSeek();
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ($schemaIdToSeek == $name) {
                $schema['sale_price'] = $this->defineSalePriceField();
                $salePriceAdded = true;
            }
        }

        if (!$salePriceAdded) {
            $schema['sale_price'] = $this->defineSalePriceField();
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Define the field after which the sale field will be inserted (by default - price)
     *
     * @return string
     */
    protected function getSchemaIdToSeek()
    {
        return 'price';
    }

    /**
     * Defines the sale price field information
     *
     * @return array
     */
    protected function defineSalePriceField()
    {
        return array(
            static::SCHEMA_CLASS => 'XLite\Module\CDev\Sale\View\ProductModifySale',
            static::SCHEMA_LABEL => '',
            static::SCHEMA_REQUIRED => false,
            static::SCHEMA_FIELD_ONLY => false,
        );
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
        $participateSale = $this->getPostedData('participateSale');
        $data['participateSale'] = false;

        if ($participateSale) {
            $data['participateSale'] = true;
            $data['discountType'] = $this->getPostedData('discountType');
            $data['salePriceValue'] = doubleval($this->getPostedData('salePriceValue'));
        }

        parent::setModelProperties($data);
    }

}
