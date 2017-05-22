<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Controller\Admin;

/**
 * Module settings
 */
abstract class Module extends \XLite\Controller\Admin\Module implements \XLite\Base\IDecorator
{
    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            $this->getModuleID()
            && 'CDev\AmazonS3Images' == $this->getModule()->getActualName()
            && \XLite\Core\Request::getInstance()->isGet()
            && !\XLite\Core\TopMessage::getInstance()->getPreviousMessages()
        ) {
            $this->checkAmazonS3Settings();
        }

        parent::handleRequest();
    }

    /**
     * Check amazon S3 settings 
     * 
     * @return void
     */
    protected function checkAmazonS3Settings()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->AmazonS3Images;

        if (!function_exists('curl_init')) {
            \XLite\Core\TopMessage::addError(
                'This module uses PHP\'s cURL functions which are disabled on your web server'
            );

        } elseif (
            $config->access_key
            && $config->secret_key
            && !\XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isValid()
        ) {
            \XLite\Core\TopMessage::addWarning(
                'Connection to Amazon S3 failed.'
                . ' Check whether the AWS Access key и AWS Secret key specified in the module settings are correct.'
            );
        }
    }
}
