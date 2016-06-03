<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model;

/**
 * OrderItem
 *
 * @HasLifecycleCallbacks
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Pin codes (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\PINCodes\Model\PinCode", mappedBy="orderItem")
     */
    protected $pinCodes;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->pinCodes = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return true if current mail interface is customer interface.
     * Method is used for mail invoice template
     *
     * @return boolean
     */
    public function isCustomerInterface()
    {
        return \XLite::CUSTOMER_INTERFACE == \XLite\Core\Layout::getInstance()->getMailInterface()
            || \XLite::CUSTOMER_INTERFACE == \XLite\Core\Layout::getInstance()->getInterface();
    }

    /**
     * Count pin codes
     *
     * @return array
     */
    public function countPinCodes()
    {
        return $this->getPinCodes()->count();
    }

    /**
     * Count pin codes
     *
     * @return array
     */
    public function getSoldPinCodes()
    {
        $result = array();
        foreach ($this->getPinCodes() as $pin) {
            if ($pin->getIsSold()) {
                $result[] = $pin;
            }
        }

        return $result;
    }

    /**
     * Counts amount of PIN codes that should be assigned to this order item
     *
     * @return integer
     */
    public function countMissingPinCodes()
    {
        $count = 0;
        if ($this->getProduct()->getPinCodesEnabled()) {
            $count = $this->getAmount() - $this->countPinCodes();
        }

        return $count; 
    }

    /**
     * Acquire pin codes from assigned product
     *
     * @return array|void
     */
    public function acquirePinCodes()
    {
        $locker = \XLite\Core\Lock\OrderItemLocker::getInstance();

        $locker->waitForUnlocked($this, 5);
        $locker->lock($this);

        $pincodes = null;
        $product = $this->getProduct();
        if ($product->getPinCodesEnabled()) {

            $pincodes = $product->getAutoPinCodes()
                ? $this->getAutoPinCodes($product, $this->getAmount())
                : \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode')
                    ->getAvailablePinCodes($product, $this->getAmount());

            if (count($pincodes) !== $this->getAmount()) {
                \XLite\Logger::getInstance()->log(
                    'Could not acquire pin code for order item #' . $this->getItemId(), 
                    LOG_ERR
                );
            }

            foreach ($pincodes as $code) {
                if (!$code->isPersistent()) {
                    \XLite\Core\Database::getEM()->persist($code);
                }

                $code->setOrderItem($this);
                $code->setIsBlocked(true);
                $this->addPinCodes($code);
            }
        }

        $locker->unlock($this);
        return $pincodes;
    }

    /**
     * Get auto pin codes from assigned product
     *
     * @param \XLite\Model\Product  $product    Product to assign
     * @param integer               $amount     Amount of codes
     *
     * @return array
     */
    public function getAutoPinCodes(\XLite\Model\Product $product, $amount)
    {
        $codes = array();

        for ($i=0; $i < $amount; $i++) {
            $code = new \XLite\Module\CDev\PINCodes\Model\PinCode;
            $code->setProduct($product);
            $code->generateCode();

            $codes[] = $code;
        }

        return $codes;
    }

    /**
     * Sale pin codes 
     * 
     * @return void
     */
    public function salePinCodes()
    {
        foreach ($this->getPinCodes() as $pin) {
            $pin->setIsSold(true);
        }
    }

    /**
     * Release PIN codes linked to the order item
     *
     * @return void
     */
    public function releasePINCodes()
    {
        $product = $this->getProduct();

        if ($product->getPinCodesEnabled() && $this->getPinCodes()) {

            $isAuto = $product->getAutoPinCodes();

            foreach ($this->getPinCodes() as $pin) {

                $this->getPinCodes()->removeElement($pin);

                if ($isAuto) {
                    \XLite\Core\Database::getEM()->remove($pin);

                } else {
                    $pin->setOrderItem(null);
                    $pin->setIsBlocked(0);
                }
            }
        }
    }

    /**
     * Add pinCodes
     *
     * @param \XLite\Module\CDev\PINCodes\Model\PinCode $pinCodes
     * @return OrderItem
     */
    public function addPinCodes(\XLite\Module\CDev\PINCodes\Model\PinCode $pinCodes)
    {
        $this->pinCodes[] = $pinCodes;
        return $this;
    }

    /**
     * Get pinCodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPinCodes()
    {
        return $this->pinCodes;
    }
}
