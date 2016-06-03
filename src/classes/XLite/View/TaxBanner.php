<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Tax banner page
 *
 * @ListChild (list="admin.main.page.content.sub_section", zone="admin")
 */
class TaxBanner extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'tax_classes';
        $result[] = 'sales_tax';
        $result[] = 'vat_tax';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'tax_banner/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'tax_banner/body.twig';
    }

    /**
     * Return AvaTax Module link
     *
     * @return string
     */
    protected function getAvaTaxLink()
    {
        list($author, $module) = explode('\\', 'XC\\AvaTax');

        return \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getMarketplaceUrlByName($author, $module);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Controller\Admin\TaxClasses::isEnabled();
    }
}
