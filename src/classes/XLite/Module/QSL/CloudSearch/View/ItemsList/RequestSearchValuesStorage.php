<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-2016 Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\QSL\CloudSearch\View\ItemsList;

use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;

/**
 * RequestSearchValuesStorage
 */
class RequestSearchValuesStorage extends \XLite\View\ItemsList\RequestSearchValuesStorage implements \XLite\Base\IDecorator
{
    const FILTER_PARAM_PREFIX = 'filter_';

    const ARRAY_VALUES_SEPARATOR = '__';

    /**
     * Get param value
     *
     * @param string $serviceName Search condition service name
     *
     * @return mixed
     */
    public function getValue($serviceName)
    {
        if ($serviceName == ProductRepo::P_CLOUD_FILTERS) {
            return $this->getFilters();
        }

        return parent::getValue($serviceName);
    }

    /**
     * Parse and return
     *
     * @return array
     */
    protected function getFilters()
    {
        $httpParams = $this->parseCGIParams($_SERVER['QUERY_STRING']);

        $filters = [];

        foreach ($httpParams as $key => $v) {
            if (strpos($key, self::FILTER_PARAM_PREFIX) === 0) {
                $filterName = substr($key, strlen(self::FILTER_PARAM_PREFIX));

                $filters[$filterName] = [];

                $values = is_array($v) ? $v : array($v);

                foreach ($values as $value) {
                    $filters[$filterName][] = $value;
                }
            }
        }

        return $filters;
    }

    /**
     * Parse HTTP query string in a *standard* CGI way. PHP uses a different scheme that doesn't allow us to pass arbitrary strings as keys (for example, filter names with spaces in them). Also it will allow duplicate keys for array passing.
     *
     * @param $str
     *
     * @return array
     */
    protected function parseCGIParams($str)
    {
        $arr = array();

        $pairs = explode('&', $str);

        foreach ($pairs as $i) {
            $kv = explode('=', $i, 2);

            if (count($kv) == 2) {
                list($name, $value) = $kv;

                $name  = urldecode($name);
                $value = urldecode($value);
            } else {
                list($name) = $kv;
                $value = null;

                $name = urldecode($name);
            }

            if (isset($arr[$name])) {
                if (is_array($arr[$name])) {
                    $arr[$name][] = $value;
                } else {
                    $arr[$name] = array($arr[$name], $value);
                }
            } else {
                $arr[$name] = $value;
            }
        }

        return $arr;
    }
}