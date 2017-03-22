<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Controller\Customer;

/**
 * Sitemap controller
 */
class Sitemap extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $generator = \XLite\Module\CDev\XMLSitemap\Logic\SitemapGenerator::getInstance();

        $index = intval(\XLite\Core\Request::getInstance()->index);
        $content = $index ? $generator->getSitemap($index) : $generator->getIndex();
        if ($generator->isGenerated() && !$generator->isGenerationStarted()) {
            $this->displayContent($content);
        }else{
            $this->headerStatus(404);
        }

        $this->forceSendResponse();
        if (
            (!$generator->isGenerated() || $generator->isObsolete())
            && !$generator->isGenerationStarted()
        ) {
            $generator->generate();
        }
        die (0);
    }

    /**
     * Display content 
     * 
     * @param string $content Content
     *  
     * @return void
     */
    protected function displayContent($content)
    {
        ob_start();
        header('Content-Type: application/xml; charset=UTF-8');
        header('Content-Length: ' . strlen($content));
        header('ETag: ' . md5($content));

        print ($content);

        $this->silent = true;
    }

    /**
     * Force browser to display response to user and continue executing
     * 
     * @return void;
     */
    protected function forceSendResponse()
    {
        ignore_user_abort(true);

        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', 1);
        }
        if(session_id()) {
            session_write_close();
        }
        header('Connection: close');
        header('Content-Length: '.ob_get_length());
        ob_end_flush();
        flush();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && !\XLite\Module\CDev\XMLSitemap\Logic\SitemapGenerator::getInstance()->isEmpty();
    }
}

