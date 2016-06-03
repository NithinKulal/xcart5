<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DataSource\Base;

/**
 * Abstract collection
 * Implements SeekableIterator and Countable interfaces
 */
abstract class Collection implements \Countable, \SeekableIterator
{

    /**
     * Abstract data source
     * 
     * @var \XLite\Core\DataSource\ADataSource
     */
    protected $dataSource;

    /**
     * Constructor 
     * 
     * @param \XLite\Core\DataSource\ADataSource $dataSource Abstract data source
     *  
     * @return void
     */
    public function __construct(\XLite\Core\DataSource\ADataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Data source getter
     * 
     * @return \XLite\Core\DataSource\ADataSource
     */
    protected function getDataSource()
    {
        return $this->dataSource;
    }

}
