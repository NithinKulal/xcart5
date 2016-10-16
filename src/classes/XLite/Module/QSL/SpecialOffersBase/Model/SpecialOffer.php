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
 * Special Offer model.
 *
 * It stores information on what special offer logic should be used and offer settings.
 *
 * @Entity (repositoryClass="\XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer")
 * @Table  (name="special_offers",
 *      indexes={
 *          @Index (name="offer_id", columns={"offer_id"}),
 *          @Index (name="name", columns={"name"}),
 *          @Index (name="position", columns={"position"}),
 *          @Index (name="promoHome", columns={"promoHome"}),
 *          @Index (name="promoOffers", columns={"promoOffers"}),
 *          @Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class SpecialOffer extends \XLite\Model\Base\I18n
{
    /**
     * Unique identifier of the offer.
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $offer_id;

    /**
     * Offer Type.
     *
     * @var \XLite\Module\QSL\SpecialOffersBase\Model\OfferType
     *
     * @ManyToOne  (targetEntity="XLite\Module\QSL\SpecialOffersBase\Model\OfferType", inversedBy="specialOffers")
     * @JoinColumn (name="type_id", referencedColumnName="type_id", onDelete="CASCADE")
     */
    protected $offerType; // when the offer type is deleted, the operation cascades to all realted offers (via SQL)
    
    /**
     * Administrative name of the special offer.
     *
     * @var string
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Position of the exit offer among other ones in the list.
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Whether the offer is enabled, or not.
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Date range (begin)
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $activeFrom = 0;

    /**
     * Date range (end)
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $activeTill = 0;

    /**
     * Identifiers of other special offers that this offer may not apply together on the same item.
     * 
     * @var array
     * 
     * @Column (type="array", nullable=true)
     */
    protected $exclusions = array();

    /**
     * One-to-one relation with special_offer_images table
     *
     * @var \XLite\Module\QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image
     *
     * @OneToOne  (targetEntity="XLite\Module\QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image", mappedBy="specialOffer", cascade={"remove"})
     */
    protected $image;

    /**
     * Whether the short promo text and image is displayed on the home page, or not.
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $promoHome = true;

    /**
     * Whether the short promo text and image is displayed on Special Offers page.
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $promoOffers = true;
    
    /**
     * Get the model ID.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getBrandId();
    }

    /**
     * Check if this offer can apply on an item together with the specified offer.
     * 
     * @param integer $otherOfferId Identifier of the other offer to check.
     * 
     * @return boolean
     */
    public function canApplyTogether($otherOfferId)
    {
    	return !(in_array($otherOfferId, $this->exclusions));
    }

    /**
     * Since Doctrine lifecycle callbacks do not allow to modify associations, we've added this method
     *
     * @param string $type Type of current operation
     *
     * @return void
     */
    public function prepareEntityBeforeCommit($type)
    {
        if (static::ACTION_UPDATE == $type && !$this->getOfferType()->hasAllRequiredClasses()) {
            $this->setEnabled(false);
        }

        parent::prepareEntityBeforeCommit($type);
    }

    /**
     * Returns the offer identifier.
     *
     * @return integer 
     */
    public function getOfferId()
    {
        return $this->offer_id;
    }

    /**
     * Sets the administrative name for the offer type.
     *
     * @param string $name Administrative name
     * 
     * @return SpecialOffer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the administrative name for the offer type.
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the position of the offer among others.
     *
     * @param integer $position Position
     *
     * @return SpecialOffer
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Returns the position of the offer among others.
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Confgiures whether the special offer is enabled, or disabled.
     *
     * @param boolean $enabled New state
     *
     * @return SpecialOffer
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Checks if the special offer is enabled, or not.
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the date that the offer is active from (timestamp).
     *
     * @param integer $activeFrom Date (timestamp)
     *
     * @return SpecialOffer
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = intval($activeFrom);

        return $this;
    }

    /**
     * Returns the date that the offer is active from (timestamp).
     *
     * @return integer 
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Configures the date that the offer is active till (timestamp).
     *
     * @param integer $activeTill Date (timestamp)
     *
     * @return SpecialOffer
     */
    public function setActiveTill($activeTill)
    {
        $this->activeTill = intval($activeTill);

        return $this;
    }

    /**
     * Returns the date that the offer is active till (timestamp).
     *
     * @return integer 
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }

    /**
     * Configures the list of special offers that this one cannot be combined with.
     *
     * @param array $exclusions Special offers
     * 
     * @return SpecialOffer
     */
    public function setExclusions($exclusions)
    {
        $this->exclusions = $exclusions;

        return $this;
    }

    /**
     * Returns the list of special offers that this one cannot be combined with.
     *
     * @return array 
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * Configures whether the promo should be displayed on the home page.
     *
     * @param boolean $promoHome State
     *
     * @return SpecialOffer
     */
    public function setPromoHome($promoHome)
    {
        $this->promoHome = $promoHome;

        return $this;
    }

    /**
     * Checks if the promo should be displayed on the home page.
     *
     * @return boolean 
     */
    public function getPromoHome()
    {
        return $this->promoHome;
    }

    /**
     * Configures whether the promo should be displayed on the Special Offers page.
     *
     * @param boolean $promoOffers State
     *
     * @return SpecialOffer
     */
    public function setPromoOffers($promoOffers)
    {
        $this->promoOffers = $promoOffers;

        return $this;
    }

    /**
     * Checks if the promo should be displayed on the Special Offers page.
     *
     * @return boolean 
     */
    public function getPromoOffers()
    {
        return $this->promoOffers;
    }

    /**
     * Sets the type for the special offer.
     *
     * @param \XLite\Module\QSL\SpecialOffersBase\Model\OfferType $offerType Offer type
     *
     * @return SpecialOffer
     */
    public function setOfferType(\XLite\Module\QSL\SpecialOffersBase\Model\OfferType $offerType = null)
    {
        $this->offerType = $offerType;
        return $this;
    }

    /**
     * Returns the type of the special offer.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\OfferType 
     */
    public function getOfferType()
    {
        return $this->offerType;
    }

    /**
     * Associates the special offer with an image.
     *
     * @param \XLite\Module\QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image $image
     *
     * @return SpecialOffer
     */
    public function setImage(\XLite\Module\QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Returns the special offer image.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image 
     */
    public function getImage()
    {
        return $this->image;
    }
}