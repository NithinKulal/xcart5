<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Processor;

/**
 * Purchase order
 */
class PurchaseOrder extends \XLite\Model\Payment\Processor\Offline
{
    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'checkout/purchase_order.twig';
    }

    /**
     * Get input errors
     *
     * @param array $data Input data
     *
     * @return array
     */
    public function getInputErrors(array $data)
    {
        $errors = parent::getInputErrors($data);

        foreach ($this->getInputDataLabels() as $k => $t) {
            if (!isset($data[$k]) || !$data[$k]) {
                $errors[] = \XLite\Core\Translation::lbl('X field is required', array('field' => $t));
            }
        }

        return $errors;
    }


    /**
     * Get input data labels list
     *
     * @return array
     */
    protected function getInputDataLabels()
    {
        return array(
            'po_number'    => 'PO number',
            'po_company'   => 'Company name',
            'po_purchaser' => 'Name of purchaser',
            'po_position'  => 'Position',
        );
    }

    /**
     * Get input data access levels list
     *
     * @return array
     */
    protected function getInputDataAccessLevels()
    {
        return array(
            'po_number'    => \XLite\Model\Payment\TransactionData::ACCESS_CUSTOMER,
            'po_company'   => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'po_purchaser' => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'po_position'  => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
        );
    }

    /**
     * Get list of primary input fields
     *
     * @return array
     */
    protected function getPrimaryInputDataFields()
    {
        return array(
            'po_number',
        );
    }
}
