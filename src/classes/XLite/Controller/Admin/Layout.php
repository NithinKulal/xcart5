<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Layout
 */
class Layout extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'change_layout';

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
     * Returns templates store URL
     *
     * @return string
     */
    public function getTemplatesStoreURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/extensions/xcart-templates.html');
    }

    /**
     * Returns templates store URL
     *
     * @return string
     */
    public function getFreeQuoteURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/contact-us.html?reason=subj_2');
    }

    /**
     * Change template
     *
     * @return void
     */
    protected function doActionChangeTemplate()
    {
        \XLite\Core\Request::getInstance()->switch = $this->getSwitchData();

        unset(\XLite\Core\Session::getInstance()->returnURL);
        $controller = new \XLite\Controller\Admin\AddonsListInstalled(\XLite\Core\Request::getInstance()->getData());
        $controller->init();

        $controller->doActionSwitch();

        $this->setReturnURL(
            $this->buildURL('layout', '', array('moduleId' => \Xlite\Core\Request::getInstance()->template))
        );
    }

    /**
     * Return switch data
     *
     * @return array
     */
    protected function getSwitchData()
    {
        $result = array();
        $template = \Xlite\Core\Request::getInstance()->template;
        $moduleId = null;
        $color = null;

        if (\XLite\View\FormField\Select\Template::SKIN_STANDARD !== $template) {
            list ($moduleId, $color) = (false !== strpos($template, '_'))
                ? explode('_', $template)
                : array($template, null);
        }

        if ($color) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'Layout',
                    'name'     => 'color',
                    'value'    => $color,
                )
            );
        }

        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();
        // turn current skin module off
        if ($module && $module->getModuleId() !== (int) $moduleId) {
            $result[$module->getModuleId()] = array(
                'old' => true,
                'new' => null
            );
        }

        if (\XLite\View\FormField\Select\Template::SKIN_STANDARD !== $template
            && (!$module || $module->getModuleId() !== (int) $moduleId)
        ) {
            $result[$moduleId] = array(
                'old' => false,
                'new' => true
            );
        }

        return $result;
    }

    /**
     * Change layout
     *
     * @return void
     */
    protected function doActionChangeLayout()
    {
        $layoutType = \XLite\Core\Request::getInstance()->layout_type;
        $availableLayoutTypes = \XLite\Core\Layout::getInstance()->getAvailableLayoutTypes();

        if (in_array($layoutType, $availableLayoutTypes, true)) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'Layout',
                    'name' => 'layout_type',
                    'value' => $layoutType,
                )
            );
        }

        \XLite\Core\TopMessage::addInfo(
            'Layout has been changed. Review the updated storefront.',
            array(
                'storefront' => $this->getShopURL('')
            )
        );

        $this->setReturnURL($this->buildURL('layout'));
    }

    // {{{ Layout types

    /**
     * Returns available layout types
     *
     * @return array
     */
    public function getLayoutTypes()
    {
        return \XLite\Core\Layout::getInstance()->getAvailableLayoutTypes();
    }

    /**
     * Returns current layout types
     *
     * @return string
     */
    public function getLayoutType()
    {
        return \XLite\Core\Layout::getInstance()->getLayoutType();
    }
}
