<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Product inventory
 *
 * @Entity
 * @Table  (name="inventory",
 *      indexes={
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 *
 * @HasLifecycleCallbacks
 */
class Inventory extends \XLite\Model\AEntity
{
    /**
     * Default amounts
     */
    const AMOUNT_DEFAULT_INV_TRACK = 1000;
    const AMOUNT_DEFAULT_LOW_LIMIT = 10;

    /**
     * Inventory unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $inventoryId;

    /**
     * Is inventory tracking enabled or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Amount
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $amount = self::AMOUNT_DEFAULT_INV_TRACK;

    /**
     * Is low limit notification enabled for customer or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $lowLimitEnabledCustomer = false;

    /**
     * Is low limit notification enabled for admin or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $lowLimitEnabled = false;

    /**
     * Low limit amount
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $lowLimitAmount = self::AMOUNT_DEFAULT_LOW_LIMIT;

    /**
     * Product (association)
     *
     * @var \XLite\Model\Product
     *
     * @OneToOne   (targetEntity="XLite\Model\Product", inversedBy="inventory")
     * @JoinColumn (name="id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Setter
     *
     * @param integer $amount Amount to set
     *
     * @return void
     */
    public function setAmount($amount)
    {
        $this->amount = $this->correctAmount($amount);
    }

    /**
     * Setter
     *
     * @param integer $amount Amount to set
     *
     * @return void
     */
    public function setLowLimitAmount($amount)
    {
        $this->lowLimitAmount = $this->correctAmount($amount);
    }

    /**
     * Increase / decrease product inventory amount
     *
     * @param integer $delta Amount delta
     *
     * @return void
     */
    public function changeAmount($delta)
    {
        if ($this->getEnabled()) {
            $this->setAmount($this->getPublicAmount() + $delta);
        }
    }

    /**
     * Get public amount
     *
     * @return integer
     */
    public function getPublicAmount()
    {
        return $this->getAmount();
    }

    /**
     * Return product amount available to add to cart
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        return $this->getEnabled() ? $this->getPublicAmount() - $this->getLockedAmount() : $this->getDefaultAmount();
    }

    /**
     * Get low available amount
     *
     * @return integer
     */
    public function getLowAvailableAmount()
    {
        return $this->getEnabled()
            ? min($this->getLowDefaultAmount(), $this->getPublicAmount() - $this->getLockedAmount())
            : $this->getLowDefaultAmount();
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->getEnabled() ? 0 >= $this->getAvailableAmount() : false;
    }

    /**
     * Check if product amount is less than its low limit
     *
     * @return boolean
     */
    public function isLowLimitReached()
    {
        return $this->getEnabled()
            && $this->getLowLimitEnabled()
            && $this->getPublicAmount() <= $this->getLowLimitAmount();
    }

    /**
     * List of controllers which should not send notifications
     * 
     * @return array
     */
    protected function getForbiddenControllers()
    {
        return array(
            '\XLite\Controller\Admin\EventTask',
            '\XLite\Controller\Admin\ProductList',
            '\XLite\Controller\Admin\Product',
        );
    }

    /**
     * Check if notifications should be sended in current situation
     * 
     * @return boolean
     */
    protected function isShouldSend()
    {
        $currentController = \XLite::getInstance()->getController();
        $isControllerFirbidden = array_reduce(
            $this->getForbiddenControllers(),
            function ($carry, $controllerName) use ($currentController) {
                return $carry ?: ($currentController instanceof $controllerName);
            },
            false
        );
        return
            \XLite\Core\Request::getInstance()->event !== 'import'
            && !$isControllerFirbidden;
    }

    /**
     * Perform some actions before inventory saved
     *
     * @return void
     *
     * @PostUpdate
     */
    public function proccessPostUpdate()
    {
        if ($this->isLowLimitReached() && $this->isShouldSend()) {
            $this->sendLowLimitNotification();
            $this->updateLowStockUpdateTimestamp();
        }
    }


    /**
     * Check and (if needed) correct amount value
     *
     * @param integer $amount Value to check
     *
     * @return integer
     */
    protected function correctAmount($amount)
    {
        return max(0, (int) $amount);
    }

    /**
     * Get list of cart items containing current product
     *
     * @return array
     */
    protected function getLockedItems()
    {
        return $this->getProduct()
            ? \XLite\Model\Cart::getInstance()->getItemsByProductId($this->getProduct()->getProductId())
            : array();
    }

    /**
     * Return "locked" amount: items already added to the cart
     *
     * @return integer
     */
    protected function getLockedAmount()
    {
        return \Includes\Utils\ArrayManager::sumObjectsArrayFieldValues($this->getLockedItems(), 'getAmount', true);
    }

    /**
     * Get a low default amount
     *
     * @return integer
     */
    protected function getLowDefaultAmount()
    {
        return 1;
    }

    /**
     * Default qty value to show to customers
     *
     * @return integer
     */
    public function getDefaultAmount()
    {
        return self::AMOUNT_DEFAULT_INV_TRACK;
    }

    /**
     * Send notification to admin about product low limit
     *
     * @return void
     */
    protected function sendLowLimitNotification()
    {
        \XLite\Core\Mailer::sendLowLimitWarningAdmin($this->prepareDataForNotification());
    }

    /**
     * Prepare data for 'low limit warning' email notifications
     *
     * @return array
     */
    protected function prepareDataForNotification()
    {
        $data = array();

        $product = $this->getProduct();

        $data['product'] = $product;
        $data['name']    = $product->getName();
        $data['sku']     = $product->getSKU();
        $data['amount']  = $this->getAmount();

        $params = array(
            'product_id' => $product->getProductId(),
            'page'       => 'inventory',
        );
        $data['adminURL'] = \XLite\Core\Converter::buildFullURL('product', '', $params, \XLite::getAdminScript(), false);

        return $data;
    }

    /**
     * Update low stock update timestamp
     *
     * @return void
     */
    protected function updateLowStockUpdateTimestamp()
    {
        \XLite\Core\TmpVars::getInstance()->lowStockUpdateTimestamp = LC_START_TIME;
    }

    /**
     * Get inventoryId
     *
     * @return integer 
     */
    public function getInventoryId()
    {
        return $this->inventoryId;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Inventory
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set lowLimitEnabledCustomer
     *
     * @param boolean $lowLimitEnabledCustomer
     * @return Inventory
     */
    public function setLowLimitEnabledCustomer($lowLimitEnabledCustomer)
    {
        $this->lowLimitEnabledCustomer = $lowLimitEnabledCustomer;
        return $this;
    }

    /**
     * Get lowLimitEnabledCustomer
     *
     * @return boolean 
     */
    public function getLowLimitEnabledCustomer()
    {
        return $this->lowLimitEnabledCustomer;
    }

    /**
     * Set lowLimitEnabled
     *
     * @param boolean $lowLimitEnabled
     * @return Inventory
     */
    public function setLowLimitEnabled($lowLimitEnabled)
    {
        $this->lowLimitEnabled = $lowLimitEnabled;
        return $this;
    }

    /**
     * Get lowLimitEnabled
     *
     * @return boolean 
     */
    public function getLowLimitEnabled()
    {
        return $this->lowLimitEnabled;
    }

    /**
     * Get lowLimitAmount
     *
     * @return integer 
     */
    public function getLowLimitAmount()
    {
        return $this->lowLimitAmount;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Inventory
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }
}
