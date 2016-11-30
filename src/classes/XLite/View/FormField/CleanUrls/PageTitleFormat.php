<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\CleanUrls;


class PageTitleFormat extends \XLite\View\FormField\AFormField
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'form_field/clean_urls/page_title_format.css';

        return $list;
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
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
        return 'form_field/clean_urls/page_title_format.twig';
    }

    /**
     * Return text for help tooltip
     *
     * @return string
     */
    protected function getHelpLabel()
    {
        return static::t('These options separated by X, you can change it by modify X label', [
            'delimiter' => static::t('title-delimiter'),
            'modify_url' => $this->buildURL('labels', '', ['substring' => 'title-delimiter'])
        ]);
    }
}