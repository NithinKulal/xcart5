<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select;
use XLite\Logic\Order\Modifier\AModifier;

/**
 * Shipping method
 */
class ShippingMethod extends \XLite\View\FormField\Inline\Base\Single
{
    const PARAM_MODE_ORDER = 'mode_order';

    /**
     * Method ids
     *
     * @var array
     */
    protected $methodIds;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/inline/select/shipping_method/controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_MODE_ORDER => new \XLite\Model\WidgetParam\TypeBool('Is order mode', false),
        ];
    }

    /**
     * @return boolean
     */
    protected function isOrderMode()
    {
        return (boolean)$this->getParam(static::PARAM_MODE_ORDER);
    }

    /**
     * Save widget value in entity
     *
     * @param array $field Field data
     *
     * @return void
     */
    public function saveValueShippingId($field)
    {
        $shippingId = $field['widget']->getValue();
        if ($shippingId !== \XLite\View\FormField\Select\ShipMethod::KEY_DELETED
            && $shippingId !== \XLite\View\FormField\Select\ShipMethod::KEY_UNAVAILABLE
        ) {
            $shippingId = (int)$shippingId;

            $shippingMethod = $shippingId
                ? \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($shippingId)
                : null;

            if ((int)$this->getEntity()->getShippingId() !== $shippingId) {
                $newName = $shippingMethod
                    ? $shippingMethod->getName()
                    : 'None';
                $oldName = $this->getEntity()->getShippingMethodName();

                \XLite\Controller\Admin\Order::setOrderChanges(
                    $this->getParam(static::PARAM_FIELD_NAME),
                    sprintf('%s (id: %d)', $newName, $shippingId),
                    sprintf('%s (id: %d)', $oldName, $this->getEntity()->getShippingId())
                );
            }

            $this->getEntity()->setShippingId($shippingId);

            $this->getEntity()->setShippingMethodName(
                $shippingMethod ? $shippingMethod->getName() : null
            );
        }
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\ShipMethod';
    }

    /**
     * Get view value
     *
     * @param array $field Field
     *
     * @return string
     */
    protected function getViewValue(array $field)
    {
        $name = null;

        if ($this->getEntity()->getShippingId()) {
            $method = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find(
                $this->getEntity()->getShippingId()
            );
            if ($method) {
                $name = $method->getName();

                if (!in_array($method->getMethodId(), $this->getMethodIds(), true)) {
                    $name .= ' (' . static::t('unavailable') . ')';
                }
            }
        }

        if (!$name) {
            $name = $this->getEntity()->getShippingMethodName();
            if ($name) {
                $name .= ' (' . static::t('deleted') . ')';
            }
        }

        return $name ?: static::t('None');
    }

    /**
     * Get modifier
     *
     * @return array
     */
    protected function getMethodIds()
    {
        if (null === $this->methodIds) {
            $modifier = $this->getEntity()
                ->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
            $modifier->setMode($this->isOrderMode() ? AModifier::MODE_ORDER : AModifier::MODE_CART);

            /** @var \XLite\Model\Shipping\Rate $a */
            $this->methodIds = array_map(function ($a) {
                return $a->getMethod()->getMethodId();
            }, $modifier->getRates());
        }

        return $this->methodIds;
    }

    /**
     * @inheritdoc
     */
    protected function prepareAdditionalFieldParams(array $field)
    {
        return parent::prepareAdditionalFieldParams($field) + [
            static::PARAM_MODE_ORDER => $this->isOrderMode()
        ];
    }

    /**
     * Get field name parts
     *
     * @param array $field Field
     *
     * @return array
     */
    protected function getNameParts(array $field)
    {
        return [$field[static::FIELD_NAME]];
    }

    /**
     * Check - escape value or not
     *
     * @return boolean
     */
    protected function isEscapeValue()
    {
        return false;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return trim(parent::getContainerClass() . ' shipping-method-selector');
    }
}
