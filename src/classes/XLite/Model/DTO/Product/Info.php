<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Product;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use XLite\Core\Translation;
use XLite\Model\DTO\Base\CommonCell;

class Info extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param Info                      $dto
     * @param ExecutionContextInterface $context
     */
    public static function validate($dto, ExecutionContextInterface $context)
    {
        if (!empty($dto->default->sku) && !static::isSKUValid($dto)) {
            static::addViolation($context, 'default.sku', Translation::lbl('SKU must be unique'));
        }

        if ($dto->marketing->meta_description_type === 'C' && '' === trim($dto->marketing->meta_description)) {
            static::addViolation($context, 'marketing.meta_description', Translation::lbl('This field is required'));
        }

        if (!$dto->marketing->clean_url->autogenerate && !$dto->marketing->clean_url->force) {
            $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');
            $value = $dto->marketing->clean_url->clean_url . '.html';

            if (!$repo->isURLUnique($value, 'XLite\Model\Product', $dto->default->identity)) {
                static::addViolation($context, 'marketing.clean_url.clean_url', Translation::lbl('The Clean URL entered is already in use.'));
            }
        }
    }

    /**
     * @param Info $dto
     *
     * @return boolean
     */
    protected static function isSKUValid($dto)
    {
        $sku = $dto->default->sku;
        $identity = $dto->default->identity;

        $entity = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($sku);

        return !$entity || (int) $entity->getProductId() === (int) $identity;
    }

    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        $categories = [];
        foreach ($object->getCategories() as $category) {
            $categories[] = $category->getCategoryId();
        }

        $objectImages = $object->getImages();
        $images = [0 => [
            'delete'   => false,
            'position' => '',
            'alt'      => '',
            'temp_id'  => '',
        ]];
        foreach ($objectImages as $image) {
            $images[$image->getId()] = [
                'delete'   => false,
                'position' => '',
                'alt'      => '',
                'temp_id'  => '',
            ];
        }

        $default = [
            'identity' => $object->getProductId(),

            'name'               => $object->getName(),
            'sku'                => $object->getSku(),
            'images'             => $images,
            'category'           => $categories,
            'description'        => $object->getBriefDescription(),
            'full_description'   => $object->getDescription(),
            'available_for_sale' => $object->getEnabled(),
            'arrival_date'       => $object->getArrivalDate() ?: time(),
        ];
        $this->default = new CommonCell($default);

        $memberships = [];
        foreach ($object->getMemberships() as $membership) {
            $memberships[] = $membership->getMembershipId();
        }

        $taxClass = $object->getTaxClass();
        $inventoryTracking = new CommonCell([
            'inventory_tracking' => $object->getInventoryEnabled(),
            'quantity'           => $object->getAmount(),
        ]);
        $pricesAndInventory = [
            'memberships'        => $memberships,
            'tax_class'          => $taxClass ? $taxClass->getId() : null,
            'price'              => $object->getPrice(),
            'inventory_tracking' => $inventoryTracking,
        ];
        $this->prices_and_inventory = new CommonCell($pricesAndInventory);

        $shippingBox = new CommonCell([
            'separate_box' => $object->getUseSeparateBox(),
            'dimensions'   => [
                'length' => $object->getBoxLength(),
                'width'  => $object->getBoxWidth(),
                'height' => $object->getBoxHeight(),
            ],
        ]);
        $itemsInBox = new CommonCell([
            'items_in_box' => $object->getItemsPerBox(),
        ]);
        $shipping = [
            'weight'            => $object->getWeight(),
            'requires_shipping' => $object->getShippable(),
            'shipping_box'      => $shippingBox,
            'items_in_box'      => $itemsInBox,
        ];
        $this->shipping = new CommonCell($shipping);

        $cleanURL = new CommonCell([
            'autogenerate' => !(boolean) $object->getCleanURL(),
            'clean_url'    => rtrim($object->getCleanURL(), '.html'),
            'force'        => false,
        ]);

        $marketing = [
            'meta_description_type' => $object->getMetaDescType(),
            'meta_description'      => $object->getMetaDesc(),
            'meta_keywords'         => $object->getMetaTags(),
            'product_page_title'    => $object->getMetaTitle(),
            'clean_url'             => $cleanURL,
        ];
        $this->marketing = new CommonCell($marketing);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $object->setName((string) $default->name);

        $sku = trim($default->sku);
        $object->setSku((string) $sku);

        $object->processFiles('images', $default->images);

        $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')->findByIds($default->category);
        $object->replaceCategoryProductsLinksByCategories($categories);

        $object->setBriefDescription((string) $rawData['default']['description']);
        $object->setDescription((string) $rawData['default']['full_description']);

        $object->setEnabled((boolean) $default->available_for_sale);
        $object->setArrivalDate((int) $default->arrival_date);

        $priceAndInventory = $this->prices_and_inventory;
        $memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findByIds($priceAndInventory->memberships);
        $object->replaceMembershipsByMemberships($memberships);

        $taxClass = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($priceAndInventory->tax_class);
        $object->setTaxClass($taxClass);

        $object->setPrice($priceAndInventory->price);

        $object->setInventoryEnabled((boolean) $priceAndInventory->inventory_tracking->inventory_tracking);
        $object->setAmount($priceAndInventory->inventory_tracking->quantity);

        $shipping = $this->shipping;
        $object->setWeight($shipping->weight);
        $object->setShippable((boolean) $shipping->requires_shipping);

        $shippingBox = $shipping->shipping_box;
        $object->setUseSeparateBox((boolean) $shippingBox->separate_box);

        $object->setBoxLength($shippingBox->dimensions['length']);
        $object->setBoxWidth($shippingBox->dimensions['width']);
        $object->setBoxHeight($shippingBox->dimensions['height']);

        $object->setItemsPerBox($shipping->items_in_box->items_in_box);

        $marketing = $this->marketing;
        $object->setMetaDescType($marketing->meta_description_type);
        $object->setMetaDesc((string) $marketing->meta_description);
        $object->setMetaTags((string) $marketing->meta_keywords);
        $object->setMetaTitle((string) $marketing->product_page_title);

        if ($marketing->clean_url->autogenerate
            || empty($marketing->clean_url->clean_url)
        ) {
            $object->setCleanURL(\XLite\Core\Database::getRepo('XLite\Model\CleanURL')->generateCleanURL($object));

        } else {
            $value = $marketing->clean_url->clean_url . '.html';
            if ($marketing->clean_url->force) {
                $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');
                $conflictEntity = $repo->getConflict(
                    $value,
                    $object,
                    $object->getProductId()
                );

                if ($conflictEntity && $value !== $conflictEntity->getCleanURL()) {
                    /** @var \Doctrine\Common\Collections\Collection $cleanURLs */
                    $cleanURLs = $conflictEntity->getCleanURLs();
                    /** @var \XLite\Model\CleanURL $cleanURL */
                    foreach ($cleanURLs as $cleanURL) {
                        if ($value === $cleanURL->getCleanURL()) {
                            $cleanURLs->removeElement($cleanURL);
                            \XLite\Core\Database::getEM()->remove($cleanURL);

                            break;
                        }
                    }
                }

                $object->setCleanURL((string) $value, true);

            } else {
                $object->setCleanURL((string) $value);
            }

        }
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterUpdate($object, $rawData = null)
    {
        $object->updateQuickData();
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterCreate($object, $rawData = null)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Attribute')->generateAttributeValues($object);

        if (!$object->getSku()) {
            $sku = \XLite\Core\Database::getRepo('XLite\Model\Product')->generateSKU($object);
            $object->setSku((string) $sku);
        }
    }
}
