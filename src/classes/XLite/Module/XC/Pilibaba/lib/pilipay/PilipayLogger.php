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

/**
 * Class PilipayLogger
 * This class is used for customizing logging.
 *
 * For example:
 *
 *   // to record logs into a file:
 *   PilipayLogger::instance()->setHandler(function($level, $msg){
 *       file_put_contents('path/to/pilipay/log/file', sprintf('%s %s: %s'.PHP_EOL, date('Y-m-d H:i:s'), $level, $msg));
 *   });
 *
 */
class PilipayLogger
{
    /**
     * @param callable $handler function ($level, $msg)...
     */
    public function setHandler($handler){
        $this->handler = $handler;
    }

    /**
     * @param $level string error/info/debug...
     * @param $msg string
     */
    public function log($level, $msg){
        if (!is_null($this->handler)){
            call_user_func($this->handler, $level, $msg);
        }
    }

    /**
     * @return PilipayLogger
     */
    public static function instance(){
        if (!self::$instance){
            self::$instance = new PilipayLogger();
        }

        return self::$instance;
    }

    protected static $instance;
    private $handler = null;
}

