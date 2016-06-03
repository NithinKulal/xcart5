<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model;

use \XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Active currency
 *
 * @Entity
 * @Table  (
 *      name="active_currencies",
 *      indexes={
 *          @Index (name="position", columns={"position"}),
 *          @Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class ActiveCurrency extends \XLite\Model\AEntity
{
    /**
     * Active currency ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $active_currency_id;

    /**
     * Currency rate
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $rate = 1;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Last rate update date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $rateDate = 0;

    /**
     * Currency
     *
     * @var \XLite\Model\Currency
     *
     * @OneToOne (targetEntity="XLite\Model\Currency", inversedBy="active_currency")
     * @JoinColumn (name="currency_id", referencedColumnName="currency_id")
     */
    protected $currency;

    /**
     * Countries
     *
     * @var \XLite\Model\Country[]
     *
     * @OneToMany (targetEntity="XLite\Model\Country", mappedBy="active_currency")
     */
    protected $countries;

    /**
     * Default delimiter format
     *
     * @var array
     */
    protected $defaultDelimiterFormat = array('', '.');

    /**
     * Allowed thousands format
     *
     * @var array
     */
    protected $allowedThousandsDelimiter = array(' ','.',',','');

    /**
     * Allowed decimal delimiter
     *
     * @var array
     */
    protected $allowedDecimalDelimiter = array('.',',');

    /**
     * Update entity
     *
     * @return boolean
     */
    public function update()
    {
        $this->getCurrency()->update();

        return parent::update();
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Get currency name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getCurrency()->getName();
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getCurrency()->getCode();
    }

    /**
     * Get currency prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->getCurrency()->getPrefix();
    }

    /**
     * Get currency suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->getCurrency()->getSuffix();
    }

    /**
     * Get currency format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->getCurrency()->getThousandDelimiter()
        . \XLite\View\FormField\Select\FloatFormat::FORMAT_DELIMITER
        . $this->getCurrency()->getDecimalDelimiter();
    }

    /**
     * Get rate update date as string
     *
     * @return string
     */
    public function getRateDateAsString()
    {
        return (0 == $this->getRateDate())
            ? '-'
            : \XLite\Core\Converter::getInstance()->formatDate($this->getRateDate())
            . ' ' . \XLite\Core\Converter::getInstance()->formatDayTime($this->getRateDate());
    }

    /**
     * Get countries list
     *
     * @return string
     */
    public function getCountriesList()
    {
        $return = array();

        $countries = $this->getCountries();

        if (count($countries) > 0) {
            foreach ($countries as $country) {
                $return[] = $country->getCode();
            }
        }

        return empty($return) ? '...' : implode(', ', $return);
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return ActiveCurrency
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (boolean) $enabled;
        return $this;
    }

    /**
     * Set rate
     *
     * @param float $value Value
     *
     * @return void
     */
    public function setRate($value)
    {
        if ($this->isDefaultCurrency()) {
            $this->setRateDate(0);
        } else {
            $this->rateDate = \XLite\Core\Converter::getInstance()->time();
        }

        $this->rate = $value;
    }

    /**
     * Set currency prefix
     *
     * @param string $value Value
     *
     * @return void
     */
    public function setPrefix($value)
    {
        $this->getCurrency()->setPrefix($value);
    }

    /**
     * Set currency suffix
     *
     * @param string $value Value
     *
     * @return void
     */
    public function setSuffix($value)
    {
        $this->getCurrency()->setSuffix($value);
    }

    /**
     * Set currency format
     *
     * @param string $value Value
     *
     * @return void
     */
    public function setFormat($value)
    {
        $format = $this->getDelimitersFormat($value);

        $this->getCurrency()->setThousandDelimiter($format[0]);
        $this->getCurrency()->setDecimalDelimiter($format[1]);
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getActiveCurrencyId();
    }

    /**
     * Is default currency
     *
     * @return boolean
     */
    public function isDefaultCurrency()
    {
        return $this->getCurrency()->getCurrencyId() == \XLite\Core\Config::getInstance()->General->shop_currency;
    }

    /**
     * Get default value
     *
     * @return boolean
     */
    public function getDefaultValue()
    {
        return $this->isDefaultCurrency();
    }

    /**
     * Get delimiters format from string
     *
     * @param string $format Format
     *
     * @return array
     */
    protected function getDelimitersFormat($format)
    {
        $return = explode(\XLite\View\FormField\Select\FloatFormat::FORMAT_DELIMITER, $format);

        if (!is_array($return)) {
            $return = $this->defaultDelimiterFormat;
        } else {
            if (!in_array($return[0], $this->allowedThousandsDelimiter)) {
                $return[0] = $this->defaultDelimiterFormat[0];
            }

            if (!in_array($return[1], $this->allowedDecimalDelimiter)) {
                $return[1] = $this->defaultDelimiterFormat[1];
            }
        }

        return $return;
    }

    /**
     * Return first assigned country
     *
     * @return \XLite\Model\Country
     */
    public function getFirstCountry()
    {
        $countries = $this->getCountries();

        return isset($countries[0]) ? $countries[0] : null;
    }

    /**
     * Check if currency has assigned countries
     *
     * @return boolean
     */
    public function hasAssignedCountries()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
            ->hasAssignedCountries($this->getCode());
    }

    /**
     * Get active_currency_id
     *
     * @return integer 
     */
    public function getActiveCurrencyId()
    {
        return $this->active_currency_id;
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
     * @return ActiveCurrency
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
     * Set rateDate
     *
     * @param integer $rateDate
     * @return ActiveCurrency
     */
    public function setRateDate($rateDate)
    {
        $this->rateDate = $rateDate;
        return $this;
    }

    /**
     * Get rateDate
     *
     * @return integer 
     */
    public function getRateDate()
    {
        return $this->rateDate;
    }

    /**
     * Set currency
     *
     * @param \XLite\Model\Currency $currency
     * @return ActiveCurrency
     */
    public function setCurrency(\XLite\Model\Currency $currency = null)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Add countries
     *
     * @param \XLite\Model\Country $countries
     * @return ActiveCurrency
     */
    public function addCountries(\XLite\Model\Country $countries)
    {
        $this->countries[] = $countries;
        return $this;
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountries()
    {
        return $this->countries;
    }
}