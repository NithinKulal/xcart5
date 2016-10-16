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
 * Special offer types multilingual data
 *
 * @Entity
 *
 * @Table (name="special_offer_type_translations",
 *         indexes={
 *              @Index (name="ci", columns={"code","id"}),
 *              @Index (name="id", columns={"id"}),
 *              @Index (name="name", columns={"name"})
 *         }
 * )
 */
class OfferTypeTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Administrative name of the offer type.
     *
     * @var string
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Sets the administative name for the offer type.
     *
     * @param string $name Administrative name
     *
     * @return OfferTypeTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the administative name of the type.
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the identifier of the translation.
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
     * @return OfferTypeTranslation
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