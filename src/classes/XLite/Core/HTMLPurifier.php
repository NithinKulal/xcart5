<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * HTML Purifier wrapper
 */
class HTMLPurifier extends \XLite\Base\Singleton
{
    /**
     * HTML Purifier object
     *
     * @var \HTMLPurifier
     */
    protected static $purifier = null;

    /**
     * Get HTML purifier object
     *
     * @return \HTMLPurifier
     */
    public static function getPurifier($force = false)
    {
        if ($force || !isset(static::$purifier)) {
            require_once LC_DIR_LIB . 'htmlpurifier/library/HTMLPurifier.auto.php';
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', LC_DIR_DATACACHE);
            // Set some HTML5 properties
            $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
            $config->set('HTML.DefinitionRev', 1);

            // Get additional options from etc/config.php
            $options = \XLite::getInstance()->getOptions('html_purifier');

            if (empty($options)) {
                $options = static::getDefaultOptions();
            }

            $config = static::addConfigOptions($config, $options);

            static::$purifier = new \HTMLPurifier($config);
        }

        return static::$purifier;
    }

    /**
     * Add options to HTML Purifier config
     *
     * @param \HTMLPurifier_Config $config Config instance
     *
     * @return \HTMLPurifier_Config
     */
    public static function addConfigOptions($config, $options)
    {
        foreach ($options as $name => $value) {

            if ('1' == $value) {
                $value = true;

            } elseif ('0' == $value) {
                $value = false;
            }

            $method = 'prepareOptionValue' . \XLite\Core\Converter::convertToCamelCase(str_replace('.', '', $name));

            if (method_exists(static::getInstance(), $method)) {
                $value = static::$method($value);
            }

            if (!is_null($value)) {
                $config->set($name, $value);
            }
        }

        $config = static::postprocessOptions($config, $options);

        return $config;
    }

    /**
     * Add options to HTML Purifier config
     *
     * @param \HTMLPurifier_Config $config Config instance
     *
     * @return \HTMLPurifier_Config
     */
    protected static function postprocessOptions($config, $options)
    {
        if ($options['HTML.SafeIframe'] && empty($options['URI.SafeIframeRegexp'])) {
            $config->set('URI.SafeIframeRegexp', '%.*%');
        }

        return $config;
    }

    /**
     * Prepare value for option URI.SafeIframeRegexp
     *
     * @param array|string $value Value
     *
     * @return string|null
     */
    protected static function prepareOptionValueURISafeIframeRegexp($value)
    {
        if (!empty($value)) {

            if (!is_array($value)) {
                $value = array($value);
            }

            foreach ($value as $k => $v) {
                $v = trim($v);
                $value[$k] = preg_quote($v, '%');
            }

            $value = array_merge($value, static::getPermittedDomains());
            $value = array_unique($value);

            $value = '%^(http:|https:)?//(' . implode('|', $value) . ')%';

        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Get list of additional permitted domains for URI.SafeIframeRegexp option
     *
     * @return array
     */
    protected static function getPermittedDomains()
    {
        $result = array();

        $hostDetails = \XLite::getInstance()->getOptions('host_details');

        $domains = explode(',', $hostDetails['domains']);
        $domains[] = $hostDetails['http_host'];
        $domains[] = $hostDetails['https_host'];

        $domains = array_unique(array_filter($domains));

        foreach ($domains as $domain) {
            $result[] = trim($domain) . '/' . ltrim($hostDetails['web_dir'], '/');
        }

        return $result;
    }

    /**
     * Get default HTML Purifier config options
     *
     * @return array
     */
    public static function getDefaultOptions()
    {
        return array(
            'Attr.AllowedFrameTargets' => true,
            'Attr.AllowedFrameTargets' => array('_blank', '_self', '_top', '_parent'),
            'Attr.EnableID'            => true,
            'HTML.SafeEmbed'           => true,
            'HTML.SafeObject'          => true,
            'HTML.SafeIframe'          => true,
            'URI.SafeIframeRegexp'     => array('www.youtube.com/embed/', 'www.youtube-nocookie.com/embed/', 'player.vimeo.com/video/'),
        );
    }

    /**
     * Purify value
     *
     * @param string $value Text value
     *
     * @return string
     */
    public static function purify($value)
    {
        return static::getPurifier()->purify($value);
    }
}
