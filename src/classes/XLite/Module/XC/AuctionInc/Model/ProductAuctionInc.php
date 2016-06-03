<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Model;

/**
 * Product inventory
 *
 * @Entity
 * @Table  (name="product_auction_inc",
 *      indexes={
 *          @Index (name="product_id", columns={"product_id"})
 *      }
 * )
 */
class ProductAuctionInc extends \XLite\Model\AEntity
{
    /**
     * Inventory unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product (association)
     *
     * @var \XLite\Model\Product
     *
     * @OneToOne   (targetEntity="XLite\Model\Product", inversedBy="auctionIncData")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Calculation method
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $calculationMethod = 'C';

    /**
     * Package
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $package = 'T';

    /**
     * Dimensions
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $dimensions = array(0, 0, 0);

    /**
     * Weight UOM
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=3)
     */
    protected $weightUOM = 'LBS';

    /**
     * Dimensions UOM
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $dimensionsUOM = 'IN';

    /**
     * Insurable
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $insurable = 'N';

    /**
     * Origin code
     *
     * @var string
     *
     * @Column (type="string", length=20)
     */
    protected $originCode = 'default';

    /**
     * On-demand
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $onDemand = array();

    /**
     * Supplemental item handling mode
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $supplementalItemHandlingMode = '';

    /**
     * Supplemental item handling code
     *
     * @var string
     *
     * @Column (type="string", length=20)
     */
    protected $supplementalItemHandlingCode;

    /**
     * Supplemental item handling fee
     *
     * @var float
     *
     * @Column (type="money", precision=14, scale=4)
     */
    protected $supplementalItemHandlingFee;

    /**
     * Carrier accessorial fees
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $carrierAccessorialFees = array();

    /**
     * Fixed fee mode
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $fixedFeeMode = 'F';

    /**
     * Fixed fee code
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $fixedFeeCode;

    /**
     * Fixed fee 1
     *
     * @var float
     *
     * @Column (type="money", precision=14, scale=4)
     */
    protected $fixedFee1;

    /**
     * Fixed fee 2
     *
     * @var float
     *
     * @Column (type="money", precision=14, scale=4)
     */
    protected $fixedFee2;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        $config = \XLite\Core\Config::getInstance()->XC->AuctionInc;

        $this->setCalculationMethod($config->calculationMethod);
        $this->setPackage($config->package);
        $this->setInsurable($config->insurable);
        $this->setFixedFeeMode($config->fixedFeeMode);
        $this->setFixedFeeCode($config->fixedFeeCode);
        $this->setFixedFee1($config->fixedFee1);
        $this->setFixedFee2($config->fixedFee2);
    }

    /**
     * Set weight
     *
     * @param float $weight Weight
     *
     * @return void
     */
    public function setWeight($weight)
    {
        /** @var \XLite\Model\Product $product */
        $product = $this->getProduct();

        $product->setWeight($weight);
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        /** @var \XLite\Model\Product $product */
        $product = $this->getProduct();

        return $product->getWeight();
    }

    /**
     * Set dimensions
     *
     * @param array $dimensions Dimensions
     *
     * @return void
     */
    public function setDimensions($dimensions)
    {
        $this->dimensions = (is_array($dimensions) && 3 === count($dimensions))
            ? array_values($dimensions)
            : array(0, 0, 0);
    }

    /**
     * Get dimensions
     *
     * @return array
     */
    public function getDimensions()
    {
        return (is_array($this->dimensions) && 3 === count($this->dimensions))
            ? array_values($this->dimensions)
            : array(0, 0, 0);
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
     * Set calculationMethod
     *
     * @param string $calculationMethod
     * @return ProductAuctionInc
     */
    public function setCalculationMethod($calculationMethod)
    {
        $this->calculationMethod = $calculationMethod;
        return $this;
    }

    /**
     * Get calculationMethod
     *
     * @return string 
     */
    public function getCalculationMethod()
    {
        return $this->calculationMethod;
    }

    /**
     * Set package
     *
     * @param string $package
     * @return ProductAuctionInc
     */
    public function setPackage($package)
    {
        $this->package = $package;
        return $this;
    }

    /**
     * Get package
     *
     * @return string 
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set weightUOM
     *
     * @param string $weightUOM
     * @return ProductAuctionInc
     */
    public function setWeightUOM($weightUOM)
    {
        $this->weightUOM = $weightUOM;
        return $this;
    }

    /**
     * Get weightUOM
     *
     * @return string 
     */
    public function getWeightUOM()
    {
        return $this->weightUOM;
    }

    /**
     * Set dimensionsUOM
     *
     * @param string $dimensionsUOM
     * @return ProductAuctionInc
     */
    public function setDimensionsUOM($dimensionsUOM)
    {
        $this->dimensionsUOM = $dimensionsUOM;
        return $this;
    }

    /**
     * Get dimensionsUOM
     *
     * @return string 
     */
    public function getDimensionsUOM()
    {
        return $this->dimensionsUOM;
    }

    /**
     * Set insurable
     *
     * @param string $insurable
     * @return ProductAuctionInc
     */
    public function setInsurable($insurable)
    {
        $this->insurable = $insurable;
        return $this;
    }

    /**
     * Get insurable
     *
     * @return string 
     */
    public function getInsurable()
    {
        return $this->insurable;
    }

    /**
     * Set originCode
     *
     * @param string $originCode
     * @return ProductAuctionInc
     */
    public function setOriginCode($originCode)
    {
        $this->originCode = $originCode;
        return $this;
    }

    /**
     * Get originCode
     *
     * @return string 
     */
    public function getOriginCode()
    {
        return $this->originCode;
    }

    /**
     * Set onDemand
     *
     * @param array $onDemand
     * @return ProductAuctionInc
     */
    public function setOnDemand($onDemand)
    {
        $this->onDemand = $onDemand;
        return $this;
    }

    /**
     * Get onDemand
     *
     * @return array 
     */
    public function getOnDemand()
    {
        return $this->onDemand;
    }

    /**
     * Set supplementalItemHandlingMode
     *
     * @param string $supplementalItemHandlingMode
     * @return ProductAuctionInc
     */
    public function setSupplementalItemHandlingMode($supplementalItemHandlingMode)
    {
        $this->supplementalItemHandlingMode = $supplementalItemHandlingMode;
        return $this;
    }

    /**
     * Get supplementalItemHandlingMode
     *
     * @return string 
     */
    public function getSupplementalItemHandlingMode()
    {
        return $this->supplementalItemHandlingMode;
    }

    /**
     * Set supplementalItemHandlingCode
     *
     * @param string $supplementalItemHandlingCode
     * @return ProductAuctionInc
     */
    public function setSupplementalItemHandlingCode($supplementalItemHandlingCode)
    {
        $this->supplementalItemHandlingCode = $supplementalItemHandlingCode;
        return $this;
    }

    /**
     * Get supplementalItemHandlingCode
     *
     * @return string 
     */
    public function getSupplementalItemHandlingCode()
    {
        return $this->supplementalItemHandlingCode;
    }

    /**
     * Set supplementalItemHandlingFee
     *
     * @param float $supplementalItemHandlingFee
     * @return ProductAuctionInc
     */
    public function setSupplementalItemHandlingFee($supplementalItemHandlingFee)
    {
        $this->supplementalItemHandlingFee = $supplementalItemHandlingFee;
        return $this;
    }

    /**
     * Get supplementalItemHandlingFee
     *
     * @return float 
     */
    public function getSupplementalItemHandlingFee()
    {
        return $this->supplementalItemHandlingFee;
    }

    /**
     * Set carrierAccessorialFees
     *
     * @param array $carrierAccessorialFees
     * @return ProductAuctionInc
     */
    public function setCarrierAccessorialFees($carrierAccessorialFees)
    {
        $this->carrierAccessorialFees = $carrierAccessorialFees;
        return $this;
    }

    /**
     * Get carrierAccessorialFees
     *
     * @return array 
     */
    public function getCarrierAccessorialFees()
    {
        return $this->carrierAccessorialFees;
    }

    /**
     * Set fixedFeeMode
     *
     * @param string $fixedFeeMode
     * @return ProductAuctionInc
     */
    public function setFixedFeeMode($fixedFeeMode)
    {
        $this->fixedFeeMode = $fixedFeeMode;
        return $this;
    }

    /**
     * Get fixedFeeMode
     *
     * @return string 
     */
    public function getFixedFeeMode()
    {
        return $this->fixedFeeMode;
    }

    /**
     * Set fixedFeeCode
     *
     * @param string $fixedFeeCode
     * @return ProductAuctionInc
     */
    public function setFixedFeeCode($fixedFeeCode)
    {
        $this->fixedFeeCode = $fixedFeeCode;
        return $this;
    }

    /**
     * Get fixedFeeCode
     *
     * @return string 
     */
    public function getFixedFeeCode()
    {
        return $this->fixedFeeCode;
    }

    /**
     * Set fixedFee1
     *
     * @param float $fixedFee1
     * @return ProductAuctionInc
     */
    public function setFixedFee1($fixedFee1)
    {
        $this->fixedFee1 = $fixedFee1;
        return $this;
    }

    /**
     * Get fixedFee1
     *
     * @return float 
     */
    public function getFixedFee1()
    {
        return $this->fixedFee1;
    }

    /**
     * Set fixedFee2
     *
     * @param float $fixedFee2
     * @return ProductAuctionInc
     */
    public function setFixedFee2($fixedFee2)
    {
        $this->fixedFee2 = $fixedFee2;
        return $this;
    }

    /**
     * Get fixedFee2
     *
     * @return float 
     */
    public function getFixedFee2()
    {
        return $this->fixedFee2;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return ProductAuctionInc
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
