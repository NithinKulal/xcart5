<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes\Controller\Admin;

/**
 * Performance
 */
class Layout extends \XLite\Controller\Admin\Layout implements \XLite\Base\IDecorator
{
    /**
     * Return switch data
     *
     * @return array
     */
    protected function getSwitchData()
    {
        $template = \Xlite\Core\Request::getInstance()->template;

        if (\XLite\View\FormField\Select\Template::SKIN_STANDARD === $template) {
            /** @var \XLite\Model\Repo\Config $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
            $repo->createOption(
                array(
                    'category' => 'Layout',
                    'name'     => 'color',
                    'value'    => 'Default',
                )
            );

            $result = array();

        } else {
            $result = parent::getSwitchData();
        }

        return $result;
    }
}
