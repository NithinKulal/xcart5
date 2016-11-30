<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Model;

/**
 * Class represents an order
 */
abstract class Product extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        $this->schemaDefault['useAsSegmentCondition'] = array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL      => 'MailChimp Segment Condition',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_HELP       => 'Allow this product to be used as a MailChimp segment condition',
        );

        parent::__construct($params, $sections);
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
        $data['useAsSegmentCondition'] = 'Y' == $data['useAsSegmentCondition'];

        parent::updateModelProperties($data);
    }
}