<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Array manager
 *
 */
abstract class ArrayManager extends \Includes\Utils\AUtils
{
    /**
     * Check if passed has no duplicate elements (except of the "skip" ones)
     * TODO:  to improve
     *
     * @param array  $array       Array to check
     * @param string &$firstValue First duplicated value
     * @param array  $skip        Values to skip OPTIONAL
     *
     * @return boolean
     */
    public static function isUnique(array $array, &$firstValue, array $skip = null)
    {
        $result = true;

        foreach (array_count_values($array) as $key => $value) {
            if (!isset($skip) || !in_array($key, $skip)) {
                if (1 < $value) {
                    $result = false;
                    $firstValue = $key;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * http://php.net/manual/en/function.array-merge-recursive.php#92195
     *
     * @param array          $array1  Data array #1
     * @param array          $array2  Data array #2
     *
     * @return array
     */
    public static function mergeRecursiveDistinct( array $array1, array $array2 )
    {
        $merged = $array1;

        foreach ( $array2 as $key => &$value ) {
            if (is_array($value)
                && isset($merged[$key])
                && is_array($merged[$key])
            ){
                $merged[$key] = static::mergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Method to remove empty elements from multidimensional array
     *
     * @param array          $data  Data array
     *
     * @return array
     */
    public static function filterMultidimensional(array $data)
    {
        $result = array();

        foreach ($data as $key => $value) {
            $filteredValue = $value;

            if ($value && is_array($value)) {
                $filteredValue = static::filterMultidimensional($value);
            }

            if ($filteredValue) {
                $result[$key] = $filteredValue;
            }
        }

        return $result;
    }

    /**
     * Method to safely get array element (or a whole array)
     *
     * @param array          $data  Data array
     * @param integer|string $index  Array index
     * @param boolean        $strict Flag; return value or null in any case
     *
     * @return array|mixed|null
     */
    public static function getIndex(array $data, $index = null, $strict = false)
    {
        return isset($index) ? (isset($data[$index]) ? $data[$index] : null) : ($strict ? null : $data);
    }

    /**
     * Return array elements having the corresponded keys
     *
     * @param array   $data   Array to filter
     * @param array   $keys   Keys (filter rule)
     * @param boolean $invert Flag; determines which function to use: "diff" or "intersect" OPTIONAL
     *
     * @return array
     */
    public static function filterByKeys(array $data, array $keys, $invert = false)
    {
        $method = $invert ? 'array_diff_key' : 'array_intersect_key';

        return $method($data, array_fill_keys($keys, true));
    }

    /**
     * Wrapper to return property from object
     *
     * @param object  $object   Object to get property from
     * @param string  $field    Field to get
     * @param boolean $isGetter Determines if the second param is a property name or a method
     *
     * @return mixed
     */
    public static function getObjectField($object, $field, $isGetter = false)
    {
        return $isGetter ? $object->$field() : $object->$field;
    }

    /**
     * Return some array index
     *
     * @param array  $array Array to use
     * @param string $field Field to return
     *
     * @return array
     */
    public static function getArraysArrayFieldValues(array $array, $field)
    {
        foreach ($array as &$element) {
            $element = static::getIndex($element, $field, true);
        }

        return $array;
    }

    /**
     * Search entities in array by a field value
     *
     * @param array  $array Array to search
     * @param string $field Field to search by
     * @param mixed  $value Value to use for comparison
     *
     * @return mixed
     */
    public static function searchAllInArraysArray(array $array, $field, $value)
    {
        $result = array();

        foreach ($array as $key => $element) {
            $element = (array) $element;
            if (static::getIndex($element, $field, true) == $value) {
                $result[$key] = $element;
            }
        }

        return $result;
    }

    /**
     * Search entities in array by a field value
     *
     * @param array  $array Array to search
     * @param string $field Field to search by
     * @param mixed  $value Value to use for comparison
     *
     * @return mixed
     */
    public static function searchInArraysArray(array $array, $field, $value)
    {
        $list = static::searchAllInArraysArray($array, $field, $value);

        return $list ? reset($list) : null;
    }

    /**
     * Return some object property values
     *
     * @param array   $array    Array to use
     * @param string  $field    Field to return
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return array
     */
    public static function getObjectsArrayFieldValues(array $array, $field, $isGetter = true)
    {
        foreach ($array as &$element) {
            $element = static::getObjectField($element, $field, $isGetter);
        }

        return $array;
    }

    /**
     * Search entities in array by a field value
     *
     * @param array   $array    Array to search
     * @param string  $field    Field to search by
     * @param mixed   $value    Value to use for comparison
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return mixed
     */
    public static function searchAllInObjectsArray(array $array, $field, $value, $isGetter = true)
    {
        $result = array();

        foreach ($array as $key => $element) {
            if (static::getObjectField($element, $field, $isGetter) == $value) {
                $result[$key] = $element;
            }
        }

        return $result;
    }

    /**
     * Search entity in array by a field value
     *
     * @param array   $array    Array to search
     * @param string  $field    Field to search by
     * @param mixed   $value    Value to use for comparison
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return mixed
     */
    public static function searchInObjectsArray(array $array, $field, $value, $isGetter = true)
    {
        $list = static::searchAllInObjectsArray($array, $field, $value, $isGetter);

        return $list ? reset($list) : null;
    }

    /**
     * Sum some object property values
     *
     * @param array   $array    Array to use
     * @param string  $field    Field to sum by
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return mixed
     */
    public static function sumObjectsArrayFieldValues(array $array, $field, $isGetter = true)
    {
        return array_sum(static::getObjectsArrayFieldValues($array, $field, $isGetter));
    }

    /**
     * Takes batches of items from $data split by size of $batchSize and applies $callback to each batch.
     *
     * @param array    $data        Array of items
     * @param integer  $batchSize   Size of batches
     * @param callable $callback    Callback which is applied to batches
     *
     * @return void
     */
    public static function eachCons($data, $batchSize, $callback)
    {
        $batches = static::partition($data, $batchSize);

        foreach ($batches as $batch) {
            call_user_func($callback, $batch);
        }
    }

    /**
     * Returns batches of items from $data with size of $batchSize.
     *
     * @param array    $data        Array of items
     * @param integer  $batchSize   Size of batches
     *
     * @return array
     */
    public static function partition($data, $batchSize)
    {
        $size = count($data);

        $result = array();

        for ($position = 0; $position < $size; $position += $batchSize) {
            $batch = array_slice($data, $position, $batchSize);

            $result[] = $batch;
        }

        return $result;
    }

    /**
     * Find item
     *
     * FIXME: parameters are passed incorrectly into "call_user_func"
     * FIXME: "userData" parameter is not used
     *
     * @param mixed    &$data    Data
     * @param callback $callback Callback
     * @param mixed    $userData Additional data OPTIONAL
     *
     * @return array|void
     */
    public static function findValue(&$data, $callback, $userData = null)
    {
        $found = null;

        foreach ($data as $key => $value) {

            // Input argument
            if (call_user_func($callback, $value, $userData)) {
                $found = $value;
                break;
            }
        }

        return $found;
    }

    /**
     * Filter array
     *
     * FIXME: must use the "array_filter" function
     * FIXME: parameters are passed incorrectly into "call_user_func"
     * FIXME: "userData" parameter is not used
     *
     * @param mixed    &$data    Data
     * @param callback $callback Callback
     * @param mixed    $userData Additional data OPTIONAL
     *
     * @return array
     */
    public static function filter(&$data, $callback, $userData = null)
    {
        $result = array();

        foreach ($data as $key => $value) {

            // Input argument
            if (call_user_func($callback, $value, $userData)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Rearrange $array by moving elements from $filterArray before or after the rest elements of $array
     *
     * @param array   $array         Array to rearrange
     * @param array   $filterArray   Array containing elements which should be rearrannged
     * @param boolean $moveDirection Direction: true - before, false - after OPTIONAL
     *
     * @return array
     */
    public static function rearrangeArray(array $array, array $filterArray, $moveDirection = false)
    {
        $movingElements = array();
        $restElements = array();

        reset($array);

        while (list($key, $element) = each($array)) {

            $found = false;

            if (!is_array($element)) {
                $found = in_array($element, $filterArray);

            } else {

                foreach ($filterArray as $k => $v) {

                    $diff = array_diff_assoc($element, $v);

                    if (empty($diff)) {
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                $movingElements[$key] = $element;

            } else {
                $restElements[$key] = $element;
            }
        }

        if ($moveDirection) {
            $result = $movingElements + $restElements;

        } else {
            $result = $restElements + $movingElements;
        }

        return $result;
    }
}
