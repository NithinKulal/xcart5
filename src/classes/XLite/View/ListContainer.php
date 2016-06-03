<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Module\XC\ThemeTweaker;

/**
 * View list collection container
 */
class ListContainer extends \XLite\View\AView
{
    const PARAM_INNER_TEMPLATE  = 'inner';
    const PARAM_INNER_LIST      = 'innerList';
    const PARAM_GROUP_NAME      = 'group';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_INNER_TEMPLATE => new \XLite\Model\WidgetParam\TypeFile('Template', ''),
            self::PARAM_INNER_LIST => new \XLite\Model\WidgetParam\TypeString('Inner List', ''),
            self::PARAM_GROUP_NAME => new \XLite\Model\WidgetParam\TypeString('Group name', ''),
        );
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getInnerTemplate()
    {
        return $this->getParam(self::PARAM_INNER_TEMPLATE);
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getInnerList()
    {
        return $this->getParam(self::PARAM_INNER_LIST);
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getGroupName()
    {
        return $this->getParam(self::PARAM_GROUP_NAME);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'list_container.twig';
    }

    /**
     * Print widget inner content
     *
     * @return string
     */
    public function displayInnerContent()
    {
        if ($this->getInnerList()) {
            $this->displayViewListContent($this->getInnerList());
        } elseif ($this->getInnerTemplate()) {
            $this->display($this->getInnerTemplate());
        } else {
            \XLite\Logger::getInstance()->log('No list or template was given to ListContainer', LOG_ERR);
        }
    }
}
