<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Base;

use XLite\Core\MagicMethodsIntrospectionInterface;

/**
 * Single-field
 */
abstract class Single extends \XLite\View\FormField\Inline\AInline
{
    /**
     * Define form field
     *
     * @return string
     */
    abstract protected function defineFieldClass();

    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        return array(
            $this->getParam(static::PARAM_FIELD_NAME) => array(
                static::FIELD_NAME  => $this->getParam(static::PARAM_FIELD_NAMESPACE)
                    ?: $this->getParam(static::PARAM_FIELD_NAME),
                static::FIELD_CLASS => $this->defineFieldClass(),
            ),
        );
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        $result = null;

        $entity = $this->getEntity();
        $method = 'get' . \XLite\Core\Converter::convertToCamelCase($this->getParam(static::PARAM_FIELD_NAME));

        if (
            method_exists($entity, $method)
            || ($entity instanceof MagicMethodsIntrospectionInterface && $entity->hasMagicMethod($method))
            ) {
            // $method assembled from 'get' + field short name
            $result = $this->getEntity()->$method();
        }

        return $result;
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        return $this->getEntityValue();
    }

    /**
     * Get single field
     *
     * @return array
     */
    protected function getSingleField()
    {
        $list = $this->getFields();

        return array_shift($list);
    }

    /**
     * Get single field as widget
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getSingleFieldAsWidget()
    {
        $field = $this->getSingleField();

        return $field['widget'];
    }
}
