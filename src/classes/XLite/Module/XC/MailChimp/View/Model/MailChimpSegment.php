<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Model;

/**
 * Settings dialog model widget
 */
class MailChimpSegment extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'useOrdersLastMonth'    => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL      => 'Filter by order frequency',
            self::SCHEMA_REQUIRED   => false,
        ),
        'ordersLastMonth'       => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL      => 'Orders last month',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW   => array (
                    'useOrdersLastMonth'    => 'Y',
                )
            ),
        ),
        'useOrderAmount'        => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL      => 'Filter by order amount',
            self::SCHEMA_REQUIRED   => false,
        ),
        'orderAmount'           => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL      => 'Total amount of orders',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW   => array (
                    'useOrderAmount'        => 'Y',
                )
            ),
        ),
        'useMemberships'        => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL      => 'Filter by profile membership',
            self::SCHEMA_REQUIRED   => false,
        ),
        'memberships'           => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\Memberships',
            self::SCHEMA_LABEL      => 'Memberships',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW   => array (
                    'useMemberships'        => 'Y',
                )
            ),
        ),
        'useProducts'           => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL      => 'Filter by specific products',
            self::SCHEMA_REQUIRED   => false,
        ),
        'products'              => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\MailChimp\View\FormField\Select\SegmentProducts',
            self::SCHEMA_LABEL      => 'Products',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW   => array (
                    'useProducts'           => 'Y',
                )
            ),
        )
    );

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpSegment')->find(
                $this->getModelId()
            )
            : null;

        return $model ?: new \XLite\Module\XC\MailChimp\Model\MailChimpSegment();
    }

    /**
     * Return list of the "Button" widgets
     * Do not use this method if you want sticky buttons panel.
     * The sticky buttons panel class has the buttons definition already.
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['update'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL     => 'Update',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE  => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE     => 'action',
            )
        );

        $result['back'] = new \XLite\View\Button\Link(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL     => 'Back to segments list',
                \XLite\View\Button\Link::PARAM_LOCATION     => \XLite\Core\Converter::buildURL(
                    'mailchimp_list_segments',
                    '',
                    array(
                        'id'    => $this->getModelObject()->getList()->getId()
                    )
                ),
                \XLite\View\Button\AButton::PARAM_STYLE     => 'action always-enabled',
            )
        );

        return $result;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\MailChimp\View\Form\Model\MailChimpSegment';
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
        $memberships = isset($data['memberships']) ? $data['memberships'] : array();
        unset($data['memberships']);

        $products = isset($data['products']) ? $data['products'] : array();
        unset($data['products']);

        foreach (array('useOrdersLastMonth', 'useOrderAmount', 'useMemberships', 'useProducts') as $field) {
            $data[$field] = 'Y' == $data[$field];
        }

        parent::setModelProperties($data);

        /**
         * @var \XLite\Module\XC\MailChimp\Model\MailChimpSegment $model MailChimp segment
         */
        $model = $this->getModelObject();

        // Update memberships
        foreach ($model->getMemberships() as $membership) {
            $membership->getProducts()->removeElement($model);
        }

        $model->getMemberships()->clear();

        if (
            isset($memberships)
            && $memberships
        ) {
            foreach ($memberships as $mid) {
                $membership = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($mid);

                if ($membership) {
                    $model->addMemberships($membership);
                    $membership->addProduct($model);
                }
            }
        } else {
            $model->setUseMemberships(false);
        }

        // Update products
        foreach ($model->getProducts() as $product) {
            $model->getProducts()->removeElement($product);
        }

        if (
            isset($products)
            && $products
        ) {
            foreach ($products as $pid) {
                $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($pid);

                if ($product) {
                    $model->addProducts($product);
                }
            }
        } else {
            $model->setUseProducts(false);
        }
    }
}
