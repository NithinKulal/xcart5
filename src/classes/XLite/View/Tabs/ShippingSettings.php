<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to shipping settings
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ShippingSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'shipping/style.css';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = [
            'shipping_settings' => [
                'weight' => 100,
                'title' => 'Settings',
                'template' => 'shipping/settings.twig',
            ],
            'shipping_methods' => [
                'weight' => 200,
                'title' => 'Carrier services',
                'widget'    => 'XLite\View\ItemsList\Model\Shipping\Methods',
            ],
            'shipping_test' => [
                'weight' => 300,
                'title' => 'Test rates',
                'template' => 'shipping/test.twig',
            ],
        ];

        if (\XLite::getController() instanceof \XLite\Controller\Admin\ShippingSettings) {
            $list[\XLite\Core\Request::getInstance()->target] = $list['shipping_settings'];
            unset($list['shipping_settings']);
        }

        return $list;
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $method = $this->getMethod();
        if ($method
            && !$method->getProcessorObject()->isConfigured()
        ) {
            unset($this->tabs['shipping_methods'], $this->tabs['shipping_test']);
        }

        if (isset($this->tabs['shipping_methods']) && count($method->getChildrenMethods()) === 1) {
            unset($this->tabs['shipping_methods']);
        }

        return parent::prepareTabs();
    }

    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        switch ($target) {
            case 'shipping_settings':
                $result = $this->getMethod()->getProcessorObject()->getSettingsURL();
                break;

            case 'shipping_methods':
            case 'shipping_test':
                $result = $this->buildURL($target, '', ['processor' => $this->getProcessorId()]);
                break;

            default:
                $result = parent::buildURL($target);
        }

        return $result;
    }

    /**
     * Returns settings template
     *
     * @return string
     */
    protected function getSettingsTemplate()
    {
        $method = $this->getMethod();

        return $method->getProcessorObject()->getSettingsTemplate();
    }

    /**
     * Returns test template
     *
     * @return string
     */
    protected function getTestTemplate()
    {
        $method = $this->getMethod();

        return $method->getProcessorObject()->getTestTemplate();
    }

    /**
     * Returns shipping method
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        return \XLite::getController()->getMethod();
    }

    /**
     * Returns shipping method
     *
     * @return null|integer
     */
    protected function getProcessorId()
    {
        return \XLite::getController()->getProcessorId();
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getMethod();
    }
}
