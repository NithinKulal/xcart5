<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Popup;

/**
 * Order's payment data
 */
class PaymentData extends \XLite\View\FormField\Inline\Popup\APopup
{
    /**
     * Cached transaction
     *
     * @var \XLite\Model\Payment\Transaction|false
     */
    protected $transaction = null;

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/inline/popup/payment_data/style.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!$this->getViewOnly()) {
            $list[] = 'form_field/inline/popup/payment_data/controller.js';
        }

        return $list;
    }

    /**
     * Return true if widget is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible() && $this->getTransactionData();
    }

    /**
     * Get popup widget
     *
     * @return string
     */
    protected function getPopupWidget()
    {
        return '\XLite\View\PaymentMethodData';
    }

    /**
     * Get popup target
     *
     * @return string
     */
    protected function getPopupTarget()
    {
        return 'order';
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Hidden';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $paymentMethod = $this->getTransaction()
            ? $this->getTransaction()->getPaymentMethod()->getAdaptedServiceName()
            : '';

        return parent::getContainerClass() . ' inline-payment-method-data ' . $paymentMethod;
    }

    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        $fields = array();

        foreach ($this->getTransactionData() as $field) {
            $fields[$field['name']] = array(
                static::FIELD_NAME  => $field['name'],
                static::FIELD_CLASS => $this->defineFieldClass(),
            );
        }

        return $fields;
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
        $entity = $this->getTransactionData($field[static::FIELD_NAME]);

        return $entity ? $entity->getValue() : null;
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
        return array(
            $this->getParam(static::PARAM_FIELD_NAME),
            $field[static::FIELD_NAME],
        );
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/popup/payment_data/view.twig';
    }

    /**
     * Get field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'form_field/inline/popup/payment_data/field.twig';
    }

    /**
     * Get popup parameters
     *
     * @return array
     */
    protected function getPopupParameters()
    {
        $list = parent::getPopupParameters();

        $list['order_id'] = $this->getOrder()->getOrderId();
        $list['transaction_id'] = $this->getTransactionId();

        return $list;
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $entity = $this->getTransactionData($field['field'][static::FIELD_NAME]);

        if ($entity) {

            $oldValue = $entity ? $entity->getValue() : null;

            // Register order changes
            if ($value != $oldValue) {

                $entity->setValue($value);

                $this->registerOrderChanges($field, $value, $oldValue);

                if (!$entity->isPersistent()) {
                    $transaction = $entity->getTransaction();
                    $transaction->addData($entity);
                    \XLite\Core\Database::getEM()->persist($entity);
                }
            }
        }
    }

    /**
     * Register address changes in order history
     * Prepare data to register as an order changes
     *
     * @param array $field      Field
     * @param mixed $value      Value
     * @param mixed $oldValue   Old value
     *
     * @return void
     */
    protected function registerOrderChanges(array $field, $value, $oldValue)
    {
        $fieldName = $field['field'][static::FIELD_NAME];

        $transaction = $this->getTransaction();

        if ($transaction) {
            if ($transaction->getPaymentMethod()) {
                $paymentMethodName = $transaction->getPaymentMethod()->getName();

                $data = $transaction->getPaymentMethod()->getProcessor()->getTransactionData($transaction);
                foreach($data as $item) {
                    if ($field['field'][static::FIELD_NAME] == $item['name']) {
                        $fieldName = $item['title'];
                        break;
                    }
                }

            } else {
                $paymentMethodName = $transaction->getMethodName();
            }
        }

        if (!empty($paymentMethodName)) {
            $paymentMethodName = static::t('Payment data ({{method}})', array('method' => $paymentMethodName));

        } else {
            $paymentMethodName = static::t('Payment method data');
        }

        \XLite\Controller\Admin\Order::setOrderChanges(
            $paymentMethodName . ':' . $fieldName,
            $value,
            $oldValue
        );
    }

    /**
     * Get transaction ID
     *
     * @return integer
     */
    protected function getTransactionId()
    {
        return (int) preg_replace('/[^\d]+(\d+)/', '\\1', $this->getParam(static::PARAM_FIELD_NAME));
    }

    /**
     * Get transaction
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function getTransaction()
    {
        if (!isset($this->transaction)) {
            $this->transaction = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Transaction')->find($this->getTransactionId());
            if (!$this->transaction) {
                $this->transaction = false;
            }
        }

        return $this->transaction;
    }

    /**
     * Get list of transaction data (array if $name is not specified, entity if specified)
     *
     * @param string $name Parameters name OPTIONAL
     *
     * @return array|string
     */
    protected function getTransactionData($name = null)
    {
        $result = array();

        $transaction = $this->getTransaction();

        if ($transaction && $name) {
            $result = null;
            foreach ($transaction->getTransactionData(true) as $cell) {
                if ($cell->getName() == $name) {
                    $result = $cell;
                    break;
                }
            }

        } elseif ($transaction
            && $transaction->getPaymentMethod()
            && $transaction->getPaymentMethod()->getProcessor()
        ) {
            $result = $transaction->getPaymentMethod()->getProcessor()->getTransactionData($transaction);
            $prefix = 'transaction-' . $transaction->getTransactionId() . '-';
            foreach ($result as $k => $v) {
                $result[$k]['css_class'] = $v['name'] . ' ' . $prefix . $v['name'];
            }
        }

        return $result;
    }
}
