<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Attribute option
 */
class AttributeOption extends \XLite\View\FormField\Input\Text\Base\Combobox
{
    /**
     * Common params
     */
    const PARAM_ATTRIBUTE  = 'attribute';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ATTRIBUTE => new \XLite\Model\WidgetParam\TypeObject(
                'Attribute', null, false, 'XLite\Model\Attribute'
            ),
        );
    }

    /**
     * Get URL
     *
     * @return string
     */
    protected function getURL()
    {
        return parent::getURL() . '&id='
            . (
                $this->getParam(self::PARAM_ATTRIBUTE)
                    ? $this->getParam(self::PARAM_ATTRIBUTE)->getId() : ''
            );
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (
            $value
            && is_object($value)
        ) {
            if ($value instanceOf \XLite\Model\AttributeValue\AttributeValueSelect) {
                $value = $value->getAttributeOption();
            }
            $value = $value ? $value->getName() : '';
        }

        parent::setValue($value);
    }

    /**
     * Get dictionary name
     *
     * @return string
     */
    protected function getDictionary()
    {
        return 'attributeOption';
    }
}
