<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\FormField\Select;

/**
 * Use Custom Open Graph selector
 */
class CustomOpenGraph extends \XLite\View\FormField\Input\Text
{
    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/GoSocial/product.js';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/GoSocial';
    }

    /**
     * Return default template
     *
     * @return array
     */
    protected function getFieldTemplate()
    {
        return 'custom_og.twig';
    }

    /**
     * Get entity object
     *
     * @return \XLite\Model\Product|\XLite\Model\Category
     */
    protected function getEntity()
    {
        switch ($this->getTarget()) {
            case 'product':
                $result = $this->getProduct();
                break;

            case 'category':
                $result = $this->getCategory();
                break;

            case 'front_page':
                $result = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();
                break;

            default:
                $result = null;
        }

        return $result;
    }

    /**
     * Get entity's OpenGraphMetaTags
     *
     * @param boolean $flag Flag
     *
     * @return string
     */
    protected function getOpenGraphMetaTags($flag)
    {
        return $this->getEntity() ? $this->getEntity()->getOpenGraphMetaTags($flag) : '';
    }

    /**
     * Get entity's useCustomOG flag
     *
     * @return string
     */
    protected function getUseCustomOG()
    {
        return $this->getEntity() ? $this->getEntity()->getUseCustomOG() : '';
    }
}
