<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Model\EntityVersion\EntityVersionInterface;
use XLite\Core\Model\EntityVersion\EntityVersionTrait;
use XLite\Model\Product\ProductStockAvailabilityPolicy;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * The "product" model class
 *
 * @Entity
 * @Table  (name="products",
 *      indexes={
 *          @Index (name="sku", columns={"sku"}),
 *          @Index (name="price", columns={"price"}),
 *          @Index (name="weight", columns={"weight"}),
 *          @Index (name="free_shipping", columns={"free_shipping"}),
 *          @Index (name="customerArea", columns={"enabled","arrivalDate"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Product extends \XLite\Model\Base\Catalog implements \XLite\Model\Base\IOrderItem, EntityVersionInterface
{
    use EntityVersionTrait;
    use ExecuteCachedTrait;

    /**
     * Default amounts
     */
    const AMOUNT_DEFAULT_INV_TRACK = 1000;
    const AMOUNT_DEFAULT_LOW_LIMIT = 10;

    /**
     * Product unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $product_id;

    /**
     * Product price
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
     * Product SKU
     *
     * @var string
     *
     * @Column (type="string", length=32, nullable=true)
     */
    protected $sku;

    /**
     * Is product available or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Product weight
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $weight = 0.0000;

    /**
     * Is product shipped in separate box
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useSeparateBox = false;

    /**
     * Product box width
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxWidth = 0.0000;

    /**
     * Product box length
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxLength = 0.0000;

    /**
     * Product box height
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxHeight = 0.0000;

    /**
     * How many product items can be placed in a box
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $itemsPerBox = 1;

    /**
     * Flag: false - product is shippable, true - product is not shippable
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $free_shipping = false;

    /**
     * If false then the product is free from any taxes
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $taxable = true;

    /**
     * Custom javascript code
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $javascript = '';

    /**
     * Arrival date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $arrivalDate = 0;

    /**
     * Creation date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date = 0;

    /**
     * Update date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $updateDate = 0;

    /**
     * Is product need process or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $needProcess = true;

    /**
     * Relation to a CategoryProducts entities
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @OneToMany (targetEntity="XLite\Model\CategoryProducts", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"orderby" = "ASC"})
     */
    protected $categoryProducts;

    /**
     * Product order items
     *
     * @var \XLite\Model\OrderItem
     *
     * @OneToMany (targetEntity="XLite\Model\OrderItem", mappedBy="object")
     */
    protected $order_items;

    /**
     * Product images
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Image\Product\Image", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"orderby" = "ASC"})
     */
    protected $images;

    // {{{ Inventory properties

    /**
     * Is inventory tracking enabled or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $inventoryEnabled = true;

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
    protected $lowLimitEnabledCustomer = true;

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

    // }}}

    /**
     * Product class (relation)
     *
     * @var \XLite\Model\ProductClass
     *
     * @ManyToOne  (targetEntity="XLite\Model\ProductClass")
     * @JoinColumn (name="product_class_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $productClass;

    /**
     * Tax class (relation)
     *
     * @var \XLite\Model\TaxClass
     *
     * @ManyToOne  (targetEntity="XLite\Model\TaxClass")
     * @JoinColumn (name="tax_class_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $taxClass;

    /**
     * Attributes
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Attribute", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"position" = "ASC"})
     */
    protected $attributes;

    /**
     * Attribute value (checkbox)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueCheckbox", mappedBy="product", cascade={"all"})
     */
    protected $attributeValueC;

    /**
     * Attribute value (text)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueText", mappedBy="product", cascade={"all"})
     */
    protected $attributeValueT;

    /**
     * Attribute value (select)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect", mappedBy="product", cascade={"all"})
     */
    protected $attributeValueS;

    /**
     * Show product attributes in a separate tab
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $attrSepTab = true;

    /**
     * How much product is sold (used in Top selling products statistics)
     *
     * @var integer
     */
    protected $sold = 0;

    /**
     * Quick data
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\QuickData", mappedBy="product", cascade={"all"})
     */
    protected $quickData;

    /**
     * Storage of current attribute values according to the clear price will be calculated
     *
     * @var array
     */
    protected $attrValues = array();

    /**
     * Memberships
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\Membership", inversedBy="products")
     * @JoinTable (name="product_membership_links",
     *      joinColumns={@JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")}
     * )
     */
    protected $memberships;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * Meta description type
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $metaDescType = 'A';

    /**
     * Sales
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $sales = 0;

    /**
     * Flag to exporting entities
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xcPendingExport = false;

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        /** @var \XLite\Model\Product $newProduct */
        $newProduct = parent::cloneEntity();

        $this->cloneEntityScalar($newProduct);

        if (!$newProduct->update() || !$newProduct->getProductId()) {
            \XLite::getInstance()->doGlobalDie('Can not clone product');
        }

        $this->cloneEntityModels($newProduct);
        $this->cloneEntityCategories($newProduct);
        $this->cloneEntityAttributes($newProduct);
        $this->cloneEntityImages($newProduct);

        $newProduct->update();

        foreach ($newProduct->getCleanURLs() as $url) {
            $newProduct->getCleanURLs()->removeElement($url);
            \XLite\Core\Database::getEM()->remove($url);
        }

        $newProduct->setSales(0);

        return $newProduct;
    }

    /**
     * Clone entity (scalar fields)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityScalar(\XLite\Model\Product $newProduct)
    {
        $newProduct->setSku(\XLite\Core\Database::getRepo('XLite\Model\Product')->assembleUniqueSKU($this->getSku()));
        $newProduct->setName($this->getCloneName($this->getName()));
    }

    /**
     * Clone entity (model fields)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityModels(\XLite\Model\Product $newProduct)
    {

        $newProduct->setTaxClass($this->getTaxClass());
        $newProduct->setProductClass($this->getProductClass());
    }

    /**
     * Clone entity (categories)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityCategories(\XLite\Model\Product $newProduct)
    {
        foreach ($this->getCategories() as $category) {
            $link = new \XLite\Model\CategoryProducts;
            $link->setProduct($newProduct);
            $link->setCategory($category);
            $newProduct->addCategoryProducts($link);
            \XLite\Core\Database::getEM()->persist($link);
        }
    }

    /**
     * Clone entity (attributes)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityAttributes(\XLite\Model\Product $newProduct)
    {
        foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
            $methodGet = 'getAttributeValue' . $type;
            $methodAdd = 'addAttributeValue' . $type;
            foreach ($this->$methodGet() as $value) {
                if (!$value->getAttribute()->getProduct()) {
                    $newValue = $value->cloneEntity();
                    $newValue->setProduct($newProduct);
                    $newProduct->$methodAdd($newValue);
                    \XLite\Core\Database::getEM()->persist($newValue);
                }
            }
        }

        foreach ($this->getAttributes() as $attribute) {
            $class = $attribute->getAttributeValueClass($attribute->getType());
            $repo = \XLite\Core\Database::getRepo($class);
            $methodAdd = 'addAttributeValue' . $attribute->getType();
            $newAttribute = $attribute->cloneEntity();
            $newAttribute->setProduct($newProduct);
            $newProduct->addAttributes($newAttribute);
            \XLite\Core\Database::getEM()->persist($newAttribute);
            foreach ($repo->findBy(array('attribute' => $attribute, 'product' => $this)) as $value) {
                $newValue = $value->cloneEntity();
                $newValue->setProduct($newProduct);
                $newValue->setAttribute($newAttribute);
                $newProduct->$methodAdd($newValue);
                \XLite\Core\Database::getEM()->persist($newValue);
            }
        }
    }

    /**
     * Clone entity (images)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityImages(\XLite\Model\Product $newProduct)
    {
        foreach ($this->getImages() as $image) {
            $newImage = $image->cloneEntity();
            $newImage->setProduct($newProduct);
            $newProduct->addImages($newImage);
        }
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->categoryProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images           = new \Doctrine\Common\Collections\ArrayCollection();
        $this->order_items      = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships      = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributeValueC  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributeValueS  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributeValueT  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributes       = new \Doctrine\Common\Collections\ArrayCollection();
        $this->quickData        = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get object unique id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getProductId();
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Get price: modules should never overwrite this method
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get clear price: this price can be overwritten by modules
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getPrice();
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
     * Get quick data price
     *
     * @return float
     */
    public function getQuickDataPrice()
    {
        $price = $this->getClearPrice();

        foreach ($this->prepareAttributeValues() as $av) {
            if (is_object($av)) {
                $price += $av->getAbsoluteValue('price');
            }
        }

        return $price;
    }

    /**
     * Get clear weight
     *
     * @return float
     */
    public function getClearWeight()
    {
        return $this->getWeight();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getSoftTranslation()->getName();
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
     * Get quantity of the product
     *
     * @return integer
     */
    public function getQty()
    {
        return $this->getPublicAmount();
    }

    /**
     * Get image
     *
     * @return \XLite\Model\Image\Product\Image
     */
    public function getImage()
    {
        return $this->getImages()->get(0);
    }

    /**
     * Get public images
     *
     * @return array
     */
    public function getPublicImages()
    {
        return $this->getImages()->toArray();
    }

    /**
     * Get free shipping flag
     *
     * @return boolean
     */
    public function getFreeShipping()
    {
        return $this->free_shipping;
    }

    /**
     * Get shippable flag
     *
     * @return boolean
     */
    public function getShippable()
    {
        return !$this->getFreeShipping();
    }

    /**
     * Set shippable flag
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setShippable($value)
    {
        $this->setFreeShipping(!$value);
    }

    /**
     * Return true if product can be purchased
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return \XLite::isAdminZone() || $this->isPublicAvailable();
    }

    /**
     * Return true if product can be purchased in customer interface
     *
     * @return boolean
     */
    public function isPublicAvailable()
    {
        return $this->isVisible()
            && $this->availableInDate()
            && !$this->isOutOfStock();
    }

    /**
     * Check product visibility
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->getEnabled()
            && $this->hasAvailableMembership();
    }

    /**
     * Get membership Ids
     *
     * @return array
     */
    public function getMembershipIds()
    {
        $result = array();

        foreach ($this->getMemberships() as $membership) {
            $result[] = $membership->getMembershipId();
        }

        return $result;
    }

    /**
     * Flag if the category and active profile have the same memberships. (when category is displayed or hidden)
     *
     * @return boolean
     */
    public function hasAvailableMembership()
    {
        return 0 === $this->getMemberships()->count()
            || in_array(\XLite\Core\Auth::getInstance()->getMembershipId(), $this->getMembershipIds());
    }

    /**
     * Flag if the product is available according date/time
     *
     * @return boolean
     */
    public function availableInDate()
    {
        return !$this->getArrivalDate()
            || static::getUserTime() > $this->getArrivalDate();
    }

    /**
     * Check if product has image or not
     *
     * @return boolean
     */
    public function hasImage()
    {
        return null !== $this->getImage() && $this->getImage()->isPersistent();
    }

    /**
     * Return image URL
     *
     * @return string|void
     */
    public function getImageURL()
    {
        return $this->getImage() ? $this->getImage()->getURL() : null;
    }

    /**
     * Return random product category
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return \XLite\Model\Category
     */
    public function getCategory($categoryId = null)
    {
        $result = $this->getLink($categoryId)->getCategory();

        if (empty($result)) {
            $result = new \XLite\Model\Category();
        }

        return $result;
    }

    /**
     * Return random product category ID
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return integer
     */
    public function getCategoryId($categoryId = null)
    {
        return $this->getCategory($categoryId)->getCategoryId();
    }

    /**
     * Return list of product categories
     *
     * @return array
     */
    public function getCategories()
    {
        $result = array();

        foreach ($this->getCategoryProducts() as $cp) {
            $result[] = $cp->getCategory();
        }

        return $result;
    }

    // {{{ Inventory methods

    /**
     * Setter
     *
     * @param int $value Value to set
     *
     * @return void
     */
    public function setAmount($value)
    {
        $this->amount = $this->correctAmount($value);
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
        if ($this->getInventoryEnabled()) {
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
     * Get low available amount
     *
     * @return integer
     */
    public function getLowAvailableAmount()
    {
        return $this->getInventoryEnabled()
            ? min($this->getLowDefaultAmount(), $this->getAvailableAmount())
            : $this->getLowDefaultAmount();
    }

    /**
     * Check if product amount is less than its low limit
     *
     * @return boolean
     */
    public function isLowLimitReached()
    {
        return $this->getInventoryEnabled()
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
        $result = false;

        if (!defined('LC_CACHE_BUILDING')) {
            $currentController = \XLite::getInstance()->getController();
            $isControllerFirbidden = array_reduce(
                $this->getForbiddenControllers(),
                function ($carry, $controllerName) use ($currentController) {
                    return $carry ?: ($currentController instanceof $controllerName);
                },
                false
            );

            $result = \XLite\Core\Request::getInstance()->event !== 'import'
                && !$isControllerFirbidden;
        }

        return $result;
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
     * Get ProductStockAvailabilityPolicy associated with this product
     *
     * @return ProductStockAvailabilityPolicy
     */
    public function getStockAvailabilityPolicy()
    {
        return new ProductStockAvailabilityPolicy($this);
    }

    /**
     * Return product amount available to add to cart
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        return $this->getStockAvailabilityPolicy()->getAvailableAmount(Cart::getInstance());
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->getStockAvailabilityPolicy()->isOutOfStock(Cart::getInstance());
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->getInventoryEnabled()
            && $this->getLowLimitEnabledCustomer()
            && $this->getPublicAmount() <= $this->getLowLimitAmount()
            && !$this->isOutOfStock();
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

        $data['product'] = $this;
        $data['name']    = $this->getName();
        $data['sku']     = $this->getSKU();
        $data['amount']  = $this->getAmount();

        $params = array(
            'product_id' => $this->getProductId(),
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

    // }}}

    /**
     * Set sku and trim it to max length
     *
     * @param string $sku
     *
     * @return void
     */
    public function setSku($sku)
    {
        $this->sku = substr(
            $sku,
            0,
            \XLite\Core\Database::getRepo('XLite\Model\Product')->getFieldInfo('sku', 'length')
        );
    }

    /**
     * Get product Url
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getProductId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite\Core\Converter::buildURL('product', '', array('product_id' => $this->getProductId()))
            )
            : null;
    }

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURL($withAttributes = false)
    {
        return $this->getProductId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'product',
                        '',
                        $this->getParamsForFrontURL($withAttributes),
                        \XLite::getCustomerScript()
                    )
                )
            )
            : null;
    }

    /**
     * @return array
     */
    protected function getParamsForFrontURL($withAttributes = false)
    {
        $result = [
            'product_id'        => $this->getProductId(),
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
            $this->getAttrValues(),
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
     * Minimal available amount
     *
     * @return integer
     */
    public function getMinPurchaseLimit()
    {
        return 1;
    }

    /**
     * Maximal available amount
     *
     * @return integer
     */
    public function getMaxPurchaseLimit()
    {
        return (int) \XLite\Core\Config::getInstance()->General->default_purchase_limit;
    }

    /**
     * Return product position in category
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return integer|void
     */
    public function getOrderBy($categoryId = null)
    {
        $link = $this->getLink($categoryId);

        return $link ? $link->getOrderBy() : null;
    }

    /**
     * Count product images
     *
     * @return integer
     */
    public function countImages()
    {
        return count($this->getPublicImages());
    }

    /**
     * Try to fetch product description
     *
     * @return string
     */
    public function getCommonDescription()
    {
        return $this->getBriefDescription() ?: $this->getDescription();
    }

    /**
     * Get processed product brief description
     *
     * @return string
     */
    public function getProcessedBriefDescription()
    {
        $value = $this->getBriefDescription();

        return $value
            ? static::getPreprocessedValue($value)
            : $value;
    }

    /**
     * Get processed product description
     *
     * @return string
     */
    public function getProcessedDescription()
    {
        $value = $this->getDescription();

        return $value
            ? static::getPreprocessedValue($value)
            : $value;
    }

    /**
     * Get taxable basis
     *
     * @return float
     */
    public function getTaxableBasis()
    {
        return $this->getNetPrice();
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @PrePersist
     */
    public function prepareBeforeCreate()
    {
        $time = \XLite\Core\Converter::time();

        if (!$this->getDate()) {
            $this->setDate($time);
        }

        if (!$this->getArrivalDate()) {
            $this->setArrivalDate(mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time)));
        }

        $this->prepareBeforeUpdate();
    }

    /**
     * Prepare update date
     *
     * @return void
     *
     * @PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        $this->setUpdateDate(\XLite\Core\Converter::time());

        if (\XLite\Core\Converter::isEmptyString($this->getSku())) {
            $this->setSku(null);
        }
    }

    /**
     * Prepare remove
     *
     * @return void
     *
     * @PreRemove
     */
    public function prepareBeforeRemove()
    {
        // No default actions. May be used in modules
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
        if ($this->isLowLimitReached()
            && $this->isShouldSend()
            && $this->isInventoryChanged()
        ) {
            $this->sendLowLimitNotification();
            $this->updateLowStockUpdateTimestamp();
        }
    }

    /**
     * Check if product inventory changed
     *
     * @return boolean
     */
    public function isInventoryChanged()
    {
        $changeset = \XLite\Core\Database::getEM()
            ->getUnitOfWork()
            ->getEntityChangeSet($this);

        return isset($changeset['amount']);
    }

    /**
     * Set product class
     *
     * @param \XLite\Model\ProductClass $productClass Product class OPTIONAL
     *
     * @return \XLite\Model\Product
     */
    public function setProductClass(\XLite\Model\ProductClass $productClass = null)
    {
        if ($this->productClass
            && (
                !$productClass
                || $productClass->getId() !== $this->productClass->getId()
            )
        ) {
            $this->preprocessChangeProductClass();
        }

        $this->productClass = $productClass;

        return $this;
    }

    /**
     * Get attr values
     *
     * @return array
     */
    public function getAttrValues()
    {
        return $this->attrValues;
    }

    /**
     * Set attr values
     *
     * @param array $value Value
     *
     * @return void
     */
    public function setAttrValues($value)
    {
        $this->attrValues = $value;
    }

    /**
     * Sort editable attributes
     *
     * @param array $a Attribute A
     * @param array $b Attribute B
     *
     * @return boolean
     */
    protected function sortEditableAttributes($a, $b)
    {
        return $a['position'] >= $b['position'];
    }

    /**
     * Get editable attributes
     *
     * @return array
     */
    public function getEditableAttributes()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineEditableAttributes();
        }, ['getEditableAttributes', $this->getProductId()]);
    }

    /**
     * @return array
     */
    protected function defineEditableAttributes()
    {
        $result = [];

        foreach ((array) \XLite\Model\Attribute::getTypes() as $type => $name) {
            $class = \XLite\Model\Attribute::getAttributeValueClass($type);
            if (is_subclass_of($class, 'XLite\Model\AttributeValue\Multiple')) {
                $result[] = \XLite\Core\Database::getRepo($class)->findMultipleAttributes($this);

            } elseif ('\XLite\Model\AttributeValue\AttributeValueText' === $class) {
                $result[] = \XLite\Core\Database::getRepo($class)->findEditableAttributes($this);
            }
        }

        $result = (array) call_user_func_array('array_merge', $result);
        usort($result, [$this, 'sortEditableAttributes']);

        if ($result) {
            foreach ($result as $k => $v) {
                $result[$k] = $v[0];
            }
        }

        return $result;
    }

    /**
     * Get editable attributes ids
     *
     * @return array
     */
    public function getEditableAttributesIds()
    {
        $result = array();

        foreach ($this->getEditableAttributes() as $a) {
            $result[] = $a->getId();
        }
        sort($result);

        return $result;
    }

    /**
     * Check - product has editable attrbiutes or not
     *
     * @return boolean
     */
    public function hasEditableAttributes()
    {
        return 0 < count($this->getEditableAttributes());
    }

    /**
     * Get multiple attributes
     *
     * @return array
     */
    public function getMultipleAttributes()
    {
        $result = array();

        foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
            $class = \XLite\Model\Attribute::getAttributeValueClass($type);
            if (is_subclass_of($class, 'XLite\Model\AttributeValue\Multiple')) {
                $result = array_merge(
                    $result,
                    \XLite\Core\Database::getRepo($class)->findMultipleAttributes($this)
                );
            }
        }

        if ($result) {
            foreach ($result as $k => $v) {
                $result[$k] = $v[0];
            }
        }

        return $result;
    }

    /**
     * Get multiple attributes ids
     *
     * @return array
     */
    public function getMultipleAttributesIds()
    {
        $result = array();

        foreach ($this->getMultipleAttributes() as $a) {
            $result[] = $a->getId();
        }
        sort($result);

        return $result;
    }

    /**
     * Check - product has multiple attributes or not
     *
     * @return boolean
     */
    public function hasMultipleAttributes()
    {
        return 0 < count($this->getMultipleAttributes());
    }

    /**
     * Update quick data
     *
     * @return void
     */
    public function updateQuickData()
    {
        if ($this->isPersistent()) {
            \XLite\Core\QuickData::getInstance()->updateProductData($this);
        }
    }

    /**
     * @return boolean
     */
    protected function showPlaceholderOption()
    {
        if (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'quicklook') {

            return \XLite::getController()->getTarget() !== 'product';

        } elseif (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'product_page') {

            return true;
        }

        return false;
    }

    /**
     * Prepare attribute values
     *
     * @param array $ids Request-based selected attribute values OPTIONAL
     *
     * @return array
     */
    public function prepareAttributeValues($ids = array())
    {
        $attributeValues = array();
        foreach ($this->getEditableAttributes() as $a) {
            if ($a->getType() === \XLite\Model\Attribute::TYPE_TEXT) {
                $value = isset($ids[$a->getId()]) ? $ids[$a->getId()] : $a->getAttributeValue($this)->getValue();
                $attributeValues[$a->getId()] = array(
                    'attributeValue' => $a->getAttributeValue($this),
                    'value'          => $value,
                );

            } else {
                if (!$this->showPlaceholderOption()) {
                    $attributeValues[$a->getId()] = $a->getDefaultAttributeValue($this);
                }

                if (isset($ids[$a->getId()])) {
                    foreach ($a->getAttributeValue($this) as $av) {
                        if ($av->getId() == $ids[$a->getId()]) {
                            $attributeValues[$a->getId()] = $av;
                            break;
                        }
                    }
                }
            }
        }

        return $attributeValues;
    }

    /**
     * Define the specific clone name for the product
     *
     * @param string $name Product name
     *
     * @return string
     */
    protected function getCloneName($name)
    {
        return $name . ' [ clone ]';
    }

    /**
     * Preprocess change product class
     *
     * @return void
     */
    protected function preprocessChangeProductClass()
    {
        if ($this->productClass) {
            foreach ($this->productClass->getAttributes() as $a) {
                $class = $a->getAttributeValueClass($a->getType());
                $repo = \XLite\Core\Database::getRepo($class);
                foreach ($repo->findBy(array('product' => $this, 'attribute' => $a)) as $v) {
                    $repo->delete($v);
                }
            }
        }
    }

    /**
     * Return certain Product <--> Category association
     *
     * @param integer|null $categoryId Category ID
     *
     * @return \XLite\Model\CategoryProducts|void
     */
    protected function findLinkByCategoryId($categoryId)
    {
        $result = null;

        foreach ($this->getCategoryProducts() as $cp) {
            if ($cp->getCategory() && $cp->getCategory()->getCategoryId() == $categoryId) {
                $result = $cp;
            }
        }

        return $result;
    }

    /**
     * Return certain Product <--> Category association
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return \XLite\Model\CategoryProducts
     */
    protected function getLink($categoryId = null)
    {
        $result = empty($categoryId)
            ? $this->getCategoryProducts()->first()
            : $this->findLinkByCategoryId($categoryId);

        if (empty($result)) {
            $result = new \XLite\Model\CategoryProducts();
        }

        return $result;
    }

    /**
     * Returns position of the product in the given category
     *
     * @param integer $category
     *
     * @return integer
     */
    public function getPosition($category)
    {
        return $this->getOrderBy($category);
    }

    /**
     * Sets the position of the product in the given category
     *
     * @param array $value
     *
     * @return void
     */
    public function setPosition($value)
    {
        $link = $this->getLink($value['category']);
        $link->setProduct($this);
        $link->setOrderby($value['position']);

        \XLite\Core\Database::getEM()->flush($link);
    }

    /**
     * Returns meta description
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return 'A' === $this->getMetaDescType()
            ? strip_tags($this->getCommonDescription())
            : $this->getSoftTranslation()->getMetaDesc();
    }

    /**
     * Returns meta description type
     *
     * @return string
     */
    public function getMetaDescType()
    {
        $result = $this->metaDescType;

        if (!$result) {
            $metaDescPresent = array_reduce($this->getTranslations()->toArray(), function ($carry, $item) {
                return $carry ?: (bool) $item->getMetaDesc();
            }, false);

            $result = $metaDescPresent ? 'C' : 'A';
        }

        return $result;
    }

    // {{{ Sales statistics

    /**
     * Set sales
     *
     * @param integer $sales Sales
     */
    public function setSales($sales)
    {
        $this->sales = max(0, $sales);
    }

    /**
     * Return sales
     *
     * @return integer
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Update sales
     */
    public function updateSales()
    {
        $this->setSales(
            $this->getRepository()->findSalesByProduct($this)
        );
    }

    // }}}

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Product
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
     * Set weight
     *
     * @param float $weight
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = (float) $weight;
        return $this;
    }

    /**
     * Set useSeparateBox
     *
     * @param boolean $useSeparateBox
     * @return Product
     */
    public function setUseSeparateBox($useSeparateBox)
    {
        $this->useSeparateBox = $useSeparateBox;
        return $this;
    }

    /**
     * Get useSeparateBox
     *
     * @return boolean 
     */
    public function getUseSeparateBox()
    {
        return $this->useSeparateBox;
    }

    /**
     * Set boxWidth
     *
     * @param decimal $boxWidth
     * @return Product
     */
    public function setBoxWidth($boxWidth)
    {
        $this->boxWidth = $boxWidth;
        return $this;
    }

    /**
     * Get boxWidth
     *
     * @return decimal 
     */
    public function getBoxWidth()
    {
        return $this->boxWidth;
    }

    /**
     * Set boxLength
     *
     * @param decimal $boxLength
     * @return Product
     */
    public function setBoxLength($boxLength)
    {
        $this->boxLength = $boxLength;
        return $this;
    }

    /**
     * Get boxLength
     *
     * @return decimal 
     */
    public function getBoxLength()
    {
        return $this->boxLength;
    }

    /**
     * Set boxHeight
     *
     * @param decimal $boxHeight
     * @return Product
     */
    public function setBoxHeight($boxHeight)
    {
        $this->boxHeight = $boxHeight;
        return $this;
    }

    /**
     * Get boxHeight
     *
     * @return decimal 
     */
    public function getBoxHeight()
    {
        return $this->boxHeight;
    }

    /**
     * Set itemsPerBox
     *
     * @param integer $itemsPerBox
     * @return Product
     */
    public function setItemsPerBox($itemsPerBox)
    {
        $this->itemsPerBox = $itemsPerBox;
        return $this;
    }

    /**
     * Get itemsPerBox
     *
     * @return integer 
     */
    public function getItemsPerBox()
    {
        return $this->itemsPerBox;
    }

    /**
     * Set free_shipping
     *
     * @param boolean $freeShipping
     * @return Product
     */
    public function setFreeShipping($freeShipping)
    {
        $this->free_shipping = (boolean) $freeShipping;
        return $this;
    }

    /**
     * Set taxable
     *
     * @param boolean $taxable
     * @return Product
     */
    public function setTaxable($taxable)
    {
        $this->taxable = $taxable;
        return $this;
    }

    /**
     * Get taxable
     *
     * @return boolean 
     */
    public function getTaxable()
    {
        return $this->taxable;
    }

    /**
     * Set javascript
     *
     * @param text $javascript
     * @return Product
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;
        return $this;
    }

    /**
     * Get javascript
     *
     * @return text 
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * Set arrivalDate
     *
     * @param integer $arrivalDate
     * @return Product
     */
    public function setArrivalDate($arrivalDate)
    {
        $this->arrivalDate = $arrivalDate;
        return $this;
    }

    /**
     * Get arrivalDate
     *
     * @return integer 
     */
    public function getArrivalDate()
    {
        return $this->arrivalDate;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Product
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set updateDate
     *
     * @param integer $updateDate
     * @return Product
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * Get updateDate
     *
     * @return integer 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set needProcess
     *
     * @param boolean $needProcess
     * @return Product
     */
    public function setNeedProcess($needProcess)
    {
        $this->needProcess = $needProcess;
        return $this;
    }

    /**
     * Get needProcess
     *
     * @return boolean 
     */
    public function getNeedProcess()
    {
        return $this->needProcess;
    }

    /**
     * Set attrSepTab
     *
     * @param boolean $attrSepTab
     * @return Product
     */
    public function setAttrSepTab($attrSepTab)
    {
        $this->attrSepTab = $attrSepTab;
        return $this;
    }

    /**
     * Get attrSepTab
     *
     * @return boolean 
     */
    public function getAttrSepTab()
    {
        return $this->attrSepTab;
    }

    /**
     * Set metaDescType
     *
     * @param string $metaDescType
     * @return Product
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
        return $this;
    }

    /**
     * Set xcPendingExport
     *
     * @param boolean $xcPendingExport
     * @return Product
     */
    public function setXcPendingExport($xcPendingExport)
    {
        $this->xcPendingExport = $xcPendingExport;
        return $this;
    }

    /**
     * Get xcPendingExport
     *
     * @return boolean 
     */
    public function getXcPendingExport()
    {
        return $this->xcPendingExport;
    }

    /**
     * Add categoryProducts
     *
     * @param \XLite\Model\CategoryProducts $categoryProducts
     * @return Product
     */
    public function addCategoryProducts(\XLite\Model\CategoryProducts $categoryProducts)
    {
        $this->categoryProducts[] = $categoryProducts;
        return $this;
    }

    /**
     * Get categoryProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategoryProducts()
    {
        return $this->categoryProducts;
    }

    /**
     * @param \XLite\Model\Category[] $categories
     */
    public function addCategoryProductsLinksByCategories($categories)
    {
        foreach ($categories as $category) {
            $category->updateLastUsage();

            if (!$this->hasCategoryProductsLinkByCategory($category)) {
                $categoryProduct = new \XLite\Model\CategoryProducts();
                $categoryProduct->setProduct($this);
                $categoryProduct->setCategory($category);

                $this->addCategoryProducts($categoryProduct);
            }
        }
    }

    /**
     * @param \XLite\Model\Category $category
     */
    public function addCategory($category)
    {
        $categoryProduct = new \XLite\Model\CategoryProducts();
        $categoryProduct->setProduct($this);
        $categoryProduct->setCategory($category);

        $this->addCategoryProducts($categoryProduct);
    }

    /**
     * @param \XLite\Model\Category[] $categories
     */
    public function removeCategoryProductsLinksByCategories($categories)
    {
        $categoryProductsLinks = [];

        foreach ($categories as $category) {
            $categoryProductsLink = $this->findCategoryProductsLinkByCategory($category);
            if ($categoryProductsLink) {
                $categoryProductsLinks[] = $categoryProductsLink;
            }
        }

        if ($categoryProductsLinks) {
            \XLite\Core\Database::getRepo('XLite\Model\CategoryProducts')->deleteInBatch(
                $categoryProductsLinks
            );
        }
    }

    /**
     * @param \XLite\Model\Category[] $categories
     */
    public function replaceCategoryProductsLinksByCategories($categories)
    {
        $categoriesToAdd = [];
        foreach ($categories as $category) {
            $category->updateLastUsage();

            if (!$this->hasCategoryProductsLinkByCategory($category)) {
                $categoriesToAdd[] = $category;
            }
        }

        $categoriesIds = array_map(function ($item) {
            /** @var \XLite\Model\Category $item */
            return (int) $item->getCategoryId();
        }, $categories);

        $categoryProductsLinksToDelete = [];
        foreach ($this->getCategoryProducts() as $categoryProduct) {
            if (!in_array((int) $categoryProduct->getCategory()->getCategoryId(), $categoriesIds, true)) {

                $categoryProductsLinksToDelete[] = $categoryProduct;
            }
        }

        if ($categoryProductsLinksToDelete) {
            \XLite\Core\Database::getRepo('XLite\Model\CategoryProducts')->deleteInBatch(
                $categoryProductsLinksToDelete
            );
        }

        if ($categoriesToAdd) {
            $this->addCategoryProductsLinksByCategories($categoriesToAdd);
        }
    }

    /**
     * @param \XLite\Model\Category $category
     *
     * @return bool
     */
    public function hasCategoryProductsLinkByCategory($category)
    {
        return (boolean) $this->findCategoryProductsLinkByCategory($category);
    }

    /**
     * @param \XLite\Model\Category $category
     *
     * @return \XLite\Model\CategoryProducts
     */
    public function findCategoryProductsLinkByCategory($category)
    {
        /** @var \XLite\Model\CategoryProducts $categoryProduct */
        foreach ($this->getCategoryProducts() as $categoryProduct) {
            if ((int) $categoryProduct->getCategory()->getCategoryId() === (int) $category->getCategoryId()) {

                return $categoryProduct;
            }
        }

        return null;
    }

    /**
     * Add order_items
     *
     * @param \XLite\Model\OrderItem $orderItems
     * @return Product
     */
    public function addOrderItems(\XLite\Model\OrderItem $orderItems)
    {
        $this->order_items[] = $orderItems;
        return $this;
    }

    /**
     * Get order_items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderItems()
    {
        return $this->order_items;
    }

    /**
     * Add images
     *
     * @param \XLite\Model\Image\Product\Image $images
     * @return Product
     */
    public function addImages(\XLite\Model\Image\Product\Image $images)
    {
        $this->images[] = $images;
        return $this;
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Get productClass
     *
     * @return \XLite\Model\ProductClass 
     */
    public function getProductClass()
    {
        return $this->productClass;
    }

    /**
     * Set taxClass
     *
     * @param \XLite\Model\TaxClass $taxClass
     * @return Product
     */
    public function setTaxClass(\XLite\Model\TaxClass $taxClass = null)
    {
        $this->taxClass = $taxClass;
        return $this;
    }

    /**
     * Get taxClass
     *
     * @return \XLite\Model\TaxClass 
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Add attributes
     *
     * @param \XLite\Model\Attribute $attributes
     * @return Product
     */
    public function addAttributes(\XLite\Model\Attribute $attributes)
    {
        $this->attributes[] = $attributes;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add attributeValueC
     *
     * @param \XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC
     * @return Product
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
     * Add attributeValueT
     *
     * @param \XLite\Model\AttributeValue\AttributeValueText $attributeValueT
     * @return Product
     */
    public function addAttributeValueT(\XLite\Model\AttributeValue\AttributeValueText $attributeValueT)
    {
        $this->attributeValueT[] = $attributeValueT;
        return $this;
    }

    /**
     * Get attributeValueT
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributeValueT()
    {
        return $this->attributeValueT;
    }

    /**
     * Add attributeValueS
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS
     * @return Product
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
     * Add quickData
     *
     * @param \XLite\Model\QuickData $quickData
     * @return Product
     */
    public function addQuickData(\XLite\Model\QuickData $quickData)
    {
        $this->quickData[] = $quickData;
        return $this;
    }

    /**
     * Get quickData
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuickData()
    {
        return $this->quickData;
    }

    /**
     * Add memberships
     *
     * @param \XLite\Model\Membership $memberships
     * @return Product
     */
    public function addMemberships(\XLite\Model\Membership $memberships)
    {
        $this->memberships[] = $memberships;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * @param \XLite\Model\Membership[] $memberships
     */
    public function addMembershipsByMemberships($memberships)
    {
        foreach ($memberships as $membership) {
            if (!$this->hasMembershipByMembership($membership)) {
                $this->addMemberships($membership);
            }
        }
    }

    /**
     * @param \XLite\Model\Membership[] $memberships
     */
    public function removeMembershipsByMemberships($memberships)
    {
        foreach ($memberships as $membership) {
            if ($this->hasMembershipByMembership($membership)) {
                $this->getMemberships()->removeElement($membership);
            }
        }
    }

    /**
     * @param \XLite\Model\Membership[] $memberships
     */
    public function replaceMembershipsByMemberships($memberships)
    {
        $ids = array_map(function ($item) {
            /** @var \XLite\Model\Membership $item */
            return (int) $item->getMembershipId();
        }, $memberships);

        $toRemove = [];
        foreach ($this->getMemberships() as $membership) {
            if (!in_array((int) $membership->getMembershipId(), $ids, true)) {
                $toRemove[] = $membership;
            }
        }

        $this->addMembershipsByMemberships($memberships);
        $this->removeMembershipsByMemberships($toRemove);
    }

    /**
     * @param \XLite\Model\Membership $membership
     *
     * @return boolean
     */
    public function hasMembershipByMembership($membership)
    {
        return (boolean) $this->getMembershipByMembership($membership);
    }

    /**
     * @param \XLite\Model\Membership $membership
     *
     * @return mixed|null
     */
    public function getMembershipByMembership($membership)
    {
        foreach ($this->getMemberships() as $membershipObject) {
            if ((int) $membership->getMembershipId() === (int) $membershipObject->getMembershipId()) {
                return $membershipObject;
            }
        }

        return null;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return Product
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;
        return $this;
    }

    /**
     * Get cleanURLs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
    }

    /**
     * Set inventoryEnabled
     *
     * @param boolean $inventoryEnabled
     * @return Product
     */
    public function setInventoryEnabled($inventoryEnabled)
    {
        $this->inventoryEnabled = $inventoryEnabled;
        return $this;
    }

    /**
     * Get inventoryEnabled
     *
     * @return boolean 
     */
    public function getInventoryEnabled()
    {
        return $this->inventoryEnabled;
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
     * @return Product
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
     * @return Product
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
     * Checks if given property is available to modification through layout editor mode.
     *
     * @param  string  $property Checked entity property
     * @return boolean
     */
    public function getFieldMetadata($property)
    {
        return array(
            'data-model' => $this->getEntityName(),
            'data-identifier' => $this->getProductId(),
            'data-property' => $property,
        );
    }
}
