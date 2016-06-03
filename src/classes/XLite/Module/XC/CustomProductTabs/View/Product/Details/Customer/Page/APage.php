<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\Product\Details\Customer\Page;

/**
 * APage
 */
class APage extends \XLite\View\Product\Details\Customer\Page\APage implements \XLite\Base\IDecorator
{
    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        $weight = 5000;

        foreach ($this->getProduct()->getTabs() as $tab) {
            if ($tab->getEnabled()) {
                $list['tab' . $tab->getId()] = array(
                    'widget'     => '\XLite\Module\XC\CustomProductTabs\View\Tab',
                    'parameters' => array(
                        'tab' => $tab,
                    ),
                    'name'       => $tab->getName(),
                    'weight'     => $weight,
                );
                $weight++;
            }
        }

        return $list;
    }
}
