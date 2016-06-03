<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Tabber is a component allowing to organize your dialog into pages and
 * switch between the page using Tabs at the top.
 *
 * @ListChild (list="admin.center", zone="admin", weight="1000")
 */
class Tabber extends \XLite\View\AView
{
    /**
     * Widget parameters names
     */
    const PARAM_BODY      = 'body';
    const PARAM_SWITCH    = 'switch';

    /**
     * Lazy initialization cache
     *
     * @var array
     */
    protected $pages;

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (!\XLite::isAdminZone()
                || 0 < count($this->getTabberPages())
            )
            && \XLite::getController()->checkAccess();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabber.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_BODY   => new \XLite\Model\WidgetParam\TypeString('Body template file', '', false),
            static::PARAM_SWITCH => new \XLite\Model\WidgetParam\TypeString('Switch', 'page', false),
        ];
    }

    /**
     * Get prepared pages array for tabber
     *
     * @return array
     */
    protected function getTabberPages()
    {
        if (null === $this->pages) {
            $this->pages = [];
            $url = $this->get('url');
            $switch = $this->getParam(static::PARAM_SWITCH);

            $dialogPages = \XLite::getController()->getTabPages();

            if (is_array($dialogPages)) {
                foreach ($dialogPages as $page => $title) {
                    $linkTemplate = null;
                    if (is_array($title)) {
                        $linkTemplate = $title['linkTemplate'];
                        $title = $title['title'];
                    }
                    $p = new \XLite\Base();
                    $pageURL = preg_replace('/' . $switch . '=(\w+)/', $switch . '=' . $page, $url);
                    $p->set('url', $pageURL);
                    $p->set('title', $title);
                    $p->set('linkTemplate', $linkTemplate);
                    $p->set('key', $page);
                    $pageSwitch = sprintf($switch . '=' . $page);
                    $p->set('selected', (preg_match('/' . preg_quote($pageSwitch) . '(\Z|&)/Ss', $url)));
                    $this->pages[] = $p;
                }
            }

            // if there is only one tab page, set it as a seleted with the default URL
            if (1 === count($this->pages) || 'default' === $this->getPage()) {
                $this->pages[0]->set('selected', $url);
            }

        }

        return $this->pages;
    }

    /**
     * Return body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return $this->getParam(static::PARAM_BODY) ?: $this->getPageTemplate();
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        return !(\XLite::getController() instanceof \XLite\Controller\Admin\Settings)
            && 1 < count($this->getTabberPages());
    }
}
