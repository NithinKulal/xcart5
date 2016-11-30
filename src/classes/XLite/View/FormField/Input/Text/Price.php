<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Price
 */
class Price extends \XLite\View\FormField\Input\Text\Symbol
{
    const PARAM_CURRENCY = 'currency';
    const PARAM_DASHED  = 'dashed';

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $currency = $this->getCurrency();
        foreach ($this->getWidgetParams() as $name => $param) {
            if (static::PARAM_E == $name) {
                $param->setValue($currency->getE());

            } elseif (static::PARAM_THOUSAND_SEPARATOR == $name) {
                $param->setValue($currency->getThousandDelimiter());

            } elseif (static::PARAM_DECIMAL_SEPARATOR == $name) {
                $param->setValue($currency->getDecimalDelimiter());
            }
        }
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return floatval(parent::prepareRequestData($value));
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return $this->getParam(static::PARAM_CURRENCY);
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        $result = $this->getSymbolType() === 'prefix'
            ? $this->getCurrency()->getPrefix()
            : $this->getCurrency()->getSuffix();

        return $result ?: $this->getCurrency()->getCode();
    }

    /**
     * Return symbol type
     *
     * @return string
     */
    public function getSymbolType()
    {
        return $this->getCurrency()->getSuffix() && !$this->getCurrency()->getPrefix()
            ? 'suffix'
            : 'prefix';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_CURRENCY => new \XLite\Model\WidgetParam\TypeObject(
                'Currency',
                \XLite::getInstance()->getCurrency(),
                false,
                'XLite\Model\Currency'
            ),
            self::PARAM_DASHED  => new \XLite\Model\WidgetParam\TypeBool('Dash as empty value', false),
        );
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'price';

        return $classes;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();
        $attributes['value'] = $this->formatValue($attributes['value']);

        $attributes['data-dashed']  = $this->getParam(self::PARAM_DASHED);

        return $attributes;
    }

    /**
     * Format value
     *
     * @param float $value Value
     *
     * @return string
     */
    protected function formatValue($value)
    {
        return number_format(
            round($value, $this->getE()),
            $this->getE(),
            '.',
            ''
        );
    }

    /**
     * Get mantis
     *
     * @return integer
     */
    protected function getE()
    {
        return $this->getCurrency()->getE();
    }
}
