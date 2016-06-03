<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Model;

/**
 * Category model widget extention
 */
class Category extends \XLite\View\Model\Category implements \XLite\Base\IDecorator
{
    /**
     * OG widgets into the default section
     *
     * @param array $params
     * @param array $sections
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        $schema = array();
        $useCustomOgAdded = false;
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('meta_desc' == $name) {
                $schema['useCustomOG'] = $this->defineCustomOgField();
                $useCustomOgAdded = true;
            }
        }

        if (!$useCustomOgAdded) {
            $schema['useCustomOG'] = $this->defineCustomOgField();
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Defines the custom OG field information
     *
     * @return array
     */
    protected function defineCustomOgField()
    {
        return array(
            static::SCHEMA_CLASS => 'XLite\Module\CDev\GoSocial\View\FormField\Select\CustomOpenGraph',
            static::SCHEMA_LABEL => 'Open Graph meta tags',
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
        $data['useCustomOG'] = $this->getPostedData('useCustomOG');
        $nonFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $data['ogMeta'] = isset($nonFilteredData['postedData']['ogMeta']) ? $nonFilteredData['postedData']['ogMeta'] : '';

        parent::setModelProperties($data);
    }
}
