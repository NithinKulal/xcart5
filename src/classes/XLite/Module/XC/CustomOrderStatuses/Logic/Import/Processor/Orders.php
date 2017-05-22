<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Logic\Import\Processor;

use \XLite\Model\Order;
use \XLite\Core\Database;

/**
 * Class Orders
 *
 * @Decorator\Depend("XC\OrdersImport")
 */
class Orders extends \XLite\Module\XC\OrdersImport\Logic\Import\Processor\Orders implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['paymentStatus'] = [
            static::COLUMN_IS_MULTICOLUMN => true,
            static::COLUMN_IS_MULTIROW => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        $columns['shippingStatus'] = [
            static::COLUMN_IS_MULTICOLUMN => true,
            static::COLUMN_IS_MULTIROW => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        return $columns;
    }

    /**
     * Detect shippingStatus header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectPaymentStatusHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('paymentStatus.*', $row);
    }

    /**
     * Detect shippingStatus header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectShippingStatusHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('shippingStatus.*', $row);
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
        + [
            'ORDER-PAYMENT-CUSTOM-STATUS-NF' => 'Payment status not found, new status will be created',
            'ORDER-SHIPPING-CUSTOM-STATUS-NF' => 'Shipping status not found, new status will be created',
        ];
    }

    /**
     * Verify 'paymentStatus' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyPaymentStatus($value, array $column)
    {
        foreach ($value as &$field) {
            $field = array_shift($field);
        }

        $code = isset($value['paymentStatus']) ? $value['paymentStatus'] : null;
        if (!$code || !Database::getRepo('XLite\Model\Order\Status\Payment')->findOneBy(['code' => $code])) {
            $lang = $this->importer->getLanguageCode();
            $name = isset($value['paymentStatusLabel_' . $lang]) ? $value['paymentStatusLabel_' . $lang] : null;
            
            if ($name) {
                $status = Database::getRepo('XLite\Model\Order\Status\Payment')->findOneByName($name);

                if (!$status) {
                    $this->addWarning('ORDER-PAYMENT-CUSTOM-STATUS-NF', ['column' => $column, 'value' => $name]);
                }
            } else {
                $this->addWarning('ORDER-PAYMENT-STATUS-NF', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Verify 'shippingStatus' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyShippingStatus($value, array $column)
    {
        foreach ($value as &$field) {
            $field = array_shift($field);
        }

        $code = isset($value['shippingStatus']) ? $value['shippingStatus'] : null;
        if (!$code || !Database::getRepo('XLite\Model\Order\Status\Shipping')->findOneBy(['code' => $code])) {
            $lang = $this->importer->getLanguageCode();
            $name = isset($value['shippingStatusLabel_' . $lang]) ? $value['shippingStatusLabel_' . $lang] : null;

            if ($name) {
                $status = Database::getRepo('XLite\Model\Order\Status\Shipping')->findOneByName($name);

                if (!$status) {
                    $this->addWarning('ORDER-SHIPPING-CUSTOM-STATUS-NF', ['column' => $column, 'value' => $name]);
                }
            } else {
                $this->addWarning('ORDER-SHIPPING-STATUS-NF', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Import 'paymentStatus' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importPaymentStatusColumn(Order $order, $value, array $column)
    {
        foreach ($value as &$field) {
            $field = array_shift($field);
        }

        $repo = Database::getRepo('XLite\Model\Order\Status\Payment');
        if ($status = $repo->findOneBy(['code' => $value['paymentStatus']])) {
            $order->setPaymentStatus($status);
        } else {
            $lang = $this->importer->getLanguageCode();
            $name = isset($value['paymentStatusLabel_' . $lang]) ? $value['paymentStatusLabel_' . $lang] : null;

            if ($name) {
                $status = $repo->findOneByName($name);

                if (!$status) {
                    $status = $repo->insert(null, false);
                    $translationData = [];

                    foreach ($value as $header => $translation) {
                        if (!empty($translation) && preg_match_all('/^paymentStatusLabel_([a-z]{2})$/i', $header, $matches)) {
                            $translationData[$matches[1][0]] = $translation;
                        }
                    }

                    $this->updateModelTranslations($status, $translationData);
                }

                $order->setPaymentStatus($status);
            } else {
                $order->setPaymentStatus($repo->findOneBy([
                    'code' => \XLite\Model\Order\Status\Payment::STATUS_QUEUED
                ]));
            }
        }
    }

    /**
     * Import 'shippingStatus' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importShippingStatusColumn(Order $order, $value, array $column)
    {
        foreach ($value as &$field) {
            $field = array_shift($field);
        }

        $repo = Database::getRepo('XLite\Model\Order\Status\Shipping');

        if ($status = $repo->findOneBy(['code' => $value['shippingStatus']])) {
            $order->setShippingStatus($status);
        } else {
            $lang = $this->importer->getLanguageCode();
            $name = isset($value['shippingStatusLabel_' . $lang]) ? $value['shippingStatusLabel_' . $lang] : null;

            if ($name) {
                $status = $repo->findOneByName($name);

                if (!$status) {
                    $status = $repo->insert(null, false);
                    $translationData = [];

                    foreach ($value as $header => $translation) {
                        if (!empty($translation) && preg_match_all('/^shippingStatusLabel_([a-z]{2})$/i', $header, $matches)) {
                            $translationData[$matches[1][0]] = $translation;
                        }
                    }

                    $this->updateModelTranslations($status, $translationData);
                }

                $order->setShippingStatus($status);
            } else {
                $order->setShippingStatus($repo->findOneBy([
                    'code' => \XLite\Model\Order\Status\Shipping::STATUS_NEW
                ]));
            }
        }
    }
}