<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

use XLite\View\FormField\Input\PriceOrPercent;

/**
 * Shipping method model
 *
 * @Entity
 * @Table  (name="shipping_methods",
 *      indexes={
 *          @Index (name="processor", columns={"processor"}),
 *          @Index (name="carrier", columns={"carrier"}),
 *          @Index (name="enabled", columns={"enabled"}),
 *          @Index (name="position", columns={"position"})
 *      }
 * )
 */
class Method extends \XLite\Model\Base\I18n
{
    /**
     * A unique ID of the method
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $method_id;

    /**
     * Processor class name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $processor = '';

    /**
     * Carrier of the method (for instance, "UPS" or "USPS")
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $carrier = '';

    /**
     * Unique code of shipping method (within processor space)
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $code = '';

    /**
     * Whether the method is enabled or disabled
     *
     * @var string
     *
     * @Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * A position of the method among other registered methods
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Shipping rates (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Model\Shipping\Markup", mappedBy="shipping_method", cascade={"all"})
     */
    protected $shipping_markups;

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
     * Added status
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $added = false;

    /**
     * Specific module family name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $moduleName = '';

    /**
     * Flag:
     *   1 - method has been got from marketplace,
     *   0 - method has been added after distr or module installation
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $fromMarketplace = false;

    /**
     * Icon URL (used for methods from marketplace)
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $iconURL;

    /**
     * Table type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=3, nullable=true)
     */
    protected $tableType;

    /**
     * Handling fee (surcharge) for online methods
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $handlingFee = 0;

    /**
     * Handling fee type(absolute or percent)
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $handlingFeeType = \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->shipping_markups = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get processor class object
     *
     * @return null|\XLite\Model\Shipping\Processor\AProcessor
     */
    public function getProcessorObject()
    {
        return \XLite\Model\Shipping::getProcessorObjectByProcessorId($this->getProcessor());
    }

    /**
     * Returns processor module
     *
     * @return \XLite\Model\Module
     */
    public function getProcessorModule()
    {
        $module = null;
        $processor = $this->getProcessorObject();

        if ($processor) {
            $module = $this->getProcessorObject()->getModule();
        } else {
            $moduleName = $this->getModuleName();

            if ($moduleName) {
                list ($author, $name) = explode('_', $moduleName);
                /** @var \XLite\Model\Repo\Module $repo */
                $repo = \XLite\Core\Database::getRepo('XLite\Model\Module');
                $module = $repo->findModuleByName($author . '\\' . $name);
            }
        }

        return $module;
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @return string
     */
    public function getAdminIconURL()
    {
        $url = $this->getProcessorObject()
            ? $this->getProcessorObject()->getAdminIconURL($this)
            : $this->getIconURL();

        if (true === $url || null === $url) {
            $module = $this->getProcessorModule();
            $url = $module
                ? \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    'modules/' . $module->getAuthor() . '/' . $module->getName() . '/method_icon.jpg'
                )
                : null;
        }

        return $url;
    }

    /**
     * Return true if rates exists for this shipping method
     *
     * @return boolean
     */
    public function hasRates()
    {
        return (bool) $this->getRatesCount();
    }

    /**
     * Get count of rates specified for this shipping method
     *
     * @return integer
     */
    public function getRatesCount()
    {
        return count($this->getShippingMarkups());
    }

    /**
     * Check if method is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled
            && (null === $this->getProcessorObject() || $this->getProcessorObject()->isConfigured());
    }

    /**
     * Returns present status
     *
     * @return boolean
     */
    public function isAdded()
    {
        return (bool) $this->added;
    }

    /**
     * Set present status
     *
     * @param boolean $value Value
     */
    public function setAdded($value)
    {
        $changed = $this->added !== $value;
        $this->added = (bool) $value;

        if (!$this->added) {
            $this->setEnabled(false);

        } elseif ($changed) {
            $last = $this->getRepository()->findOneCarrierMaxPosition();
            $this->setPosition($last ? $last->getPosition() + 1 : 0);
        }
    }

    /**
     * Check if shipping method is from marketplace
     *
     * @return bool
     */
    public function isFromMarketplace()
    {
        return (bool) $this->getFromMarketplace();
    }

    /**
     * Returns module author and name (with underscore as separator)
     *
     * @return string
     */
    public function getModuleName()
    {
        $result = $this->moduleName;

        if (!$this->isFromMarketplace()) {
            $processor = $this->getProcessorObject();
            if ($processor) {
                $module = $processor->getModule();
                if ($module) {
                    $result = $module->getAuthor() . '_' . $module->getName();
                }
            }
        }

        return $result;
    }

    /**
     * Return parent method for online carrier service
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function getParentMethod()
    {
        return 'offline' !== $this->getProcessor() && '' !== $this->getCarrier()
            ? $this->getRepository()->findOnlineCarrier($this->getProcessor())
            : null;
    }

    /**
     * Retuns children methods for online carrier
     *
     * @return array
     */
    public function getChildrenMethods()
    {
        return 'offline' !== $this->getProcessor() && '' === $this->getCarrier()
            ? $this->getRepository()->findMethodsByProcessor($this->getProcessor(), false)
            : array();
    }

    /**
     * Returns handling fee
     *
     * @return float
     */
    public function getHandlingFee()
    {
        return [
            PriceOrPercent::PRICE_VALUE => $this->getHandlingFeeValue(),
            PriceOrPercent::TYPE_VALUE => $this->getHandlingFeeType()
        ];
    }

    /**
     * Returns handling fee
     *
     * @return float
     */
    public function getHandlingFeeValue()
    {
        $parentMethod = $this->getParentMethod();

        return $parentMethod ? $parentMethod->getHandlingFeeValue() : $this->handlingFee;
    }

    /**
     * Returns handling fee
     *
     * @param float
     *
     * @return float
     */
    public function setHandlingFeeValue($value)
    {
        $this->handlingFee = $value;

        return $this;
    }

    /**
     * Get method_id
     *
     * @return integer 
     */
    public function getMethodId()
    {
        return $this->method_id;
    }

    /**
     * Set processor
     *
     * @param string $processor
     * @return Method
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * Get processor
     *
     * @return string 
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Set carrier
     *
     * @param string $carrier
     * @return Method
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
        return $this;
    }

    /**
     * Get carrier
     *
     * @return string 
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Method
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Method
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
     * Set position
     *
     * @param integer $position
     * @return Method
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get added
     *
     * @return boolean
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     * @return Method
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * Set fromMarketplace
     *
     * @param boolean $fromMarketplace
     * @return Method
     */
    public function setFromMarketplace($fromMarketplace)
    {
        $this->fromMarketplace = $fromMarketplace;
        return $this;
    }

    /**
     * Get fromMarketplace
     *
     * @return boolean 
     */
    public function getFromMarketplace()
    {
        return $this->fromMarketplace;
    }

    /**
     * Set iconURL
     *
     * @param string $iconURL
     * @return Method
     */
    public function setIconURL($iconURL)
    {
        $this->iconURL = $iconURL;
        return $this;
    }

    /**
     * Get iconURL
     *
     * @return string 
     */
    public function getIconURL()
    {
        return $this->iconURL;
    }

    /**
     * Set tableType
     *
     * @param string $tableType
     * @return Method
     */
    public function setTableType($tableType)
    {
        $this->tableType = $tableType;
        return $this;
    }

    /**
     * Get tableType
     *
     * @return string 
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * Set handlingFee
     *
     * @param array $handlingFee
     * @return Method
     */
    public function setHandlingFee($handlingFee)
    {
        $this->setHandlingFeeValue(
            isset($handlingFee[PriceOrPercent::PRICE_VALUE])
                ? $handlingFee[PriceOrPercent::PRICE_VALUE]
                : 0
        );

        $this->setHandlingFeeType(
            isset($handlingFee[PriceOrPercent::TYPE_VALUE])
                ? $handlingFee[PriceOrPercent::TYPE_VALUE]
                : \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE
        );

        return $this;
    }

    /**
     * Return handling fee type, possible values:
     * \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE
     * \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_PERCENT
     *
     * @return string
     */
    public function getHandlingFeeType()
    {
        $parentMethod = $this->getParentMethod();

        return $parentMethod ? $parentMethod->getHandlingFeeType() : $this->handlingFeeType;
    }

    /**
     * Set shipping handling fee type (% or absolute)
     *
     * @param string $handlingFeeType
     * @return $this
     */
    public function setHandlingFeeType($handlingFeeType)
    {
        $this->handlingFeeType = $handlingFeeType;
        return $this;
    }

    /**
     * Add shipping_markups
     *
     * @param \XLite\Model\Shipping\Markup $shippingMarkups
     * @return Method
     */
    public function addShippingMarkups(\XLite\Model\Shipping\Markup $shippingMarkups)
    {
        $this->shipping_markups[] = $shippingMarkups;
        return $this;
    }

    /**
     * Get shipping_markups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShippingMarkups()
    {
        return $this->shipping_markups;
    }

    /**
     * Set taxClass
     *
     * @param \XLite\Model\TaxClass $taxClass
     * @return Method
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
}
