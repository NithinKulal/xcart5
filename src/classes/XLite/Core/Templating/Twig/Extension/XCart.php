<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Extension;

use Twig_Environment;
use Twig_Error_Runtime;
use Twig_Extension;
use Twig_Markup;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use XLite\Core\Templating\Twig\Functions;
use XLite\Core\Templating\Twig\TokenParser\Form;
use XLite\Core\Templating\Twig\TokenParser\Widget;
use XLite\Core\Templating\Twig\TokenParser\WidgetList;

class XCart extends Twig_Extension
{
    public function getTokenParsers()
    {
        return [new Form(), new Widget(), new WidgetList()];
    }

    public function getFunctions()
    {
        $functions = new Functions();

        return [
            new Twig_SimpleFunction(
                'widget',
                [$functions, 'widget'],
                [
                    'is_variadic'       => true,
                    'needs_environment' => true,
                    'needs_context'     => true,
                    'node_class'        => 'XLite\Core\Templating\Twig\Node\Expression\IntactArgNamesFunction',
                ]
            ),
            new Twig_SimpleFunction(
                'widget_list',
                [$functions, 'widget_list'],
                [
                    'is_variadic'       => true,
                    'needs_environment' => true,
                    'needs_context'     => true,
                    'node_class'        => 'XLite\Core\Templating\Twig\Node\Expression\IntactArgNamesFunction',
                ]
            ),

            new Twig_SimpleFunction('t', [$functions, 't']),
            new Twig_SimpleFunction('url', [$functions, 'url'], ['needs_environment' => true, 'needs_context' => true]),
            new Twig_SimpleFunction('asset', [$functions, 'asset']),
        ];
    }

    public function getFilters()
    {
        return [
            // TODO: remove to get back to the default escaping strategy when escaping-on-input will be removed (HTML Purifier)
            new Twig_SimpleFilter('escape', 'XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter', ['needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe']),
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'xcart';
    }
}

/**
 * Escapes a string.
 *
 * A copy of standard Twig 1.24 'twig_escape_filter' function with 'double_encode' set to false
 *
 * @param Twig_Environment $env        A Twig_Environment instance
 * @param string           $string     The value to be escaped
 * @param string           $strategy   The escaping strategy
 * @param string           $charset    The charset
 * @param bool             $autoescape Whether the function is called by the auto-escaping feature (true) or by the developer (false)
 *
 * @return string
 */
function xcart_twig_escape_filter(
    Twig_Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false
) {
    if ($autoescape && $string instanceof Twig_Markup) {
        return $string;
    }

    if (!is_string($string)) {
        if (is_object($string) && method_exists($string, '__toString')) {
            $string = (string)$string;
        } elseif (in_array($strategy, array('html', 'js', 'css', 'html_attr', 'url'))) {
            return $string;
        }
    }

    if (null === $charset) {
        $charset = $env->getCharset();
    }

    switch ($strategy) {
        case 'html':
            // see http://php.net/htmlspecialchars

            // Using a static variable to avoid initializing the array
            // each time the function is called. Moving the declaration on the
            // top of the function slow downs other escaping strategies.
            static $htmlspecialcharsCharsets;

            if (null === $htmlspecialcharsCharsets) {
                if (defined('HHVM_VERSION')) {
                    $htmlspecialcharsCharsets = array('utf-8' => true, 'UTF-8' => true);
                } else {
                    $htmlspecialcharsCharsets = array(
                        'ISO-8859-1'  => true, 'ISO8859-1' => true,
                        'ISO-8859-15' => true, 'ISO8859-15' => true,
                        'utf-8'       => true, 'UTF-8' => true,
                        'CP866'       => true, 'IBM866' => true, '866' => true,
                        'CP1251'      => true, 'WINDOWS-1251' => true, 'WIN-1251' => true,
                        '1251'        => true,
                        'CP1252'      => true, 'WINDOWS-1252' => true, '1252' => true,
                        'KOI8-R'      => true, 'KOI8-RU' => true, 'KOI8R' => true,
                        'BIG5'        => true, '950' => true,
                        'GB2312'      => true, '936' => true,
                        'BIG5-HKSCS'  => true,
                        'SHIFT_JIS'   => true, 'SJIS' => true, '932' => true,
                        'EUC-JP'      => true, 'EUCJP' => true,
                        'ISO8859-5'   => true, 'ISO-8859-5' => true, 'MACROMAN' => true,
                    );
                }
            }

            if (isset($htmlspecialcharsCharsets[$charset])) {
                return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset, false);
            }

            if (isset($htmlspecialcharsCharsets[strtoupper($charset)])) {
                // cache the lowercase variant for future iterations
                $htmlspecialcharsCharsets[$charset] = true;

                return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset, false);
            }

            $string = twig_convert_encoding($string, 'UTF-8', $charset);
            $string = htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);

            return twig_convert_encoding($string, $charset, 'UTF-8');

        case 'js':
            // escape all non-alphanumeric characters
            // into their \xHH or \uHHHH representations
            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', '_twig_escape_js_callback', $string);

            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'css':
            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9]#Su', '_twig_escape_css_callback', $string);

            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'html_attr':
            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9,\.\-_]#Su', '_twig_escape_html_attr_callback', $string);

            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'url':
            if (PHP_VERSION_ID < 50300) {
                return str_replace('%7E', '~', rawurlencode($string));
            }

            return rawurlencode($string);

        default:
            static $escapers;

            if (null === $escapers) {
                $escapers = $env->getExtension('core')->getEscapers();
            }

            if (isset($escapers[$strategy])) {
                return call_user_func($escapers[$strategy], $env, $string, $charset);
            }

            $validStrategies = implode(', ', array_merge(array('html', 'js', 'url', 'css', 'html_attr'), array_keys($escapers)));

            throw new Twig_Error_Runtime(sprintf('Invalid escaping strategy "%s" (valid ones: %s).', $strategy, $validStrategies));
    }
}