<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

use Doctrine\ORM\Event\LifecycleEventArgs;
use XLite\Module\XC\MailChimp\Core\Action;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Module\XC\MailChimp\Main;

/**
 * Class represents an order
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Use product in segment conditions
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useAsSegmentCondition = false;

    /**
     * @PostPersist
     */
    public function prepareBeforeCreateCorrect(LifecycleEventArgs $event)
    {
        if (!$this->getProductId()) {
            $this->product_id = $event->getEntity()->getProductId();
        }

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpECommerce::getInstance()->createProduct($store->getId(), $this);
            }
        }
    }

    /**
     * @inheritdoc
     * @PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        parent::prepareBeforeUpdate();

        $changeSet = \XLite\Core\Database::getEM()->getUnitOfWork()->getEntityChangeSet($this);

        if (isset($changeSet['enabled'])) {
            unset($changeSet['enabled']);
        }

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()
            && $this->getProductId()
            && array_filter($changeSet)
        ) {
            MailChimpQueue::getInstance()->addAction(
                'productUpdate' . $this->getProductId(),
                new Action\ProductUpdate($this)
            );
        }
    }

    /**
     * @inheritdoc
     * @PreRemove
     */
    public function prepareBeforeRemove()
    {
        parent::prepareBeforeRemove();

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpECommerce::getInstance()->removeProduct($store->getId(), $this->getProductId());
            }
        }
    }

    /**
     * Set useAsSegmentCondition
     *
     * @param boolean $useAsSegmentCondition
     * @return Product
     */
    public function setUseAsSegmentCondition($useAsSegmentCondition)
    {
        $this->useAsSegmentCondition = $useAsSegmentCondition;
        return $this;
    }

    /**
     * Get useAsSegmentCondition
     *
     * @return boolean 
     */
    public function getUseAsSegmentCondition()
    {
        return $this->useAsSegmentCondition;
    }
    
    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURLForMailChimp($withAttributes = false)
    {
        return $this->getProductId()
            ? \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL(
                    'product',
                    '',
                    $this->getParamsForFrontURL($withAttributes),
                    \XLite::getCustomerScript()
                )
            )
            : null;
    }
}