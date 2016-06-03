<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DataSource;

/**
 * Abstract data source
 */
abstract class ADataSource
{
    /**
     * Data source configuration
     * 
     * @var \XLite\Model\DataSource
     */
    protected $configuration;

    /**
     * Get standardized data source information array
     * 
     * @return array
     */
    abstract public function getInfo();

    /**
     * Checks whether the data source is valid
     * 
     * @return boolean
     */
    abstract public function isValid();

    /**
     * Request and return products collection
     * 
     * @return \XLite\Core\DataSource\Base\Products
     */
    abstract public function getProductsCollection();

    /**
     * Request and return categories collection
     * 
     * @return \XLite\Core\DataSource\Base\Categories
     */
    abstract public function getCategoriesCollection();

    /**
     * Get all data sources
     * 
     * @return array
     */
    public static function getDataSources()
    {
        return array(
            '\XLite\Core\DataSource\Ecwid',
        );
    }

    /**
     * Constructor
     * 
     * @param \XLite\Model\DataSource $configuration Data source configuration model
     *  
     * @return void
     */
    public function __construct(\XLite\Model\DataSource $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get current data source configuration object
     * 
     * @return \XLite\Model\DataSource
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }
}
