<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Filter;

/**
 * Attribute list widget
 */
class AttributeList extends \XLite\Module\XC\ProductFilter\View\Filter\AFilter
{
    /**
     * Widget param names
     */
    const PARAM_GROUP   = 'group';
    const PARAM_CLASSES = 'classes';

    /**
     * Get step title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttributeGroup()
            ? $this->getAttributeGroup()->getName()
            : null;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $hasAttributes = true;

        if ($this->getAttributeGroup()) {
            $hasAttributes = $this->getAttributeGroup()->hasNonEmptyAttributes();
        }

        return parent::isVisible()
            && $this->getAttributesList(true)
            && $hasAttributes;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_GROUP => new \XLite\Model\WidgetParam\TypeObject(
                'Group', null, false, '\XLite\Model\AttributeGroup'
            ),
            self::PARAM_CLASSES => new \XLite\Model\WidgetParam\TypeCollection(
                'Product classes', []
            ),
        ];
    }

    /**
     * Get attribute group
     *
     * @return \XLite\Model\AttributeGroup
     */
    protected function getAttributeGroup()
    {
        return $this->getParam(static::PARAM_GROUP);
    }

    /**
     * Get product classes
     *
     * @return array
     */
    protected function getProductClasses()
    {
        return $this->getParam(static::PARAM_CLASSES);
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/sidebar';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/attributes.twig';
    }


    /**
     * Get attributes list
     *
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getAttributesList($countOnly = false)
    {
        $data = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->search(
            $this->getAttributesListConditions($countOnly),
            $countOnly
        );

        return $countOnly
            ? $data
            : $this->prepareAttributesList($data);
    }

    /**
     * Get attributes list conditions
     *
     * @param boolean $countOnly Count only flag
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getAttributesListConditions($countOnly)
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->attributeGroup = $this->getAttributeGroup();
        if (!$cnd->attributeGroup) {
            $classes = $this->getProductClasses();
            $productClasses = array_filter(
                is_array($classes)
                ? $classes
                : $classes->toArray()
            );
            $cnd->productClass = !empty($productClasses) ? $productClasses : null;
        }
        $cnd->product = null;
        $cnd->visible = true;
        $cnd->type = \XLite\Model\Attribute::getFilteredTypes();
        $cnd->orderBy = $this->getSortingOrder();

        return $cnd;
    }

    /**
     * Prepare attributes list
     *
     * @param array $data Attributes
     *
     * @return array
     */
    protected function prepareAttributesList(array $data)
    {
        $filterValues = $this->getFilterValues();
        $filterValues = (isset($filterValues['attribute']) && is_array($filterValues['attribute']))
            ? $filterValues['attribute']
            : [];

        $result = [];
        foreach ($data as $attribute) {
            $row = $this->prepareAttributeElement($attribute, $filterValues);
            if ($row) {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Prepare attribute element
     *
     * @param \XLite\Model\Attribute $attribute    Attribute
     * @param array                  $filterValues Filter values defined in prepareAttributesList()
     *
     * @return array
     */
    protected function prepareAttributeElement(\XLite\Model\Attribute $attribute, array $filterValues)
    {
        $result = null;

        if ($attribute::TYPE_SELECT === $attribute->getType()) {
            $category = $this->getCategory();
            if ($category) {
                $options = $category->getAttributeOptionsByAttribute($attribute);
            }
        }

        if ($attribute::TYPE_SELECT !== $attribute->getType() || 0 < count($options)) {
            $params = [
                'fieldName' => 'filter[attribute][' . $attribute->getId() . ']',
                'label'     => $attribute->getName(),
                'attribute' => $attribute,
                'useColon'  => false,
                'value'     => isset($filterValues[$attribute->getId()]) ? $filterValues[$attribute->getId()] : ''
            ];
            $class = 'type-' . strtolower($attribute->getType());
            if ($attribute::TYPE_CHECKBOX === $attribute->getType() && $params['value']) {
                $class .= ' checked';

            } elseif ($attribute::TYPE_SELECT === $attribute->getType()) {
                $params['options'] = $options;
            }

            $result = [
                'class'  => $class,
                'widget' => $this->getWidget($params, $attribute->getFilterWidgetClass()),
            ];
        }

        return $result;
    }

    /**
     * Returns sorting based on config option
     *
     * @return array
     */
    protected function getSortingOrder()
    {
        return 'A' === \XLite\Core\Config::getInstance()->XC->ProductFilter->attributes_sorting_type
            ? ['translations.name', 'asc']
            : ['productClass.position', 'asc'];
    }
}
