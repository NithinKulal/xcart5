<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Product view model
 */
class InventoryTracking extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'inventoryEnabled' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Inventory tracking for this product is',
        ),
        'amount' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Quantity in stock',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'inventoryEnabled' => array(1),
                ),
            ),
        ),
        'lowLimitEnabledCustomer' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Show low stock warning on product page',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'inventoryEnabled' => array(1),
                ),
            ),
        ),
        'lowLimitEnabled' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Notify administrator if the stock quantity of this product goes below a certain limit',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'inventoryEnabled' => array(1),
                ),
            ),
        ),
        'lowLimitAmount' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Low limit quantity',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'inventoryEnabled' => array(1),
                ),
            ),
        ),
    );

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * getDefaultFieldValue
     *
     * @param string $name Field name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        // Categories can be provided via request
        if ('categories' === $name) {
            $categoryId = \XLite\Core\Request::getInstance()->category_id;
            $value = $categoryId ? array(
                \XLite\Core\Database::getRepo('XLite\Model\Category')->find($categoryId),
            ) : $value;
        }

        return $value;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Category
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Model\Product;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Product\Modify\Inventory';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Update',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        return $result;
    }
}
