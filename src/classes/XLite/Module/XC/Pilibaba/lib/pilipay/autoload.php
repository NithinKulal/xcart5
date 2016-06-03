<?php
/**
 * NOTICE OF LICENSE
 * Copyright (c) 2015~2016 Pilibaba.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *
 *  @author    Pilibaba <developer@pilibaba.com>
 *  @copyright 2015~2016 Pilibaba.com
 *  @license   https://opensource.org/licenses/MIT The MIT License
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (defined('PHP_VERSION') && version_compare(PHP_VERSION, '5.3') >= 0) {
    // use autoloader in higher versions
    /**
     * Pilipay's autoloader
     * @param $class string the class to be loaded
     */
    function PilipaySplAutoloader($class)
    {
        $pilipay = 'Pilipay';
        $class = ltrim($class, '\\');
        if (strncmp($class, $pilipay, strlen($pilipay)) === 0) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
            if (file_exists($file)) {
                include($file);
            }
        }
    }

    spl_autoload_register('PilipaySplAutoloader');
} else {
    // require all Pilipay's files directly in lower version
    !class_exists('PilipayLogger', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayLogger.php');
    !class_exists('PilipayModel', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayModel.php');
    !class_exists('PilipayError', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayError.php');
    !class_exists('PilipayCurl', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayCurl.php');
    !class_exists('PilipayGood', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayGood.php');
    !class_exists('PilipayOrder', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayOrder.php');
    !class_exists('PilipayPayResult', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayPayResult.php');
    !class_exists('PilipayConfig', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayConfig.php');
    !class_exists('PilipayCurrency', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayCurrency.php');
    !class_exists('PilipayWarehouseAddress', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayWarehouseAddress.php');
}

