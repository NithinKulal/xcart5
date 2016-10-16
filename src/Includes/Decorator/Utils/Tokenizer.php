<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Utils;

/**
 * Tokenizer
 */
abstract class Tokenizer extends \Includes\Decorator\Utils\AUtils
{
    /**
     * Body cache
     *
     * @var array
     */
    protected static $bodyCache = array();

    /**
     * Definition cache
     *
     * @var array
     */
    protected static $headCache = array();

    // {{{ Methods to get class-related tokens

    /**
     * Search for class declaration and return full class name
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getFullClassName($path)
    {
        $result = static::getClassName($path) ?: static::getInterfaceName($path);

        if ($result) {
            $namespace = static::getNamespace($path);
            if ($namespace) {
                $result = $namespace . '\\' . $result;
            }
        }

        return $result;
    }

    /**
     * Get parent class name
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getParentClassName($path)
    {
        return preg_match('/class\s+\S+\s+extends\s+(\S+)/Ss', static::getHead($path), $match)
            ? $match[1]
            : null;
    }

    /**
     * Return list of implemented interfaces
     *
     * @param string $path Repository file path
     *
     * @return array
     */
    public static function getInterfaces($path)
    {
        return preg_match('/class\s+\S+\s+(?:extends\s+\S+\s+)?implements\s+([\\\\A-Za-z0-9,\s]+)/Ss', static::getHead($path), $match)
            ? array_map('trim', explode(',', $match[1]))
            : array();
    }

    /**
     * Check - class is final or not
     *
     * @param string $path Repository file path
     *
     * @return boolean
     */
    public static function isFinal($path)
    {
        return (bool) preg_match('/[\r\n]\s*final\s+class\s+\S+/Ss', static::getHead($path));
    }

    /**
     * Check - class is abstract or not
     *
     * @param string $path Repository file path
     *
     * @return boolean
     */
    public static function isAbstract($path)
    {
        return (bool) preg_match('/[\r\n]\s*abstract\s+class\s+\S+/Ss', static::getHead($path));
    }

    /**
     * Return class DocBlock
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getDocBlock($path)
    {
        return preg_match('/[\n\r](\/\*\*\s*(?:[\r\n]\s+\*[^\r\n]*)+[\r\n]\s+\*\/\s*)[\r\n](?:(abstract|final)\s+)?(?:class|interface)\s+\S+/USs', static::getHead($path), $match)
            ? trim($match[1])
            : null;
    }

    /**
     * Check if method is declared in class
     *
     * @param string $path   Repository file path
     * @param string $method Method to search
     *
     * @return boolean
     */
    public static function hasMethod($path, $method)
    {
        $limit = ini_get('pcre.backtrack_limit') ?: 100000;

        return (bool) preg_match(
            '/^\s*(?:(?:absract|static|public|protected|private)\s+)*function\s+' . $method . '\s*\(/Sm',
            static::getContentHead(static::getContent($path), $limit)
        );
    }

    /**
     * Get class name
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getClassName($path)
    {
        return preg_match('/[\r\n]\s*(?:(?:abstract|final)\s+)?class\s+(\S+)\s+/Ss', static::getHead($path), $match)
            ? $match[1]
            : null;
    }

    /**
     * Get interface name
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getInterfaceName($path)
    {
        return preg_match('/[\r\n]\s*interface\s+(\S+)/Ss', static::getHead($path), $match)
            ? $match[1]
            : null;
    }

    /**
     * Get trait name
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getTraitName($path)
    {
        return preg_match('/[\r\n]\s*trait\s+(\S+)/Ss', static::getHead($path), $match)
            ? $match[1]
            : null;
    }

    /**
     * Get namespace
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    public static function getNamespace($path)
    {
        return preg_match('/[\r\n]\s*namespace\s+(\S+)\s*;/Ss', static::getHead($path), $match)
            ? $match[1]
            : null;
    }

    // }}}

    // {{{ Methods to modify source code

    /**
     * Compose and return source code by tokens list
     *
     * @param string $path      Repository file path
     * @param string $namespace New namespace
     * @param string $class     New class name
     * @param string $parent    New parent class
     * @param string $docblock  New docblock OPTIONAL
     * @param string $prefix    New prefix {abstract|final} OPTIONAL
     *
     * @return string
     */
    public static function getSourceCode($path, $namespace, $class, $parent, $docblock = null, $prefix = null)
    {
        $body = static::getContent($path);

        // Class has been moved to a new location
        if (isset($namespace)) {
            $body = static::replaceNamespace($body, $namespace);
        }

        // Node class has been changed
        if (isset($class)) {
            $body = static::replaceClassName($body, $class);
        }

        // Parent class may be changed if class node has been "replanted" in classes tree
        if (isset($parent)) {
            $body = static::replaceParentClassName($body, $parent);
        }

        // Needed for some Doctrine plugins
        if (isset($docblock)) {
            $body = static::replaceDocblock($body, $docblock);
        }

        // To make abstract base classes in Decorator chains
        if (isset($prefix)) {
            $body = static::replaceClassType($body, $prefix);
        }

        if (isset(static::$bodyCache[$path])) {
            unset(static::$bodyCache[$path]);
        }

        return $body;
    }

    /**
     * Set new namespace in the tokens list
     *
     * @param string $body  File body
     * @param string $token Namespace to set
     *
     * @return string
     */
    protected static function replaceNamespace($body, $token)
    {
        return preg_replace('/([\r\n]\s*namespace\s+)\S+;/Ss', '$1' . $token . ';', $body);
    }

    /**
     * Set new class name in the tokens list
     *
     * @param string $body  File body
     * @param string $token Class name to set
     *
     * @return string
     */
    protected static function replaceClassName($body, $token)
    {
        return preg_replace('/([\r\n](?:abstract\s+|final\s+)?(?:class|interface)\s+)\S+/Ss', '$1' . $token, $body);
    }

    /**
     * Set new parent class name in the tokens list
     *
     * @param string $body  File body
     * @param string $token Class name to set
     *
     * @return string
     */
    protected static function replaceParentClassName($body, $token)
    {
        return preg_replace('/(class\s+\S+\s+extends\s+)\S+/Ss', '$1' . $token, $body);
    }

    /**
     * Set new docblock in the tokens list
     *
     * @param string $body  File body
     * @param string $token Docblock to set
     *
     * @return string
     */
    protected static function replaceDocblock($body, $token)
    {
        return preg_match('/\*\/\s*[\r\n](?:(?:abstract|final)\s+)?(?:class|interface)\s+\S+/Ss', static::getContentHead($body))
            ? preg_replace(
                '/([\n\r])\/\*\*\s*(?:[\r\n]\s+\*[^\r\n]*)+[\r\n]\s+\*\/\s*([\r\n](?:(?:abstract|final)\s+)?(?:class|interface)\s+\S+)/USs',
                '$1' . trim($token) . '$2',
                $body
            )
            : preg_replace(
                '/([\r\n](?:(?:abstract|final)\s+)?(?:class|interface)\s+\S+)/USs',
                PHP_EOL . trim($token) . '$1',
                $body
            );
    }

    /**
     * Replace class type
     *
     * @param string $body  File body
     * @param string $token Class type to set
     *
     * @return string
     */
    protected static function replaceClassType($body, $token)
    {
        return preg_replace('/([\r\n])(?:(?:abstract|final)\s+)?(class\s+\S+)/Ss', '$1' . $token . ' $2', $body);
    }

    // }}}

    // {{{ Methods to modify code-related tokens

    /**
     * Add portion of code to the class source (to the end)
     *
     * @param string $path Repository file path
     * @param string $code Code to add
     *
     * @return string
     */
    public static function addCodeToClassBody($path, $code)
    {
        return preg_replace('/([\r\n])(\}\s*$)/Ss', '$1' . PHP_EOL . $code . PHP_EOL . '$2', static::getContent($path));
    }

    // }}}

    // {{{ Auxiliary methods

    /**
     * Get content
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    protected static function getContent($path)
    {
        if (!isset(static::$bodyCache[$path])) {
            static::$bodyCache = array(
                $path => file_get_contents($path),
            );
        }

        return static::$bodyCache[$path];
    }

    /**
     * Get head
     *
     * @param string $path Repository file path
     *
     * @return string
     */
    protected static function getHead($path)
    {
        if (!isset(static::$headCache[$path])) {
            static::$headCache = array(
                $path => static::getContentHead(static::getContent($path)),
            );
        }

        return static::$headCache[$path];
    }

    /**
     * Get head
     *
     * @param string  $content Content
     * @param integer $limit   Limit OPTIONAL
     *
     * @return string
     */
    protected static function getContentHead($content, $limit = 8192)
    {
        return substr($content, 0, $limit);
    }

    // }}}
}
