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
 * @ListChild (list="taxes.help.section", zone="admin", weight=10)
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
        $result[] = 'canadian_taxes';

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
     * Define list of help links
     *
     * @return array
     */
    protected function defineHelpLinks()
    {
        $links = array();
        $links[] = array(
            'title' => 'Setting up VAT / GST',
            'url'   => '//kb.x-cart.com/en/taxes/setting_up_vat_gst.html',
        );
        $links[] = array(
            'title' => 'Setting up sales tax',
            'url'   => '//kb.x-cart.com/en/taxes/setting_up_sales_tax.html',
        );
        $links[] = array(
            'title' => 'Setting up Canadian taxes',
            'url'   => '//kb.x-cart.com/en/taxes/setting_up_canadian_taxes.html',
        );
        $links[] = array(
            'title' => 'Setting up tax classes',
            'url'   => '//kb.x-cart.com/en/taxes/setting_up_tax_classes.html',
        );

        return $links;
    }

    /**
     * Get list of help links
     *
     * @return array
     */
    protected function getHelpLinks()
    {
        return $this->defineHelpLinks();
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
