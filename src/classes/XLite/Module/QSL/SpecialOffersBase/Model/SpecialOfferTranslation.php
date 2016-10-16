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
 * Special offers multilingual data
 *
 * @Entity
 *
 * @Table (name="special_offer_translations",
 *         indexes={
 *              @Index (name="ci", columns={"code","id"}),
 *              @Index (name="id", columns={"id"}),
 *              @Index (name="title", columns={"title"})
 *         }
 * )
 */
class SpecialOfferTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Special offer title.
     *
     * @var string
     * @Column (type="string", length=255)
     */
    protected $title;

    /**
     * Short promotional text.
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $shortPromoText = '';

    /**
     * Full description.
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $description = '';

    /**
     * Cart promotional text.
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $cartPromoText = '';

    /**
     * Cart qualified text.
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $cartAppliedText = '';
    
    /**
     * Sets the title that will be displayed for the special offer to customers.
     *
     * @param string $title New title
     *
     * @return SpecialOfferTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the title that will be displayed for the special offer to customers.
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the short promo text for the special offer.
     *
     * @param text $shortPromoText Short promo text
     *
     * @return SpecialOfferTranslation
     */
    public function setShortPromoText($shortPromoText)
    {
        $this->shortPromoText = $shortPromoText;

        return $this;
    }

    /**
     * Returns the short promo text for the special offer.
     *
     * @return text 
     */
    public function getShortPromoText()
    {
        return $this->shortPromoText;
    }

    /**
     * Configures the description for the special offer.
     *
     * @param text $description Text
     *
     * @return SpecialOfferTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the special offer description.
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Configures the promo text that will appear for the special offer on the cart page.
     *
     * @param text $cartPromoText Promo text
     *
     * @return SpecialOfferTranslation
     */
    public function setCartPromoText($cartPromoText)
    {
        $this->cartPromoText = $cartPromoText;

        return $this;
    }

    /**
     * Returns the promo text that will appear for the special offer on the cart page.
     *
     * @return text 
     */
    public function getCartPromoText()
    {
        return $this->cartPromoText;
    }

    /**
     * Configures the promo text that will appear on the cart page for the special offer when it is applied.
     *
     * @param text $cartAppliedText Promo text
     *
     * @return SpecialOfferTranslation
     */
    public function setCartAppliedText($cartAppliedText)
    {
        $this->cartAppliedText = $cartAppliedText;

        return $this;
    }

    /**
     * Returns the promo text that should appear on the cart page for the special offer when it is applied.
     *
     * @return text 
     */
    public function getCartAppliedText()
    {
        return $this->cartAppliedText;
    }

    /**
     * Returns the translation identifier.
     *
     * @return integer 
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Sets the language code for the translation.
     *
     * @param string $code Code
     *
     * @return SpecialOfferTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Returns the language code for the translation.
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
