<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Change attribute values from cart / wishlist item
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ACustomer
{
    use ExecuteCachedTrait;

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('"{{product}} product" attributes', ['product' => $this->getItem()->getName()]);
    }

    /**
     * Initialize controller
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if (!$this->getItem()) {
            $this->redirect();
        }
    }

    /**
     * Get cart / wishlist item
     *
     * @return \XLite\Model\OrderItem
     */
    public function getItem()
    {
        $itemId = \XLite\Core\Request::getInstance()->item_id;

        return $this->executeCachedRuntime(function () use ($itemId) {
            if (is_numeric($itemId)
                && \XLite\Core\Request::getInstance()->source === 'cart'
            ) {
                $item = $this->getCart()->getItemByItemId($itemId);

                if ($item
                    && $item->getProduct()
                    && $item->hasAttributeValues()
                ) {
                    return $item;
                }
            }

            return false;
        }, ['getItem', $itemId]);
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        $product = $this->getItem()->getProduct();
        if (\XLite\Core\Request::getInstance()->attribute_values) {
            $product->setAttrValues(\XLite\Core\Request::getInstance()->attribute_values);
        }

        return $product;
    }

    /**
     * Return selected attribute values ids
     *
     * @return array
     */
    public function getSelectedAttributeValuesIds()
    {
        return $this->getItem()->getAttributeValuesPlain();
    }

    /**
     * Common method to determine current location
     *
     * @return array
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Perform some actions before redirect
     *
     * FIXME: check. Action should not be an optional param
     *
     * @param string $action Current action OPTIONAL
     *
     * @return void
     */
    protected function actionPostprocess($action = null)
    {
        parent::actionPostprocess($action);

        if ($action) {
            $this->assembleReturnURL();
        }
    }

    /**
     * Assemble return url
     *
     * @return void
     */
    protected function assembleReturnURL()
    {
        $this->setReturnURL($this->buildURL(\XLite::TARGET_DEFAULT));

        if ($this->internalError) {
            $this->setReturnURL(
                $this->buildURL(
                    'change_attribute_values',
                    '',
                    [
                        'source'     => \XLite\Core\Request::getInstance()->source,
                        'storage_id' => \XLite\Core\Request::getInstance()->storage_id,
                        'item_id'    => \XLite\Core\Request::getInstance()->item_id,
                    ]
                )
            );

        } elseif (\XLite\Core\Request::getInstance()->source === 'cart') {
            $this->setReturnURL($this->buildURL('cart'));
        }
    }

    /**
     * Change product attribute values
     *
     * @param array $attributeValues Attrbiute values (prepared, from request)
     *
     * @return boolean
     */
    protected function saveAttributeValues(array $attributeValues)
    {
        $this->getItem()->setAttributeValues($attributeValues);

        return true;
    }

    /**
     * Change product attribute values
     *
     * @return void
     */
    protected function doActionChange()
    {
        if ('cart' === \XLite\Core\Request::getInstance()->source) {
            $attributeValues = $this->getProduct()->prepareAttributeValues(
                \XLite\Core\Request::getInstance()->attribute_values
            );
            if ($this->saveAttributeValues($attributeValues)) {
                $this->updateCart();

                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\TopMessage::addInfo('Attributes have been successfully changed');

                $this->checkItemsAmount();

                $this->setSilenceClose();

            } else {

                $message = $this->getErrorMessage();

                \XLite\Core\TopMessage::addError($message);

                \XLite\Core\Session::getInstance()->error_message = static::t($message);

                $this->setInternalRedirect();
                $this->internalError = true;
            }
        }
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return 'Please select other attribute';
    }

    /**
     * Check amount for all cart items
     *
     * @return void
     */
    protected function checkItemsAmount()
    {
        foreach ($this->getCart()->getItemsWithWrongAmounts() as $item) {
            $this->processInvalidAmountError($item);
        }
    }

    /**
     * Show message about wrong product amount
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return void
     */
    protected function processInvalidAmountError(\XLite\Model\OrderItem $item)
    {
        \XLite\Core\TopMessage::addWarning(
            'You tried to buy more items of "{{product}}" product {{description}} than are in stock. We have {{amount}} item(s) only. Please adjust the product quantity.',
            [
                'product'     => $item->getProduct()->getName(),
                'description' => $item->getExtendedDescription(),
                'amount'      => $item->getProductAvailableAmount(),
            ]
        );
    }
}
