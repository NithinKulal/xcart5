<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model;

use XLite\Model\Cart;
use XLite\Module\XC\ProductVariants\Model\Product\ProductVariantsStockAvailabilityPolicy;

/**
 * Product variant
 *
 * @Entity
 * @Table  (name="product_variants")
 *
 * @HasLifecycleCallbacks
 */
class ProductVariant extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Price
     *
     * @var float
     *
     * @Column (
     *      type="money",
     *      precision=14,
     *      scale=4,
     *      options={
     *          @\XLite\Core\Doctrine\Annotation\Behavior (list={"taxable"}),
     *          @\XLite\Core\Doctrine\Annotation\Purpose (name="net", source="clear"),
     *          @\XLite\Core\Doctrine\Annotation\Purpose (name="display", source="net")
     *      }
     *  )
     */
    protected $price = 0.0000;

    /**
     * Default price flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $defaultPrice = true;

    /**
     * Amount
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $amount = 0;

    /**
     * Default amount flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $defaultAmount = true;

    /**
     * Weight
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */

    protected $weight = 0.0000;

    /**
     * Default weight flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $defaultWeight = true;

    /**
     * Product SKU
     *
     * @var string
     *
     * @Column (type="string", length=32, nullable=true)
     */
    protected $sku;

    /**
     * Default flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $defaultValue = false;

    /**
     * Image
     *
     * @var \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image
     *
     * @OneToOne  (targetEntity="XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image", mappedBy="product_variant", cascade={"all"})
     */
    protected $image;

    /**
     * Attribute value (checkbox)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueCheckbox", inversedBy="variants")
     * @JoinTable (
     *      name="product_variant_attribute_value_checkbox",
     *      joinColumns={@JoinColumn (name="variant_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="attribute_value_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $attributeValueC;

    /**
     * Attribute value (select)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect", inversedBy="variants")
     * @JoinTable (
     *      name="product_variant_attribute_value_select",
     *      joinColumns={@JoinColumn (name="variant_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="attribute_value_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $attributeValueS;

    /**
     * Product order items
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @OneToMany (targetEntity="XLite\Model\OrderItem", mappedBy="variant")
     */
    protected $orderItems;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->attributeValueC = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributeValueS = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return mixed
     */
    public function getAttributeValue(\XLite\Model\Attribute $attribute)
    {
        $result = null;

        foreach ($this->getValues() as $v) {
            if ($v->getAttribute()->getId() == $attribute->getId()) {
                $result = $v;
                break;
            }
        }

        return $result;
    }

    /**
     * Get attribute values
     *
     * @return array
     */
    public function getValues()
    {
        return array_merge(
            $this->getAttributeValueS()->toArray(),
            $this->getAttributeValueC()->toArray()
        );
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
        if (!$this->getDefaultAmount()) {
            $this->setAmount($this->getAmount() + $delta);
        }
    }

    /**
     * Get attribute values hash
     *
     * @return string
     */
    public function getValuesHash()
    {
        $hash = array();
        foreach ($this->getValues() as $av) {
            $hash[] = $av->getAttribute()->getId() . '_' . $av->getId();
        }
        sort($hash);

        return md5(implode('_', $hash));
    }

    /**
     * Get quick data price
     *
     * @return float
     */
    public function getQuickDataPrice()
    {
        return $this->getClearPrice();
    }

    /**
     * Get clear price
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getDefaultPrice()
            ? $this->getProduct()->getPrice()
            : $this->getPrice();
    }

    /**
     * Get net Price
     *
     * @return float
     */
    public function getNetPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getClearPrice', array('taxable'), 'net');
    }

    /**
     * Get display Price
     *
     * @return float
     */
    public function getDisplayPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getNetPrice', array('taxable'), 'display');
    }

    /**
     * Get clear weight
     *
     * @return float
     */
    public function getClearWeight()
    {
        return $this->getDefaultWeight()
            ? $this->getProduct()->getWeight()
            : $this->getWeight();
    }

    /**
     * Get display sku
     *
     * @return float
     */
    public function getDisplaySku()
    {
        return $this->getSku() ?: $this->getProduct()->getSku();
    }

    /**
     * Get SKU
     *
     * @return string
     */
    public function getSku()
    {
        return null !== $this->sku ? (string) $this->sku : null;
    }

    /**
     * Set sku and trim it to max length
     *
     * @param string $sku
     *
     * @return void
     */
    public function setSku($sku)
    {
        $this->sku = substr($sku, 0, \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->getFieldInfo('sku', 'length'));
    }

    /**
     * Set needProcess to related product
     *
     * @param boolean $needProcess
     *
     * @return \XLite\Model\Product
     */
    public function setNeedProcess($needProcess)
    {
        if ($this->getProduct()) {
            $this->getProduct()->setNeedProcess($needProcess);
        }

        return $this->getProduct();
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->getProduct()
            && $this->getProduct()->getInventoryEnabled()
            && $this->getProduct()->getLowLimitEnabledCustomer()
            && ($this->getPublicAmount() <= $this->getProduct()->getLowLimitAmount())
            && !$this->isOutOfStock();
    }

    /**
     * Return true if product variant can be purchased
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->availableInDate()
            && !$this->isOutOfStock();
    }

    /**
     * Flag if the product is available according date/time
     *
     * @return boolean
     */
    public function availableInDate()
    {
        return $this->getProduct()
            ? $this->getProduct()->availableInDate()
            : true;
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        /** @var ProductVariantsStockAvailabilityPolicy $availabilityPolicy */
        $availabilityPolicy = $this->getProduct()->getStockAvailabilityPolicy();

        return $availabilityPolicy->isVariantOutOfStock(Cart::getInstance(), $this->getId());
    }

    /**
     * Return public amount
     *
     * @return integer
     */
    public function getPublicAmount()
    {
        return $this->getDefaultAmount()
            ? $this->getProduct()->getPublicAmount()
            : $this->getAmount();
    }

    /**
     * Return product amount available to add to cart
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        /** @var ProductVariantsStockAvailabilityPolicy $availabilityPolicy */
        $availabilityPolicy = $this->getProduct()->getStockAvailabilityPolicy();

        return $availabilityPolicy->getAvailableVariantAmount(Cart::getInstance(), $this->getId());
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        if ($this->getSku()) {
            $newEntity->setSku(
                \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                    ->assembleUniqueSKU($this->getSku())
            );
        }

        $this->cloneEntityImage($newEntity);

        return $newEntity;
    }

    /**
     * Clone entity (image)
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $newEntity New entity
     *
     * @return void
     */
    public function cloneEntityImage(\XLite\Module\XC\ProductVariants\Model\ProductVariant $newEntity)
    {
        if ($this->getImage()) {
            $newImage = $this->getImage()->cloneEntity();
            $newImage->setProductVariant($newEntity);
            $newEntity->setImage($newImage);
        }
    }

    /**
     * Return taxable
     *
     * @return boolean
     */
    public function getTaxable()
    {
        return $this->getProduct()->getTaxable();
    }

    /**
     * Check if product amount is less than its low limit
     *
     * @return boolean
     */
    public function isLowLimitReached()
    {
        /** @var \XLite\Model\Product $product */
        $product = $this->getProduct();

        return $product->getInventoryEnabled()
            && $product->getLowLimitEnabled()
            && $this->getPublicAmount() <= $product->getLowLimitAmount();
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
     * Check if notifications should be sent in current situation
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
     * Send notification to admin about product low limit
     *
     * @return void
     */
    protected function sendLowLimitNotification()
    {
        \XLite\Core\Mailer::sendLowVariantLimitWarningAdmin(
            $this->prepareDataForNotification()
        );
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

        $data['product']            = $product;
        $data['name']               = $product->getName();
        $data['attributes']         = $this->prepareAttributesForEmail();
        $data['sku']                = $this->getDisplaySku();
        $data['amount']             = $this->getAmount();
        $data['variantsTabUrl']     = $this->getUrlToVariant();

        return $data;
    }

    /**
     * Prepare attributes for view
     *
     * @return array
     */
    protected function prepareAttributesForEmail()
    {
        $attrs = array();
        foreach ($this->getValues() as $attributeValue) {
            if ($attributeValue->getAttribute()->isVariable($this->getProduct())) {
                $attrs[] = array(
                    'name'  => $attributeValue->getAttribute()->getName(),
                    'value' => $attributeValue->asString(),
                );
            }
        }

        return $attrs;
    }

    /**
     * Get url to variants tab
     *
     * @return string
     */
    protected function getUrlToVariant()
    {
        $params = array(
            'product_id' => $this->getProduct()->getProductId(),
            'page'       => 'variants',
        );
        $fullUrl = \XLite\Core\Converter::buildFullURL(
            'product',
            '',
            $params,
            \XLite::getAdminScript()
        );

        $hashForUrl = sprintf('#data-%d-amount', $this->getId());

        return $fullUrl . $hashForUrl;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return ProductVariant
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set defaultPrice
     *
     * @param boolean $defaultPrice
     * @return ProductVariant
     */
    public function setDefaultPrice($defaultPrice)
    {
        $this->defaultPrice = $defaultPrice;
        return $this;
    }

    /**
     * Get defaultPrice
     *
     * @return boolean 
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return ProductVariant
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
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
     * Set defaultAmount
     *
     * @param boolean $defaultAmount
     * @return ProductVariant
     */
    public function setDefaultAmount($defaultAmount)
    {
        $this->defaultAmount = $defaultAmount;
        return $this;
    }

    /**
     * Get defaultAmount
     *
     * @return boolean 
     */
    public function getDefaultAmount()
    {
        return $this->defaultAmount;
    }

    /**
     * Set weight
     *
     * @param decimal $weight
     * @return ProductVariant
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return decimal 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set defaultWeight
     *
     * @param boolean $defaultWeight
     * @return ProductVariant
     */
    public function setDefaultWeight($defaultWeight)
    {
        $this->defaultWeight = $defaultWeight;
        return $this;
    }

    /**
     * Get defaultWeight
     *
     * @return boolean 
     */
    public function getDefaultWeight()
    {
        return $this->defaultWeight;
    }

    /**
     * Set defaultValue
     *
     * @param boolean $defaultValue
     * @return ProductVariant
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Get defaultValue
     *
     * @return boolean 
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return ProductVariant
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

    /**
     * Set image
     *
     * @param \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image $image
     * @return ProductVariant
     */
    public function setImage(\XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add attributeValueC
     *
     * @param \XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC
     * @return ProductVariant
     */
    public function addAttributeValueC(\XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC)
    {
        $this->attributeValueC[] = $attributeValueC;
        return $this;
    }

    /**
     * Get attributeValueC
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributeValueC()
    {
        return $this->attributeValueC;
    }

    /**
     * Add attributeValueS
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS
     * @return ProductVariant
     */
    public function addAttributeValueS(\XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS)
    {
        $this->attributeValueS[] = $attributeValueS;
        return $this;
    }

    /**
     * Get attributeValueS
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributeValueS()
    {
        return $this->attributeValueS;
    }

    /**
     * Add orderItems
     *
     * @param \XLite\Model\OrderItem $orderItems
     * @return ProductVariant
     */
    public function addOrderItems(\XLite\Model\OrderItem $orderItems)
    {
        $this->orderItems[] = $orderItems;
        return $this;
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }
}
