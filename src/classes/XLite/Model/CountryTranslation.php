<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Country translations
 *
 * @Entity
 * @Table (name="country_translations",
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="country", columns={"country"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class CountryTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Country name
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $country;

    /**
     * Set country
     *
     * @param string $country
     * @return CountryTranslation
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get label_id
     *
     * @return integer 
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return CountryTranslation
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
}
