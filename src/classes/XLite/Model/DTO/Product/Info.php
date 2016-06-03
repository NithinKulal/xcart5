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
     * @param Info                      $object
     * @param ExecutionContextInterface $context
     */
    public static function validate($object, ExecutionContextInterface $context)
    {
        if (!empty($object->default->sku) && !static::isSKUValid($object)) {
            static::addViolation($context, 'default.sku', Translation::lbl('SKU must be unique'));
        }

        if ($object->marketing->meta_description_type === 'C' && '' === trim($object->marketing->meta_description)) {
            static::addViolation($context, 'marketing.meta_description', Translation::lbl('This field is required'));
        }
    }

    /**
     * @param Info $object
     *
     * @return boolean
     */
    protected static function isSKUValid($object)
    {
        $sku = $object->default->sku;
        $identity = $object->default->identity;

        $entity = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($sku);

        return !$entity || (int) $entity->getProductId() === (int) $identity;
    }

    /**
     * @param mixed|\XLite\Model\Product $data
     */
    protected function init($data)
    {
        $categories = [];
        foreach ($data->getCategories() as $category) {
            $categories[] = $category->getCategoryId();
        }

        $objectImages = $data->getImages();
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
            'identity' => $data->getProductId(),

            'name'               => $data->getName(),
            'sku'                => $data->getSku(),
            'images'             => $images,
            'category'           => $categories,
            'description'        => $data->getBriefDescription(),
            'full_description'   => $data->getDescription(),
            'available_for_sale' => $data->getEnabled(),
            'arrival_date'       => $data->getArrivalDate() ?: time(),
        ];
        $this->default = new CommonCell($default);

        $memberships = [];
        foreach ($data->getMemberships() as $membership) {
            $memberships[] = $membership->getMembershipId();
        }

        $taxClass = $data->getTaxClass();
        $inventoryTracking = new CommonCell([
            'inventory_tracking' => $data->getInventoryEnabled(),
            'quantity'           => $data->getAmount(),
        ]);
        $pricesAndInventory = [
            'memberships'        => $memberships,
            'tax_class'          => $taxClass ? $taxClass->getId() : null,
            'price'              => $data->getPrice(),
            'inventory_tracking' => $inventoryTracking,
        ];
        $this->prices_and_inventory = new CommonCell($pricesAndInventory);

        $shippingBox = new CommonCell([
            'separate_box' => $data->getUseSeparateBox(),
            'dimensions'   => [
                'length' => $data->getBoxLength(),
                'width'  => $data->getBoxWidth(),
                'height' => $data->getBoxHeight(),
            ],
        ]);
        $itemsInBox = new CommonCell([
            'items_in_box' => $data->getItemsPerBox(),
        ]);
        $shipping = [
            'weight'            => $data->getWeight(),
            'requires_shipping' => $data->getShippable(),
            'shipping_box'      => $shippingBox,
            'items_in_box'      => $itemsInBox,
        ];
        $this->shipping = new CommonCell($shipping);

        $cleanURL = new CommonCell([
            'autogenerate' => empty($data->getCleanURL()),
            'clean_url'    => rtrim($data->getCleanURL(), '.html'),
        ]);

        $marketing = [
            'meta_description_type' => $data->getMetaDescType(),
            'meta_description'      => $data->getMetaDesc(),
            'meta_keywords'         => $data->getMetaTags(),
            'product_page_title'    => $data->getMetaTitle(),
            'clean_url'             => $cleanURL,
        ];
        $this->marketing = new CommonCell($marketing);
    }

    /**
     * @param \XLite\Model\Product $dataObject
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($dataObject, $rawData = null)
    {
        $default = $this->default;

        $dataObject->setName((string) $default->name);

        $sku = trim($default->sku);
        $dataObject->setSku((string) $sku);

        $dataObject->processFiles('images', $default->images);

        $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')->findByIds($default->category);
        $dataObject->replaceCategoryProductsLinksByCategories($categories);

        $dataObject->setBriefDescription((string) $default->description);
        $dataObject->setDescription((string) $default->full_description);

        $dataObject->setEnabled((boolean) $default->available_for_sale);
        $dataObject->setArrivalDate((int) $default->arrival_date);

        $priceAndInventory = $this->prices_and_inventory;
        $memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findByIds($priceAndInventory->memberships);
        $dataObject->replaceMembershipsByMemberships($memberships);

        $taxClass = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($priceAndInventory->tax_class);
        $dataObject->setTaxClass($taxClass);

        $dataObject->setPrice($priceAndInventory->price);

        $dataObject->setInventoryEnabled((boolean) $priceAndInventory->inventory_tracking->inventory_tracking);
        $dataObject->setAmount($priceAndInventory->inventory_tracking->quantity);

        $shipping = $this->shipping;
        $dataObject->setWeight($shipping->weight);
        $dataObject->setShippable((boolean) $shipping->requires_shipping);

        $shippingBox = $shipping->shipping_box;
        $dataObject->setUseSeparateBox((boolean) $shippingBox->separate_box);

        $dataObject->setBoxLength($shippingBox->dimensions['length']);
        $dataObject->setBoxWidth($shippingBox->dimensions['width']);
        $dataObject->setBoxHeight($shippingBox->dimensions['height']);

        $dataObject->setItemsPerBox($shipping->items_in_box->items_in_box);

        $marketing = $this->marketing;
        $dataObject->setMetaDescType($marketing->meta_description_type);
        $dataObject->setMetaDesc((string) $marketing->meta_description);
        $dataObject->setMetaTags((string) $marketing->meta_keywords);
        $dataObject->setMetaTitle((string) $marketing->product_page_title);

        if (
            $marketing->clean_url->autogenerate
            || empty($marketing->clean_url->clean_url)
        ) {
            $dataObject->setCleanURL(\XLite\Core\Database::getRepo('XLite\Model\CleanURL')->generateCleanURL($dataObject));

        } else {
            $dataObject->setCleanURL((string) $marketing->clean_url->clean_url . '.html');
        }
    }

    /**
     * @param \XLite\Model\Product $dataObject
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterCreate($dataObject, $rawData = null)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Attribute')->generateAttributeValues($dataObject);

        if (!$dataObject->getSku()) {
            $sku = \XLite\Core\Database::getRepo('XLite\Model\Product')->generateSKU($dataObject);
            $dataObject->setSku((string) $sku);
        }
    }
}
