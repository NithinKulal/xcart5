<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Currency
 *
 * @Entity
 * @Table (name="currencies",
 *      indexes = {
 *          @Index (name="code", columns={"code"})
 *      }
 * )
 */
class Currency extends \XLite\Model\Base\I18n
{
    /**
     * Currency unique id (ISO 4217 number)
     *
     * @var integer
     *
     * @Id
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $currency_id;

    /**
     * Currency code (ISO 4217 alpha-3)
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=3, unique=true)
     */
    protected $code;

    /**
     * Symbol
     *
     * @var string
     *
     * @Column (type="string", length=16)
     */
    protected $symbol;

    /**
     * Prefix
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $prefix = '';

    /**
     * Suffix
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $suffix = '';

    /**
     * Number of digits after the decimal separator.
     *
     * @var integer
     *
     * @Column (type="smallint")
     */
    protected $e = 0;

    /**
     * Decimal part delimiter
     * @var string
     *
     * @Column (type="string", length=8)
     */
    protected $decimalDelimiter = '.';

    /**
     * Thousand delimier
     *
     * @var string
     *
     * @Column (type="string", length=8)
     */
    protected $thousandDelimiter = '';

    /**
     * Orders
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Order", mappedBy="currency")
     */
    protected $orders;

    /**
     * Countries
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Country", mappedBy="currency", cascade={"all"})
     */
    protected $countries;


    /**
     * Set currency Id
     *
     * @param integer $value Currency id
     * TODO - Doctrine is not generate setter for identifier. We must reworkt it
     *
     * @return void
     */
    public function setCurrencyId($value)
    {
        $this->currency_id = $value;
    }

    /**
     * Get urrency symbol to display in interface
     *
     * @param boolean $strict Flag: true - return only prefix or suffix, false - return code if prefix and suffix isn't specified for currency
     *
     * @return string
     */
    public function getCurrencySymbol($strict = true)
    {
        return $this->getPrefix() ?: ($this->getSuffix() ?: (!$strict ? $this->getCode() : ''));
    }

    /**
     * Round value
     *
     * @param float $value Value
     *
     * @return float
     */
    public function roundValue($value)
    {
        return \XLite\Logic\Math::getInstance()->roundByCurrency($value, $this);
    }

    /**
     * Round value as integer
     *
     * @param float $value Value
     *
     * @return integer
     */
    public function roundValueAsInteger($value)
    {
        return intval(round($this->roundValue($value) * pow(10, $this->getE()), 0));
    }

    /**
     * Convert integer to float
     *
     * @param integer $value Value
     *
     * @return float
     */
    public function convertIntegerToFloat($value)
    {
        return $value / pow(10, $this->getE());
    }

    /**
     * Format value
     *
     * @param float $value Value
     *
     * @return string
     */
    public function formatValue($value)
    {
        return \XLite\Logic\Math::getInstance()->formatValue($value, $this);
    }

    /**
     * Get minimum value
     *
     * @return float
     */
    public function getMinimumValue()
    {
        return $this->convertIntegerToFloat(1);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->orders    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->countries = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Format value as parts list
     *
     * @param float $value Value
     *
     * @return array
     */
    public function formatParts($value)
    {
        return \XLite\Logic\Math::getInstance()->formatParts($value, $this);
    }


    /**
     * Get currency_id
     *
     * @return integer 
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Currency
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
     * Set symbol
     *
     * @param string $symbol
     * @return Currency
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * Get symbol
     *
     * @return string 
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return Currency
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Get prefix
     *
     * @return string 
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return Currency
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * Get suffix
     *
     * @return string 
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set e
     *
     * @param smallint $e
     * @return Currency
     */
    public function setE($e)
    {
        $this->e = $e;
        return $this;
    }

    /**
     * Get e
     *
     * @return smallint 
     */
    public function getE()
    {
        return $this->e;
    }

    /**
     * Set decimalDelimiter
     *
     * @param string $decimalDelimiter
     * @return Currency
     */
    public function setDecimalDelimiter($decimalDelimiter)
    {
        $this->decimalDelimiter = $decimalDelimiter;
        return $this;
    }

    /**
     * Get decimalDelimiter
     *
     * @return string 
     */
    public function getDecimalDelimiter()
    {
        return $this->decimalDelimiter;
    }

    /**
     * Set thousandDelimiter
     *
     * @param string $thousandDelimiter
     * @return Currency
     */
    public function setThousandDelimiter($thousandDelimiter)
    {
        $this->thousandDelimiter = $thousandDelimiter;
        return $this;
    }

    /**
     * Get thousandDelimiter
     *
     * @return string 
     */
    public function getThousandDelimiter()
    {
        return $this->thousandDelimiter;
    }

    /**
     * Add orders
     *
     * @param \XLite\Model\Order $orders
     * @return Currency
     */
    public function addOrders(\XLite\Model\Order $orders)
    {
        $this->orders[] = $orders;
        return $this;
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add countries
     *
     * @param \XLite\Model\Country $countries
     * @return Currency
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
