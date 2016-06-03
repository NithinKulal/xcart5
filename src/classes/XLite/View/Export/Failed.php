<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Export;

/**
 * Failed section 
 */
class Failed extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/failed.twig';
    }

    /**
     * Get errors 
     * 
     * @return array
     */
    protected function getErrors()
    {
        $list = $this->getGenerator() ? $this->getGenerator()->getErrors() : array();
        $result = array();
        foreach ($list as $message) {
            if (!isset($result[$message['title']])) {
                $result[$message['title']] = $message;
            }
        }

        return $result;
    }
}

