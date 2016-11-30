<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Module\XC\ProductVariants\Model;

use Doctrine\ORM\Event\LifecycleEventArgs;
use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Module\XC\MailChimp\Core\Action;
use XLite\Module\XC\MailChimp\Main;

/**
 * Class ProductVariant
 * 
 * @Decorator\Depend ("XC\ProductVariants")
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\Model\ProductVariant implements \XLite\Base\IDecorator
{
    /**
     * @PostPersist
     */
    public function prepareBeforeCreate(LifecycleEventArgs $event)
    {
        if (!$this->getId()) {
            $this->id = $event->getEntity()->getId();
        }
        
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            MailChimpQueue::getInstance()->addAction(
                'productUpdateVariants' . $this->getProduct()->getProductId(),
                new Action\ProductUpdate($this->getProduct())
            );
        }
    }

    /**
     * @PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            MailChimpQueue::getInstance()->addAction(
                'productUpdateVariants' . $this->getProduct()->getProductId(),
                new Action\ProductUpdate($this->getProduct())
            );
        }
    }

    /**
     * @PreRemove
     */
    public function prepareBeforeRemove()
    {
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            MailChimpQueue::getInstance()->addAction(
                'productUpdateVariants' . $this->getProduct()->getProductId(),
                new Action\ProductUpdate($this->getProduct())
            );
        }
    }

    /**
     * @return array
     */
    protected function getParamsForFrontURL($withAttributes = false)
    {
        $result = [
            'product_id'        => $this->getProduct()->getProductId(),
        ];

        if ($withAttributes) {
            $result['attribute_values'] = $this->getAttributeValuesParams();
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getAttributeValuesParams()
    {
        $validAttributes = array_filter(
            $this->getValues(),
            function ($attr) {
                return $attr &&  $attr->getAttribute();
            }
        );

        $paramsStrings = array_map(
            function($attr) {
                return $attr->getAttribute()->getId() . '_' . $attr->getId();
            },
            $validAttributes
        );

        return trim(join(',', $paramsStrings), ',');
    }

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURLForMailChimp($withAttributes = true)
    {
        return $this->getProduct() && $this->getId()
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