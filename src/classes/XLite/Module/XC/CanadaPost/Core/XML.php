<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core;

/**
 * XML converter
 */
class XML extends \XLite\Core\XML
{
    /**
     * Convert parsed XML document
     *
     * @param string $parsedXml Parsed XMl document
     *
     * @return \XLite\Core\CommonCell
     */
    public static function convertParsedXmlDocument($parsedXml)
    {
        $elements = new \XLite\Core\CommonCell();

        foreach ($parsedXml['#'] as $field => $value) {

            $field = static::convertXmlFieldNameToCamelCase($field);

            if (static::isSingleXmlElem($value)) {

                // Simple element (final)
                $elements->{$field} = static::convertSingleXmlElem($value);

            } else if (static::isSimpleXmlElemsContainer($value)) {

                // Simple elements container
                $elements->{$field} = static::convertParsedXmlDocument($value[0]);

            } else if (static::isMultipleXmlElemsContainer($value)) {

                // Multiple elements container (list)
                $commonElemName = array_shift(array_keys($value[0]['#']));

                $subElements = array();

                foreach ($value[0]['#'][$commonElemName] as $subField => $subValue) {
                    $subElements[] = static::convertParsedXmlDocument($subValue);
                }

                $elements->{$field} = $subElements;
            }
        }

        return $elements;
    }

    /**
     * Convert single XML element into an object
     *
     * @param array $parsedXmlElem Parsed XML element data
     *
     * @return mixed
     */
    public static function convertSingleXmlElem($parsedXmlElem)
    {
        if (static::hasXmlElemAttrs($parsedXmlElem)) {

            $field = new \XLite\Core\CommonCell();

            $field->attrs = static::convertXmlElemAttrs($parsedXmlElem);
            $field->value = static::convertXmlElemValue($parsedXmlElem[0]['#']);

        } else {

            $field = static::convertXmlElemValue($parsedXmlElem[0]['#']);
        }

        return $field;
    }

    /**
     * Check - has a XML element attributes or not
     *
     * @param array $parsedXmlElem Parsed XML element data
     *
     * @return boolean
     */
    public static function hasXmlElemAttrs($parsedXmlElem)
    {
        return (
            isset($parsedXmlElem[0]['@'])
            && !empty($parsedXmlElem[0]['@'])
        );
    }

    /**
     * Convert XML element attributes to an object
     *
     * @param array $parsedXmlElem Parsed XML element data
     *
     * @return \XLite\Core\CommonCell
     */
    public static function convertXmlElemAttrs($parsedXmlElem)
    {
        $attrs = new \XLite\Core\CommonCell();

        foreach ($parsedXmlElem[0]['@'] as $attr => $value) {
            $attrs->{static::convertXmlFieldNameToCamelCase($attr)} = trim($value);
        }

        return $attrs;
    }

    /**
     * Convert XML element value
     *
     * @param string $value XML element value
     *
     * @return mixed
     */
    public static function convertXmlElemValue($value)
    {
        $value = trim($value);

        if (
            is_string($value)
            && ('true' === $value || 'false' === $value)
        ) {
            $value = ('true' === $value) ? true : false;
        }

        return $value;
    }

    /**
     * Check - is XML element is multiple (similar) elements container or not
     *
     * @param array $parsedXmlElem Parsed XML element data
     *
     * @return bool
     */
    public function isMultipleXmlElemsContainer($parsedXmlElem)
    {
        return (
            is_array($parsedXmlElem[0]['#'])
            && 1 == count($parsedXmlElem[0]['#'])
        );
    }

    /**
     * Check - is XMl element is simple container or not
     *
     * @param array $parsedXmlElem Parsed XML element data
     *
     * @return boolean
     */
    public static function isSimpleXmlElemsContainer($parsedXmlElem)
    {
        return (
            is_array($parsedXmlElem[0]['#'])
            && 1 < count($parsedXmlElem[0]['#'])
        );
    }

    /**
     * Check - is XML element is single (final) element
     *
     * @param array $parsedXmlElem Parsed XML element data
     *
     * @return boolean
     */
    public static function isSingleXmlElem($parsedXmlElem)
    {
        return (
            is_string($parsedXmlElem[0]['#'])
            || !is_array($parsedXmlElem[0]['#'])
        );
    }

    /**
     * Convert XML fields and/or attributes names to camel case
     *
     * @param string $field XML field/attribute name
     *
     * @return string
     */
    public static function convertXmlFieldNameToCamelCase($field)
    {
        return lcfirst(\XLite\Core\Converter::convertToCamelCase(str_replace('-', '_', $field)));
    }
}
