<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 */
class SectionsNavigation extends Sections
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            array(
                array(
                    'file'  => FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/style.less',
                    'media' => 'screen',
                    'merge' => 'bootstrap/css/bootstrap.less',
                ),
            )
        );
    }

    public function getJSFiles()
    {
        return array(
            FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/navigation-item.js',
            FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/navigation.js',
        );
    }

    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/template.twig';
    }

    /**
     * Defines the additional data array
     *
     * @return array
     */
    protected function defineWidgetData()
    {
        $sections = array_keys($this->defineTabs());

        $data = array();

        if (count($sections) > 0) {
            $data['start_with'] = array_shift($sections);
        }

        return $data;
    }

    /**
     * Outputs the additional data as json text
     *
     * @return string
     */
    protected function getWidgetData()
    {
        return json_encode($this->defineWidgetData());
    }
}
