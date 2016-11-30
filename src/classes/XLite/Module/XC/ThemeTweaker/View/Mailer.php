<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Theme tweaker template page view
 */
class Mailer extends \XLite\View\Mailer implements \XLite\Base\IDecorator
{
    public function getNotificationEditableContent($dir, $data, $interface)
    {
        array_walk($data, function ($value, $key) {
            $this->set($key, $value);
        });

        $this->set('dir', $dir);
        $result = $this->compile('modules/XC/ThemeTweaker/common/layout.twig', $interface, false);

        return \XLite\Core\Mailer::getInstance()->populateVariables($result);
    }

    public function getNotificationPreviewContent($dir, $data, $interface)
    {
        array_walk($data, function ($value, $key) {
            $this->set($key, $value);
        });

        $this->set('dir', $dir);
        $result = $this->compile($this->get('layoutTemplate'), $interface, true);

        return \XLite\Core\Mailer::getInstance()->populateVariables($result);
    }
}