<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select;

/**
 * Payment method
 */
class PaymentMethod extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Save widget value in entity
     *
     * @param array $field Field data
     *
     * @return void
     */
    public function saveValuePaymentMethod($field)
    {
        $paymentId = $field['widget']->getValue();

        $serviceCodes = array(
            \XLite\View\FormField\Select\PaymentMethod::KEY_DELETED,
            \XLite\View\FormField\Select\PaymentMethod::KEY_NONE,
        );

        if (!in_array($paymentId, $serviceCodes)) {

            $entity = $this->getEntity();

            $oldMethodId = $entity->getPaymentMethod() ? $entity->getPaymentMethod()->getMethodId() : null;

            $oldValue = sprintf(
                '%s (id: %d)',
                $entity->getPaymentMethod() ? $entity->getPaymentMethod()->getName() : static::t('None'),
                $entity->getPaymentMethod() ? $oldMethodId : 'n/a'
            );

            if (!$entity->isPersistent()) {
                $entity->setOrder($this->getOrder());
                $this->getOrder()->addPaymentTransactions($entity);
            }

            $paymentMethod = $this->preprocessValueBeforeSave($paymentId);
            $entity->setPaymentMethod($paymentMethod);

            if ($entity->getPaymentMethod() && $entity->getPaymentMethod()->getMethodId() != $oldMethodId) {
                \XLite\Controller\Admin\Order::setOrderChanges(
                    $this->getParam(static::PARAM_FIELD_NAME),
                    sprintf(
                        '%s (id: %d)',
                        $entity->getPaymentMethod() ? $entity->getPaymentMethod()->getName() : 'n/a',
                        $entity->getPaymentMethod() ? $entity->getPaymentMethod()->getMethodId() : 'n/a'
                    ),
                    $oldValue
                );
            }
        }
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\PaymentMethod';
    }

    /**
     * Preprocess value before save
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function preprocessValueBeforeSave($value)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->find(parent::preprocessValueBeforeSave($value));
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
        return parent::getEntity()
            ? (
                $this->getEntity()->getPaymentMethod()
                ? $this->getEntity()->getPaymentMethod()->getTitle()
                : $this->getEntity()->getMethodLocalName()
            )
            : static::t('None');
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
        $parts = array($field[static::FIELD_NAME]);

        if ($this->getParam(static::PARAM_FIELD_NAMESPACE) == 'paymentMethods') {
            $parts[] = $this->getEntityUniqueIdentifier();
        }

        return $parts;
    }

    /**
     * Correct result of getEntity() as entity may be null
     *
     * @return \XLite\Model\AEntity
     */
    protected function getEntity()
    {
        return parent::getEntity()
            ?: new \XLite\Model\Payment\Transaction;
    }

    /**
     * Get additional CSS classes for the field widget
     *
     * @param array $field Field data
     *
     * @return string
     */
    protected function getAdditionalFieldStyle($field)
    {
        $style = parent::getAdditionalFieldStyle($field);

        return ($style ? $style . ' ' : '') . 'not-affect-recalculate';
    }
}
