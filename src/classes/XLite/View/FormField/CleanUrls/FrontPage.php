<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\CleanUrls;


class FrontPage extends \XLite\View\FormField\AFormField
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'form_field/clean_urls/home_page.css';

        return $list;
    }
    
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_ITEMS_LIST;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/clean_urls/home_page.twig';
    }


    /**
     * Get homepage category model
     *
     * @return \XLite\Model\Category
     */
    protected function getModel()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find(1);
    }

    /**
     * Return homepage elements list
     *
     * @return array
     */
    protected function getHomepageElementsList()
    {
        return [
            'title' => [
                'label' => 'Front page title',
                'value' => $this->getModel()->getMetaTitle()
                    ?: $this->getModel()->getName()
            ],
            'meta_desc' => [
                'label' => 'Meta description',
                'value' => $this->getModel()->getMetaDesc()
            ],
            'meta_tags' => [
                'label' => 'Meta keywords',
                'value' => $this->getModel()->getMetaTags()
            ],
        ];
    }

    protected function getFrontPageEditURL()
    {
        return $this->buildURL('front_page');
    }
}
