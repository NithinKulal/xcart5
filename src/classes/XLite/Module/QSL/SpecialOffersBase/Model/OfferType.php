<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\Model;

/**
 * Special Offer Type model.
 *
 * It stores information on what special offer logic should be used and offer settings.
 *
 * @Entity (repositoryClass="\XLite\Module\QSL\SpecialOffersBase\Model\Repo\OfferType")
 * @Table  (name="special_offer_types",
 *      indexes={
 *          @Index (name="type_id", columns={"type_id"}),
 *          @Index (name="processorClass", columns={"processorClass"}),
 *          @Index (name="position", columns={"position"}),
 *          @Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class OfferType extends \XLite\Model\Base\I18n
{
    /**
     * Unique identifier of the offer type.
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $type_id;

    /**
     * Whether the offer type is enabled, or not.
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Name of the class that implements the offer logic.
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $processorClass;
    
    /**
     * Name of the class that implements the View Model logic.
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $viewModelClass;

    /**
     * Position of the exit offer among other ones in the list.
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Special offers of this type.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer", mappedBy="offerType", cascade={"remove"})
     */
    protected $specialOffers; // when the offer type is deleted, the operation cascades to all related offers (via Doctrine)
    
    /**
     * Cached processor class instance.
     * 
     * @var \XLite\Module\QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
     */
    protected $processor;

    /**
     * Checks if the special offer has correct processor and view model classes.
     * 
     * @return boolean
     */
    public function hasAllRequiredClasses()
    {
        return \XLite\Core\Operator::isClassExists($this->getProcessorClass())
            && \XLite\Core\Operator::isClassExists($this->getViewModelClass());
    }
    
    /**
     * Returns the processor class for this special offer type.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
     */
    public function getProcessor()
    {
        if (!isset($this->processor)) {
            $this->processor = $this->factoryProcessor();
        }

        return $this->processor;
    }

    /**
     * Creates a new instance of the processor class for this special offer type.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
     */
    protected function factoryProcessor()
    {
        $class = $this->getProcessorClass();

        return \XLite\Core\Operator::isClassExists($class) ? new $class : null;
    }

    /**
     * Returns the model identifier.
     *
     * @return integer 
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Confgiures whether the special offer type is enabled, or disabled.
     *
     * @param boolean $enabled New state
     *
     * @return OfferType
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Checks if the special offer type is enabled, or not.
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Updates the name of the processor class for the special offer.
     *
     * @param string $processorClass Class name
     *
     * @return OfferType
     */
    public function setProcessorClass($processorClass)
    {
        $this->processorClass = $processorClass;

        return $this;
    }

    /**
     * Returns the name of the processor class for the special offer type.
     *
     * @return string 
     */
    public function getProcessorClass()
    {
        return $this->processorClass;
    }

    /**
     * Updates the name of the view class for the special offer type.
     *
     * @param string $viewModelClass Class name
     *
     * @return OfferType
     */
    public function setViewModelClass($viewModelClass)
    {
        $this->viewModelClass = $viewModelClass;

        return $this;
    }

    /**
     * Returns the name of the view class for the special offer type.
     *
     * @return string 
     */
    public function getViewModelClass()
    {
        return $this->viewModelClass;
    }

    /**
     * Updates the position of the special offer type among others.
     *
     * @param integer $position New position
     * 
     * @return OfferType
     */
    public function setPosition($position)
    {
        $this->position = $position;
        
        return $this;
    }

    /**
     * Returns the position of the special offer type among others.
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Associates a special offer with the special offer type.
     *
     * @param \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer $specialOffers Special offer model
     * 
     * @return OfferType
     */
    public function addSpecialOffers(\XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer $specialOffers)
    {
        $this->specialOffers[] = $specialOffers;

        return $this;
    }

    /**
     * Returns special offers of the type.
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSpecialOffers()
    {
        return $this->specialOffers;
    }

}