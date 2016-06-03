<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Layout
 */
class Layout extends \XLite\Controller\Admin\Layout implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'switch_layout_mode';

        return $list;
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
     * Switch state
     *
     * @return void
     */
    protected function doActionSwitchLayoutMode()
    {
        $value = !\XLite\Core\Config::getInstance()->XC->ThemeTweaker->layout_mode;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\ThemeTweaker',
                'name'     => 'layout_mode',
                'value'    => $value,
            )
        );

        \XLite\Core\TopMessage::addInfo(
            $value
                ? 'Layout editor is enabled'
                : 'Layout editor is disabled'
        );

        $this->setReturnURL($this->buildURL('layout'));
    }

    /**
     * Add warning after template is changed if custom CSS was defined and enabled
     *
     * @return void
     */
    protected function doActionChangeTemplate()
    {
        parent::doActionChangeTemplate();

        if (\XLite\Core\Config::getInstance()->XC->ThemeTweaker->use_custom_css) {
            $content = \Includes\Utils\FileManager::read(
                \XLite\Module\XC\ThemeTweaker\Main::getThemeDir() . 'custom.css'
            );
            if (!empty($content)) {
                \XLite\Core\TopMessage::getInstance()->addWarning(
                    'There are some custom CSS styles in your store. These styles may affect the look of the installed template. Review the custom styles and disable them if necessary.',
                    array('url' => $this->buildURL('custom_css'))
                );
            }
        }
    }
}
