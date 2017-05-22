<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\Model;

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
        $isTagSelectorAdded = false;
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('name' == $name) {
                $schema['tags'] = $this->defineTagSelector();
                $isTagSelectorAdded = true;
            }
        }

        if (!$isTagSelectorAdded) {
            $schema['tags'] = $this->defineTagSelector();
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Defines tag selector widget as schema child
     *
     * @return array
     */
    protected function defineTagSelector()
    {
        return array(
            static::SCHEMA_CLASS      => 'XLite\Module\XC\ProductTags\View\FormField\Select\Tags\ProductTags',
            static::SCHEMA_LABEL      => static::t('Tags'),
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_DEPENDENCY => array(),
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
        $model = $this->getModelObject();

        $tags = isset($data['tags']) ? $data['tags'] : array();
        unset($data['tags']);

        $model->getTags()->clear();

        if (is_array($tags)) {
            // Add new links
            foreach ($tags as $id) {
                $tag = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')->find($id);
                if ($tag) {
                    $model->addTags($tag);
                    $tag->addProducts($model);
                }
            }
        }

        parent::updateModelProperties($data);
    }
}
