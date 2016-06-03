<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Theme tweaker templates controller
 */
class ThemeTweakerTemplates extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'switch';

        return $list;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Look & Feel');
    }

    /**
     * Returns link to store front
     *
     * @return string
     */
    public function getStoreFrontLink()
    {
        $styleClass = \XLite\Core\Config::getInstance()->XC->ThemeTweaker->edit_mode
            ? ''
            : 'hidden';

        $button = new \XLite\View\Button\SimpleLink(array(
            \XLite\View\Button\SimpleLink::PARAM_LABEL => 'Open storefront',
            \XLite\View\Button\SimpleLink::PARAM_LOCATION => $this->getShopURL(),
            \XLite\View\Button\SimpleLink::PARAM_BLANK => true,
            \XLite\View\Button\SimpleLink::PARAM_STYLE => $styleClass,
        ));

        return $button->getContent();
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\Module\XC\ThemeTweaker\View\ItemsList\Model\Template;
        $list->processQuick();
    }

    /**
     * Switch state
     *
     * @return void
     */
    protected function doActionSwitch()
    {
        $value = !\XLite\Core\Config::getInstance()->XC->ThemeTweaker->edit_mode;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\ThemeTweaker',
                'name'     => 'edit_mode',
                'value'    => $value,
            )
        );

        \XLite\Core\TopMessage::addInfo(
            $value
                ? 'Webmaster mode is enabled'
                : 'Webmaster mode is disabled'
        );

        $this->setReturnURL($this->buildURL('theme_tweaker_templates'));
    }
}
